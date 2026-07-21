<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">

    <style>

        body{
            font-family: DejaVu Sans, sans-serif;
            font-size:12px;
            color:#000;
        }

        table{
            width:100%;
            border-collapse:collapse;
        }

        th,td{
            border:1px solid #000;
            padding:6px;
            text-align:center;
        }

        th{
            background:#e5e7eb;
        }

        .title{
            text-align:center;
            font-size:20px;
            font-weight:bold;
        }

        .subtitle{
            text-align:center;
            margin-bottom:20px;
        }

        .info{
            margin-top:15px;
            margin-bottom:20px;
        }

        .info td{
            border:none;
            padding:3px;
            text-align:left;
        }

        .summary td{
            border:1px solid #000;
            text-align:left;
            padding:8px;
        }

    </style>

</head>

<body>

{{-- School Header --}}

<table style="border:none">

<tr style="border:none">

<td style="border:none;width:80px">

@if($school && $school->logo)

<img
src="{{ public_path('school_logos/'.$school->logo) }}"
width="70">

@endif

</td>

<td style="border:none;text-align:center">

<div class="title">{{ $school->name }}</div>

<div>{{ $school->address }}</div>

<div>{{ $school->phone }}</div>

<h3>ANNUAL RESULT</h3>

</td>

</tr>

</table>

<hr>

<table class="info">

<tr>

<td><strong>Name:</strong> {{ $student->name }}</td>

<td><strong>Admission No:</strong> {{ $student->admission_number }}</td>

</tr>

<tr>

<td><strong>Class:</strong> {{ $student->schoolClass->name }}</td>

<td><strong>Session:</strong> {{ $session->name }}</td>

</tr>

</table>

<table>

<thead>

<tr>

<th>Subject</th>

<th>1st</th>

<th>2nd</th>

<th>3rd</th>

<th>Total</th>

<th>Average</th>

<th>Grade</th>

<th>Remark</th>

</tr>

</thead>

<tbody>

@foreach($cumulativeResults as $result)

<tr>

<td style="text-align:left">
{{ $result->subject->name }}
</td>

<td>{{ $result->first }}</td>

<td>{{ $result->second }}</td>

<td>{{ $result->third }}</td>

<td>{{ $result->total }}</td>

<td>{{ number_format($result->average,2) }}</td>

<td>{{ $result->grade }}</td>

<td>{{ $result->remark }}</td>

</tr>

@endforeach

</tbody>

</table>

<br>

<table class="summary">

    <tr>
        <td>
            <strong>Annual Total:</strong>
            {{ number_format($annualTotal, 2) }}
        </td>

        <td>
            <strong>Annual Average:</strong>
            {{ number_format($annualAverage, 2) }}%
        </td>
    </tr>

    <tr>
        <td>
            <strong>Annual Position:</strong>
            {{ $annualPosition ?? '—' }}
        </td>

        <td>
            <strong>Total Students:</strong>
            {{ $totalStudents }}
        </td>
    </tr>

    <tr>
        <td colspan="2"
            style="
                text-align:center;
                font-weight:bold;
                font-size:14px;
                color:{{ $promotionStatus == 'Promoted' ? 'green' : 'red' }};
            ">
            Promotion Status:
            {{ strtoupper($promotionStatus) }}
        </td>
    </tr>

</table>

<br><br>

<table style="border:none">

<tr style="border:none">

<td style="border:none;text-align:center">

_________________________<br>

Class Teacher

</td>

<td style="border:none;text-align:center">

_________________________<br>

Principal

</td>

</tr>

</table>

</body>

</html>