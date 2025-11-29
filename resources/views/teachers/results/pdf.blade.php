<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Result — {{ $student->name }}</title>
    <style>
        body { font-family: sans-serif; color: #111827; margin: 0; padding: 0; }
        .container { width: 90%; margin: auto; padding: 20px; background-color: #ffffff; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .school-info h1 { color: #1e40af; font-weight: bold; margin: 0; font-size: 20px; }
        .school-info p { margin: 0; color: #4b5563; font-size: 12px; }
        .result-badge { background-color: #facc15; color: #78350f; padding: 4px 8px; font-size: 12px; font-weight: bold; border-radius: 4px; }
        .student-card, .session-card, .summary-card { border: 1px solid #d1d5db; border-radius: 8px; padding: 10px; margin-bottom: 15px; background-color: #f9fafb; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #d1d5db; padding: 6px; font-size: 12px; text-align: center; }
        th { background-color: #dbeafe; color: #1e40af; }
        .teacher-remark { border: 1px solid #d1d5db; border-radius: 8px; padding: 10px; background-color: #f9fafb; margin-top: 15px; }
        .watermark { position: absolute; top: 50%; left: 50%; width: 200px; opacity: 0.08; transform: translate(-50%, -50%) rotate(-15deg); z-index: 0; }
    </style>
</head>
<body>
    <div class="container">

        {{-- Watermark --}}
        @if($school && $school->logo)
            <img src="{{ public_path('school_logos/' . $school->logo) }}" class="watermark">
        @endif

        {{-- School Header --}}
        <div class="header">
            <div class="school-info">
                <h1>{{ $school->name ?? 'School Name' }}</h1>
                <p>{{ $school->address ?? '' }}</p>
                <p>Contact: {{ $school->phone ?? 'Not set' }}</p>
            </div>
            <div class="result-badge">Result Sheet</div>
        </div>

        {{-- Student Info Cards --}}
        <div style="display: flex; justify-content: space-between; margin-bottom: 15px;">
            <div class="student-card" style="width: 32%;">
                <strong>Student Info</strong><br>
                Name: {{ $student->name }}<br>
                Admission No: {{ $student->admission_number ?? '—' }}<br>
                Class: {{ $student->schoolClass->name ?? '—' }}
            </div>
            <div class="session-card" style="width: 32%;">
                <strong>Session & Term</strong><br>
                Term: {{ $term->name ?? '—' }}<br>
                Session: {{ $session->name ?? '—' }}<br>
                Date: {{ now()->format('d M, Y') }}
            </div>
            <div class="summary-card" style="width: 32%;">
                <strong>Summary</strong><br>
                @php
                    $totalScore = $results->sum('total_score');
                    $subjectCount = $results->count();
                    $average = $subjectCount ? number_format($totalScore / $subjectCount, 2) : '0.00';
                @endphp
                Total Score: {{ $totalScore }}<br>
                Average: {{ $average }}<br>
                Position: {{ $position ?? '—' }} out of {{ $total_students ?? '—' }}
            </div>
        </div>

        {{-- Results Table --}}
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
                        <td>{{ $result->test_score }}</td>
                        <td>{{ $result->exam_score }}</td>
                        <td>{{ $result->total_score }}</td>
                        <td>{{ $result->grade }}</td>
                        <td>{{ $result->remark }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Teacher Remark --}}
        @if($results->first() && $results->first()->teacher_remark)
            <div class="teacher-remark">
                <strong>Teacher's Remark:</strong> {{ $results->first()->teacher_remark }}
            </div>
        @endif

    </div>
</body>
</html>
