<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>Certificate</title>
    <style>
        /* A4 Landscape = 297mm × 210mm */
        @page {
            margin: 0mm;
            background-image: url('{{ public_path('images/certificate-bg.png') }}');
            background-image-resize: 6;
        }
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            color: #0f172a;
        }

        /* ── Absolute positions (A4-L: 297×210 mm) ─────────────
           Frame inner safe zone:
             Top:   30mm  |  Bottom: 175mm
             Left:  45mm  |  Right:  252mm
           ────────────────────────────────────────────────────── */

        .cert-title {
            position: absolute;
            top: 32mm;
            left: 0mm;
            width: 297mm;
            text-align: center;
            font-size: 34pt;
            font-weight: bold;
            color: #1e3a8a;
            line-height: 1;
        }
        .cert-subtitle {
            position: absolute;
            top: 52mm;
            left: 0mm;
            width: 297mm;
            text-align: center;
            font-size: 11pt;
            font-weight: bold;
            color: #b45309;
        }

        /* Gold separator line */
        .gold-sep {
            position: absolute;
            top: 64mm;
            left: 60mm;
            width: 177mm;
            border-top: 1.5px solid #b45309;
        }

        /* "Presented to" label */
        .label-presented {
            position: absolute;
            top: 68mm;
            left: 0mm;
            width: 297mm;
            text-align: center;
            font-size: 9pt;
            color: #64748b;
        }

        /* Student Name */
        .student-name {
            position: absolute;
            top: 74mm;
            left: 45mm;
            width: 207mm;
            text-align: center;
            font-size: 28pt;
            font-weight: bold;
            color: #0f172a;
        }

        /* "For successfully completing" label */
        .label-course {
            position: absolute;
            top: 102mm;
            left: 0mm;
            width: 297mm;
            text-align: center;
            font-size: 9pt;
            color: #64748b;
        }

        /* Course name */
        .course-name {
            position: absolute;
            top: 109mm;
            left: 45mm;
            width: 207mm;
            text-align: center;
            font-size: 20pt;
            font-weight: bold;
            color: #1e3a8a;
        }

        /* Date */
        .cert-date {
            position: absolute;
            top: 132mm;
            left: 0mm;
            width: 297mm;
            text-align: center;
            font-size: 9pt;
            color: #64748b;
        }

        /* ── SIGNATURES ── */

        /* Left signature (Instructor) */
        .sig-left {
            position: absolute;
            top: 148mm;
            left: 52mm;
            width: 70mm;
            text-align: center;
        }
        /* Right signature (Platform) */
        .sig-right {
            position: absolute;
            top: 148mm;
            left: 175mm;
            width: 70mm;
            text-align: center;
        }
        .sig-name {
            font-size: 13pt;
            font-style: italic;
            color: #1e3a8a;
            margin-bottom: 3mm;
        }
        .sig-rule {
            border-top: 1.5px solid #b45309;
            padding-top: 2mm;
            font-size: 10pt;
            font-weight: bold;
            color: #1e3a8a;
        }
        .sig-role {
            font-size: 8pt;
            color: #64748b;
            font-weight: normal;
            margin-top: 1mm;
        }

        /* QR Code – centered horizontally: (297/2 - 22/2) = 137.5mm */
        .qr-block {
            position: absolute;
            top: 143mm;
            left: 136mm;
            width: 25mm;
            text-align: center;
        }
        .qr-img {
            width: 22mm;
            height: 22mm;
        }
        .qr-uuid {
            font-size: 5.5pt;
            color: #94a3b8;
            margin-top: 1mm;
            word-break: break-all;
        }
    </style>
</head>
<body>

<div class="cert-title">شهادة إتمام</div>
<div class="cert-subtitle">CERTIFICATE OF COMPLETION</div>
<div class="gold-sep"></div>

<div class="label-presented">تُمنح هذه الشهادة إلى &nbsp;|&nbsp; Presented to</div>
<div class="student-name">{{ $user->name }}</div>

<div class="label-course">لإتمامه بنجاح الدورة التدريبية &nbsp;|&nbsp; For successfully completing</div>
<div class="course-name">{{ $course->title }}</div>

<div class="cert-date">تاريخ الإصدار / Date: {{ $certificate->issued_at->format('d / m / Y') }}</div>

{{-- Left Signature: Instructor --}}
<div class="sig-left">
    <div class="sig-name">{{ $course->instructor?->name ?? 'Instructor' }}</div>
    <div class="sig-rule">
        مدرب الدورة
        <div class="sig-role">Course Instructor</div>
    </div>
</div>

{{-- QR Code: center of page --}}
<div class="qr-block">
    @php
        $verifyUrl = route('certificates.verify', $certificate->uuid);
        $qrCode = base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(88)->generate($verifyUrl));
    @endphp
    <img src="data:image/svg+xml;base64,{{ $qrCode }}" class="qr-img">
    <div class="qr-uuid">{{ $certificate->uuid }}</div>
</div>

{{-- Right Signature: Platform --}}
<div class="sig-right">
    <div class="sig-name">Platform Director</div>
    <div class="sig-rule">
        إدارة المنصة
        <div class="sig-role">Platform Management</div>
    </div>
</div>

</body>
</html>
