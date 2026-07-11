<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Result — {{ $student->name }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #111827; margin: 0; padding: 0; }
        .container { width: 95%; margin: auto; padding: 10px; }
        .header { width: 100%; margin-bottom: 15px; }
        .school-name { font-weight: bold; color: #1e40af; font-size: 16px; }
        .school-contact { font-size: 10px; color: #4b5563; margin-top: 2px; }
        .result-badge { background-color: #facc15; color: #78350f; font-weight: bold; font-size: 10px; padding: 2px 5px; border-radius: 3px; float: right; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #d1d5db; padding: 4px; text-align: center; font-size: 11px; }
        th { background-color: #dbeafe; color: #1e40af; }
        .student-card, .session-card, .summary-card { border: 1px solid #d1d5db; padding: 5px; background-color: #f3f4f6; }
        .teacher-remark { border: 1px solid #d1d5db; padding: 5px; background-color: #f3f4f6; margin-top: 5px; }
        .watermark { position: absolute; top: 50%; left: 50%; width: 150px; opacity: 0.05; transform: translate(-50%, -50%) rotate(-15deg); }
    </style>
</head>
<body>
    <div class="container">

        @if($school && $school->logo)
        
              <img src="{{ asset('school_logos/' . $school->logo) }}" class="watermark">
        @endif

        
        <div class="header">
            <span class="school-name">{{ $school->name ?? 'School Name' }}</span>
            <span class="result-badge">Result Sheet</span><br>
            <span class="school-contact">{{ $school->address ?? '' }}</span><br>
            <span class="school-contact">Contact: {{ $school->phone ?? 'Not set' }}</span>
        </div>

        {{-- Info Cards using table --}}
        <table style="margin-bottom:10px;">
            <tr>
                <td class="student-card" style="width:33%;">
                    <strong>Student Info</strong><br>
                    Name: {{ $student->name }}<br>
                    Admission No: {{ $student->admission_number ?? '—' }}<br>
                    Class: {{ $student->schoolClass->name ?? '—' }}
                </td>
                <td class="session-card" style="width:33%;">
                    <strong>Session & Term</strong><br>
                    Term: {{ $term->name ?? '—' }}<br>
                    Session: {{ $session->name ?? '—' }}<br>
                    Date: {{ now()->format('d M, Y') }}
                </td>
                <td class="summary-card" style="width:33%;">
                    <strong>Summary</strong><br>
                    @php
                        $totalScore = $results->sum('total_score');
                        $subjectCount = $results->count();
                        $average = $subjectCount ? number_format($totalScore / $subjectCount, 2) : '0.00';
                    @endphp
                    Total Score: {{ $totalScore }}<br>
                    Average: {{ $average }}<br>
                    Position: {{ $position ?? '—' }} out of {{ $total_students ?? '—' }}
                </td>
            </tr>
        </table>

        {{-- Attendance Summary --}}
<div style="border:1px solid #d1d5db; padding:8px; margin-top:12px; margin-bottom:12px; background:#f9fafb;">

    <div style="font-weight:bold; color:#1e40af; text-transform:uppercase; margin-bottom:8px;">
        Attendance Summary
    </div>

    <table style="width:100%; border-collapse:collapse;">
        <tr>
            <td style="width:33%; border:1px solid #d1d5db; padding:8px; text-align:center; background:#f3f4f6;">
                <div style="font-size:11px; font-weight:bold; color:#6b7280; text-transform:uppercase;">
                    Time School Opened
                </div>

                <div style="margin-top:5px; font-size:18px; font-weight:bold; color:#2563eb;">
                    {{ $attendanceSummary->school_opened ?? '—' }}
                </div>
            </td>

            <td style="width:33%; border:1px solid #d1d5db; padding:8px; text-align:center; background:#f3f4f6;">
                <div style="font-size:11px; font-weight:bold; color:#6b7280; text-transform:uppercase;">
                    Times Present
                </div>

                <div style="margin-top:5px; font-size:18px; font-weight:bold; color:#16a34a;">
                    {{ $attendanceSummary->times_present ?? '—' }}
                </div>
            </td>

            <td style="width:33%; border:1px solid #d1d5db; padding:8px; text-align:center; background:#f3f4f6;">
                <div style="font-size:11px; font-weight:bold; color:#6b7280; text-transform:uppercase;">
                    Times Absent
                </div>

                <div style="margin-top:5px; font-size:18px; font-weight:bold; color:#dc2626;">
                    {{ $attendanceSummary->times_absent ?? '—' }}
                </div>
            </td>
        </tr>
    </table>

</div>

        {{-- Results Table with alternating row colors --}}
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
                @foreach($results as $index => $result)
                    <tr style="background-color: {{ $index % 2 == 0 ? '#ffffff' : '#f9fafb' }};">
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

        {{-- Next Term School Fees --}}
<div style="border:1px solid #d1d5db; padding:8px; margin-top:12px; background:#f9fafb;">

    <div style="font-weight:bold; color:#1e40af; text-transform:uppercase; margin-bottom:8px;">
        Next Term School Fees
    </div>

    @if($nextTermFee)

        <table style="width:100%; border-collapse:collapse;">
            <tr>
                <td style="width:25%; padding:4px 0;">
                    <strong>Term:</strong>
                </td>
                <td style="padding:4px 0;">
                    @switch($nextTermFee->term)
                        @case('first')
                            First Term
                            @break

                        @case('second')
                            Second Term
                            @break

                        @case('third')
                            Third Term
                            @break

                        @default
                            {{ ucfirst($nextTermFee->term) }}
                    @endswitch
                </td>
            </tr>

            <tr>
                <td style="padding:4px 0;">
                    <strong>Session:</strong>
                </td>
                <td style="padding:4px 0;">
                    {{ $nextTermFee->session }}
                </td>
            </tr>

            <tr>
                <td style="padding:4px 0;">
                    <strong>Amount:</strong>
                </td>
                <td style="padding:4px 0; font-size:14px; font-weight:bold; color:#15803d;">
                    NGN{{ number_format($nextTermFee->amount, 2) }}
                </td>
            </tr>
        </table>

    @else

        <p style="color:#dc2626; margin:0;">
            Next term school fees have not been published yet.
        </p>

    @endif

</div>

        {{-- Teacher Remark --}}
        @if($results->first() && $results->first()->teacher_remark)
            <div class="teacher-remark">
                <strong>Teacher's Remark:</strong> {{ $results->first()->teacher_remark }}
            </div>
        @endif

        {{-- Next Term Begins --}}
@if($setting && $setting->next_term_begins)

<div style="border-top:1px solid #d1d5db; margin-top:12px; padding-top:8px;">

    <div style="font-weight:bold; color:#1e40af; text-transform:uppercase; margin-bottom:6px;">
        Next Term Begins
    </div>

    <div style="font-size:14px; font-weight:bold; color:#111827;">
        {{ $setting->next_term_begins->format('d F, Y') }}
    </div>

</div>

@endif

    </div>
</body>
</html>
