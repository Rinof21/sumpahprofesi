@extends('layouts.app')

@section('title', 'Naskah Lafal Sumpah - ' . $period->studyProgram->name)

@section('content')
<div class="print:m-0 print:p-0">

    <!-- Header Actions (Hidden in Print) -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 print:hidden">
        <div>
            <a href="{{ route('token-access.portal') }}" class="text-xs font-bold text-[#4e73df] hover:underline flex items-center gap-1 mb-1">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Pendaftaran Peserta
            </a>
            <h1 class="text-2xl font-black text-gray-800 tracking-tight">Naskah Lafal Sumpah Official</h1>
            <p class="text-xs font-semibold text-gray-500">Teks sakral lafal sumpah profesi untuk {{ $period->studyProgram->name }} ({{ $period->name }}).</p>
        </div>

        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-lg shadow-sm flex items-center gap-2 transition-colors">
                <i class="fa-solid fa-print"></i> Cetak Dokumen
            </button>
            <a href="{{ route('token-access.portal') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs rounded-lg transition-colors flex items-center gap-1.5">
                <i class="fa-solid fa-id-card text-xs"></i> Pendaftaran
            </a>
        </div>
    </div>

    <!-- Main Printable Document Card -->
    <div class="bg-white p-6 sm:p-10 rounded-xl shadow-sm border border-gray-200 max-w-4xl mx-auto text-gray-900 print:shadow-none print:border-none print:p-0">
        
        <!-- Kop Surat Resmi -->
        <div class="border-b-2 border-gray-900 pb-4 mb-6 text-center">
            <h3 class="text-xs uppercase font-black text-gray-600 tracking-widest">KEMENTERIAN PENDIDIKAN TINGGI, SAINS, DAN TEKNOLOGI</h3>
            <h2 class="text-lg font-black text-gray-900 uppercase mt-0.5">FAKULTAS KEDOKTERAN UNIVERSITAS TANJUNGPURA</h2>
            <p class="text-[11px] font-semibold text-gray-600">Panitia Pelaksana Angkat Sumpah Profesi {{ $period->studyProgram->name }}</p>
            <p class="text-[10px] text-gray-500">Sekretariat: Jl. Prof. Dr. H. Hadari Nawawi, Pontianak &bull; Periode {{ $period->quarter_code }} ({{ $period->event_date->format('Y') }})</p>
        </div>

        <!-- Title -->
        <div class="text-center mb-6">
            <h1 class="text-xl font-black text-gray-900 uppercase tracking-tight">NASKAH LAFAL SUMPAH PROFESI {{ strtoupper($period->studyProgram->name) }}</h1>
            <div class="inline-block border-b-2 border-[#4e73df] w-24 my-1"></div>
            <p class="text-xs font-bold text-gray-600">Gelar Kelulusan: <span class="uppercase text-[#4e73df] font-black">{{ $period->studyProgram->degree_title }}</span></p>
        </div>

        @if($period->studyProgram->oath_pdf_path)
            {{-- PDF Viewer Resmi dari Admin --}}
            <div class="rounded-lg border border-blue-200 overflow-hidden mb-8 print:hidden">
                <div class="bg-blue-50 px-4 py-2.5 flex items-center justify-between border-b border-blue-200">
                    <span class="text-[11px] font-bold text-blue-800 flex items-center gap-1.5">
                        <i class="fa-solid fa-file-pdf text-red-500"></i> Dokumen Resmi Lafal Sumpah (PDF)
                    </span>
                    <a href="{{ asset('storage/' . $period->studyProgram->oath_pdf_path) }}" target="_blank" download class="px-3 py-1 bg-[#4e73df] hover:bg-[#2e59d9] text-white font-bold text-[10px] rounded flex items-center gap-1 transition-colors">
                        <i class="fa-solid fa-download"></i> Unduh File PDF
                    </a>
                </div>
                <iframe src="{{ asset('storage/' . $period->studyProgram->oath_pdf_path) }}" class="w-full border-0" style="height: 600px;" title="Naskah Lafal Sumpah PDF"></iframe>
            </div>
        @else
            <div class="p-4 bg-blue-50/70 border border-blue-200 rounded-lg text-xs font-semibold text-blue-900 mb-6 flex items-center gap-2 print:hidden">
                <i class="fa-solid fa-info-circle text-[#4e73df] text-base"></i>
                <span>Menampilkan teks lafal sumpah standar program studi {{ $period->studyProgram->name }}. Dokumen PDF resmi dapat diunduh jika telah diunggah oleh panitia.</span>
            </div>
        @endif

        <!-- Teks Lafal Sumpah Versi Teks Terstruktur -->
        <div class="space-y-6">
            
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 text-xs text-gray-700">
                <div class="font-bold text-gray-900 uppercase tracking-wide mb-1 text-[11px]">Panduan Pembacaan Lafal Sumpah:</div>
                <p class="leading-relaxed">
                    Pembacaan sumpah dipimpin oleh Dekan/Pimpinan Fakultas bersama para Rohaniwan Agama. Calon wisudawan membacakan lafal sumpah sesuai dengan agama dan keyakinan masing-masing secara khidmat dan serempak.
                </p>
            </div>

            <!-- Kalimat Pembuka per Agama -->
            <div>
                <h4 class="text-xs font-black uppercase text-gray-700 tracking-wider mb-2">1. Kalimat Sumpah Pembuka (Berdasarkan Agama):</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                    <div class="p-2.5 bg-blue-50/50 rounded border border-blue-100">
                        <span class="font-black text-[#4e73df] uppercase text-[10px] block">Agama Islam:</span>
                        <p class="font-semibold italic text-gray-800 mt-0.5">"Demi Allah Saya Bersumpah / Wallahi..."</p>
                    </div>
                    <div class="p-2.5 bg-purple-50/50 rounded border border-purple-100">
                        <span class="font-black text-purple-700 uppercase text-[10px] block">Agama Kristen Protestan & Katolik:</span>
                        <p class="font-semibold italic text-gray-800 mt-0.5">"Saya Berjanji Di Hadapan Tuhan Yang Maha Esa..."</p>
                    </div>
                    <div class="p-2.5 bg-amber-50/50 rounded border border-amber-100">
                        <span class="font-black text-amber-700 uppercase text-[10px] block">Agama Hindu:</span>
                        <p class="font-semibold italic text-gray-800 mt-0.5">"Om Atmahnam / Demi Hyang Widhi Wasa Saya Bersumpah..."</p>
                    </div>
                    <div class="p-2.5 bg-emerald-50/50 rounded border border-emerald-100">
                        <span class="font-black text-emerald-700 uppercase text-[10px] block">Agama Buddha & Konghucu:</span>
                        <p class="font-semibold italic text-gray-800 mt-0.5">"Namo Buddhaya / Kehadapan Tian Saya Bersumpah..."</p>
                    </div>
                </div>
            </div>

            <!-- Butir-Butir Sumpah Resmi -->
            <div class="pt-2">
                <h4 class="text-xs font-black uppercase text-gray-700 tracking-wider mb-3">2. Butir-Butir Pernyataan Sumpah Profesi {{ $period->studyProgram->name }}:</h4>
                <div class="p-5 bg-white rounded-lg border-2 border-gray-200 text-xs sm:text-sm text-gray-800 leading-relaxed space-y-3 font-medium">
                    <p><strong>1.</strong> Bahwa saya akan mengabdikan hidup saya guna kepentingan kemanusiaan dan memelihara derajat kesehatan masyarakat setinggi-tingginya.</p>
                    
                    @if($period->studyProgram->code === 'KED')
                        <p><strong>2.</strong> Bahwa saya akan menjalankan tugas profesi kedokteran dengan cara yang terhormat dan berkesusilaan, sesuai dengan martabat keluhuran profesi Kedokteran Indonesia.</p>
                        <p><strong>3.</strong> Bahwa kesehatan penderita senantiasa akan saya jadikan pertimbangan utama dan saya akan merahasiakan segala sesuatu yang saya ketahui karena pekerjaan dan keilmuan saya.</p>
                        <p><strong>4.</strong> Bahwa saya akan memperlakukan teman sejawat saya sebagaimana saudara kandung dan memelihara dengan sekuat tenaga martabat dan tradisi luhur profesi dokter.</p>
                        <p><strong>5.</strong> Bahwa saya ikrarkan sumpah ini dengan sungguh-sungguh dan dengan penuh keinsyafan atas pertanggungjawaban saya kepada Tuhan Yang Maha Esa.</p>
                    @elseif($period->studyProgram->code === 'APT')
                        <p><strong>2.</strong> Bahwa saya akan menjalankan tugas kefarmasian dengan penuh kejujuran dan tanggung jawab moral sesuai Kode Etik Apoteker Indonesia.</p>
                        <p><strong>3.</strong> Bahwa saya akan merahasiakan segala rahasia kefarmasian dan resep yang dipercayakan kepada saya.</p>
                        <p><strong>4.</strong> Bahwa saya tidak akan mempergunakan pengetahuan kefarmasian saya untuk sesuatu yang bertentangan dengan hukum dan perikemanusiaan.</p>
                        <p><strong>5.</strong> Bahwa saya ikrarkan janji ini dengan penuh kesadaran dan kebebasan jiwa tanpa paksaan.</p>
                    @elseif($period->studyProgram->code === 'NRS')
                        <p><strong>2.</strong> Bahwa saya akan membaktikan diri dalam pelayanan keperawatan tanpa membeda-bedakan suku, agama, ras, dan kedudukan sosial pasien.</p>
                        <p><strong>3.</strong> Bahwa saya akan memegang teguh rahasia pasien yang saya ketahui dan melindungi hak-hak serta harkat martabat manusia.</p>
                        <p><strong>4.</strong> Bahwa saya akan senantiasa memelihara hubungan baik dan kerjasama yang harmonis dengan rekan sejawat perawat serta tenaga kesehatan lainnya.</p>
                        <p><strong>5.</strong> Bahwa saya ikrarkan sumpah ini secara tulus dan bertanggung jawab kepada Tuhan Yang Maha Esa.</p>
                    @else
                        <p><strong>2.</strong> Bahwa saya akan menjalankan tugas profesi kesehatan dengan cara yang terhormat dan bersusila, sesuai dengan martabat profesi {{ $period->studyProgram->name }}.</p>
                        <p><strong>3.</strong> Bahwa kesehatan dan keselamatan pasien akan selalu menjadi pertimbangan utama saya.</p>
                        <p><strong>4.</strong> Bahwa saya akan merahasiakan segala sesuatu yang saya ketahui karena pekerjaan dan keilmuan saya sebagai {{ $period->studyProgram->degree_title }}.</p>
                        <p><strong>5.</strong> Bahwa saya ikrarkan sumpah ini dengan sungguh-sungguh dan penuh rasa tanggung jawab.</p>
                    @endif
                </div>
            </div>

        </div>

        <!-- Printable Footer -->
        <div class="hidden print:block mt-12 pt-6 border-t-2 border-gray-300 text-xs">
            <div class="flex justify-between items-end">
                <div>
                    <p class="font-bold text-gray-700">Fakultas Kedokteran UNTAN</p>
                    <p class="text-[10px] text-gray-500">Dokumen Naskah Lafal Sumpah Resmi</p>
                </div>
                <div class="text-center w-64">
                    <p class="text-gray-700">Pontianak, {{ $period->event_date->translatedFormat('d F Y') }}</p>
                    <p class="font-bold text-gray-800 mt-1 mb-16">Dekan Fakultas Kedokteran,</p>
                    <p class="font-black text-gray-950 underline">( Pimpinan Fakultas )</p>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
