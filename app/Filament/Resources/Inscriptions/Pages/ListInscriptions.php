<?php

namespace Modules\FPSplanificationstage\Filament\Resources\Inscriptions\Pages;

use Filament\Schemas\Components\Tabs\Tab;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\FPSplanificationstage\Models\Inscription;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\FPSplanificationstage\Filament\Resources\Inscriptions\InscriptionResource;

class ListInscriptions extends ListRecords
{
    protected static string $resource =
        InscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(
                    'Nouvelle inscription'
                ),
        ];
    }
    /*
     * INSCRIPTIONS_ONGLETS_STAGE_HISTORIQUE_V1_1
     *
     * Toutes :
     *   toutes les inscriptions.
     *
     * Par stage :
     *   sessions en cours ou futures, regroupées
     *   par libellé court du stage.
     *
     * Historique :
     *   sessions dont la date de fin est passée.
     *
     * Aucun déplacement / suppression en base :
     * ce sont uniquement des vues de la même table.
     */

    public function getTabs(): array
    {
        return [
            'toutes' =>
                Tab::make(
                    'Toutes'
                )
                    ->badge(
                        fn (): int =>
                            Inscription::query()
                                ->count()
                    ),

            'par_stage' =>
                Tab::make(
                    'Par stage'
                )
                    ->badge(
                        fn (): int =>
                            Inscription::query()
                                ->whereHas(
                                    'sessionStage',
                                    fn (
                                        Builder $query
                                    ): Builder =>
                                        $query->where(
                                            'fin',
                                            '>=',
                                            now()
                                                ->startOfDay()
                                        )
                                )
                                ->count()
                    )
                    ->modifyQueryUsing(
                        fn (
                            Builder $query
                        ): Builder =>
                            $query->whereHas(
                                'sessionStage',
                                fn (
                                    Builder $sessionQuery
                                ): Builder =>
                                    $sessionQuery->where(
                                        'fin',
                                        '>=',
                                        now()
                                            ->startOfDay()
                                    )
                            )
                    ),

            'historique' =>
                Tab::make(
                    'Historique'
                )
                    ->badge(
                        fn (): int =>
                            Inscription::query()
                                ->whereHas(
                                    'sessionStage',
                                    fn (
                                        Builder $query
                                    ): Builder =>
                                        $query->where(
                                            'fin',
                                            '<',
                                            now()
                                                ->startOfDay()
                                        )
                                )
                                ->count()
                    )
                    ->modifyQueryUsing(
                        fn (
                            Builder $query
                        ): Builder =>
                            $query->whereHas(
                                'sessionStage',
                                fn (
                                    Builder $sessionQuery
                                ): Builder =>
                                    $sessionQuery->where(
                                        'fin',
                                        '<',
                                        now()
                                            ->startOfDay()
                                    )
                            )
                    ),
        ];
    }

    public function getDefaultActiveTab(): string|int|null
    {
        return 'toutes';
    }

    public function updatedActiveTab(): void
    {
        parent::updatedActiveTab();

        /*
         * Le groupement n'est activé QUE pour l'onglet
         * "Par stage". En revenant sur Toutes ou Historique,
         * on retrouve la table normale.
         */
        $this->tableGrouping =
            $this->activeTab
            === 'par_stage'
                ? 'sessionStage.stage.libelle_court'
                : null;
    }

    public function table(
        Table $table
    ): Table {
        return $table
            ->groups([
                Group::make(
                    'sessionStage.stage.libelle_court'
                )
                    ->label(
                        'Stage'
                    )
                    ->titlePrefixedWithLabel(
                        false
                    )
                    ->getDescriptionFromRecordUsing(
                        function (
                            Inscription $record
                        ): ?string {
                            $libelleLong =
                                trim(
                                    (string) (
                                        $record
                                            ->sessionStage
                                            ?->stage
                                            ?->libelle_long
                                        ?? ''
                                    )
                                );

                            return $libelleLong !== ''
                                ? $libelleLong
                                : null;
                        }
                    )
                    ->collapsible(),
            ])
            ->groupingSettingsHidden();
    }

}