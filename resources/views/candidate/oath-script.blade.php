@extends('layouts.app')

@section('title', 'Naskah Lafal Sumpah ' . $candidate->period->studyProgram->name)

@section('content')
<!-- Page Heading & Actions -->
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <a href="{{ route('candidate.dashboard') }}" class="text-xs font-bold text-[#4e73df] hover:underline flex items-center gap-1 mb-1">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Dasbor
        </a>
        <h1 class="text-2xl font-black text-gray-800 tracking-tight">Naskah Lafal Sumpah Official</h1>
        <p class="text-xs font-semibold text-gray-500">Teks sumpah resmi terpersonalisasi untuk {{ $candidate->full_name }} ({{ $candidate->period->studyProgram->name }})</p>
    </div>

    <button onclick="window.print()" class="px-4 py-2 bg-[#4e73df] hover:bg-[#2e59d9] text-white font-bold text-xs rounded-lg shadow-sm flex items-center gap-2">
        <i class="fa-solid fa-print"></i> Cetak / Simpan PDF
    </button>
</div>

<!-- Main Printable Document Card inside SB Admin 2 Layout -->
<div class="bg-white p-8 sm:p-12 rounded-lg shadow-sm border border-gray-200 max-w-4xl mx-auto text-gray-900">
    
    <!-- Kop Surat Resmi -->
    <div class="border-b-2 border-gray-900 pb-4 mb-6 text-center">
        <h3 class="text-xs uppercase font-black text-gray-600 tracking-widest">FAKULTAS KEDOKTERAN DAN ILMU KESEHATAN</h3>
        <h2 class="text-lg font-black text-gray-900 uppercase mt-0.5">PANITIA PELAKSANA LAFAL SUMPAH PROFESI</h2>
        <p class="text-[11px] font-semibold text-gray-500">Sekretariat {{ $candidate->period->studyProgram->organization }} &bull; Periode {{ $candidate->period->quarter_code }} {{ $candidate->period->event_date->format('Y') }}</p>
    </div>

    <!-- Title -->
    <div class="text-center mb-6">
        <h1 class="text-xl font-black text-gray-900 uppercase tracking-tight">NASKAH LAFAL SUMPAH {{ strtoupper($candidate->period->studyProgram->name) }}</h1>
        <div class="inline-block border-b-2 border-[#4e73df] w-24 my-1"></div>
        <p class="text-xs font-bold text-gray-600">Berdasarkan Agama / Kepercayaan: <strong class="uppercase text-[#4e73df]">{{ $candidate->religion }}</strong></p>
    </div>

    @if($candidate->period->studyProgram->oath_pdf_path)
        {{-- PDF Viewer: dokumen sumpah dari Admin --}}
        <div class="rounded-lg border border-blue-200 overflow-hidden mb-8">
            <div class="bg-blue-50 px-4 py-2.5 flex items-center justify-between border-b border-blue-200">
                <span class="text-[11px] font-bold text-blue-800 flex items-center gap-1.5">
                    <i class="fa-solid fa-file-pdf text-red-500"></i> Dokumen Resmi Lafal Sumpah (PDF)
                </span>
                <a href="{{ asset('storage/' . $candidate->period->studyProgram->oath_pdf_path) }}" target="_blank" download class="px-3 py-1 bg-[#4e73df] hover:bg-[#2e59d9] text-white font-bold text-[10px] rounded flex items-center gap-1 transition-colors">
                    <i class="fa-solid fa-download"></i> Unduh PDF
                </a>
            </div>
            <iframe src="{{ asset('storage/' . $candidate->period->studyProgram->oath_pdf_path) }}" class="w-full border-0" style="height: 600px;" title="Naskah Lafal Sumpah PDF"></iframe>
        </div>
    @else
        {{-- Fallback: teks sumpah hardcoded per agama & prodi --}}

        <!-- Candidate Meta Box -->
        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mb-6 grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
            <div>
                <span class="text-gray-500 text-[10px] font-bold uppercase block">Nama Lengkap</span>
                <span class="font-extrabold text-gray-900">{{ $candidate->full_name }}</span>
            </div>
            <div>
                <span class="text-gray-500 text-[10px] font-bold uppercase block">NIM / ID</span>
                <span class="font-mono font-bold text-[#4e73df]">{{ $candidate->nim }}</span>
            </div>
            <div>
                <span class="text-gray-500 text-[10px] font-bold uppercase block">Program Studi</span>
                <span class="font-bold text-gray-800">{{ $candidate->period->studyProgram->name }} ({{ $candidate->period->studyProgram->degree_title }})</span>
            </div>
        </div>

        <!-- Preface by Religion -->
        @php
            $preface = match($candidate->religion) {
                'Islam' => 'Demi Allah Saya Bersumpah / Ollaahi:',
                'Protestan', 'Katolik' => 'Saya Berjanji Di Hadapan Tuhan Yang Maha Esa:',
                'Hindu' => 'Om Atmahnam / Demi Hyang Widhi Wasa Saya Bersumpah:',
                'Buddha' => 'Namo Buddhaya / Demi Sang Hyang Adi Buddha Saya Bersumpah:',
                'Konghucu' => 'Kehadapan Tian / Shandi Saya Bersumpah:',
                default => 'Demi Tuhan Yang Maha Esa Saya Bersumpah:',
            };
        @endphp

        <div class="mb-6 font-bold text-center text-sm text-gray-800 italic bg-blue-50 py-2.5 px-4 rounded border border-blue-200">
            "{{ $preface }}"
        </div>

        <!-- Text Sumpah Spesifik Prodi & Organisasi Profesi (IDI / IAI / PPNI) -->
        <div class="space-y-4 text-xs sm:text-sm text-gray-800 leading-relaxed text-justify px-2 sm:px-6 font-medium">
            <p><strong>1.</strong> Bahwa saya akan mengabdikan hidup saya guna kepentingan perikemanusiaan dan kesehatan masyarakat.</p>
            
            @if($candidate->period->studyProgram->code === 'KED')
                <p><strong>2.</strong> Bahwa saya akan menjalankan tugas saya dengan cara yang terhormat dan bersusila, sesuai dengan martabat profesi Kedokteran (Ikatan Dokter Indonesia).</p>
                <p><strong>3.</strong> Bahwa kesehatan penderita senantiasa akan saya utamakan dan saya akan merahasiakan segala sesuatu yang saya ketahui karena pekerjaan saya dan karena keilmuan kedokteran saya.</p>
                <p><strong>4.</strong> Bahwa saya akan memelihara dengan sekuat tenaga martabat dan tradisi luhur jabatan Kedokteran Indonesia.</p>
            @elseif($candidate->period->studyProgram->code === 'APT')
                <p><strong>2.</strong> Bahwa saya akan menjalankan tugas kefarmasian dengan cara yang terhormat dan penuh tanggung jawab sesuai Kode Etik Apoteker Indonesia (Ikatan Apoteker Indonesia).</p>
                <p><strong>3.</strong> Bahwa saya akan merahasiakan segala rahasia pekerjaan kefarmasian dan resep obat yang dipercayakan kepada saya.</p>
                <p><strong>4.</strong> Bahwa saya tidak akan mempergunakan pengetahuan kefarmasian saya untuk sesuatu yang bertentangan dengan hukum perikemanusiaan.</p>
            @elseif($candidate->period->studyProgram->code === 'NRS')
                <p><strong>2.</strong> Bahwa saya akan melaksanakan tugas keperawatan dengan penuh tanggung jawab, membela hak-hak pasien, serta menghormati harkat dan martabat manusia (Persatuan Perawat Nasional Indonesia).</p>
                <p><strong>3.</strong> Bahwa saya akan memelihara hubungan baik dan kerjasama yang harmonis dengan sejawat perawat dan tenaga kesehatan lainnya.</p>
                <p><strong>4.</strong> Bahwa saya akan senantiasa mempertahankan standar pelayanan keperawatan yang setinggi-tingginya.</p>
            @else
                <p><strong>2.</strong> Bahwa saya akan menjalankan tugas profesi kesehatan dengan cara yang terhormat dan penuh integritas moral.</p>
                <p><strong>3.</strong> Bahwa saya akan senantiasa mengutamakan keselamatan dan kesejahteraan pasien/klien di atas kepentingan pribadi.</p>
            @endif

            <p><strong>5.</strong> Saya ikrarkan sumpah ini dengan sungguh-sungguh dan dengan mempertaruhkan kehormatan diri saya.</p>
        </div>

        <!-- Closing Phrase for Christian / Catholic -->
        @if(in_array($candidate->religion, ['Protestan', 'Katolik']))
            <div class="mt-4 text-center text-xs font-bold italic text-gray-700">
                "Kiranya Tuhan Menolong Saya."
            </div>
        @endif
    @endif

    <!-- Signatures Block -->
    <div class="mt-12 pt-6 border-t border-gray-300 grid grid-cols-2 gap-8 text-center text-xs">
        <div>
            <p class="text-gray-500 mb-14 font-semibold">Yang Mengucapkan Sumpah,</p>
            <p class="font-black text-gray-900 uppercase underline">{{ $candidate->full_name }}</p>
            <p class="text-[11px] text-gray-500 font-mono">NIM: {{ $candidate->nim }}</p>
        </div>
        <div>
            <p class="text-gray-500 mb-14 font-semibold">Ketua Senat / Dekan FKIK,</p>
            <p class="font-black text-gray-900 uppercase underline">Prof. dr. H. Dean FKIK, Sp.S</p>
            <p class="text-[11px] text-gray-500 font-mono">NIP. 19750101 200212 1 001</p>
        </div>
    </div>

</div>
@endsection
