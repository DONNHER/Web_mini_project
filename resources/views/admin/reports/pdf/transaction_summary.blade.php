@extends('admin.reports.pdf.layout')

@section('content')
    <div class="summary-box">
        <h3 style="margin-top:0">Transaction Summary Report</h3>
        <p>Active Portfolio: {{ number_format($data->sum('principal_amount')) }} PHP</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Loan ID</th>
                <th>Borrower</th>
                <th>Asset</th>
                <th>Principal</th>
                <th>Status</th>
                <th>Due Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $loan)
                <tr>
                    <td>#{{ $loan->id }}</td>
                    <td>{{ $loan->user->name }}</td>
                    <td>{{ $loan->loanProduct?->name }}</td>
                    <td>{{ number_format($loan->principal_amount, 2) }}</td>
                    <td>{{ strtoupper($loan->status) }}</td>
                    <td>{{ $loan->due_date ? $loan->due_date->format('Y-m-d') : 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
