<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Akun Mahasiswa - Alumni MNI IPB</title>

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
        
        <!-- Header -->
        <div class="text-center space-y-2">
            @if(file_exists(public_path('images/logoMNI.jpeg')))
                <img src="{{ asset('images/logoMNI.jpeg') }}" alt="Logo Prodi MNI IPB" class="h-16 w-16 rounded-2xl mx-auto object-cover shadow-xl shadow-emerald-500/20 border border-white/20 mb-2">
            @elseif(file_exists(public_path('images/logoMNI.png')))
                <img src="{{ asset('images/logoMNI.png') }}" alt="Logo Prodi MNI IPB" class="h-16 w-auto mx-auto object-contain drop-shadow-xl mb-2">
            @elseif(file_exists(public_path('images/logo.png')))
                <img src="{{ asset('images/logo.png') }}" alt="Logo Prodi MNI IPB" class="h-16 w-auto mx-auto object-contain drop-shadow-xl mb-2">
            @elseif(file_exists(public_path('images/logo.svg')))
                <img src="{{ asset('images/logo.svg') }}" alt="Logo Prodi MNI IPB" class="h-16 w-auto mx-auto object-contain drop-shadow-xl mb-2">
            @elseif(file_exists(public_path('images/logo.jpg')) || file_exists(public_path('images/logo.jpeg')))
                <img src="{{ asset('images/' . (file_exists(public_path('images/logo.jpg')) ? 'logo.jpg' : 'logo.jpeg')) }}" alt="Logo Prodi MNI IPB" class="h-16 w-auto mx-auto object-contain drop-shadow-xl mb-2">
            @else
                <div class="w-14 h-14 rounded-3xl bg-gradient-to-tr from-emerald-600 to-teal-500 mx-auto flex items-center justify-center text-white font-black text-xl shadow-xl shadow-emerald-600/30 border border-emerald-400/20">
                    MNI
                </div>
            @endif
            <h1 class="font-heading text-2xl font-extrabold text-white tracking-tight">Daftar Akun Mahasiswa</h1>
            <p class="text-xs text-slate-300">Khusus Mahasiswa & Alumni Manajemen Industri IPB University</p>
        </div>

        @if($errors->any())
        <div class="bg-rose-500/20 text-rose-300 border border-rose-500/30 px-4 py-3 rounded-2xl text-xs font-semibold space-y-1 backdrop-blur-sm">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
        @endif

        <!-- Card Form -->
        <div class="bg-white/95 backdrop-blur-xl rounded-3xl p-6 sm:p-8 shadow-2xl space-y-4 border border-white/20">
            <form method="POST" action="{{ route('register') }}" class="space-y-3.5">
                @csrf

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Nama Lengkap</label>
                    <input type="text" name="name" required value="{{ old('name') }}" placeholder="Nama Anda"
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">NIM Mahasiswa IPB</label>
                    <input type="text" name="nim_nip" required value="{{ old('nim_nip') }}" placeholder="Contoh: J3A120001"
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Email (Apps IPB / Personal)</label>
                    <input type="email" name="email" required value="{{ old('email') }}" placeholder="nama@apps.ipb.ac.id"
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Nomor WhatsApp Aktif</label>
                    <input type="text" name="no_wa" required value="{{ old('no_wa') }}" placeholder="08123456789"
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700">Password</label>
                        <input type="password" name="password" required placeholder="••••••••"
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700">Konfirmasi</label>
                        <input type="password" name="password_confirmation" required placeholder="••••••••"
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                    </div>
                </div>

                <button type="submit" class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl font-bold text-sm shadow-lg shadow-emerald-600/30 transition-all cursor-pointer mt-2">
                    Buat Akun Mahasiswa
                </button>
            </form>

            <div class="text-center pt-1">
                <p class="text-xs text-slate-500 font-medium">
                    Sudah punya akun? 
                    <a href="{{ route('login') }}" class="font-bold text-emerald-700 hover:text-emerald-800 hover:underline">Masuk ke Sistem</a>
                </p>
            </div>
        </div>

    </div>

</body>
</html>
