@extends('layouts.app')

@section('title', 'Katalog Alumni MNI IPB')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

    <!-- Top Banner & Stats Overview -->
    <div class="bg-gradient-to-r from-slate-950 via-slate-900 to-indigo-950 rounded-3xl p-6 sm:p-8 text-white shadow-xl shadow-slate-900/10 relative overflow-hidden border border-slate-800/80">
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-emerald-500/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-indigo-500/15 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="inline-flex items-center space-x-2 bg-emerald-500/15 text-emerald-300 border border-emerald-500/30 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Direktori Alumni MNI IPB</span>
                </div>
                <h1 class="font-heading text-2xl sm:text-3xl font-extrabold tracking-tight text-white">
                    Data Alumni & Jaringan Karir
                </h1>
                <p class="text-slate-300 text-xs sm:text-sm max-w-xl leading-relaxed">
                    Sistem informasi dan penelusuran karir alumni Manajemen Industri IPB University.
                    @if(Auth::check() && Auth::user()->isMahasiswa())
                        Gunakan fitur <strong class="text-emerald-300">"Minta WA"</strong> untuk terhubung dengan alumni melalui Admin Prodi MNI.
                    @endif
                </p>
            </div>

            @if(Auth::check() && Auth::user()->isAdmin())
            <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                <button onclick="document.getElementById('importModal').classList.remove('hidden')" 
                   class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl font-bold text-xs shadow-md shadow-indigo-600/30 transition-all flex items-center space-x-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    <span>Import CSV</span>
                </button>
                <a href="{{ route('alumni.export') }}" target="_blank" 
                   class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl font-bold text-xs shadow-md shadow-emerald-600/30 transition-all flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>Export CSV</span>
                </a>
            </div>
            @endif
        </div>

        <!-- Key Metrics Row -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 mt-8 pt-6 border-t border-white/10">
            <div class="bg-white/5 hover:bg-white/[0.08] transition-all rounded-2xl p-4 backdrop-blur-sm border border-white/10">
                <span class="text-xs text-slate-400 font-semibold block">Total Alumni Terdata</span>
                <span class="text-2xl sm:text-3xl font-extrabold text-white mt-1 block tracking-tight">{{ number_format($totalAlumni) }}</span>
            </div>
            @if(Auth::check() && Auth::user()->isAdmin())
            <div class="bg-white/5 hover:bg-white/[0.08] transition-all rounded-2xl p-4 backdrop-blur-sm border border-white/10">
                <span class="text-xs text-slate-400 font-semibold block">Bersedia Dosen Tamu</span>
                <span class="text-2xl sm:text-3xl font-extrabold text-emerald-400 mt-1 block tracking-tight">{{ number_format($totalBersediaDosen) }}</span>
            </div>
            @else
            <div class="bg-white/5 hover:bg-white/[0.08] transition-all rounded-2xl p-4 backdrop-blur-sm border border-white/10">
                <span class="text-xs text-slate-400 font-semibold block">Bidang Usaha Terdata</span>
                <span class="text-2xl sm:text-3xl font-extrabold text-emerald-400 mt-1 block tracking-tight">{{ count($listBidang) }} Bidang</span>
            </div>
            @endif
            <div class="bg-white/5 hover:bg-white/[0.08] transition-all rounded-2xl p-4 backdrop-blur-sm border border-white/10">
                <span class="text-xs text-slate-400 font-semibold block">Rentang Angkatan</span>
                <span class="text-2xl sm:text-3xl font-extrabold text-indigo-300 mt-1 block tracking-tight">{{ count($listAngkatan) }} Angkatan (43-59)</span>
            </div>
        </div>
    </div>

    <!-- Search & Filter Card -->
    <div class="bg-white rounded-3xl p-5 shadow-xs border border-slate-200/90">
        <form method="GET" action="{{ route('alumni.index') }}" class="grid grid-cols-1 sm:grid-cols-2 {{ Auth::check() && Auth::user()->isAdmin() ? 'md:grid-cols-6' : 'md:grid-cols-5' }} gap-3">
            
            <!-- Search Keyword -->
            <div class="md:col-span-2 relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" name="search" value="{{ $search }}" 
                       placeholder="Cari nama alumni, perusahaan, posisi, kota..." 
                       class="w-full pl-10 pr-4 py-2.5 bg-slate-50/80 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all placeholder:text-slate-400">
            </div>

            <!-- Filter Angkatan -->
            <div>
                <select name="angkatan" class="w-full py-2.5 px-3 bg-slate-50/80 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all cursor-pointer">
                    <option value="">Semua Angkatan</option>
                    @foreach($listAngkatan as $ang)
                        <option value="{{ $ang }}" {{ $angkatan == $ang ? 'selected' : '' }}>Angkatan {{ $ang }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Bidang Usaha -->
            <div>
                <select name="bidang" class="w-full py-2.5 px-3 bg-slate-50/80 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all cursor-pointer">
                    <option value="">Semua Bidang Usaha</option>
                    @foreach($listBidang as $b)
                        <option value="{{ $b }}" {{ $bidang == $b ? 'selected' : '' }}>{{ $b }}</option>
                    @endforeach
                </select>
            </div>

            @if(Auth::check() && Auth::user()->isAdmin())
            <!-- Filter Dosen Tamu (Admin) -->
            <div>
                <select name="bersedia_dosen" class="w-full py-2.5 px-3 bg-slate-50/80 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all cursor-pointer">
                    <option value="">Dosen Tamu (Semua)</option>
                    <option value="Bersedia" {{ $statusDosen == 'Bersedia' ? 'selected' : '' }}>Bersedia</option>
                    <option value="Tidak bersedia" {{ $statusDosen == 'Tidak bersedia' ? 'selected' : '' }}>Tidak Bersedia</option>
                </select>
            </div>
            @endif

            <!-- Actions -->
            <div class="flex items-center space-x-2">
                <button type="submit" class="w-full py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl font-bold text-xs sm:text-sm shadow-xs transition-all flex items-center justify-center space-x-1.5 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    <span>Filter</span>
                </button>
                @if($search || $angkatan || $statusDosen || $bidang)
                <a href="{{ route('alumni.index') }}" class="py-2.5 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl font-bold text-xs transition-all flex items-center justify-center" title="Reset Filter">
                    Reset
                </a>
                @endif
            </div>

        </form>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white rounded-3xl shadow-xs border border-slate-200/90 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div class="flex items-center space-x-2.5">
                <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                <h3 class="font-heading font-bold text-slate-900 text-base">Direktori Alumni MNI IPB</h3>
            </div>
            <span class="text-xs text-slate-500 font-medium">
                Menampilkan <strong class="text-slate-800">{{ $alumnis->firstItem() ?? 0 }} - {{ $alumnis->lastItem() ?? 0 }}</strong> dari <strong class="text-slate-800">{{ $alumnis->total() }}</strong> alumni
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-4 px-6">Nama Alumni</th>
                        <th class="py-4 px-4">Angkatan</th>
                        <th class="py-4 px-4">Email</th>
                        <th class="py-4 px-4">Karir & Tempat Kerja</th>
                        <th class="py-4 px-4">Bidang Usaha</th>
                        <th class="py-4 px-4">Domisili</th>
                        @if(Auth::check() && Auth::user()->isAdmin())
                            <th class="py-4 px-4">Dosen Tamu</th>
                            <th class="py-4 px-4">No. WhatsApp</th>
                        @endif
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($alumnis as $alumni)
                    <tr class="hover:bg-slate-50/90 transition-colors group">
                        
                        <!-- Alumni Info -->
                        <td class="py-4 px-6">
                            <span class="font-bold text-slate-900 block group-hover:text-emerald-700 transition-colors">
                                {{ $alumni->nama_lengkap }}
                            </span>
                            <span class="text-xs text-slate-400 block">
                                Panggilan: <span class="text-slate-600 font-medium">{{ $alumni->nama_panggilan ?: '-' }}</span>
                            </span>
                        </td>

                        <!-- Angkatan -->
                        <td class="py-4 px-4">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                {{ $alumni->angkatan ?: '-' }}
                            </span>
                        </td>

                        <!-- Email Alumni -->
                        <td class="py-4 px-4 text-xs">
                            @if($alumni->email_pribadi)
                                <a href="mailto:{{ $alumni->email_pribadi }}" class="text-emerald-700 hover:text-emerald-800 font-semibold hover:underline inline-flex items-center gap-1.5" title="Kirim email: {{ $alumni->email_pribadi }}">
                                    <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    <span class="truncate max-w-[160px]">{{ $alumni->email_pribadi }}</span>
                                </a>
                            @elseif($alumni->email_perusahaan && $alumni->email_perusahaan != '-')
                                <a href="mailto:{{ $alumni->email_perusahaan }}" class="text-indigo-700 hover:text-indigo-800 font-semibold hover:underline inline-flex items-center gap-1.5" title="Kirim email kantor: {{ $alumni->email_perusahaan }}">
                                    <svg class="w-3.5 h-3.5 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    <span class="truncate max-w-[160px]">{{ $alumni->email_perusahaan }}</span>
                                </a>
                            @else
                                <span class="text-slate-400 italic">-</span>
                            @endif
                        </td>

                        <!-- Karir & Perusahaan -->
                        <td class="py-4 px-4">
                            <div class="min-w-0">
                                <span class="font-bold text-slate-800 text-xs block truncate max-w-[180px]" title="{{ $alumni->nama_perusahaan }}">
                                    {{ $alumni->nama_perusahaan ?: '-' }}
                                </span>
                                <span class="text-xs text-slate-500 font-medium block truncate max-w-[180px]" title="{{ $alumni->jabatan_posisi }}">
                                    {{ $alumni->jabatan_posisi ?: '-' }}
                                </span>
                            </div>
                        </td>

                        <!-- Bidang Usaha -->
                        <td class="py-4 px-4">
                            @if($alumni->bidang_industri && $alumni->bidang_industri != '-')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200/80">
                                    {{ $alumni->bidang_industri }}
                                </span>
                            @else
                                <span class="text-slate-400 italic text-xs">-</span>
                            @endif
                        </td>

                        <!-- Domisili -->
                        <td class="py-4 px-4 text-xs text-slate-600 font-medium whitespace-nowrap">
                            @if($alumni->kota_domisili && $alumni->kota_domisili != '-')
                                <span class="inline-flex items-center gap-1">
                                    <svg class="w-3 h-3 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span>{{ $alumni->kota_domisili }}</span>
                                </span>
                            @else
                                <span class="text-slate-400 italic">-</span>
                            @endif
                        </td>

                        @if(Auth::check() && Auth::user()->isAdmin())
                            <!-- Dosen Tamu Badge (Admin) -->
                            <td class="py-4 px-4 whitespace-nowrap">
                                @if(trim($alumni->bersedia_dosen_tamu) == 'Bersedia')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        <svg class="w-3.5 h-3.5 mr-1 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                        Bersedia
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-600 border border-rose-200">
                                        <svg class="w-3.5 h-3.5 mr-1 text-rose-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                        Tidak Bersedia
                                    </span>
                                @endif
                            </td>

                            <!-- Nomor WhatsApp (Khusus Admin saja) -->
                            <td class="py-4 px-4 text-xs whitespace-nowrap">
                                @if($alumni->no_hp && $alumni->no_hp != '-')
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $alumni->no_hp) }}" target="_blank" class="font-mono font-bold text-slate-800 hover:text-emerald-700 hover:underline inline-flex items-center gap-1">
                                        <span>{{ $alumni->no_hp }}</span>
                                    </a>
                                @else
                                    <span class="text-slate-400 italic">-</span>
                                @endif
                            </td>
                        @endif

                        <!-- Actions -->
                        <td class="py-4 px-6 text-right whitespace-nowrap space-x-1.5">
                            <button onclick="showDetail({{ $alumni->id }})" 
                                    class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition-all cursor-pointer">
                                Detail
                            </button>

                            @if(Auth::check() && Auth::user()->isMahasiswa())
                                @php
                                    $adminWa = "6281575006649";
                                    $waUrl = "https://wa.me/{$adminWa}";
                                @endphp
                                <a href="{{ $waUrl }}" target="_blank"
                                   class="inline-flex items-center px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold shadow-xs shadow-emerald-600/30 transition-all" title="Minta Kontak WA ke Admin">
                                    <span class="mr-1">💬</span>
                                    <span>Minta WA</span>
                                </a>
                            @endif

                            @if(Auth::check() && Auth::user()->isAdmin())
                                <form action="{{ route('alumni.destroy', $alumni->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Admin: Yakin ingin menghapus data alumni ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1.5 text-rose-600 hover:bg-rose-50 rounded-xl text-xs font-bold transition-colors cursor-pointer">
                                        Hapus
                                    </button>
                                </form>
                            @endif
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ Auth::check() && Auth::user()->isAdmin() ? 9 : 7 }}" class="py-16 text-center text-slate-400">
                            <div class="w-16 h-16 rounded-3xl bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-3">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                            </div>
                            <p class="font-bold text-slate-700 text-base">Tidak ada data alumni ditemukan</p>
                            <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">Coba ubah kata kunci pencarian atau sesuaikan filter angkatan dan bidang usaha.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        <div class="px-6 py-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3 bg-slate-50/50">
            <span class="text-xs text-slate-500 font-medium">
                Menampilkan {{ $alumnis->firstItem() ?? 0 }} - {{ $alumnis->lastItem() ?? 0 }} dari {{ $alumnis->total() }} alumni
            </span>
            <div>
                {{ $alumnis->links('pagination::simple-tailwind') }}
            </div>
        </div>
    </div>

</div>

<!-- Modal Detail Alumni -->
<div id="detailModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-3xl max-w-2xl w-full shadow-2xl overflow-hidden border border-slate-100 transform transition-all">
        <div class="bg-slate-950 text-white px-6 py-4.5 flex items-center justify-between border-b border-slate-800">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-xs font-black">
                    MNI
                </div>
                <div>
                    <h3 class="font-heading font-bold text-base text-white" id="modalTitle">Detail Data Alumni</h3>
                    <span class="text-[11px] text-slate-400 block">Informasi Terdata di Database Prodi</span>
                </div>
            </div>
            <button onclick="document.getElementById('detailModal').classList.add('hidden')" class="text-slate-400 hover:text-white p-1 rounded-xl transition-colors cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto" id="modalBody">
            <p class="text-slate-400 text-sm text-center py-6">Memuat data...</p>
        </div>
        <div class="bg-slate-50 px-6 py-3.5 border-t border-slate-100 text-right">
            <button onclick="document.getElementById('detailModal').classList.add('hidden')" class="px-5 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl font-bold text-xs shadow-xs transition-all cursor-pointer">
                Tutup
            </button>
        </div>
    </div>
</div>

@if(Auth::check() && Auth::user()->isAdmin())
<!-- Modal Import CSV/Spreadsheet -->
<div id="importModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full shadow-2xl overflow-hidden border border-slate-100 transform transition-all">
        <div class="bg-slate-950 text-white px-6 py-4.5 flex items-center justify-between border-b border-slate-800">
            <h3 class="font-heading font-bold text-base flex items-center space-x-2 text-white">
                <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                <span>Import Data Spreadsheet (CSV)</span>
            </h3>
            <button onclick="document.getElementById('importModal').classList.add('hidden')" class="text-slate-400 hover:text-white p-1 rounded-xl transition-colors cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        
        <form action="{{ route('alumni.import') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf
            <div>
                <p class="text-xs text-slate-600 mb-3 leading-relaxed">
                    Unggah file rekap respon Google Form yang telah diunduh dalam format <strong>CSV (.csv)</strong> dari Google Sheets. Data akan otomatis disinkronkan ke database.
                </p>
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-700">Pilih File CSV:</label>
                    <input type="file" name="csv_file" accept=".csv,text/csv" required 
                           class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer border border-slate-200 rounded-2xl p-2 bg-slate-50">
                </div>
            </div>

            <div class="bg-indigo-50/60 p-3.5 rounded-2xl border border-indigo-100 text-[11px] text-indigo-900 space-y-1">
                <span class="font-bold block">💡 Tips Pengunduhan:</span>
                <p>Buka Spreadsheet data alumni &gt; File &gt; Download &gt; Comma Separated Values (.csv)</p>
            </div>

            <div class="flex items-center justify-end space-x-2 pt-2">
                <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')"
                        class="px-4 py-2.5 text-slate-600 hover:bg-slate-100 rounded-xl font-bold text-xs transition-colors cursor-pointer">
                    Batal
                </button>
                <button type="submit" 
                        class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl font-bold text-xs shadow-md shadow-indigo-600/30 transition-all flex items-center space-x-1.5 cursor-pointer">
                    <span>Mulai Sinkronisasi</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
const isAdmin = {{ Auth::check() && Auth::user()->isAdmin() ? 'true' : 'false' }};
const currentUserName = "{{ Auth::check() ? Auth::user()->name : '' }}";

function showDetail(id) {
    const modal = document.getElementById('detailModal');
    const modalBody = document.getElementById('modalBody');
    const modalTitle = document.getElementById('modalTitle');

    modal.classList.remove('hidden');
    modalBody.innerHTML = '<p class="text-slate-400 text-sm py-8 text-center">Memuat data alumni...</p>';

    fetch(`/alumni/${id}`)
        .then(response => response.json())
        .then(data => {
            modalTitle.innerText = data.nama_lengkap;
            
            const adminWaNum = "6281575006649";

            const dosenTamuBadge = (data.bersedia_dosen_tamu || '').trim() === 'Bersedia' 
                ? '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">Bersedia</span>' 
                : '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-700">Tidak Bersedia</span>';

            const emailPribadiDisplay = data.email_pribadi 
                ? `<a href="mailto:${data.email_pribadi}" class="font-semibold text-emerald-700 hover:underline inline-flex items-center gap-1"><svg class="w-3.5 h-3.5 inline shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>${data.email_pribadi}</a>` 
                : '<span class="font-semibold text-slate-400">-</span>';

            const emailPerusahaanDisplay = (data.email_perusahaan && data.email_perusahaan !== '-')
                ? `<a href="mailto:${data.email_perusahaan}" class="font-semibold text-indigo-700 hover:underline inline-flex items-center gap-1"><svg class="w-3.5 h-3.5 inline shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>${data.email_perusahaan}</a>` 
                : '<span class="font-semibold text-slate-400">-</span>';

            modalBody.innerHTML = `
                <div class="space-y-4 text-slate-800 text-sm">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 bg-slate-50 p-4 rounded-2xl border border-slate-200/70">
                        <div><span class="text-[11px] text-slate-400 block font-semibold uppercase tracking-wider">Nama Panggilan</span><span class="font-bold text-slate-800">${data.nama_panggilan || '-'}</span></div>
                        <div><span class="text-[11px] text-slate-400 block font-semibold uppercase tracking-wider">Angkatan</span><span class="font-bold text-slate-800">Angkatan ${data.angkatan || '-'}</span></div>
                        <div><span class="text-[11px] text-slate-400 block font-semibold uppercase tracking-wider">Email Pribadi</span>${emailPribadiDisplay}</div>
                        <div><span class="text-[11px] text-slate-400 block font-semibold uppercase tracking-wider">Email Kantor</span>${emailPerusahaanDisplay}</div>
                        ${isAdmin ? `<div><span class="text-[11px] text-slate-400 block font-semibold uppercase tracking-wider">No. HP / WA Alumni</span><span class="font-bold font-mono text-emerald-800">${data.no_hp || '-'}</span></div>` : ''}
                        <div><span class="text-[11px] text-slate-400 block font-semibold uppercase tracking-wider">Media Sosial</span><span class="font-bold text-slate-800">${data.akun_sosmed || '-'}</span></div>
                        ${isAdmin ? `<div><span class="text-[11px] text-slate-400 block font-semibold uppercase tracking-wider">Status Dosen Tamu</span>${dosenTamuBadge}</div>` : ''}
                    </div>

                    <div class="bg-emerald-50/60 p-4 rounded-2xl border border-emerald-100 space-y-2.5">
                        <h4 class="font-heading font-extrabold text-emerald-900 text-xs uppercase tracking-wider flex items-center space-x-1.5">
                            <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <span>Informasi Karir & Domisili</span>
                        </h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                            <div><span class="text-slate-500 block font-medium">Nama Perusahaan</span><span class="font-extrabold text-slate-900 text-sm">${data.nama_perusahaan || '-'}</span></div>
                            <div><span class="text-slate-500 block font-medium">Jabatan / Posisi</span><span class="font-bold text-slate-800">${data.jabatan_posisi || '-'}</span></div>
                            <div><span class="text-slate-500 block font-medium">Bidang / Industri</span><span class="font-bold text-emerald-800">${data.bidang_industri || '-'}</span></div>
                            <div><span class="text-slate-500 block font-medium">Kota Domisili</span><span class="font-bold text-slate-800">${data.kota_domisili || '-'}</span></div>
                        </div>
                    </div>

                    ${!isAdmin ? `
                        <div class="bg-gradient-to-r from-emerald-500/15 via-teal-500/10 to-emerald-500/15 p-4 rounded-2xl border border-emerald-500/25 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <span class="text-xs text-emerald-900 font-extrabold block">Ingin Terhubung dengan Alumni Ini?</span>
                                <span class="text-[11px] text-slate-600 block">Hubungi Admin Prodi MNI IPB untuk meminta kontak WhatsApp resmi.</span>
                            </div>
                            <a href="https://wa.me/${adminWaNum}" target="_blank" 
                               class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-extrabold shadow-md shadow-emerald-600/30 transition-all flex items-center justify-center space-x-1.5 shrink-0">
                                <span>💬 Minta Kontak via WA</span>
                            </a>
                        </div>
                    ` : ''}

                    ${isAdmin && data.saran_prodi ? `
                        <div class="bg-amber-50/80 p-4 rounded-2xl border border-amber-200/60 space-y-1">
                            <span class="text-xs text-amber-900 font-bold block">Masukan / Saran untuk Prodi MNI IPB (Khusus Admin)</span>
                            <p class="text-xs text-slate-700 leading-relaxed italic">"${data.saran_prodi}"</p>
                        </div>
                    ` : ''}
                </div>
            `;
        })
        .catch(err => {
            modalBody.innerHTML = '<p class="text-rose-500 text-sm">Gagal memuat detail data.</p>';
        });
}
</script>
@endpush
