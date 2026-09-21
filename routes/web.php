<?php

use Illuminate\Support\Facades\Route;
use Modules\FPSplanificationstage\Http\Controllers\PublicBesoinFormationController;
use Modules\FPSplanificationstage\Http\Controllers\PublicInscriptionController;

/*
|--------------------------------------------------------------------------
| Actions du portail de formation
|--------------------------------------------------------------------------
|
| Toutes les pages GET sont maintenant de vraies pages Filament du panel
| fpsplanificationstage, sous :
|
| /apps/fpsplanificationstage/espace-stagiaire/planning-formations/...
|
| Il n'existe plus de second panel /apps/formations.
*/

Route::post(
    '/fpsplanificationstage/espace-stagiaire/planning-formations/besoins/nouveau',
    [
        PublicBesoinFormationController::class,
        'store',
    ]
)
    ->middleware('throttle:20,1')
    ->name('fpsplanificationstage.public.besoin.store');

Route::post(
    '/fpsplanificationstage/espace-stagiaire/planning-formations/besoins/suivi',
    [
        PublicBesoinFormationController::class,
        'rechercherSuivi',
    ]
)
    ->middleware('throttle:10,1')
    ->name('fpsplanificationstage.public.besoin.suivi.rechercher');

Route::post(
    '/fpsplanificationstage/espace-stagiaire/planning-formations/sessions/{session}/inscription',
    [
        PublicInscriptionController::class,
        'store',
    ]
)->name('fpsplanificationstage.public.inscription.store');

Route::get(
    '/fpsplanificationstage/espace-stagiaire/planning-formations/inscriptions/{code}/pdf',
    [
        PublicInscriptionController::class,
        'confirmation',
    ]
)->name('fpsplanificationstage.public.inscription.pdf');
