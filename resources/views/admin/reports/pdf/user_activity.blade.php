@extends('admin.reports.pdf.layout')

@section('content')
    <div class="summary-box">
        <h3 style="margin-top:0">User Activity Report</h3>
        <p>Period: {{ $filters['date_from'] ?? 'Beginning' }} to {{ $filters['date_to'] ?? 'Now' }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Timestamp</th>
                <th>User</th>
                <th>Event</th>
                <th>Source IP</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $audit)
                <tr>
                    <td>{{ $audit->created_at->format('Y-m-d H:i') }}</td>
                    <td>{{ $audit->user->name ?? 'System' }}</td>
                    <td>{{ ucfirst($audit->event) }}</td>
                    <td>{{ $audit->ip_address }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
