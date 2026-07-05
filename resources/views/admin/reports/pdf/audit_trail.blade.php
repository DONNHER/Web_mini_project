@extends('admin.reports.pdf.layout')

@section('content')
    <div class="summary-box">
        <h3 style="margin-top:0">System Audit Trail</h3>
        <p>Log entries for security and integrity verification.</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>User</th>
                <th>Event</th>
                <th>Auditable Type</th>
                <th>IP Address</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $audit)
                <tr>
                    <td>{{ $audit->created_at->format('Y-m-d H:i:s') }}</td>
                    <td>{{ $audit->user->name ?? 'System' }}</td>
                    <td>{{ $audit->event }}</td>
                    <td>{{ class_basename($audit->auditable_type) }} #{{ $audit->auditable_id }}</td>
                    <td>{{ $audit->ip_address }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
