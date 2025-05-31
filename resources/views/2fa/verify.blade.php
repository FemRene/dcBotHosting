@extends('layouts.app')

@section('content')
    <h1>Two-Factor Authentication</h1>

    <form method="POST" action="{{ route('2fa.verify.post') }}">
        @csrf
        <label for="one_time_password">Enter your 6-digit authentication code:</label>
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
        <button type="submit">Verify</button>
    </form>
@endsection
