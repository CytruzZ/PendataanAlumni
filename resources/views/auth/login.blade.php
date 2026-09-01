<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Portal Alumni MNI IPB University</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .font-heading {
            font-family: 'Outfit', 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gradient-to-tr from-slate-950 via-slate-900 to-indigo-950 min-h-screen flex items-center justify-center p-4 font-sans text-slate-800 antialiased relative overflow-hidden">

    <!-- Background glowing orbs -->
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-emerald-500/15 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-indigo-500/20 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-md w-full space-y-6 relative z-10">
        
        <!-- Logo & Header -->
        <div class="text-center space-y-2.5">
            @if(file_exists(public_path('images/logoMNI.jpeg')))
                <img src="{{ asset('images/logoMNI.jpeg') }}" alt="Logo Prodi MNI IPB" class="h-20 w-20 rounded-3xl mx-auto object-cover shadow-2xl shadow-emerald-500/20 border-2 border-white/20 mb-2">
            @elseif(file_exists(public_path('images/logoMNI.png')))
                <img src="{{ asset('images/logoMNI.png') }}" alt="Logo Prodi MNI IPB" class="h-20 w-auto mx-auto object-contain drop-shadow-xl mb-2">
            @elseif(file_exists(public_path('images/logo.png')))
                <img src="{{ asset('images/logo.png') }}" alt="Logo Prodi MNI IPB" class="h-20 w-auto mx-auto object-contain drop-shadow-xl mb-2">
            @elseif(file_exists(public_path('images/logo.svg')))
                <img src="{{ asset('images/logo.svg') }}" alt="Logo Prodi MNI IPB" class="h-20 w-auto mx-auto object-contain drop-shadow-xl mb-2">
            @elseif(file_exists(public_path('images/logo.jpg')) || file_exists(public_path('images/logo.jpeg')))
                <img src="{{ asset('images/' . (file_exists(public_path('images/logo.jpg')) ? 'logo.jpg' : 'logo.jpeg')) }}" alt="Logo Prodi MNI IPB" class="h-20 w-auto mx-auto object-contain drop-shadow-xl mb-2">
            @else
                <div class="w-16 h-16 rounded-3xl bg-gradient-to-tr from-emerald-600 via-teal-600 to-emerald-500 mx-auto flex items-center justify-center text-white font-black text-2xl shadow-xl shadow-emerald-600/30 border border-emerald-400/20">
                    MNI
                </div>
            @endif
            <h1 class="font-heading text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Portal Alumni MNI IPB</h1>
            <p class="text-xs sm:text-sm text-slate-300">Sistem Pendataan & Penelusuran Karir Alumni</p>
        </div>

        <!-- Notification Toast -->
        @if(session('success'))
        <div class="bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 px-4 py-3 rounded-2xl text-xs font-semibold text-center backdrop-blur-sm">
            {{ session('success') }}
        </div>
        @endif

        @if(session('warning'))
        <div class="bg-amber-500/20 text-amber-300 border border-amber-500/30 px-4 py-3 rounded-2xl text-xs font-semibold text-center backdrop-blur-sm">
            {{ session('warning') }}
        </div>
        @endif

        @if($errors->any())
        <div class="bg-rose-500/20 text-rose-300 border border-rose-500/30 px-4 py-3 rounded-2xl text-xs font-semibold space-y-1 backdrop-blur-sm">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
        @endif

        <!-- Card Container -->
        <div class="bg-white/95 backdrop-blur-xl rounded-3xl p-6 sm:p-8 shadow-2xl space-y-6 border border-white/20">
            
            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Username / Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <input type="text" name="email" id="loginEmail" required value="{{ old('email') }}"
                               placeholder="mahasiswa atau admin" 
                               class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                        <input type="password" name="password" id="loginPassword" required 
                               placeholder="••••••••" 
                               class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs pt-1">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                        <span class="text-slate-600 font-medium">Ingat Saya</span>
                    </label>
                </div>

                <button type="submit" class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl font-bold text-sm shadow-lg shadow-emerald-600/30 transition-all cursor-pointer">
                    Masuk ke Sistem
                </button>
            </form>

            <div class="text-center pt-2 border-t border-slate-100">
                <p class="text-xs text-slate-500 font-medium">
                    Belum punya akun mahasiswa? 
                    <a href="{{ route('register') }}" class="font-bold text-emerald-700 hover:text-emerald-800 hover:underline">Daftar di Sini</a>
                </p>
            </div>

        </div>

    </div>

</body>
</html>

