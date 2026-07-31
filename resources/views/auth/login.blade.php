<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BeautyFed — Sign In</title>
    <link rel="icon" type="image/png" href="{{ asset('images/beautyfed-logo.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/beautyfed-logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-display { font-family: 'Playfair Display', serif; }
        @keyframes float { 0%,100% { transform: translateY(0px); } 50% { transform: translateY(-12px); } }
        .float { animation: float 6s ease-in-out infinite; }
        @keyframes fadeInUp { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:translateY(0); } }
        .fade-up { animation: fadeInUp 0.6s ease forwards; }
    </style>
</head>
<body class="h-full bg-[#0D0D1A] flex items-center justify-center overflow-hidden">

    {{-- Background decorations --}}
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-[#4A0E2E]/30 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-[#7B1D4C]/20 rounded-full blur-3xl"></div>
        <div class="absolute top-1/2 left-1/4 w-64 h-64 bg-[#C9A84C]/5 rounded-full blur-2xl float"></div>
    </div>

    <div class="relative w-full max-w-md mx-4 fade-up">

        {{-- Logo Card --}}
        <div class="text-center mb-6">
            <img src="{{ asset('images/beautyfed-logo.png') }}" alt="BeautyFed" class="h-20 w-auto mx-auto object-contain filter drop-shadow-xl">
            <p class="text-white text-[11px] font-bold tracking-widest uppercase mt-2">Sales Management System</p>
        </div>

        {{-- Login Card --}}
        <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-8 shadow-2xl">
            <h2 class="text-lg font-semibold text-white mb-1">Welcome back</h2>
            <p class="text-white/40 text-sm mb-6">Sign in to your account</p>

            {{-- Session Status --}}
            @if(session('status'))
            <div class="mb-4 p-3 bg-emerald-500/20 border border-emerald-500/30 rounded-xl text-emerald-300 text-sm">
                {{ session('status') }}
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-medium text-white/50 mb-1.5">Email Address</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                           class="w-full bg-white/5 border {{ $errors->has('email') ? 'border-rose-500/50' : 'border-white/10' }} rounded-xl px-4 py-3 text-sm text-white placeholder-white/20 focus:outline-none focus:border-[#C9A84C]/60 transition-colors"
                           placeholder="you@beautyfed.in">
                    @error('email')
                    <p class="text-rose-400 text-xs mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-xs font-medium text-white/50 mb-1.5">Password</label>
                    <input id="password" name="password" type="password" required
                           class="w-full bg-white/5 border {{ $errors->has('password') ? 'border-rose-500/50' : 'border-white/10' }} rounded-xl px-4 py-3 text-sm text-white placeholder-white/20 focus:outline-none focus:border-[#C9A84C]/60 transition-colors"
                           placeholder="••••••••">
                    @error('password')
                    <p class="text-rose-400 text-xs mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-white/20 bg-white/5 text-[#C9A84C] focus:ring-[#C9A84C]/30">
                        <span class="text-xs text-white/50">Remember me</span>
                    </label>
                    @if(Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-xs text-[#C9A84C] hover:text-[#E8C97A] transition-colors">
                        Forgot password?
                    </a>
                    @endif
                </div>

                <button type="submit"
                        class="w-full py-3 bg-gradient-to-r from-[#C9A84C] to-[#B76E79] text-white text-sm font-bold rounded-xl hover:opacity-90 active:scale-[0.99] transition-all shadow-lg shadow-[#C9A84C]/20 mt-2">
                    Sign In to BeautyFed SMS
                </button>
            </form>
        </div>

        <p class="text-center text-white/20 text-xs mt-6">
            © {{ date('Y') }} BeautyFed · All rights reserved
        </p>
    </div>

    @livewireScripts
</body>
</html>
