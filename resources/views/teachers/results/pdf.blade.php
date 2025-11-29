<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Result Sheet</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #222;
        }

        .center { text-align: center; }

        .school-header {
            width: 100%;
            margin-bottom: 15px;
            border-bottom: 2px solid #0a4a8b;
            padding-bottom: 10px;
        }

        .school-logo {
            width: 70px;
            height: 70px;
            object-fit: contain;
        }

        .info-box {
            border: 1px solid #ccc;
            padding: 10px;
            border-radius: 6px;
            background: #f9f9f9;
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        table th {
            background: #bfe0ff;
            color: #003366;
            padding: 6px;
            border: 1px solid #777;
        }

        table td {
            padding: 6px;
            border: 1px solid #777;
        }

        .summary-box {
            border: 1px solid #aaa;
            padding: 10px;
            border-radius: 6px;
            background: #f2f2f2;
            margin-bottom: 15px;
        }

        .watermark {
            position: fixed;
            top: 35%;
            left: 25%;
            width: 400px;
            opacity: 0.1;
        }
    </style>
</head>
<body>

    {{-- Watermark --}}
    @if($school && $school->logo)
        <img src="{{ public_path('school_logos/' . $school->logo) }}" class="watermark">
    @endif

    {{-- School Header --}}
    <table class="school-header">
        <tr>
            <td width="80">
                @if($school && $school->logo)
                    <img src="{{ public_path('school_logos/' . $school->logo) }}" class="school-logo">
                @endif
            </td>
            <td class="center">
                <h2 style="margin:0; color:#0a4a8b;">{{ $school->name }}</h2>
                <p style="margin:0;">{{ $school->address }}</p>
                <p style="margin:0;">Phone: {{ $school->phone }} | Email: {{ $school->email }}</p>
                <h3 style="margin:5px 0 0;">{{ $term->name }} — {{ $session->name }}</h3>
            </td>
            <td width="80">
                <span style="background:#ffe28a; padding:4px 6px; border-radius:4px; font-weight:bold;">
                    RESULT SHEET
                </span>
            </td>
        </tr>
    </table>

    {{-- Student Info --}}
    <div class="info-box">
        <strong>Student Name:</strong> {{ $student->name }} <br>
        <strong>Admission No:</strong> {{ $student->admission_number ?? '—' }} <br>
        <strong>Class:</strong> {{ $student->schoolClass->name }} <br>
        <strong>Date:</strong> {{ now()->format('d M, Y') }}
    </div>

    {{-- Summary --}}
    <div class="summary-box">
        @php
            $total = $results->sum('total_score');
            $count = $results->count();
            $avg = $count ? number_format($total / $count, 2) : 0;
        @endphp

        <strong>Total Score:</strong> {{ $total }} <br>
        <strong>Average:</strong> {{ $avg }} <br>
        <strong>Position:</strong> {{ $position ?? '—' }} of {{ $total_students ?? '—' }}
        <p><strong>Position:</strong> {{ $position ?? '—' }} out of {{ $total_students ?? '—' }}</p>
    </div>

    {{-- Result Table --}}
    <table>
        <thead>
            <tr>
                <th>Subject</th>
                <th>Test (40)</th>
                <th>Exam (60)</th>
                <th>Total</th>
                <th>Grade</th>
                <th>Remark</th>
            </tr>
        </thead>
        <tbody>
        @foreach($results as $result)
            <tr>
                <td>{{ $result->subject->name }}</td>
                <td class="center">{{ $result->test_score }}</td>
                <td class="center">{{ $result->exam_score }}</td>
                <td class="center"><strong>{{ $result->total_score }}</strong></td>
                <td class="center"><strong>{{ $result->grade }}</strong></td>
                <td class="center">{{ $result->remark }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    {{-- Teacher Remark --}}
    @if($results->first() && $results->first()->teacher_remark)
        <div class="info-box">
            <strong>Teacher's Remark:</strong> {{ $results->first()->teacher_remark }}
        </div>
    @endif

</body>
</html>
