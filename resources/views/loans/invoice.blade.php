<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Loan Invoice #{{ $loan->id }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; line-height: 1.6; }
        .header { text-align: center; margin-bottom: 50px; border-bottom: 2px solid #000; padding-bottom: 20px; }
        .header h1 { margin: 0; text-transform: uppercase; letter-spacing: 2px; }
        .info-section { margin-bottom: 30px; }
        .info-grid { width: 100%; border-collapse: collapse; }
        .info-grid td { vertical-align: top; width: 50%; }
        .label { font-weight: bold; color: #666; font-size: 10px; text-transform: uppercase; display: block; }
        .value { font-size: 14px; font-weight: bold; }
        .table { width: 100%; margin-top: 30px; border-collapse: collapse; }
        .table th { background: #f4f4f4; text-align: left; padding: 10px; font-size: 12px; text-transform: uppercase; }
        .table td { padding: 10px; border-bottom: 1px solid #eee; font-size: 12px; }
        .total-section { margin-top: 30px; text-align: right; }
        .total-box { display: inline-block; background: #000; color: #fff; padding: 20px; border-radius: 10px; }
        .footer { margin-top: 50px; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #eee; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LendingSystem Official Invoice</h1>
        <p>Transaction ID: #{{ $loan->id }} | Generated on {{ now()->format('M d, Y') }}</p>
    </div>

    <div class="info-section">
        <table class="info-grid">
            <tr>
                <td>
                    <span class="label">Borrower Details</span>
                    <span class="value">{{ $loan->user->name }}</span><br>
                    <span class="value">{{ $loan->user->email }}</span>
                </td>
                <td style="text-align: right;">
                    <span class="label">Status</span>
                    <span class="value" style="color: #2563eb;">{{ strtoupper($loan->status) }}</span><br>
                    <span class="label">Due Date</span>
                    <span class="value">{{ $loan->due_date ? $loan->due_date->format('M d, Y') : 'N/A' }}</span>
                </td>
            </tr>
        </table>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Description</th>
                <th>Rate</th>
                <th>Term</th>
                <th style="text-align: right;">Principal</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>{{ $loan->loanProduct->name }}</strong><br>
                    <small>{{ $loan->purpose }}</small>
                </td>
                <td>{{ $loan->interest_rate }}%</td>
                <td>{{ $loan->term_months }} Months</td>
                <td style="text-align: right;">₱{{ number_format($loan->principal_amount, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="total-section">
        <div class="total-box">
            <span class="label" style="color: #aaa;">Total Repayment Amount</span>
            <div style="font-size: 24px; font-weight: 900;">₱{{ number_format($loan->total_amount, 2) }}</div>
        </div>
    </div>

    <div class="footer">
        <p>This is a computer-generated document. No signature is required.</p>
        <p>&copy; {{ date('Y') }} LendingSystem. All rights reserved.</p>
    </div>
</body>
</html>
