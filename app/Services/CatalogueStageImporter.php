<?php

namespace Modules\PlanificationStages\Services;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Modules\PlanificationStages\Models\Stage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use RuntimeException;
use Throwable;

class CatalogueStageImporter
{
    public function import(string $filePath): array
    {
        $result = [
            'analysees' => 0,
            'creees' => 0,
            'mises_a_jour' => 0,
            'inchangees' => 0,
            'desactivees' => 0,
            'ambigues' => 0,
            'prerequis' => 0,
            'erreurs' => [],
        ];

        $spreadsheet = IOFactory::load($filePath);

        $sheet = $spreadsheet->getSheetByName('catalogue de stage');

        if (! $sheet) {
            throw new RuntimeException(
                'L’onglet "catalogue de stage" est introuvable.'
            );
        }

        $rows = $sheet->toArray(
            null,
            true,
            false,
            true
        );

        if (count($rows) < 2) {
            throw new RuntimeException(
                'Le catalogue Excel est vide.'
            );
        }

        $headerRow = array_shift($rows);

        $columns = [];

        foreach ($headerRow as $column => $header) {
            if ($header === null) {
                continue;
            }

            $columns[
                $this->normalizeHeader((string) $header)
            ] = $column;
        }

        foreach ([
            'centre de formation',
            'libelle court de la formation',
        ] as $requiredHeader) {
            if (! isset($columns[$requiredHeader])) {
                throw new RuntimeException(
                    "Colonne obligatoire absente : {$requiredHeader}"
                );
            }
        }

        foreach ($rows as $index => $row) {
            $excelRow = $index + 2;

            try {
                $libelleCourt = $this->stringValue(
                    $this->value(
                        $row,
                        $columns,
                        'libelle court de la formation'
                    )
                );

                // Ignore les lignes vides.
                if ($libelleCourt === null) {
                    continue;
                }

                $result['analysees']++;

                $numero = $this->stringValue(
                    $this->value(
                        $row,
                        $columns,
                        'numero'
                    )
                );

                $centre = $this->stringValue(
                    $this->value(
                        $row,
                        $columns,
                        'centre de formation'
                    )
                );

                $matchKey = $this->makeMatchKey(
                    $centre,
                    $libelleCourt
                );

                $prerequis = $this->extractPrerequis(
                    $row,
                    $columns
                );

                $payload = [
                    'numero_externe' => $numero,

                    'date_creation_catalogue' => $this->dateValue(
                        $this->value(
                            $row,
                            $columns,
                            'date de creation'
                        )
                    ),

                    'date_maj_catalogue' => $this->dateValue(
                        $this->value(
                            $row,
                            $columns,
                            'date de maj'
                        )
                    ),

                    'nature_maj' => $this->stringValue(
                        $this->value(
                            $row,
                            $columns,
                            'nature de maj ou demande de suppression'
                        )
                    ),

                    'raf' => $this->stringValue(
                        $this->value(
                            $row,
                            $columns,
                            'raf'
                        )
                    ),

                    'centre_formation' => $centre,

                    // La faute "Typlologie" existe dans le fichier source.
                    'typologie' => $this->stringValue(
                        $this->value(
                            $row,
                            $columns,
                            'typlologie'
                        )
                    ),

                    'libelle_court' => $libelleCourt,

                    'appellation_chorus' => $this->stringValue(
                        $this->value(
                            $row,
                            $columns,
                            'appellation chorus can'
                        )
                    ),

                    'branche' => $this->stringValue(
                        $this->value(
                            $row,
                            $columns,
                            'branche'
                        )
                    ),

                    'adc' => $this->stringValue(
                        $this->value(
                            $row,
                            $columns,
                            'adc'
                        )
                    ),

                    'libelle_long' => $this->stringValue(
                        $this->value(
                            $row,
                            $columns,
                            'libelle long de la formation'
                        )
                    ),

                    'diplomes_qualifications' => $this->stringValue(
                        $this->value(
                            $row,
                            $columns,
                            'diplomes et qualifications delivres'
                        )
                    ),

                    'unite_certification' => $this->stringValue(
                        $this->value(
                            $row,
                            $columns,
                            'unite delivrant la certification'
                        )
                    ),

                    'cursus_ouvert' => $this->stringValue(
                        $this->value(
                            $row,
                            $columns,
                            'cursus ouvert'
                        )
                    ),

                    'sirh' => $this->stringValue(
                        $this->value(
                            $row,
                            $columns,
                            'sirh'
                        )
                    ),

                    'ouverture_licence' => $this->stringValue(
                        $this->value(
                            $row,
                            $columns,
                            'ouverture licence'
                        )
                    ),

                    'date_cdf' => $this->dateValue(
                        $this->value(
                            $row,
                            $columns,
                            'date de creation/maj du cdf'
                        )
                    ),

                    'ecole_pilote_cdf' => $this->stringValue(
                        $this->value(
                            $row,
                            $columns,
                            'ecole pilote du cdf'
                        )
                    ),

                    'duree_jours' => $this->numericValue(
                        $this->value(
                            $row,
                            $columns,
                            'duree (jo)'
                        )
                    ),

                    'nb_sessions_annuelles' => $this->integerValue(
                        $this->value(
                            $row,
                            $columns,
                            'nb session/an'
                        )
                    ),

                    'capacite_max' => $this->integerValue(
                        $this->value(
                            $row,
                            $columns,
                            'capacite max/session'
                        )
                    ),

                    'capacite_min' => $this->integerValue(
                        $this->value(
                            $row,
                            $columns,
                            'capacite min rentable rh avant annulation'
                        )
                    ),

                    'ouvert_off' => $this->xValue(
                        $this->value(
                            $row,
                            $columns,
                            'off'
                        )
                    ),

                    'ouvert_om' => $this->xValue(
                        $this->value(
                            $row,
                            $columns,
                            'om'
                        )
                    ),

                    'ouvert_qmm_mo' => $this->xValue(
                        $this->value(
                            $row,
                            $columns,
                            'qmm/mo'
                        )
                    ),

                    'ouverture_etrangers' => $this->nullableBoolean(
                        $this->value(
                            $row,
                            $columns,
                            'ouverture etrangers'
                        )
                    ),

                    'possibilite_ead' => $this->nullableBoolean(
                        $this->value(
                            $row,
                            $columns,
                            'possibilite ead'
                        )
                    ),

                    'duree_ead_ui' => $this->numericValue(
                        $this->value(
                            $row,
                            $columns,
                            'duree ead (ui)'
                        )
                    ),

                    'ouverture_vca' => $this->nullableBoolean(
                        $this->value(
                            $row,
                            $columns,
                            'ouverture vca'
                        )
                    ),

                    'ouverture_vae' => $this->nullableBoolean(
                        $this->value(
                            $row,
                            $columns,
                            'ouverture vae'
                        )
                    ),

                    'autres_beneficiaires' => $this->stringValue(
                        $this->value(
                            $row,
                            $columns,
                            'autres beneficiaires'
                        )
                    ),

                    'observations' => $this->stringValue(
                        $this->value(
                            $row,
                            $columns,
                            'observations'
                        )
                    ),

                    'catalogue_match_key' => $matchKey,
                    'dernier_import_at' => now(),
                ];

                $demandeSuppression = Str::contains(
                    Str::lower(
                        (string) ($payload['nature_maj'] ?? '')
                    ),
                    'suppression'
                );

                $payload['actif'] = ! $demandeSuppression;

                $hashData = [
                    'stage' => $payload,
                    'prerequis' => array_column(
                        $prerequis,
                        'libelle'
                    ),
                ];

                // Ne pas inclure la date d'import dans le hash.
                unset(
                    $hashData['stage']['dernier_import_at']
                );

                $hash = hash(
                    'sha256',
                    json_encode(
                        $hashData,
                        JSON_UNESCAPED_UNICODE |
                        JSON_UNESCAPED_SLASHES
                    )
                );

                $stage = $this->findStage(
                    $numero,
                    $matchKey,
                    $centre,
                    $libelleCourt
                );

                if ($stage === false) {
                    $result['ambigues']++;

                    $result['erreurs'][] =
                        "Ligne {$excelRow} : plusieurs stages correspondent.";

                    continue;
                }

                if ($stage === null) {
                    $payload['catalogue_hash'] = $hash;

                    $stage = Stage::create($payload);

                    $this->syncCataloguePrerequis(
                        $stage,
                        $prerequis,
                        $result
                    );

                    $result['creees']++;

                    if ($demandeSuppression) {
                        $result['desactivees']++;
                    }

                    continue;
                }

                if ($stage->catalogue_hash === $hash) {
                    $stage->forceFill([
                        'dernier_import_at' => now(),
                    ])->saveQuietly();

                    $result['inchangees']++;

                    continue;
                }

                $payload['catalogue_hash'] = $hash;

                $stage->fill($payload);
                $stage->save();

                $this->syncCataloguePrerequis(
                    $stage,
                    $prerequis,
                    $result
                );

                $result['mises_a_jour']++;

                if ($demandeSuppression) {
                    $result['desactivees']++;
                }
            } catch (Throwable $e) {
                $result['erreurs'][] =
                    "Ligne {$excelRow} : {$e->getMessage()}";
            }
        }

        return $result;
    }

    private function findStage(
        ?string $numero,
        string $matchKey,
        ?string $centre,
        string $libelle
    ): Stage|false|null {
        if ($numero !== null) {
            $matches = Stage::query()
                ->where('numero_externe', $numero)
                ->get();

            if ($matches->count() > 1) {
                return false;
            }

            if ($matches->count() === 1) {
                return $matches->first();
            }
        }

        $matches = Stage::query()
            ->where('catalogue_match_key', $matchKey)
            ->get();

        if ($matches->count() > 1) {
            return false;
        }

        if ($matches->count() === 1) {
            return $matches->first();
        }

        // Permet de rattacher un stage créé manuellement
        // avant le premier import Excel.
        $matches = Stage::query()
            ->where('centre_formation', $centre)
            ->where('libelle_court', $libelle)
            ->get();

        if ($matches->count() > 1) {
            return false;
        }

        return $matches->first();
    }

    private function syncCataloguePrerequis(
        Stage $stage,
        array $prerequis,
        array &$result
    ): void {
        // Les prérequis créés manuellement sont conservés.
        $stage->prerequis()
            ->where('source', 'catalogue')
            ->delete();

        foreach ($prerequis as $data) {
            $stage->prerequis()->create($data);
            $result['prerequis']++;
        }
    }

    private function extractPrerequis(
        array $row,
        array $columns
    ): array {
        $headers = [
            'prerequis 1 habilitation',
            'prerequis 2',
            'prerequis 3',
            'prerequis 4',
            'prerequis 5',
            'prerequis 6',
            'prerequis 7',
            'prerequis 8',
        ];

        $result = [];
        $ordre = 1;

        foreach ($headers as $header) {
            $libelle = $this->stringValue(
                $this->value(
                    $row,
                    $columns,
                    $header
                )
            );

            if ($libelle === null) {
                continue;
            }

            $result[] = [
                'ordre' => $ordre++,
                'libelle' => $libelle,
                'obligatoire' => true,
                'actif' => true,
                'source' => 'catalogue',
                'source_colonne' => $header,
            ];
        }

        return $result;
    }

    private function value(
        array $row,
        array $columns,
        string $header
    ): mixed {
        return isset($columns[$header])
            ? ($row[$columns[$header]] ?? null)
            : null;
    }

    private function normalizeHeader(
        string $value
    ): string {
        return Str::of($value)
            ->ascii()
            ->lower()
            ->squish()
            ->toString();
    }

    private function makeMatchKey(
        ?string $centre,
        string $libelle
    ): string {
        return Str::of(
            ($centre ?? '') . '|' . $libelle
        )
            ->ascii()
            ->lower()
            ->replaceMatches('/[^a-z0-9]+/', '-')
            ->trim('-')
            ->toString();
    }

    private function stringValue(
        mixed $value
    ): ?string {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === ''
            ? null
            : $value;
    }

    private function numericValue(
        mixed $value
    ): ?float {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        $value = str_replace(
            ',',
            '.',
            trim((string) $value)
        );

        return is_numeric($value)
            ? (float) $value
            : null;
    }

    private function integerValue(
        mixed $value
    ): ?int {
        $value = $this->numericValue($value);

        return $value === null
            ? null
            : (int) round($value);
    }

    private function xValue(
        mixed $value
    ): bool {
        if ($value === null) {
            return false;
        }

        return in_array(
            Str::upper(trim((string) $value)),
            ['X', 'OUI', 'YES', '1'],
            true
        );
    }

    private function nullableBoolean(
        mixed $value
    ): ?bool {
        if (
            $value === null ||
            trim((string) $value) === ''
        ) {
            return null;
        }

        $normalized = Str::upper(
            Str::ascii(
                trim((string) $value)
            )
        );

        if (
            in_array(
                $normalized,
                ['OUI', 'YES', 'X', '1'],
                true
            )
        ) {
            return true;
        }

        if (
            in_array(
                $normalized,
                ['NON', 'NO', '0'],
                true
            )
        ) {
            return false;
        }

        return null;
    }

    private function dateValue(
        mixed $value
    ): ?string {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return ExcelDate::excelToDateTimeObject(
                (float) $value
            )->format('Y-m-d');
        }

        try {
            return Carbon::parse(
                (string) $value
            )->format('Y-m-d');
        } catch (Throwable) {
            return null;
        }
    }
}