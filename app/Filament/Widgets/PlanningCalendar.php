<?php

namespace Modules\FPSplanificationstage\Filament\Widgets;

use Guava\Calendar\Enums\CalendarViewType;
use Guava\Calendar\Filament\CalendarWidget;
use Guava\Calendar\ValueObjects\CalendarEvent;
use Guava\Calendar\ValueObjects\FetchInfo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Modules\FPSplanificationstage\Filament\Resources\SessionStages\SessionStageResource;
use Modules\FPSplanificationstage\Models\SessionStage;

class PlanningCalendar extends CalendarWidget
{
    public ?string $stageFilter = '';

    public ?string $instructeurFilter = '';

    public ?string $salleFilter = '';

    public ?string $statutFilter = '';

    protected CalendarViewType $calendarView = CalendarViewType::DayGridMonth;

    protected function getEvents(FetchInfo $info): Collection | array | Builder
    {
        $query = SessionStage::query()
            ->with([
                'stage',
                'salle',
                'instructeurs',
            ])
            ->where('debut', '<=', $info->end)
            ->where('fin', '>=', $info->start);

        if ($this->stageFilter !== '') {
            $query->where('stage_id', (int) $this->stageFilter);
        }

        if ($this->salleFilter !== '') {
            $query->where('salle_id', (int) $this->salleFilter);
        }

        if ($this->instructeurFilter !== '') {
            $query->whereHas(
                'instructeurs',
                fn ($query) => $query->where('instructeurs.id', (int) $this->instructeurFilter)
            );
        }

        if ($this->statutFilter !== '') {
            $query->where('statut', $this->statutFilter);
        } else {
            $query->where('statut', '<>', 'annulee');
        }

        return $query
            ->orderBy('debut')
            ->get()
            ->map(function (SessionStage $session): CalendarEvent {
                $title = trim(
                    ($session->stage?->code_stage ? $session->stage->code_stage . ' — ' : '')
                    . ($session->stage?->libelle_court ?? 'Session')
                );

                return CalendarEvent::make()
                    ->title($title)
                    ->start($session->debut)
                    ->end($session->fin)
                    ->backgroundColor($this->statusColor($session->statut))
                    ->textColor('#ffffff')
                    ->url(
                        SessionStageResource::getUrl(
                            'edit',
                            ['record' => $session->getKey()]
                        )
                    );
            })
            ->values()
            ->all();
    }

    protected function statusColor(?string $status): string
    {
        return match ($status) {
            'brouillon' => '#64748b',
            'planifiee' => '#2563eb',
            'confirmee' => '#16a34a',
            'annulee' => '#dc2626',
            'terminee' => '#7c3aed',
            default => '#64748b',
        };
    }
}
