<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">Candidature enregistrée</x-slot>
        <div>Référence : <strong>{{ $inscription->code_inscription }}</strong></div>
        <div style="margin-top:.5rem;">Statut : {{ $inscription->statut }}</div>
        @if ($inscription->sessionStage?->stage)
            <div style="margin-top:.5rem;">Stage : {{ $inscription->sessionStage->stage->libelle_court }}</div>
        @endif
        <div style="margin-top:1rem;">
            <x-filament::button tag="a" href="/apps/fpsplanificationstage/espace-stagiaire/planning-formations">Retour au portail</x-filament::button>
        </div>
    </x-filament::section>
</x-filament-panels::page>
