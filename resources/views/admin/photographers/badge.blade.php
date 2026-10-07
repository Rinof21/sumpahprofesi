<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Badge Fotografer Resmi - {{ $photographer->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
        }
    </style>
</head>
<body class="bg-slate-900 text-slate-100 p-8 flex flex-col items-center justify-center min-h-screen">

    <div class="no-print mb-6">
        <button onclick="window.print()" class="px-6 py-2.5 bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-black text-xs rounded-xl shadow flex items-center gap-2">
            <i class="fa-solid fa-print"></i> Cetak / Print ID Badge
        </button>
    </div>

    <!-- Official Badge ID Card Format -->
    <div class="w-80 bg-slate-950 border-4 border-teal-500 rounded-3xl shadow-2xl overflow-hidden text-center text-slate-100">
        
        <!-- Header -->
        <div class="bg-teal-500 text-slate-950 p-4 font-black">
            <div class="text-[9px] tracking-widest uppercase text-slate-950/80">AKREDITASI OPERASIONAL AULA</div>
            <div class="text-sm uppercase tracking-tight">FKIK SUMPAH PROFESI</div>
        </div>

        <!-- Prominent Badge Designation Label (FR-E4) -->
        <div class="bg-amber-500 text-slate-950 py-2 px-3 font-black text-xs uppercase tracking-tight border-y border-amber-400">
            FOTOGRAFER RUANGAN RESMI (MAKS. 3)
        </div>

        <!-- Body -->
        <div class="p-6 space-y-4">
            
            <div class="w-20 h-20 mx-auto rounded-full bg-slate-900 border-2 border-teal-400 flex items-center justify-center text-teal-300 text-3xl font-bold">
                <i class="fa-solid fa-camera"></i>
            </div>

            <div>
                <div class="text-lg font-black text-white leading-tight">{{ $photographer->name }}</div>
                <div class="text-xs text-teal-300 font-bold mt-0.5">{{ $photographer->agency_name }}</div>
            </div>

            <div class="p-3 bg-slate-900 rounded-xl border border-slate-800 text-left text-xs space-y-1">
                <div class="flex justify-between">
                    <span class="text-slate-400">Prodi:</span>
                    <span class="font-bold text-slate-200">{{ $photographer->period->studyProgram->code }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Periode:</span>
                    <span class="font-bold text-slate-200">{{ $photographer->period->quarter_code }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Badge ID:</span>
                    <span class="font-mono font-bold text-teal-400">{{ $photographer->badge_code }}</span>
                </div>
            </div>

            <!-- QR Code -->
            <div class="w-24 h-24 mx-auto bg-white p-2 rounded-xl flex items-center justify-center">
                <i class="fa-solid fa-qrcode text-slate-950 text-5xl"></i>
            </div>

        </div>

        <div class="bg-slate-900 p-3 text-[9px] text-slate-400 font-semibold border-t border-slate-800">
            Wajib Dikenakan Di Leher Selama Acara Berlangsung
        </div>

    </div>

</body>
</html>
