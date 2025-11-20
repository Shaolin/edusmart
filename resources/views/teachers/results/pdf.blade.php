<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Student Result</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
        }
        .header h2 {
            margin: 0;
            font-size: 20px;
        }
        .section-title {
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 8px;
            font-size: 14px;
            border-bottom: 1px solid #000;
            padding-bottom: 3px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        table th, table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
        }
        .info-table td {
            text-align: left;
            border: none;
            padding: 3px 0;
        }
    </style>
</head>
<body>

    <!-- School Header -->
    <div class="header">
        <h2>{{ $school->school_name ?? 'School Name' }}</h2>
        <p>{{ $school->address ?? '' }}</p>
    </div>

    <!-- Student Information -->
    <div class="section-title">Student Information</div>
    <table class="info-table">
        <tr>
            <td><strong>Name:</strong> {{ $student->name }}</td>
            <td><strong>Class:</strong> {{ $student->schoolClass->name ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Session:</strong> {{ $session->name }}</td>
            <td><strong>Term:</strong> {{ $term->name }}</td>
        </tr>
    </table>

    <!-- Results Table -->
    <div class="section-title">Subject Scores</div>
    <table>
        <thead>
            <tr>
                <th>Subject</th>
                <th>Test (40)</th>
                <th>Exam (60)</th>
                <th>Total (100)</th>
                <th>Grade</th>
                <th>Remark</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($results as $res)
                <tr>
                    <td>{{ $res->subject->name }}</td>
                    <td>{{ $res->test_score }}</td>
                    <td>{{ $res->exam_score }}</td>
                    <td>{{ $res->total_score }}</td>
                    <td>{{ $res->grade }}</td>
                    <td>{{ $res->remark }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Summary -->
    @php
        $totalScore = $results->sum('total_score');
        $subjectCount = $results->count();
        $average = $subjectCount ? number_format($totalScore / $subjectCount, 2) : 0;
    @endphp

    <div class="section-title">Summary</div>
    <table class="info-table">
        <tr>
            <td><strong>Total Score:</strong> {{ $totalScore }}</td>
        </tr>
        <tr>
            <td><strong>Average:</strong> {{ $average }}</td>
        </tr>
        <tr>
            <td><strong>Class Position:</strong> {{ $position ?? '-' }} of {{ $total_students ?? '-' }}</td>
        </tr>
    </table>

</body>
</html>
