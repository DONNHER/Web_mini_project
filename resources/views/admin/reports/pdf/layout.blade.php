<!DOCTYPE html>
<html>
<head>
    <style>
        @page { margin: 100px 25px; }
        header { position: fixed; top: -60px; left: 0px; right: 0px; height: 50px; text-align: center; border-bottom: 2px solid #C06C3E; }
        footer { position: fixed; bottom: -60px; left: 0px; right: 0px; height: 50px; text-align: center; font-size: 9px; color: #999; border-top: 1px solid #eee; padding-top: 10px; }
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; color: #333; }
        h1 { margin: 0; color: #C06C3E; text-transform: uppercase; font-size: 24px; }
        .gen-date { font-size: 10px; color: #666; margin-top: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #f2f2f2; border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 10px; text-transform: uppercase; }
        td { border: 1px solid #ddd; padding: 8px; font-size: 10px; }
        .summary-box { background: #f9f9f9; padding: 15px; border-left: 4px solid #C06C3E; margin-bottom: 20px; }
        .signature-section { margin-top: 50px; width: 100%; }
        .signature-box { width: 200px; border-top: 1px solid #000; text-align: center; margin-top: 40px; float: right; }
        .signature-label { font-size: 10px; font-weight: bold; text-transform: uppercase; margin-top: 5px; }
        .pagenum:before { content: counter(page); }
    </style>
</head>
<body>
    <header>
        <h1>LendingSystem</h1>
        <div class="gen-date">OFFICIAL DOCUMENT | Generated: {{ now()->format('M d, Y H:i') }}</div>
    </header>

    <footer>
        © {{ date('Y') }} LendingSystem Enterprise. Confidential & Proprietary. | Page <span class="pagenum"></span>
    </footer>

    <main>
        @yield('content')

        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-label">Authorized Signature</div>
                <div style="font-size: 8px; color: #999;">Digital ID: {{ md5(now()) }}</div>
            </div>
        </div>
    </main>
</body>
</html>
