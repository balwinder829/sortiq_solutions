@extends('layouts.app')

@section('content')

<div class="container">

    <div class="mb-2 d-flex justify-content-between align-items-center">

        <div>
            <h3>WhatsApp QR Code</h3>
        </div>
        <div>
            <button type="button" id="resetWhatsappBtn" class="btn btn-danger">
                Reconnect WhatsApp
            </button>

            <form id="resetWhatsappForm" action="{{ route('admin.whatsapp.reset') }}" method="POST" style="display:none;">
                @csrf
            </form>

            <script>
                document.getElementById('resetWhatsappBtn').addEventListener('click', function () {
                    if (confirm('Are you sure you want to reconnect WhatsApp?')) {
                        document.getElementById('resetWhatsappForm').submit();
                    }
                });
            </script>
        </div>
    </div>
    <div>
        
        @if(session('whatsapp_token'))
            <div class="alert alert-success">
                WhatsApp is logged in. Token: <strong>{{ session('whatsapp_token') }}</strong>
            </div>
        @endif

        @if($connectionStatus === "WhatsApp already connected!")
            <div class="alert alert-success">
                WhatsApp Connection Status: <strong>{{ $connectionStatus }}</strong>
            </div>
        @elseif(strpos($connectionStatus, "data:image/png;base64") > -1)

        @else
            <div class="alert alert-warning">
                WhatsApp Connection Status: <strong>{{ $connectionStatus }}</strong>
            </div>
        @endif

        <div id="qrCodeContainer">
            <!-- QR code will be displayed here -->
            @if($qrCode)
                <img src="{{ $qrCode }}" alt="WhatsApp QR Code">
            @else
                <p>{{ $message }}</p>
            @endif
        </div>
    </div>
</div>

@endsection