@extends('admin.reports.pdf.layout')

@section('content')
    <div class="summary-box">
        <h3 style="margin-top:0">System Usage Statistics</h3>
        <p>Aggregation Period: {{ strtoupper($filters['period'] ?? 'monthly') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Period</th>
                <th>Total Applications</th>
                <th>Principal Volume</th>
                <th>Interest Yield</th>
            </tr>
        </thead>
        <tbody>
            @foreach($stats as $row)
                <tr>
                    <td>{{ $row->period }}</td>
                    <td>{{ number_format($row->total_loans) }}</td>
                    <td>{{ number_format($row->principal, 2) }} PHP</td>
                    <td>{{ number_format($row->total_value - $row->principal, 2) }} PHP</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
