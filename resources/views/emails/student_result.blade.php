<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Student Result</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f7f7f7; padding: 20px; color: #333;">

<div style="max-width: 700px; margin: auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); position: relative;">

    {{-- Optional watermark --}}
    @if($school && $school->logo)
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-15deg); opacity: 0.05; z-index: 0;">
        <img src="{{ asset('storage/' . $school->logo) }}" style="max-width: 400px;">
    </div>
    @endif

    {{-- School Info --}}
    <div style="display: flex; align-items: center; margin-bottom: 20px; position: relative; z-index: 1;">
        @if($school && $school->logo)
        <img src="{{ asset('storage/' . $school->logo) }}" style="width: 60px; height: 60px; object-fit: contain; margin-right: 15px;">
        @endif
        <div>
            <h1 style="margin: 0; color: #1a73e8;">{{ $school->name ?? 'School Name' }}</h1>
            <p style="margin: 2px 0;">{{ $school->address ?? '—' }}</p>
            <p style="margin: 2px 0;">Contact: {{ $school->phone ?? '—' }}</p>
            @if(!empty($school->email))
            <p style="margin: 2px 0;">Email: {{ $school->email }}</p>
            @endif
        </div>
    </div>

    <h2 style="color: #f59e0b; text-align: center; margin-bottom: 20px;">Result Sheet</h2>

    {{-- Student Info --}}
    <div style="margin-bottom: 20px; position: relative; z-index: 1;">
        <h3 style="margin-bottom: 10px; border-bottom: 1px solid #ddd; padding-bottom: 5px;">Student Information</h3>
        <p><strong>Name:</strong> {{ $student->name ?? '—' }}</p>
        <p><strong>Admission No:</strong> {{ $student->admission_number ?? '—' }}</p>
        <p><strong>Class:</strong> {{ $student->schoolClass->name ?? '—' }}</p>
        <p><strong>Term:</strong> {{ $term->name ?? '—' }}</p>
        <p><strong>Session:</strong> {{ $session->name ?? '—' }}</p>
        <p><strong>Date:</strong> {{ now()->format('d M, Y') }}</p>
    </div>

    {{-- Results Table --}}
    <div style="overflow-x: auto; position: relative; z-index: 1;">
        <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
            <thead>
                <tr style="background-color: #1a73e8; color: #fff;">
                    <th style="padding: 10px; border: 1px solid #ddd;">Subject</th>
                    <th style="padding: 10px; border: 1px solid #ddd;">Test</th>
                    <th style="padding: 10px; border: 1px solid #ddd;">Exam</th>
                    <th style="padding: 10px; border: 1px solid #ddd;">Total</th>
                    <th style="padding: 10px; border: 1px solid #ddd;">Grade</th>
                    <th style="padding: 10px; border: 1px solid #ddd;">Remark</th>
                </tr>
            </thead>
            <tbody>
                @foreach($results as $result)
                <tr style="background-color: #f9f9f9; text-align: center;">
                    <td style="padding: 8px; border: 1px solid #ddd; text-align: left;">{{ $result->subject->name ?? '—' }}</td>
                    <td style="padding: 8px; border: 1px solid #ddd;">{{ $result->test_score ?? 0 }}</td>
                    <td style="padding: 8px; border: 1px solid #ddd;">{{ $result->exam_score ?? 0 }}</td>
                    <td style="padding: 8px; border: 1px solid #ddd;">{{ $result->total_score ?? 0 }}</td>
                    <td style="padding: 8px; border: 1px solid #ddd;">{{ $result->grade ?? '—' }}</td>
                    <td style="padding: 8px; border: 1px solid #ddd;">{{ $result->remark ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Summary --}}
    <div style="margin-top: 15px; position: relative; z-index: 1;">
        <p><strong>Total Score:</strong> {{ $results->sum('total_score') ?? 0 }}</p>
        <p><strong>Average:</strong> {{ $results->count() > 0 ? number_format($results->sum('total_score') / $results->count(), 2) : 0 }}</p>
        <p><strong>Position:</strong> {{ $student->position ?? '—' }} out of {{ $total_students ?? '—' }} students</p>
        @if($results->first()?->teacher_remark)
        <p><strong>Teacher's Remark:</strong> {{ $results->first()->teacher_remark }}</p>
        @endif
    </div>

    {{-- Footer --}}
    <div style="margin-top: 20px; position: relative; z-index: 1;">
        <p>Regards,<br><strong>{{ $school->name ?? 'School' }}</strong></p>
    </div>

</div>

</body>
</html>
