<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BeautyFed — Reset Password</title>
    <link rel="icon" type="image/png" href="{{ asset('images/beautyfed-logo.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/beautyfed-logo.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-[#0D0D1A] flex items-center justify-center font-['Inter']">
    <div class="w-full max-w-md mx-4">
        <div class="text-center mb-8">
            <div class="inline-flex w-14 h-14 rounded-2xl bg-gradient-to-br from-[#C9A84C] to-[#B76E79] items-center justify-center text-white font-bold text-lg mb-4">BF</div>
            <h1 class="text-2xl font-bold text-white" style="font-family:'Playfair Display',serif">BeautyFed</h1>
        </div>
        <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-8">
            <h2 class="text-lg font-semibold text-white mb-1">Forgot Password?</h2>
            <p class="text-white/40 text-sm mb-6">Enter your email and we'll send a reset link.</p>
            @if(session('status'))
                <div class="mb-4 p-3 bg-emerald-500/20 border border-emerald-500/30 rounded-xl text-emerald-300 text-sm">{{ session('status') }}</div>
            @endif
            <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
                @csrf
                <input name="email" type="email" value="{{ old('email') }}" required placeholder="you@beautyfed.in"
                       class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm text-white placeholder-white/30 focus:outline-none focus:border-[#C9A84C]/60">
                @error('email')<p class="text-rose-400 text-xs">{{ $message }}</p>@enderror
                <button type="submit" class="w-full py-3 bg-gradient-to-r from-[#C9A84C] to-[#B76E79] text-white text-sm font-bold rounded-xl hover:opacity-90">
                    Send Reset Link
                </button>
            </form>
            <a href="{{ route('login') }}" class="block text-center text-xs text-white/40 hover:text-white mt-4">← Back to Sign In</a>
        </div>
    </div>
</body>
</html>
