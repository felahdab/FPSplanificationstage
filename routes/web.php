<?php

use Illuminate\Support\Facades\Route;
use Modules\PlanificationStages\Http\Controllers\PublicBesoinFormationController;
use Modules\PlanificationStages\Http\Controllers\PublicInscriptionController;
use Modules\PlanificationStages\Http\Controllers\PublicPlanningController;

/*
|--------------------------------------------------------------------------
| Interface publique des stages
|--------------------------------------------------------------------------
*/

/*
 * Portail / calendrier.
 */
Route::get(
    '/formations',
    [
        PublicPlanningController::class,
        'index',
    ]
)->name(
    'planificationstages.public.calendrier'
);

/*
 * Expression de besoin.
 */
Route::get(
    '/formations/besoins/nouveau',
    [
        PublicBesoinFormationController::class,
        'create',
    ]
)->name(
    'planificationstages.public.besoin.create'
);

Route::post(
    '/formations/besoins/nouveau',
    [
        PublicBesoinFormationController::class,
        'store',
    ]
)
    ->middleware(
        'throttle:20,1'
    )
    ->name(
        'planificationstages.public.besoin.store'
    );

/*
 * Recherche du suivi avec :
 * BES-xxxxxx + adresse e-mail.
 */
Route::get(
    '/formations/besoins/suivi',
    [
        PublicBesoinFormationController::class,
        'suiviForm',
    ]
)->name(
    'planificationstages.public.besoin.suivi.form'
);

Route::post(
    '/formations/besoins/suivi',
    [
        PublicBesoinFormationController::class,
        'rechercherSuivi',
    ]
)
    ->middleware(
        'throttle:10,1'
    )
    ->name(
        'planificationstages.public.besoin.suivi.rechercher'
    );

/*
 * Pages accessibles grâce au jeton
 * public non prédictible.
 */
Route::get(
    '/formations/besoins/{token}/confirmation',
    [
        PublicBesoinFormationController::class,
        'confirmation',
    ]
)->name(
    'planificationstages.public.besoin.confirmation'
);

Route::get(
    '/formations/besoins/{token}/suivi',
    [
        PublicBesoinFormationController::class,
        'suivi',
    ]
)->name(
    'planificationstages.public.besoin.suivi'
);

/*
 * Inscriptions aux sessions.
 */
Route::get(
    '/formations/sessions/{session}/inscription',
    [
        PublicInscriptionController::class,
        'create',
    ]
)->name(
    'planificationstages.public.inscription.create'
);

Route::post(
    '/formations/sessions/{session}/inscription',
    [
        PublicInscriptionController::class,
        'store',
    ]
)->name(
    'planificationstages.public.inscription.store'
);

Route::get(
    '/formations/inscriptions/{code}/confirmation',
    [
        PublicInscriptionController::class,
        'confirmation',
    ]
)->name(
    'planificationstages.public.inscription.confirmation'
);

\Illuminate\Support\Facades\Route::get(
    '/formations/sessions/{session}',
    [
        \Modules\PlanificationStages\Http\Controllers\PublicPlanningController::class,
        'show',
    ]
)->name(
    'planificationstages.public.session.show'
);
