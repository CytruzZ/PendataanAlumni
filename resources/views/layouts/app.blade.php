<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Pendataan Alumni MNI IPB') - Portal Resmi Alumni MNI IPB University</title>
    
    <!-- Google Fonts Inter & Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Vite CSS / JS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .font-heading {
            font-family: 'Outfit', 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col font-sans selection:bg-emerald-500 selection:text-white">
    
    <!-- Top Navigation Bar -->
    <header class="bg-white/95 backdrop-blur-md border-b border-slate-200/80 sticky top-0 z-40 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 sm:h-20">
                
                <!-- Brand / Logo -->
                <div class="flex items-center space-x-3">
                    <a href="{{ route('alumni.index') }}" class="flex items-center space-x-3.5 group">
                        @if(file_exists(public_path('images/logoMNI.jpeg')))
                            <img src="{{ asset('images/logoMNI.jpeg') }}" alt="Logo Prodi MNI IPB" class="h-11 w-11 rounded-2xl object-cover shadow-md shadow-slate-200 group-hover:scale-105 transition-transform duration-200">
                        @elseif(file_exists(public_path('images/logoMNI.png')))
                            <img src="{{ asset('images/logoMNI.png') }}" alt="Logo Prodi MNI IPB" class="h-11 w-11 rounded-2xl object-contain group-hover:scale-105 transition-transform duration-200">
                        @elseif(file_exists(public_path('images/logo.png')))
                            <img src="{{ asset('images/logo.png') }}" alt="Logo Prodi MNI IPB" class="h-11 w-auto object-contain group-hover:scale-105 transition-transform duration-200">
                        @elseif(file_exists(public_path('images/logo.svg')))
                            <img src="{{ asset('images/logo.svg') }}" alt="Logo Prodi MNI IPB" class="h-11 w-auto object-contain group-hover:scale-105 transition-transform duration-200">
                        @elseif(file_exists(public_path('images/logo.jpg')) || file_exists(public_path('images/logo.jpeg')))
                            <img src="{{ asset('images/' . (file_exists(public_path('images/logo.jpg')) ? 'logo.jpg' : 'logo.jpeg')) }}" alt="Logo Prodi MNI IPB" class="h-11 w-auto object-contain group-hover:scale-105 transition-transform duration-200">
                        @else
                            <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-emerald-600 via-teal-600 to-emerald-500 flex items-center justify-center text-white font-black text-xl shadow-lg shadow-emerald-600/25 group-hover:scale-105 group-hover:shadow-emerald-600/35 transition-all duration-200">
                                MNI
                            </div>
                        @endif
                        <div>
                            <div class="flex items-center space-x-2">
                                <span class="font-heading font-extrabold text-slate-900 text-lg sm:text-xl leading-none group-hover:text-emerald-700 transition-colors">Alumni MNI IPB</span>
                                <span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60">SV IPB</span>
                            </div>
                            <span class="text-xs text-slate-500 font-medium leading-none block mt-1">Sistem Direktori & Informasi Karir</span>
                        </div>
                    </a>
                </div>

                <!-- Right User Info & Actions -->
                <div class="flex items-center space-x-2.5 sm:space-x-3">
                    @auth
                        @if(Auth::user()->isAdmin())
                            <a href="{{ route('alumni.export') }}" target="_blank"
                               class="hidden md:inline-flex items-center px-3.5 py-2 rounded-xl text-xs font-bold text-slate-700 bg-slate-100/80 hover:bg-slate-200/80 border border-slate-200 transition-all space-x-1.5 shadow-xs">
                                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                <span>Export CSV</span>
                            </a>
                        @endif

                        <!-- User Profile Pill -->
                        <div class="flex items-center space-x-2.5 bg-slate-100/80 border border-slate-200/80 px-3 py-1.5 rounded-2xl shadow-xs">
                            <div class="w-8 h-8 rounded-xl {{ Auth::user()->isAdmin() ? 'bg-indigo-600' : 'bg-emerald-600' }} text-white flex items-center justify-center text-xs font-black shadow-sm">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <div class="hidden sm:block text-left pr-1">
                                <span class="font-bold text-slate-900 text-xs block leading-tight max-w-[130px] truncate">{{ Auth::user()->name }}</span>
                                @if(Auth::user()->isAdmin())
                                    <span class="text-[10px] text-indigo-600 font-extrabold uppercase tracking-wide block leading-tight">Admin Prodi</span>
                                @else
                                    <span class="text-[10px] text-emerald-600 font-bold block leading-tight">Mahasiswa ({{ Auth::user()->nim_nip ?: 'IPB' }})</span>
                                @endif
                            </div>
                        </div>

                        <!-- Logout Button -->
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-all border border-transparent hover:border-rose-100" title="Keluar / Logout">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl text-xs shadow-md shadow-emerald-600/25 transition-all">
                            Masuk
                        </a>
                    @endauth
                </div>

            </div>
        </div>
    </header>

    <!-- Global Toast Alert -->
    @if(session('success'))
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
        <div class="bg-emerald-500 text-white px-4 py-3 rounded-2xl shadow-lg shadow-emerald-500/20 flex items-center justify-between border border-emerald-400">
            <div class="flex items-center space-x-3">
                <svg class="w-5 h-5 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="text-xs sm:text-sm font-medium">{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-100 hover:text-white p-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>
    @endif

    @if(session('warning'))
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
        <div class="bg-amber-500 text-white px-4 py-3 rounded-2xl shadow-lg shadow-amber-500/20 flex items-center justify-between border border-amber-400">
            <div class="flex items-center space-x-3">
                <svg class="w-5 h-5 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span class="text-xs sm:text-sm font-medium">{{ session('warning') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-amber-100 hover:text-white p-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
        <div class="bg-rose-500 text-white px-4 py-3 rounded-2xl shadow-lg shadow-rose-500/20 flex items-center justify-between border border-rose-400">
            <div class="flex items-center space-x-3">
                <svg class="w-5 h-5 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="text-xs sm:text-sm font-medium">{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-rose-100 hover:text-white p-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>
    @endif

    <!-- Main Body Container -->
    <main class="flex-1 py-6 sm:py-8">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200/80 py-6 text-center text-xs text-slate-500">
        <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-2">
            <p>&copy; {{ date('Y') }} Program Studi Manajemen Industri (MNI) - Sekolah Vokasi IPB University.</p>
            <p class="text-slate-400 font-medium">Portal Pendataan & Direktori Alumni Resmi</p>
        </div>
    </footer>

    <!-- Keep-Alive Ping to Prevent 419 Page Expired -->
    <script>
    setInterval(function() {
        fetch('{{ route('ping') }}', {
            method: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        }).catch(function() {});
    }, 10 * 60 * 1000); // Ping every 10 minutes
    </script>

    @stack('scripts')
</body>
</html>
