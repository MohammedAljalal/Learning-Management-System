<?php

declare(strict_types=1);

namespace App\Services\Assessment;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\User;
use App\Services\Service;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Psr\Log\LoggerInterface;

class CertificateService extends Service
{
    public function __construct(LoggerInterface $logger)
    {
        parent::__construct($logger);
    }

    /**
     * Generate or retrieve a certificate for a user in a course.
     */
    public function generateCertificate(User $user, Course $course): Certificate
    {
        return DB::transaction(function () use ($user, $course) {
            $certificate = Certificate::firstOrCreate([
                'user_id' => $user->id,
                'course_id' => $course->id,
            ]);

            $this->logger->info("Certificate generated/retrieved", ['certificate_id' => $certificate->id]);
            return $certificate;
        });
    }

    /**
     * Render the certificate as a PDF file stream.
     */
    public function renderPdf(Certificate $certificate)
    {
        $certificate->load(['user', 'course.instructor']);
        
        $html = view('certificates.template', [
            'certificate' => $certificate,
            'user' => $certificate->user,
            'course' => $certificate->course,
        ])->render();

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4-L',
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 0,
            'margin_bottom' => 0,
            'margin_header' => 0,
            'margin_footer' => 0,
        ]);

        $mpdf->WriteHTML($html);
        
        return $mpdf;
    }
}
