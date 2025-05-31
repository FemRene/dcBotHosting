@extends('layouts.app')

@section('content')
    <h1>Set up Two-Factor Authentication (2FA)</h1>

    <p>Scan the QR code below with your Google Authenticator app or compatible TOTP app.</p>

    <div>
        {!! $qrCode !!}
    </div>

    <p>Or enter this code manually: <strong>{{ $secret }}</strong></p>

    <form method="POST" action="{{ route('2fa.enable') }}">
        @csrf
        <label for="one_time_password">Enter the 6-digit code from your app:</label>
        <input
            type="text"
            name="one_time_password"
            id="one_time_password"
            pattern="\d{6}"
            required
            autofocus
        >
        @error('one_time_password')
        <div style="color: red;">{{ $message }}</div>
        @enderror
        <button type="submit">Enable 2FA</button>
    </form>
@endsection
