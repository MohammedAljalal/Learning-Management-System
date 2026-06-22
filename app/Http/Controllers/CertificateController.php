<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Services\Assessment\CertificateService;

class CertificateController extends Controller
{
    /**
     * Download the certificate as a PDF.
     */
    public function download(Certificate $certificate, CertificateService $certificateService)
    {
        // Ensure user is authorized to download this certificate
        abort_unless($certificate->user_id === auth()->id(), 403, 'Unauthorized access to certificate.');

        $pdf = $certificateService->renderPdf($certificate);

        return response($pdf->Output('', 'S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="certificate-'.$certificate->uuid.'.pdf"');
    }

    /**
     * Public page to verify a certificate's authenticity via its UUID.
     */
    public function verify(string $uuid)
    {
        $certificate = Certificate::with(['user', 'course.instructor'])->where('uuid', $uuid)->firstOrFail();

        return view('certificates.verify', compact('certificate'));
    }
}
