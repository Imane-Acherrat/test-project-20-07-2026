@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div style="max-width: 400px; margin: 60px auto;">
    <div class="card">
        <h1 style="text-align:center;"> Factory Log Sifter</h1>
        <p class="muted" style="text-align:center; margin-bottom: 24px;">Sign in to access the monitoring dashboard</p>

        <form method="POST" action="{{ route('login.attempt') }}">
            @csrf
            <div class="field">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
            </div>
            <div class="field">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="field">
                <label style="font-weight: 400;">
                    <input type="checkbox" name="remember" style="width:auto; display:inline-block;"> Remember me
                </label>
            </div>
            <button type="submit" class="btn" style="width:100%;">Log in</button>
        </form>
    </div>
</div>
@endsection
