<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Broadsheet</title>

    <style>
        @page {
            size: A3 landscape;
            margin: 15px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #000;
        }

        h2, h3, p {
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
        }

        .header h2 {
            font-size: 22px;
        }

        .header h3 {
            font-size: 16px;
            margin-top: 5px;
        }

        .info {
            margin-top: 10px;
            margin-bottom: 15px;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead th {
            background: #e5e5e5;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
            vertical-align: middle;
        }

        td.student {
            text-align: left;
        }

        .footer {
            margin-top: 15px;
            text-align: right;
            font-size: 10px;
        }
    </style>
</head>

<body>

<div class="header">

    @if(!empty($school->logo))
        <img src="{{ public_path('storage/'.$school->logo) }}"
             width="70"
             style="margin-bottom:8px;">
    @endif

    <h2>{{ $school->name ?? 'School Name' }}</h2>

    <h3>BROADSHEET</h3>

    <div class="info">
        <strong>Class:</strong> {{ $class->name }}
        &nbsp;&nbsp;&nbsp;

        <strong>Term:</strong> {{ $term->name }}

        &nbsp;&nbsp;&nbsp;

        <strong>Session:</strong> {{ $session->name }}
    </div>

</div>

<table>

    <thead>

    <tr>

        <th>S/N</th>

        <th>Admission No</th>

        <th>Student Name</th>

        @foreach($subjects as $subject)
            <th>{{ $subject->name }}</th>
        @endforeach

        <th>Average</th>

        <th>Position</th>

    </tr>

    </thead>

    <tbody>

   @foreach($class->students as $student)

    @php
        $sum = 0;
        $count = 0;
    @endphp

    <tr>

        <td>{{ $loop->iteration }}</td>

        <td>{{ $student->admission_number ?? '-' }}</td>

        <td class="student">
            {{ $student->name }}
        </td>

        @foreach($subjects as $subject)

            @php
                $key = $student->id . '_' . $subject->id;

                $result = $results[$key] ?? null;

                $score = $result?->total_score;

                if (!is_null($score)) {
                    $sum += $score;
                    $count++;
                }
            @endphp

            <td>
                {{ $score ?? '-' }}
            </td>

        @endforeach

        <td>
            {{ $count ? number_format($sum / $count, 2) : '-' }}
        </td>

        <td>
            {{ $classPositions[$student->id] ?? '-' }}
        </td>

    </tr>

@endforeach

    </tbody>

</table>

<div class="footer">
    Generated on {{ now()->format('d M Y h:i A') }}
</div>

</body>
</html>