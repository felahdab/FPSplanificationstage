@if ($errors->any())
    <x-filament::section>
        <div style="color:#b91c1c;font-weight:700;margin-bottom:.5rem;">
            Le formulaire contient des erreurs.
        </div>
        <ul style="margin:0;padding-left:1.25rem;color:#b91c1c;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </x-filament::section>
@endif
