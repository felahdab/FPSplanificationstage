<x-filament-panels::page>
    <div class="space-y-6">

        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <div class="grid gap-4 lg:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium">Session</label>
                    <select wire:model.live="sessionId" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800">
                        <option value="">Sélectionner une session</option>
                        @foreach ($this->sessionOptions() as $id => $label)
                            <option value="{{ $id }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium">Modèle</label>
                    <div class="flex gap-2">
                        <select wire:model="templateId" class="min-w-0 flex-1 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800">
                            <option value="">Aucun modèle</option>
                            @foreach ($this->templateOptions() as $id => $label)
                                <option value="{{ $id }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <button type="button" wire:click="loadTemplate" class="rounded-lg bg-gray-700 px-4 py-2 text-sm font-semibold text-white">Charger</button>
                        <button type="button" wire:click="newTemplate" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold dark:border-gray-600">Nouveau</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            <div class="space-y-5 rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <div>
                    <h2 class="text-lg font-semibold">Modèle du message</h2>
                    <p class="mt-1 text-sm text-gray-500">Texte, ordre des blocs et champs candidats entièrement libres.</p>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium">Nom du modèle</label>
                    <input type="text" wire:model="templateName" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800" placeholder="Ex. Admission standard BIP1">
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium">Portée du modèle</label>
                    <select wire:model="templateStageId" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800">
                        @foreach ($this->stageOptions() as $id => $label)
                            <option value="{{ $id }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium">Objet</label>
                    <input type="text" wire:model="subjectTemplate" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800" placeholder="Ex. Admission {stage} — {session}">
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium">Corps complet du message</label>
                    <textarea wire:model="bodyTemplate" rows="14" class="w-full rounded-lg border-gray-300 font-mono text-sm dark:border-gray-700 dark:bg-gray-800"></textarea>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium">Format d'un candidat admis</label>
                        <textarea wire:model="admittedFormat" rows="4" class="w-full rounded-lg border-gray-300 font-mono text-sm dark:border-gray-700 dark:bg-gray-800"></textarea>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium">Format d'un candidat refusé</label>
                        <textarea wire:model="refusedFormat" rows="4" class="w-full rounded-lg border-gray-300 font-mono text-sm dark:border-gray-700 dark:bg-gray-800"></textarea>
                    </div>
                </div>

                <div class="rounded-lg bg-gray-50 p-4 text-sm dark:bg-gray-800">
                    <div class="font-semibold">Variables disponibles</div>
                    @foreach ($this->placeholderHelp() as $group => $variables)
                        <div class="mt-2">
                            <span class="font-medium">{{ $group }} :</span>
                            <span class="font-mono text-xs">{{ implode('  ', $variables) }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="flex flex-wrap gap-2">
                    <button type="button" wire:click="saveTemplate" class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-semibold text-white">Enregistrer le modèle</button>
                    @if ($templateId)
                        <button type="button" wire:click="deleteTemplate" wire:confirm="Supprimer définitivement ce modèle ?" class="rounded-lg bg-danger-600 px-4 py-2 text-sm font-semibold text-white">Supprimer le modèle</button>
                    @endif
                </div>
            </div>

            <div class="space-y-5 rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <div>
                    <h2 class="text-lg font-semibold">Candidats</h2>
                    <p class="mt-1 text-sm text-gray-500">La sélection ne modifie pas les statuts administratifs.</p>
                </div>

                @if (! $sessionId)
                    <div class="rounded-lg bg-gray-50 p-5 text-sm text-gray-500 dark:bg-gray-800">Sélectionne d'abord une session.</div>
                @elseif ($this->candidates()->isEmpty())
                    <div class="rounded-lg bg-gray-50 p-5 text-sm text-gray-500 dark:bg-gray-800">Aucun candidat pour cette session.</div>
                @else
                    <div class="space-y-3">
                        @foreach ($this->candidates() as $candidate)
                            <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                                    <div>
                                        <div class="font-semibold">{{ $candidate->grade }} {{ $candidate->nom }} {{ $candidate->prenom }}</div>
                                        <div class="mt-1 text-sm text-gray-500">
                                            {{ $candidate->unite ?: 'Unité non renseignée' }}
                                            @if ($candidate->brevet) — {{ $candidate->brevet }} @endif
                                            @if ($candidate->specialite) — {{ $candidate->specialite }} @endif
                                        </div>
                                        <div class="mt-1 text-xs text-gray-400">Statut actuel : {{ $candidate->statut }}</div>
                                    </div>

                                    <div class="min-w-52">
                                        <select wire:model="candidateDecisions.{{ $candidate->id }}" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800">
                                            <option value="admis">Admis</option>
                                            <option value="refuse">Refusé</option>
                                            <option value="ignorer">Ne pas inclure</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <button type="button" wire:click="generateMessage" class="w-full rounded-lg bg-success-600 px-4 py-3 text-sm font-semibold text-white">Générer le message</button>
            </div>
        </div>

        <div class="space-y-5 rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <div>
                <h2 class="text-lg font-semibold">Message final</h2>
                <p class="mt-1 text-sm text-gray-500">Le résultat reste entièrement modifiable avant copie.</p>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium">Objet final</label>
                <input id="admission-final-subject" type="text" wire:model="finalSubject" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800">
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium">Message final</label>
                <textarea id="admission-final-body" wire:model="finalBody" rows="18" class="w-full rounded-lg border-gray-300 font-mono text-sm dark:border-gray-700 dark:bg-gray-800"></textarea>
            </div>

            <div class="flex flex-wrap gap-2">
                <button type="button" onclick="const subject=document.getElementById('admission-final-subject').value;const body=document.getElementById('admission-final-body').value;navigator.clipboard.writeText(subject ? 'Objet : '+subject+'\n\n'+body : body);" class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-semibold text-white">Copier objet + message</button>
                <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('admission-final-body').value);" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold dark:border-gray-600">Copier uniquement le message</button>
            </div>
        </div>

    </div>
</x-filament-panels::page>
