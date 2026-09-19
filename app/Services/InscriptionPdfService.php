<?php

namespace Modules\FPSplanificationstage\Services;

use Dompdf\Dompdf;
use Dompdf\Options;
use Modules\FPSplanificationstage\Models\Inscription;

class InscriptionPdfService
{
    public function render(
        Inscription $inscription
    ): string {
        $inscription->loadMissing([
            'sessionStage.stage',
        ]);

        $html = view(
            'fpsplanificationstage::public.inscription-pdf',
            [
                'inscription' => $inscription,
            ]
        )->render();

        $options = new Options();
        $options->set(
            'defaultFont',
            'DejaVu Sans'
        );
        $options->set(
            'isRemoteEnabled',
            false
        );
        $options->set(
            'isHtml5ParserEnabled',
            true
        );

        $dompdf = new Dompdf(
            $options
        );

        $dompdf->loadHtml(
            $html,
            'UTF-8'
        );

        $dompdf->setPaper(
            'A4',
            'portrait'
        );

        $dompdf->render();

        return $dompdf->output();
    }
}
