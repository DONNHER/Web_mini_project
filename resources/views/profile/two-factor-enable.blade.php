@extends('layouts.app')

@section('title', 'Enable Two-Factor Authentication - PageTurner')

@section('header')
    <h1 class="text-3xl font-bold text-gray-900">Enable Two-Factor Authentication</h1>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">Step 1: Scan QR Code</h2>
        
        <div class="text-center mb-6">
            <div class="inline-block p-4 bg-gray-100 rounded-lg">
                {!! $qrCode !!}
            </div>
        </div>

        <div class="mb-6">
            <p class="text-gray-600 mb-2">Or enter this code manually:</p>
            <code class="bg-gray-100 px-4 py-2 rounded-lg font-mono">{{ $secret }}</code>
        </div>

        <h2 class="text-xl font-semibold mb-4">Step 2: Verify Code</h2>
        
        <form method="POST" action="{{ route('two-factor.confirm') }}">
            @csrf
            
            <div class="mb-4">
                <label for="code" class="block text-gray-700 font-medium mb-2">
                    Enter the 6-digit code from your authenticator app
                </label>
                <input type="text" 
                       name="code" 
                       id="code"
                       class="w-full md:w-1/2 border-gray-300 rounded-md shadow-sm focus:ring-brown-500 focus:border-brown-500"
                       placeholder="000000"
                       required>
            </div>

            <button type="submit" 
                    class="bg-brown-600 text-white px-6 py-2 rounded-md hover:bg-brown-700 transition">
                Verify and Enable
            </button>
        </form>
    </div>

    <!-- Recovery Codes -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-6">
        <h3 class="text-lg font-semibold text-yellow-800 mb-2">Recovery Codes</h3>
        <p class="text-yellow-700 mb-4">
            Save these recovery codes in a safe place. You can use them to access your account if you lose your device.
            Each code can only be used once.
        </p>
        
        <div class="grid grid-cols-2 gap-2">
            @foreach($recoveryCodes as $code)
                <code class="bg-white px-3 py-2 rounded border border-yellow-300 font-mono text-sm">{{ $code }}</code>
            @endforeach
        </div>

        <button onclick="printRecoveryCodes()" 
                class="mt-4 bg-yellow-600 text-white px-4 py-2 rounded-md hover:bg-yellow-700 transition">
            Print Recovery Codes
        </button>
    </div>

    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-blue-800 mb-2">Important</h3>
        <ul class="list-disc list-inside text-blue-700 space-y-1">
            <li>Keep your recovery codes safe - they're the only way you can recover your account</li>
            <li>Each recovery code can only be used once</li>
            <li>You'll need to verify a code every time you log in</li>
            <li>If you lose access to both your device and recovery codes, you may lose access to your account</li>
        </ul>
    </div>
</div>

<script>
function printRecoveryCodes() {
    var codes = @json($recoveryCodes);
    var printWindow = window.open('', '_blank');
    printWindow.document.write('<html><head><title>Recovery Codes</title>');
    printWindow.document.write('<style>body { font-family: monospace; padding: 20px; }</style>');
    printWindow.document.write('</head><body>');
    printWindow.document.write('<h1>PageTurner Bookstore - Recovery Codes</h1>');
    printWindow.document.write('<p>Keep these codes safe. Each can be used once.</p>');
    printWindow.document.write('<ul>');
    codes.forEach(function(code) {
        printWindow.document.write('<li>' + code + '</li>');
    });
    printWindow.document.write('</ul>');
    printWindow.document.write('</body></html>');
    printWindow.print();
}
</script>
@endsection