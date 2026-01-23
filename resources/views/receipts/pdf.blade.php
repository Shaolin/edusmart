<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Fee Receipt — {{ $student->name }}</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #1f2937;
        }

        .header, .footer {
            width: 100%;
            margin-bottom: 20px;
        }

        .watermark {
            position: fixed;
            top: 45%;
            left: 50%;
            width: 300px;
            opacity: 0.05;
            transform: translate(-50%, -50%) rotate(12deg);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #d1d5db;
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #f3f4f6;
        }

        .text-right { text-align: right; }
        .text-green { color: #16a34a; }
        .text-red { color: #dc2626; }

        .title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .badge {
            background-color: #fef3c7;
            padding: 4px 8px;
            font-weight: bold;
            border-radius: 4px;
        }
    </style>
</head>
<body>

{{-- Watermark --}}
@if($school && $school->logo)

    {{-- <img src="{{ public_path('storage/' . $school->logo) }}" class="watermark"> --}}
    <img src="{{ public_path('school_logos/' . $school->logo) }}" class="watermark">

@endif

{{-- Header --}}
<table class="header">
    <tr>
        <td style="width: 70%;">
            <table>
                <tr>
                    @if($school && $school->logo)
                        <td style="width: 90px;">

                            {{-- <img src="{{ public_path('storage/' . $school->logo) }}"
                                 style="width:80px; height:80px; object-fit:contain;"> --}}

                                 <img src="{{ public_path('school_logos/' . $school->logo) }}"
     style="width:80px; height:80px; object-fit:contain;">

                        </td>
                    @endif
                    <td>
                        <div class="title">{{ $school->name ?? 'School Name' }}</div>
                        <div>{{ $school->address ?? '' }}</div>
                        <div>Contact: {{ $school->phone ?? $school->contact ?? 'Not set' }}</div>
                        @if($school->email)<div>Email: {{ $school->email }}</div>@endif
                        @if($school->website)<div>Website: {{ $school->website }}</div>@endif
                    </td>
                </tr>
            </table>
        </td>
        <td class="text-right" style="vertical-align: top;">
            <span class="badge">RECEIPT</span>
        </td>
    </tr>
</table>

{{-- Student Info --}}
<div style="margin-bottom:15px; border-bottom:1px solid #d1d5db; padding-bottom:10px;">
    <div class="title">Student Information</div>
    <table>
        <tr>
            <td><strong>Name:</strong> {{ $student->name }}</td>
            <td><strong>Class:</strong> {{ $student->schoolClass->name ?? '—' }}</td>
        </tr>
        <tr>
            <td><strong>Session:</strong> {{ $session ?? '—' }}</td>
<td><strong>Term:</strong> {{ $term ?? '—' }}</td>

        </tr>
    </table>
</div>

{{-- Payment Table --}}
<table>
    <thead>
        <tr>
            <th>Fee</th>
            <th>Amount Paid (₦)</th>
            <th>Balance After Payment (₦)</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        @php $totalPaid = 0; $totalBalance = 0; @endphp

        @forelse($payments as $payment)
            @php
                $totalPaid += $payment->amount;
                $totalBalance = $payment->balance_after_payment;
            @endphp
            <tr>
                <td>{{ $payment->fee->name ?? '—' }}</td>
                <td class="text-green">₦{{ number_format($payment->amount, 2) }}</td>
                <td class="text-red">₦{{ number_format($payment->balance_after_payment, 2) }}</td>
                <td>
                    {{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-right">No payments recorded.</td>
            </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <td class="text-right"><strong>Total</strong></td>
            <td class="text-green"><strong>₦{{ number_format($totalPaid, 2) }}</strong></td>
            <td class="text-red"><strong>₦{{ number_format($totalBalance, 2) }}</strong></td>
            <td></td>
        </tr>
    </tfoot>
</table>

{{-- Footer --}}
<div class="footer" style="margin-top:20px; text-align:right;">
    <p>Thank you for your payment!</p>
</div>

</body>
</html>
