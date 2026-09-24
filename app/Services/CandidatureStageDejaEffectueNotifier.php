<?php

namespace Modules\FPSplanificationstage\Services;

use App\Models\User;
use Filament\Notifications\Notification;
use Modules\FPSplanificationstage\Models\Inscription;

class CandidatureStageDejaEffectueNotifier
{
    public function notifier(
        Inscription $inscription
    ): void {
        $gestionnaires =
            User::query()
                ->where(
                    function ($query): void {
                        $query
                            ->where(
                                'admin',
                                true
                            )
                            ->orWhereHas(
                                'permissions',
                                fn ($permissionQuery) =>
                                    $permissionQuery
                                        ->where(
                                            'name',
                                            'fpsplanificationstage::gerer_le_module'
                                        )
                            )
                            ->orWhereHas(
                                'roles.permissions',
                                fn ($permissionQuery) =>
                                    $permissionQuery
                                        ->where(
                                            'name',
                                            'fpsplanificationstage::gerer_le_module'
                                        )
                            );
                    }
                )
                ->get();

        if ($gestionnaires->isEmpty()) {
            return;
        }

        Notification::make()
            ->title(
                'Candidature non prioritaire'
            )
            ->body(
                $inscription->nom_complet
                . ' a déjà effectué le stage '
                . (
                    $inscription
                        ->sessionStage
                        ?->stage
                        ?->libelle_court
                    ?? 'sélectionné'
                )
                . '. La candidature '
                . $inscription
                    ->code_inscription
                . ' doit être traitée après les candidats prioritaires.'
            )
            ->warning()
            ->sendToDatabase(
                $gestionnaires
            );
    }
}
