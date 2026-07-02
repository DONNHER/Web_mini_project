<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e2e8f0; border-radius: 8px; }
        .header { background-color: #7c2d12; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { padding: 20px; }
        .stat-grid { display: grid; grid-template-cols: 1fr 1fr; gap: 10px; margin-top: 20px; }
        .stat-card { background-color: #f8fafc; padding: 15px; border-radius: 4px; border: 1px solid #edf2f7; }
        .stat-label { font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: bold; }
        .stat-value { font-size: 20px; color: #1e293b; font-weight: bold; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Daily Sales Report</h1>
            <p>{{ $reportData['date'] }}</p>
        </div>
        <div class="content">
            <p>Hello Administrator,</p>
            <p>Here is the summary of sales activity for yesterday:</p>

            <div class="stat-grid">
                <div class="stat-card">
                    <div class="stat-label">Total Orders</div>
                    <div class="stat-value">{{ $reportData['total_orders'] }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Total Revenue</div>
                    <div class="stat-value">₱{{ number_format($reportData['total_revenue'], 2) }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Completed</div>
                    <div class="stat-value">{{ $reportData['completed_orders'] }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Pending</div>
                    <div class="stat-value">{{ $reportData['pending_orders'] }}</div>
                </div>
            </div>

            <p style="margin-top: 30px;">Attached to this email is a detailed Excel export of all orders from yesterday.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} PageTurner Bookstore. Automated System Report.</p>
        </div>
    </div>
</body>
</html>
