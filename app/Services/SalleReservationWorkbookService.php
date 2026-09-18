<?php

namespace Modules\PlanificationStages\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\PlanificationStages\Models\Salle;
use Modules\PlanificationStages\Models\SalleOccupation;
use Modules\PlanificationStages\Models\SessionStage;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use RuntimeException;

class SalleReservationWorkbookService
{
    private const MASTER_PATH =
        'planificationstages/salles/master.xlsx';

    private const META_PATH =
        'planificationstages/salles/master.json';

    private const SOURCE =
        'excel_sharepoint';

    public function import(
        string $path,
        ?string $originalName = null
    ): array {
        if (
            ! is_file(
                $path
            )
        ) {
            throw new RuntimeException(
                'Le fichier Excel est introuvable.'
            );
        }

        $spreadsheet =
            IOFactory::load(
                $path
            );

        $sheet =
            $spreadsheet
                ->getSheetByName(
                    'resa salle'
                )
            ?? $spreadsheet
                ->getActiveSheet();

        $layout =
            $this->analyseLayout(
                $sheet
            );

        if (
            count(
                $layout['rooms']
            )
            === 0
        ) {
            throw new RuntimeException(
                'Aucune salle n’a été détectée dans le fichier.'
            );
        }

        if (
            count(
                $layout['dates']
            )
            < 200
        ) {
            throw new RuntimeException(
                'Le calendrier du fichier semble incomplet : '
                . count(
                    $layout['dates']
                )
                . ' colonnes de dates détectées.'
            );
        }

        $fileName =
            $originalName
            ?: basename(
                $path
            );

        $result =
            DB::transaction(
                function () use (
                    $sheet,
                    $layout,
                    $fileName
                ): array {
                    $roomIds = [];

                    $created = 0;
                    $updated = 0;
                    $withoutCapacity = 0;

                    foreach (
                        $layout['rooms']
                        as $roomKey =>
                        $room
                    ) {
                        $query =
                            Salle::query();

                        if (
                            filled(
                                $room['code']
                            )
                        ) {
                            $salle =
                                $query
                                    ->where(
                                        'code',
                                        $room['code']
                                    )
                                    ->first();
                        } else {
                            $salle =
                                $query
                                    ->where(
                                        'nom',
                                        $room['nom']
                                    )
                                    ->first();
                        }

                        if (! $salle) {
                            $salle =
                                new Salle();

                            $created++;
                        } else {
                            $updated++;
                        }

                        $values = [
                            'nom' =>
                                $room['nom'],

                            'actif' =>
                                true,
                        ];

                        if (
                            filled(
                                $room['code']
                            )
                        ) {
                            $values['code'] =
                                $room['code'];
                        }

                        if (
                            $room['capacite']
                            !== null
                        ) {
                            $values['capacite'] =
                                $room['capacite'];
                        }

                        $salle
                            ->forceFill(
                                $values
                            )
                            ->save();

                        if (
                            $salle->capacite
                            === null
                        ) {
                            $withoutCapacity++;
                        }

                        $roomIds[
                            $roomKey
                        ] =
                            $salle->id;
                    }

                    $occupations =
                        $this
                            ->extractOccupations(
                                $sheet,
                                $layout,
                                $roomIds,
                                $fileName
                            );

                    /*
                     * Le fichier importé représente un instantané.
                     * On remplace donc uniquement les occupations
                     * précédemment importées depuis Excel.
                     */
                    SalleOccupation::query()
                        ->where(
                            'source',
                            self::SOURCE
                        )
                        ->delete();

                    $ignoredSkeletor = 0;
                    $inserted = 0;

                    foreach (
                        $occupations
                        as $occupation
                    ) {
                        if (
                            $this
                                ->matchesExistingSkeletorSession(
                                    $occupation
                                )
                        ) {
                            $ignoredSkeletor++;

                            continue;
                        }

                        SalleOccupation::create(
                            $occupation
                        );

                        $inserted++;
                    }

                    return [
                        'annee' =>
                            $layout['year'],

                        'salles' =>
                            count(
                                $layout['rooms']
                            ),

                        'salles_creees' =>
                            $created,

                        'salles_mises_a_jour' =>
                            $updated,

                        'salles_sans_capacite' =>
                            $withoutCapacity,

                        'occupations' =>
                            $inserted,

                        'reservations_skeletor_ignorees' =>
                            $ignoredSkeletor,
                    ];
                }
            );

        Storage::disk('local')
            ->put(
                self::MASTER_PATH,
                file_get_contents(
                    $path
                )
            );

        Storage::disk('local')
            ->put(
                self::META_PATH,
                json_encode(
                    [
                        'original_name' =>
                            $fileName,

                        'year' =>
                            $result['annee'],

                        'imported_at' =>
                            now()
                                ->toIso8601String(),
                    ],
                    JSON_PRETTY_PRINT
                    | JSON_UNESCAPED_UNICODE
                )
            );

        return $result;
    }

    public function export(): string
    {
        $disk =
            Storage::disk(
                'local'
            );

        if (
            ! $disk->exists(
                self::MASTER_PATH
            )
        ) {
            throw new RuntimeException(
                'Aucun fichier maître de réservation n’a encore été importé.'
            );
        }

        $masterPath =
            $disk->path(
                self::MASTER_PATH
            );

        $spreadsheet =
            IOFactory::load(
                $masterPath
            );

        $sheet =
            $spreadsheet
                ->getSheetByName(
                    'resa salle'
                )
            ?? $spreadsheet
                ->getActiveSheet();

        $layout =
            $this->analyseLayout(
                $sheet
            );

        $dateToColumn = [];

        foreach (
            $layout['dates']
            as $column =>
            $date
        ) {
            $dateToColumn[
                $date
            ] =
                $column;
        }

        $roomsByCode = [];
        $roomsByName = [];

        foreach (
            $layout['rooms']
            as $roomKey =>
            $room
        ) {
            if (
                filled(
                    $room['code']
                )
            ) {
                $roomsByCode[
                    Str::upper(
                        $room['code']
                    )
                ] =
                    $roomKey;
            }

            $roomsByName[
                $this->normalize(
                    $room['nom']
                )
            ] =
                $roomKey;
        }

        $year =
            $layout['year'];

        $yearStart =
            Carbon::create(
                $year,
                1,
                1,
                0,
                0,
                0
            );

        $yearEnd =
            Carbon::create(
                $year,
                12,
                31,
                23,
                59,
                59
            );

        $sessions =
            SessionStage::query()
                ->with([
                    'stage',
                    'salle',
                ])
                ->whereNotNull(
                    'salle_id'
                )
                ->whereIn(
                    'statut',
                    [
                        'planifiee',
                        'confirmee',
                    ]
                )
                ->where(
                    'debut',
                    '<=',
                    $yearEnd
                )
                ->where(
                    'fin',
                    '>=',
                    $yearStart
                )
                ->orderBy(
                    'debut'
                )
                ->get();

        $existing =
            $this
                ->existingOccupiedCells(
                    $sheet,
                    $layout
                );

        $exportedSessions = 0;
        $exportedBlocks = 0;

        foreach (
            $sessions
            as $session
        ) {
            if (
                ! $session->stage
                || ! $session->salle
            ) {
                continue;
            }

            $roomKey =
                null;

            if (
                filled(
                    $session
                        ->salle
                        ->code
                )
            ) {
                $roomKey =
                    $roomsByCode[
                        Str::upper(
                            $session
                                ->salle
                                ->code
                        )
                    ]
                    ?? null;
            }

            if (
                $roomKey
                === null
            ) {
                $roomKey =
                    $roomsByName[
                        $this->normalize(
                            $session
                                ->salle
                                ->nom
                        )
                    ]
                    ?? null;
            }

            if (
                $roomKey
                === null
            ) {
                throw new RuntimeException(
                    'Export impossible : la salle « '
                    . $session
                        ->salle
                        ->nom
                    . ' » de la session '
                    . $session
                        ->code_session
                    . ' n’existe pas dans le fichier maître.'
                );
            }

            $label =
                trim(
                    (string) (
                        $session
                            ->stage
                            ->libelle_court
                        ?: $session
                            ->stage
                            ->libelle_long
                        ?: $session
                            ->code_session
                    )
                );

            $segments =
                $this
                    ->sessionSegments(
                        $session,
                        $layout,
                        $dateToColumn,
                        $roomKey
                    );

            $blankSegments = [];

            foreach (
                $segments
                as $segment
            ) {
                $state =
                    $this
                        ->segmentOccupancyState(
                            $segment,
                            $existing,
                            $label
                        );

                if (
                    $state
                    === 'same'
                ) {
                    continue;
                }

                if (
                    $state
                    === 'conflict'
                ) {
                    throw new RuntimeException(
                        'Conflit pendant l’export : '
                        . $session
                            ->code_session
                        . ' / '
                        . $label
                        . ' rencontre une réservation déjà présente dans le fichier pour '
                        . $session
                            ->salle
                            ->nom
                        . '.'
                    );
                }

                $blankSegments[] =
                    $segment;
            }

            foreach (
                $this
                    ->groupSegments(
                        $blankSegments
                    )
                as $group
            ) {
                $range =
                    Coordinate::stringFromColumnIndex(
                        $group['start_col']
                    )
                    . $group['start_row']
                    . ':'
                    . Coordinate::stringFromColumnIndex(
                        $group['end_col']
                    )
                    . $group['end_row'];

                if (
                    $group['start_col']
                        !== $group['end_col']
                    || $group['start_row']
                        !== $group['end_row']
                ) {
                    $sheet
                        ->mergeCells(
                            $range
                        );
                }

                $sheet
                    ->getCell(
                        [
                            $group['start_col'],
                            $group['start_row'],
                        ]
                    )
                    ->setValue(
                        $label
                    );

                $style =
                    $sheet
                        ->getStyle(
                            $range
                        );

                $style
                    ->getFill()
                    ->setFillType(
                        Fill::FILL_SOLID
                    )
                    ->getStartColor()
                    ->setARGB(
                        'FFFFFF00'
                    );

                $style
                    ->getAlignment()
                    ->setHorizontal(
                        Alignment::HORIZONTAL_CENTER
                    )
                    ->setVertical(
                        Alignment::VERTICAL_CENTER
                    )
                    ->setWrapText(
                        true
                    );

                $exportedBlocks++;
            }

            if (
                count(
                    $segments
                )
                > 0
            ) {
                $exportedSessions++;
            }
        }

        $relative =
            'planificationstages/salles/exports/'
            . 'resa_salle_skeletor_'
            . now()
                ->format(
                    'Ymd_His'
                )
            . '.xlsx';

        $outputPath =
            $disk->path(
                $relative
            );

        $directory =
            dirname(
                $outputPath
            );

        if (
            ! is_dir(
                $directory
            )
        ) {
            mkdir(
                $directory,
                0775,
                true
            );
        }

        $writer =
            IOFactory::createWriter(
                $spreadsheet,
                'Xlsx'
            );

        $writer->save(
            $outputPath
        );

        return $outputPath;
    }

    private function analyseLayout(
        Worksheet $sheet
    ): array {
        $highestColumn =
            Coordinate::columnIndexFromString(
                $sheet
                    ->getHighestColumn()
            );

        $highestRow =
            $sheet
                ->getHighestRow();

        $year =
            $this
                ->detectYear(
                    $sheet,
                    $highestColumn
                );

        $dates = [];
        $currentMonth = null;

        for (
            $column = 3;
            $column <= $highestColumn;
            $column++
        ) {
            $monthValue =
                $this
                    ->cellText(
                        $sheet,
                        $column,
                        2
                    );

            $month =
                $this
                    ->monthNumber(
                        $monthValue
                    );

            if (
                $month
                !== null
            ) {
                $currentMonth =
                    $month;
            }

            $dayValue =
                $this
                    ->cellText(
                        $sheet,
                        $column,
                        7
                    );

            if (
                $currentMonth
                === null
                || ! preg_match(
                    '/\d{1,2}/',
                    $dayValue,
                    $match
                )
            ) {
                continue;
            }

            $day =
                (int) $match[0];

            if (
                ! checkdate(
                    $currentMonth,
                    $day,
                    $year
                )
            ) {
                continue;
            }

            $dates[
                $column
            ] =
                sprintf(
                    '%04d-%02d-%02d',
                    $year,
                    $currentMonth,
                    $day
                );
        }

        $rooms = [];
        $rows = [];
        $currentRoom = null;

        for (
            $row = 8;
            $row <= $highestRow;
            $row++
        ) {
            $time =
                $this
                    ->parseTimeLabel(
                        $this
                            ->cellText(
                                $sheet,
                                2,
                                $row
                            )
                    );

            if (
                $time
                === null
            ) {
                $currentRoom =
                    null;

                continue;
            }

            $roomLabel =
                $this
                    ->cellText(
                        $sheet,
                        1,
                        $row
                    );

            if (
                $roomLabel
                !== ''
            ) {
                $currentRoom =
                    $this
                        ->roomMeta(
                            $roomLabel
                        );

                $rooms[
                    $currentRoom[
                        'key'
                    ]
                ] =
                    $currentRoom;
            }

            if (
                $currentRoom
                === null
            ) {
                continue;
            }

            $rows[
                $row
            ] = [
                'room_key' =>
                    $currentRoom[
                        'key'
                    ],

                'start' =>
                    $time['start'],

                'end' =>
                    $time['end'],
            ];
        }

        return [
            'year' =>
                $year,

            'dates' =>
                $dates,

            'rooms' =>
                $rooms,

            'rows' =>
                $rows,
        ];
    }

    private function detectYear(
        Worksheet $sheet,
        int $highestColumn
    ): int {
        for (
            $column = 3;
            $column <= $highestColumn;
            $column++
        ) {
            $value =
                $sheet
                    ->getCell(
                        [
                            $column,
                            4,
                        ]
                    )
                    ->getValue();

            if (
                ! is_numeric(
                    $value
                )
                || (float) $value
                    < 30000
            ) {
                continue;
            }

            try {
                return (int)
                    ExcelDate
                        ::excelToDateTimeObject(
                            (float) $value
                        )
                        ->format(
                            'Y'
                        );
            } catch (
                \Throwable
            ) {
                //
            }
        }

        throw new RuntimeException(
            'Impossible de déterminer l’année du calendrier depuis la ligne 4.'
        );
    }

    private function extractOccupations(
        Worksheet $sheet,
        array $layout,
        array $roomIds,
        string $fileName
    ): array {
        $result = [];
        $covered = [];

        foreach (
            $sheet
                ->getMergeCells()
            as $range
        ) {
            [
                [
                    $startColumn,
                    $startRow,
                ],
                [
                    $endColumn,
                    $endRow,
                ],
            ] =
                Coordinate
                    ::rangeBoundaries(
                        $range
                    );

            if (
                $startColumn
                    < 3
                || ! isset(
                    $layout['rows'][
                        $startRow
                    ]
                )
                || ! isset(
                    $layout['rows'][
                        $endRow
                    ]
                )
            ) {
                continue;
            }

            $startRowMeta =
                $layout['rows'][
                    $startRow
                ];

            $endRowMeta =
                $layout['rows'][
                    $endRow
                ];

            if (
                $startRowMeta[
                    'room_key'
                ]
                !== $endRowMeta[
                    'room_key'
                ]
            ) {
                continue;
            }

            $label =
                trim(
                    (string)
                    $sheet
                        ->getCell(
                            [
                                $startColumn,
                                $startRow,
                            ]
                        )
                        ->getCalculatedValue()
                );

            if (
                $label
                === ''
            ) {
                continue;
            }

            for (
                $row = $startRow;
                $row <= $endRow;
                $row++
            ) {
                if (
                    ! isset(
                        $layout['rows'][
                            $row
                        ]
                    )
                    || $layout['rows'][
                        $row
                    ][
                        'room_key'
                    ]
                    !== $startRowMeta[
                        'room_key'
                    ]
                ) {
                    continue 2;
                }

                for (
                    $column =
                        $startColumn;
                    $column <=
                        $endColumn;
                    $column++
                ) {
                    $covered[
                        $column
                        . ':'
                        . $row
                    ] =
                        true;
                }
            }

            foreach (
                range(
                    $startColumn,
                    $endColumn
                )
                as $column
            ) {
                if (
                    ! isset(
                        $layout['dates'][
                            $column
                        ]
                    )
                ) {
                    continue;
                }

                $this
                    ->appendOccupation(
                        $result,
                        $roomIds[
                            $startRowMeta[
                                'room_key'
                            ]
                        ],
                        $label,
                        $layout['dates'][
                            $column
                        ],
                        $startRowMeta[
                            'start'
                        ],
                        $endRowMeta[
                            'end'
                        ],
                        $fileName,
                        $sheet
                            ->getTitle(),
                        $range
                    );
            }
        }

        foreach (
            $layout['rows']
            as $row =>
            $rowMeta
        ) {
            foreach (
                $layout['dates']
                as $column =>
                $date
            ) {
                if (
                    isset(
                        $covered[
                            $column
                            . ':'
                            . $row
                        ]
                    )
                ) {
                    continue;
                }

                $label =
                    trim(
                        (string)
                        $sheet
                            ->getCell(
                                [
                                    $column,
                                    $row,
                                ]
                            )
                            ->getCalculatedValue()
                    );

                if (
                    $label
                    === ''
                ) {
                    continue;
                }

                $coordinate =
                    Coordinate
                        ::stringFromColumnIndex(
                            $column
                        )
                    . $row;

                $this
                    ->appendOccupation(
                        $result,
                        $roomIds[
                            $rowMeta[
                                'room_key'
                            ]
                        ],
                        $label,
                        $date,
                        $rowMeta[
                            'start'
                        ],
                        $rowMeta[
                            'end'
                        ],
                        $fileName,
                        $sheet
                            ->getTitle(),
                        $coordinate
                    );
            }
        }

        return array_values(
            $result
        );
    }

    private function appendOccupation(
        array &$result,
        int $salleId,
        string $label,
        string $date,
        string $startTime,
        string $endTime,
        string $fileName,
        string $sheetName,
        string $range
    ): void {
        $debut =
            Carbon::parse(
                $date
                . ' '
                . $startTime
            );

        $fin =
            Carbon::parse(
                $date
                . ' '
                . $endTime
            );

        $key =
            hash(
                'sha256',
                implode(
                    '|',
                    [
                        $salleId,
                        $label,
                        $debut
                            ->format(
                                'Y-m-d H:i:s'
                            ),
                        $fin
                            ->format(
                                'Y-m-d H:i:s'
                            ),
                        self::SOURCE,
                    ]
                )
            );

        $result[
            $key
        ] = [
            'salle_id' =>
                $salleId,

            'libelle' =>
                $label,

            'debut' =>
                $debut,

            'fin' =>
                $fin,

            'source' =>
                self::SOURCE,

            'source_fichier' =>
                $fileName,

            'excel_sheet' =>
                $sheetName,

            'excel_range' =>
                $range,

            'import_key' =>
                $key,

            'imported_at' =>
                now(),
        ];
    }

    private function matchesExistingSkeletorSession(
        array $occupation
    ): bool {
        return SessionStage::query()
            ->where(
                'salle_id',
                $occupation[
                    'salle_id'
                ]
            )
            ->whereIn(
                'statut',
                [
                    'planifiee',
                    'confirmee',
                ]
            )
            ->where(
                'debut',
                '<',
                $occupation[
                    'fin'
                ]
            )
            ->where(
                'fin',
                '>',
                $occupation[
                    'debut'
                ]
            )
            ->whereHas(
                'stage',
                fn ($query) =>
                    $query->where(
                        'libelle_court',
                        $occupation[
                            'libelle'
                        ]
                    )
            )
            ->exists();
    }

    private function existingOccupiedCells(
        Worksheet $sheet,
        array $layout
    ): array {
        $occupied = [];

        foreach (
            $sheet
                ->getMergeCells()
            as $range
        ) {
            [
                [
                    $startColumn,
                    $startRow,
                ],
                [
                    $endColumn,
                    $endRow,
                ],
            ] =
                Coordinate
                    ::rangeBoundaries(
                        $range
                    );

            if (
                $startColumn
                    < 3
                || ! isset(
                    $layout['rows'][
                        $startRow
                    ]
                )
            ) {
                continue;
            }

            $value =
                trim(
                    (string)
                    $sheet
                        ->getCell(
                            [
                                $startColumn,
                                $startRow,
                            ]
                        )
                        ->getCalculatedValue()
                );

            if (
                $value
                === ''
            ) {
                continue;
            }

            for (
                $row = $startRow;
                $row <= $endRow;
                $row++
            ) {
                if (
                    ! isset(
                        $layout['rows'][
                            $row
                        ]
                    )
                ) {
                    continue;
                }

                for (
                    $column =
                        $startColumn;
                    $column <=
                        $endColumn;
                    $column++
                ) {
                    if (
                        ! isset(
                            $layout['dates'][
                                $column
                            ]
                        )
                    ) {
                        continue;
                    }

                    $occupied[
                        $column
                        . ':'
                        . $row
                    ] =
                        $value;
                }
            }
        }

        foreach (
            $layout['rows']
            as $row =>
            $rowMeta
        ) {
            foreach (
                $layout['dates']
                as $column =>
                $date
            ) {
                $key =
                    $column
                    . ':'
                    . $row;

                if (
                    isset(
                        $occupied[
                            $key
                        ]
                    )
                ) {
                    continue;
                }

                $value =
                    trim(
                        (string)
                        $sheet
                            ->getCell(
                                [
                                    $column,
                                    $row,
                                ]
                            )
                            ->getCalculatedValue()
                    );

                if (
                    $value
                    !== ''
                ) {
                    $occupied[
                        $key
                    ] =
                        $value;
                }
            }
        }

        return $occupied;
    }

    private function sessionSegments(
        SessionStage $session,
        array $layout,
        array $dateToColumn,
        string $roomKey
    ): array {
        $rows =
            array_filter(
                $layout['rows'],
                fn (array $row): bool =>
                    $row[
                        'room_key'
                    ]
                    === $roomKey
            );

        if (
            count(
                $rows
            )
            === 0
        ) {
            return [];
        }

        $segments = [];

        $cursor =
            $session
                ->debut
                ->copy()
                ->startOfDay();

        $lastDay =
            $session
                ->fin
                ->copy()
                ->startOfDay();

        while (
            $cursor
                ->lte(
                    $lastDay
                )
        ) {
            $date =
                $cursor
                    ->format(
                        'Y-m-d'
                    );

            $column =
                $dateToColumn[
                    $date
                ]
                ?? null;

            if (
                $column
                !== null
            ) {
                $businessStart =
                    $cursor
                        ->copy()
                        ->setTime(
                            8,
                            0
                        );

                $businessEnd =
                    $cursor
                        ->copy()
                        ->setTime(
                            16,
                            0
                        );

                $start =
                    $session
                        ->debut
                        ->greaterThan(
                            $businessStart
                        )
                        ? $session
                            ->debut
                            ->copy()
                        : $businessStart;

                $end =
                    $session
                        ->fin
                        ->lessThan(
                            $businessEnd
                        )
                        ? $session
                            ->fin
                            ->copy()
                        : $businessEnd;

                if (
                    $end
                        ->greaterThan(
                            $start
                        )
                ) {
                    $startTime =
                        $start
                            ->format(
                                'H:i'
                            );

                    $endTime =
                        $end
                            ->format(
                                'H:i'
                            );

                    $startRow =
                        null;

                    $endRow =
                        null;

                    foreach (
                        $rows
                        as $rowNumber =>
                        $row
                    ) {
                        if (
                            $row['start']
                            === $startTime
                        ) {
                            $startRow =
                                $rowNumber;
                        }

                        if (
                            $row['end']
                            === $endTime
                        ) {
                            $endRow =
                                $rowNumber;
                        }
                    }

                    if (
                        $startRow
                            === null
                        || $endRow
                            === null
                    ) {
                        throw new RuntimeException(
                            'Export impossible : la session '
                            . $session
                                ->code_session
                            . ' utilise un horaire non représentable dans la grille Excel ('
                            . $startTime
                            . '-'
                            . $endTime
                            . ').'
                        );
                    }

                    $segments[] = [
                        'column' =>
                            $column,

                        'start_row' =>
                            $startRow,

                        'end_row' =>
                            $endRow,

                        'date' =>
                            $date,
                    ];
                }
            }

            $cursor
                ->addDay();
        }

        return $segments;
    }

    private function segmentOccupancyState(
        array $segment,
        array $existing,
        string $label
    ): string {
        $found = [];

        for (
            $row =
                $segment[
                    'start_row'
                ];
            $row <=
                $segment[
                    'end_row'
                ];
            $row++
        ) {
            $value =
                $existing[
                    $segment[
                        'column'
                    ]
                    . ':'
                    . $row
                ]
                ?? null;

            if (
                $value
                !== null
            ) {
                $found[] =
                    $value;
            }
        }

        if (
            count(
                $found
            )
            === 0
        ) {
            return 'blank';
        }

        foreach (
            $found
            as $value
        ) {
            if (
                trim(
                    (string) $value
                )
                !== $label
            ) {
                return 'conflict';
            }
        }

        return 'same';
    }

    private function groupSegments(
        array $segments
    ): array {
        if (
            count(
                $segments
            )
            === 0
        ) {
            return [];
        }

        usort(
            $segments,
            fn (
                array $a,
                array $b
            ): int =>
                $a['column']
                <=>
                $b['column']
        );

        $groups = [];

        foreach (
            $segments
            as $segment
        ) {
            $lastIndex =
                count(
                    $groups
                )
                - 1;

            if (
                $lastIndex
                    >= 0
                && $groups[
                    $lastIndex
                ][
                    'end_col'
                ]
                    + 1
                    === $segment[
                        'column'
                    ]
                && $groups[
                    $lastIndex
                ][
                    'start_row'
                ]
                    === $segment[
                        'start_row'
                    ]
                && $groups[
                    $lastIndex
                ][
                    'end_row'
                ]
                    === $segment[
                        'end_row'
                    ]
            ) {
                $groups[
                    $lastIndex
                ][
                    'end_col'
                ] =
                    $segment[
                        'column'
                    ];

                continue;
            }

            $groups[] = [
                'start_col' =>
                    $segment[
                        'column'
                    ],

                'end_col' =>
                    $segment[
                        'column'
                    ],

                'start_row' =>
                    $segment[
                        'start_row'
                    ],

                'end_row' =>
                    $segment[
                        'end_row'
                    ],
            ];
        }

        return $groups;
    }

    private function roomMeta(
        string $raw
    ): array {
        $name =
            preg_replace(
                '/\s+/u',
                ' ',
                trim(
                    str_replace(
                        [
                            "\r",
                            "\n",
                        ],
                        ' ',
                        $raw
                    )
                )
            );

        $code = null;

        if (
            preg_match(
                '/\b([BG]\d{3})\b/i',
                $name,
                $match
            )
        ) {
            $code =
                Str::upper(
                    $match[1]
                );
        } elseif (
            str_contains(
                $this->normalize(
                    $name
                ),
                'GASCOGNE'
            )
        ) {
            $code =
                'GASCOGNE';
        } elseif (
            str_contains(
                $this->normalize(
                    $name
                ),
                'SIMCP'
            )
        ) {
            $code =
                'SIMCP';
        }

        $capacity = null;

        if (
            preg_match(
                '/\b(\d+)\s*PAX\b/i',
                $name,
                $match
            )
        ) {
            $capacity =
                (int) $match[1];
        }

        $key =
            $code
            ?: $this->normalize(
                $name
            );

        return [
            'key' =>
                $key,

            'code' =>
                $code,

            'nom' =>
                $name,

            'capacite' =>
                $capacity,
        ];
    }

    private function parseTimeLabel(
        string $value
    ): ?array {
        $value =
            preg_replace(
                '/\s+/u',
                '',
                trim(
                    $value
                )
            );

        if (
            ! preg_match(
                '/^(\d{2})h(\d{2})-(\d{2})h(\d{2})$/i',
                $value,
                $match
            )
        ) {
            return null;
        }

        return [
            'start' =>
                $match[1]
                . ':'
                . $match[2],

            'end' =>
                $match[3]
                . ':'
                . $match[4],
        ];
    }

    private function monthNumber(
        string $value
    ): ?int {
        $value =
            $this->normalize(
                $value
            );

        $months = [
            'JANVIER' => 1,
            'FEVRIER' => 2,
            'MARS' => 3,
            'AVRIL' => 4,
            'MAI' => 5,
            'JUIN' => 6,
            'JUILLET' => 7,
            'AOUT' => 8,
            'SEPTEMBRE' => 9,
            'OCTOBRE' => 10,
            'NOVEMBRE' => 11,
            'DECEMBRE' => 12,
        ];

        foreach (
            $months
            as $monthName =>
            $number
        ) {
            if (
                str_contains(
                    $value,
                    $monthName
                )
            ) {
                return $number;
            }
        }

        return null;
    }

    private function cellText(
        Worksheet $sheet,
        int $column,
        int $row
    ): string {
        $value =
            $sheet
                ->getCell(
                    [
                        $column,
                        $row,
                    ]
                )
                ->getFormattedValue();

        if (
            $value
            === null
        ) {
            return '';
        }

        return trim(
            (string) $value
        );
    }

    private function normalize(
        string $value
    ): string {
        return Str::upper(
            preg_replace(
                '/\s+/u',
                ' ',
                Str::ascii(
                    trim(
                        $value
                    )
                )
            )
        );
    }
}
