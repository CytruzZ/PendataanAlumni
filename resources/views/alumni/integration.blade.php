@extends('layouts.app')

@section('title', 'Panduan Live Sync Google Form - Alumni MNI IPB')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

    <!-- Header Section -->
    <div class="bg-gradient-to-r from-slate-900 to-indigo-950 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden">
        <div class="inline-flex items-center space-x-2 bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wider mb-3">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
            <span>Real-time Integration Setup</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Panduan Live Sync Google Form / Sheet Privat</h1>
        <p class="text-slate-300 text-sm mt-2 max-w-2xl leading-relaxed">
            Meskipun Google Form / Spreadsheet Anda bersifat **privat**, setiap alumni yang mengisi form akan **otomatis langsung ter-update di Web Alumni secara real-time** menggunakan Google Apps Script Webhook.
        </p>
    </div>

    <!-- Webhook Secret & Endpoint Card -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        
        <!-- Endpoint Card -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 space-y-2">
            <span class="text-xs text-slate-500 font-bold uppercase tracking-wider block">Target Webhook API URL</span>
            <div class="flex items-center justify-between bg-slate-50 p-3 rounded-xl border border-slate-200">
                <code class="text-xs font-mono font-semibold text-emerald-700 truncate" id="urlText">{{ $webhookUrl }}</code>
                <button onclick="copyToClipboard('urlText')" class="ml-2 px-3 py-1 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-lg text-xs font-bold transition-all">
                    Copy
                </button>
            </div>
            <p class="text-[11px] text-slate-400">Endpoint ini siap menerima request POST secara aman dari Google Apps Script.</p>
        </div>

        <!-- Secret Key Card -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 space-y-2">
            <span class="text-xs text-slate-500 font-bold uppercase tracking-wider block">Webhook Secret Token</span>
            <div class="flex items-center justify-between bg-slate-50 p-3 rounded-xl border border-slate-200">
                <code class="text-xs font-mono font-semibold text-indigo-700" id="tokenText">{{ $secretToken }}</code>
                <button onclick="copyToClipboard('tokenText')" class="ml-2 px-3 py-1 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-lg text-xs font-bold transition-all">
                    Copy
                </button>
            </div>
            <p class="text-[11px] text-slate-400">Token rahasia untuk otentikasi keamanan pengiriman data.</p>
        </div>

    </div>

    <!-- Step by Step Setup Guide -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200 space-y-6">
        <h2 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center space-x-2">
            <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center font-black text-xs">1</span>
            <span>Langkah-Langkah Memasang Script di Google Sheets Privat Anda</span>
        </h2>

        <ol class="space-y-5 text-sm text-slate-700">
            <li class="flex items-start space-x-3">
                <span class="w-6 h-6 rounded-full bg-slate-100 text-slate-600 font-bold text-xs flex items-center justify-center flex-shrink-0 mt-0.5">1</span>
                <div>
                    <span class="font-semibold text-slate-900 block">Buka Google Sheets privat Anda</span>
                    <p class="text-xs text-slate-500 mt-0.5">Buka file spreadsheet yang terhubung dengan Google Form alumni MNI IPB Anda.</p>
                </div>
            </li>

            <li class="flex items-start space-x-3">
                <span class="w-6 h-6 rounded-full bg-slate-100 text-slate-600 font-bold text-xs flex items-center justify-center flex-shrink-0 mt-0.5">2</span>
                <div>
                    <span class="font-semibold text-slate-900 block">Buka Apps Script</span>
                    <p class="text-xs text-slate-500 mt-0.5">Klik menu <strong class="text-slate-800">Ekstensi (Extensions)</strong> &gt; <strong class="text-slate-800">Apps Script</strong> pada bagian atas menu Google Sheets.</p>
                </div>
            </li>

            <li class="flex items-start space-x-3">
                <span class="w-6 h-6 rounded-full bg-slate-100 text-slate-600 font-bold text-xs flex items-center justify-center flex-shrink-0 mt-0.5">3</span>
                <div class="w-full">
                    <span class="font-semibold text-slate-900 block">Salin & Paste Kode Script Berikut</span>
                    <p class="text-xs text-slate-500 mt-0.5 mb-3">Hapus semua isi kode `Code.gs` bawaan, lalu paste kode JavaScript berikut:</p>
                    
                    <!-- Code Snippet Box -->
                    <div class="relative bg-slate-900 text-slate-200 rounded-2xl p-4 text-xs font-mono shadow-inner overflow-x-auto border border-slate-800">
                        <button onclick="copyToClipboard('scriptSnippet')" class="absolute top-3 right-3 px-3 py-1 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg font-sans font-bold text-[11px] shadow-sm transition-all">
                            Salin Kode Script
                        </button>
                        <pre id="scriptSnippet"><code>function onFormSubmit(e) {
  var url = "{{ $webhookUrl }}";
  var secretToken = "{{ $secretToken }}";
  var payload = { token: secretToken };
  
  if (e && e.namedValues) {
    // Dipicu dari Spreadsheet Form Submit Trigger
    for (var key in e.namedValues) {
      payload[key] = e.namedValues[key][0];
    }
  } else if (e && e.response) {
    // Dipicu dari Google Form Submit Trigger
    var itemResponses = e.response.getItemResponses();
    payload['timestamp'] = new Date().toLocaleString();
    for (var i = 0; i < itemResponses.length; i++) {
      var itemResponse = itemResponses[i];
      payload[itemResponse.getItem().getTitle()] = itemResponse.getResponse();
    }
  } else if (e && e.range) {
    // Fallback baca baris dari range Spreadsheet
    var sheet = e.range.getSheet();
    var headers = sheet.getRange(1, 1, 1, sheet.getLastColumn()).getValues()[0];
    var rowData = e.range.getValues()[0];
    for (var j = 0; j < headers.length; j++) {
      payload[headers[j]] = rowData[j];
    }
  }
  
  sendWebhook(url, payload);
}

// Fungsi untuk menyinkronkan SELURUH data yang ada di Spreadsheet sekaligus ke Web secara instan
function syncAllRowsToWeb() {
  var url = "{{ $webhookUrl }}";
  var secretToken = "{{ $secretToken }}";
  var sheet = SpreadsheetApp.getActiveSpreadsheet().getActiveSheet();
  var data = sheet.getDataRange().getValues();
  if (data.length <= 1) {
    Logger.log("Spreadsheet kosong atau hanya berisi header.");
    return;
  }
  
  var headers = data[0];
  var rows = [];
  
  for (var i = 1; i < data.length; i++) {
    var row = data[i];
    if (!row[0] && !row[1] && !row[2]) continue;
    
    var rowObj = {};
    for (var j = 0; j < headers.length; j++) {
      if (headers[j]) {
        rowObj[headers[j]] = row[j];
      }
    }
    rows.push(rowObj);
  }
  
  var bulkPayload = {
    token: secretToken,
    rows: rows
  };
  
  sendWebhook(url, bulkPayload);
  Logger.log("Selesai! Berhasil mengirim " + rows.length + " data alumni sekaligus dalam 1 request.");
}

function sendWebhook(url, payload) {
  var options = {
    method: "post",
    contentType: "application/json",
    payload: JSON.stringify(payload),
    muteHttpExceptions: true
  };
  try {
    var response = UrlFetchApp.fetch(url, options);
    Logger.log("Webhook Response: " + response.getResponseCode() + " - " + response.getContentText());
  } catch (err) {
    Logger.log("Webhook Error: " + err.toString());
  }
}</code></pre>
                    </div>
                </div>
            </li>

            <li class="flex items-start space-x-3">
                <span class="w-6 h-6 rounded-full bg-slate-100 text-slate-600 font-bold text-xs flex items-center justify-center flex-shrink-0 mt-0.5">4</span>
                <div>
                    <span class="font-semibold text-slate-900 block">Buat Trigger Pemicu (Trigger "On form submit")</span>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Di sebelah kiri Apps Script, klik ikon jam <strong class="text-slate-800">Triggers (Pemicu)</strong> &gt; Klik <strong class="text-slate-800">Add Trigger (Tambahkan Pemicu)</strong>.<br>
                        - Pilih fungsi yang dijalankan: <code class="bg-slate-100 text-slate-800 px-1.5 py-0.5 rounded">onFormSubmit</code><br>
                        - Pilih sumber acara: <code class="bg-slate-100 text-slate-800 px-1.5 py-0.5 rounded">From spreadsheet</code> / <code class="bg-slate-100 text-slate-800 px-1.5 py-0.5 rounded">From form</code><br>
                        - Jenis acara: <code class="bg-slate-100 text-slate-800 px-1.5 py-0.5 rounded">On form submit</code> (Saat formulir dikirim)<br>
                        - Klik <strong>Simpan (Save)</strong> dan izinkan otorisasi akun Google Anda.
                    </p>
                </div>
            </li>
        </ol>
    </div>

    <!-- Live Test Webhook Widget Card -->
    <div class="bg-gradient-to-tr from-emerald-900 to-teal-950 rounded-3xl p-6 sm:p-8 text-white shadow-xl space-y-4 border border-emerald-800">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-extrabold text-xl">Uji Coba Live Sync Webhook</h3>
                <p class="text-emerald-200 text-xs mt-1">Simulasikan pengiriman data sampel alumni MNI IPB ke server web secara instan untuk menguji koneksi.</p>
            </div>
            <button id="testWebhookBtn" onclick="runWebhookTest()" 
                    class="px-5 py-3 bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-black rounded-2xl text-xs uppercase tracking-wider shadow-lg shadow-emerald-500/30 transition-all flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Kirim Test Payload</span>
            </button>
        </div>

        <div id="testResultBox" class="hidden bg-slate-950/80 rounded-2xl p-4 text-xs font-mono border border-emerald-500/30 text-emerald-300">
            <span class="text-slate-400 block mb-1">// Status Respon Server Webhook:</span>
            <pre id="testResultContent">Menunggu request...</pre>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function copyToClipboard(elementId) {
    const text = document.getElementById(elementId).innerText;
    navigator.clipboard.writeText(text).then(() => {
        alert('Teks berhasil disalin ke clipboard!');
    });
}

function runWebhookTest() {
    const btn = document.getElementById('testWebhookBtn');
    const box = document.getElementById('testResultBox');
    const content = document.getElementById('testResultContent');

    btn.disabled = true;
    btn.innerHTML = '<span>Mengirim...</span>';
    box.classList.remove('hidden');
    content.innerText = 'Mengirim HTTP POST request ke {{ $webhookUrl }}...';

    const testPayload = {
        token: '{{ $secretToken }}',
        timestamp: new Date().toLocaleString(),
        'Nama Lengkap': 'Testing Alumni MNI IPB (Live Test)',
        'Nama Panggilan': 'Tester',
        'Angkatan masuk kuliah (misal 57)': '58',
        'Nomor HP / WA (format : 6281-xxxxxx)': '08123456789',
        'Email pribadi': 'testing.alumni.mni@ipb.ac.id',
        'Kota domisili saat ini': 'Bogor',
        'Bidang usaha atau industri saat ini': 'Teknologi Informasi & Software',
        'Nama Perusahaan': 'IPB Innovation Center',
        'Jabatan atau posisi saat ini': 'Lead Software Engineer',
        'Apakah bersedia jika suatu saat diundang menjadi dosen tamu atau mengisi sharing session?': 'Bersedia',
        'Masukan atau saran untuk Program Studi MNI IPB University': 'Sistem Live Sync Webhook berfungsi dengan sangat baik!'
    };

    fetch('{{ $webhookUrl }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(testPayload)
    })
    .then(res => res.json())
    .then(data => {
        content.innerText = JSON.stringify(data, null, 2);
        btn.disabled = false;
        btn.innerHTML = '<span>Kirim Test Payload</span>';
    })
    .catch(err => {
        content.innerText = 'Error: ' + err.message;
        btn.disabled = false;
        btn.innerHTML = '<span>Kirim Test Payload</span>';
    });
}
</script>
@endpush
