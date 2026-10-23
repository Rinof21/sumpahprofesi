<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventRule extends Model
{
    protected $fillable = [
        'period_id',
        'title',
        'category',
        'description',
        'is_strict',
        'sort_order',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_strict' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(OathPeriod::class, 'period_id');
    }

    /**
     * Get default rules template customized for specific study program.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function getDefaultTemplateForProdi(?string $code): array
    {
        $code = strtoupper(trim($code ?? 'KED'));

        if ($code === 'APT' || str_contains($code, 'APT') || str_contains($code, 'FARM')) {
            return [
                ['title' => 'Disiplin Waktu Hadir Pelantikan Sumpah Apoteker', 'category' => 'Kehadiran', 'description' => 'Seluruh calon apoteker baru dan pendamping keluarga wajib hadir selambat-lambatnya pukul 07.30 WIB di ruang aula utama. Keterlambatan menyebabkan calon apoteker tidak diperkenankan mengikuti pengucapan sumpah apoteker bersama rohaniwan.', 'is_strict' => true, 'sort_order' => 1],
                ['title' => 'Larangan Membawa Anak Kecil & Balita ke Aula', 'category' => 'Tamu & Keluarga', 'description' => 'Demi menjaga kekhidmatan sakral prosesi pembacaan sumpah apoteker, anak kecil / balita di bawah usia 10 tahun DILARANG KERAS memasuki ruang aula prosesi. Petugas berhak menolak akses masuk di pintu aula.', 'is_strict' => true, 'sort_order' => 2],
                ['title' => 'Standar Busana Resmi Pelantikan Apoteker (Dress Code)', 'category' => 'Pakaian', 'description' => 'Calon apoteker pria mengenakan Pakaian Jas Sipil Lengkap (PSL) gelap berdasi dan sepatu pantofel formal. Calon apoteker wanita mengenakan kebaya nasional sopan dan selempang kelulusan Apoteker. Tamu berbusana batik formal atau pakaian rapi.', 'is_strict' => false, 'sort_order' => 3],
                ['title' => 'Ketentuan e-Ticket & Kuota Undangan Apoteker', 'category' => 'Tamu & Keluarga', 'description' => 'Setiap apoteker baru hanya berhak membawa maksimal 2 (dua) orang pendamping keluarga. Pendamping wajib menunjukkan e-Ticket barcode digital atau cetak resmi yang diunduh melalui portal.', 'is_strict' => false, 'sort_order' => 4],
                ['title' => 'Ketertiban Ruang Sidang & Penyerahan Dokumen STRA', 'category' => 'Prosesi Aula', 'description' => 'Seluruh gawai wajib dalam mode senyap (silent mode). Selama pembacaan lafal sumpah apoteker dan penyerahan STRA berlangsung, hadirin dilarang keluar masuk ruangan atau berbicara.', 'is_strict' => true, 'sort_order' => 5],
                ['title' => 'Dokumentasi Resmi & Pembatasan Fotografer', 'category' => 'Dokumentasi', 'description' => 'Dokumentasi di area panggung utama dan penyerahan sertifikat kompetensi hanya dilakukan oleh 3 fotografer resmi terakreditasi bertanda E-Badge. Tamu keluarga dilarang maju ke depan panggung.', 'is_strict' => false, 'sort_order' => 6],
            ];
        }

        if ($code === 'NRS' || $code === 'NS' || str_contains($code, 'NERS') || str_contains($code, 'KEP')) {
            return [
                ['title' => 'Kehadiran Tepat Waktu & Gladi Penataan Barisan Ners', 'category' => 'Kehadiran', 'description' => 'Calon wisudawan ners dan keluarga pendamping wajib hadir pukul 07.30 WIB untuk gladi penataan barisan prosesi. Keterlambatan setelah sidang senat dibuka tidak diizinkan masuk ke ruang aula.', 'is_strict' => true, 'sort_order' => 1],
                ['title' => 'Larangan Membawa Anak Kecil & Balita ke Dalam Aula', 'category' => 'Tamu & Keluarga', 'description' => 'Demi menjaga kekhidmatan sakral tradisi angkat sumpah ners, ANAK KECIL / BALITA di bawah usia 10 tahun DILARANG KERAS memasuki ruang aula prosesi.', 'is_strict' => true, 'sort_order' => 2],
                ['title' => 'Standar Busana Ners & Tradisi Kap/Pin PPNI', 'category' => 'Pakaian', 'description' => 'Wisudawan ners pria mengenakan Jas PSL Lengkap gelap berdasi. Wisudawati ners mengenakan busana profesi putih/kebaya nasional yang telah disiapkan untuk pemasangan kap perawat dan pin PPNI resmi. Tamu keluarga berbusana batik formal.', 'is_strict' => false, 'sort_order' => 3],
                ['title' => 'Ketentuan e-Ticket Undangan Resmi Keluarga', 'category' => 'Tamu & Keluarga', 'description' => 'Setiap calon ners baru membawa maksimal 2 (dua) orang pendamping keluarga dengan menunjukkan e-Ticket barcode digital atau cetak resmi.', 'is_strict' => false, 'sort_order' => 4],
                ['title' => 'Ketertiban Selama Ikrar Janji Ners Berlangsung', 'category' => 'Prosesi Aula', 'description' => 'Seluruh ponsel wajib dalam mode hening. Selama pengumandangan Mars PPNI dan pembacaan janji ners berlangsung, seluruh hadirin wajib menjaga keheningan dan tertib di kursi masing-masing.', 'is_strict' => true, 'sort_order' => 5],
                ['title' => 'Ketentuan Dokumentasi Tradisi Kap & Pin Ners', 'category' => 'Dokumentasi', 'description' => 'Tradisi sakral pemasangan kap perawat dan penyematan pin PPNI hanya didokumentasikan oleh 3 fotografer terakreditasi resmi agar prosesi berjalan tertib.', 'is_strict' => false, 'sort_order' => 6],
            ];
        }

        // Default: KED (Profesi Dokter)
        return [
            ['title' => 'Kehadiran Tepat Waktu (Disiplin Waktu Dokter)', 'category' => 'Kehadiran', 'description' => 'Calon dokter dan keluarga wajib hadir di lokasi acara selambat-lambatnya 30 menit sebelum acara dimulai (pukul 07.30 WIB). Peserta yang terlambat setelah sidang dibuka tidak diperkenankan memasuki ruangan utama.', 'is_strict' => true, 'sort_order' => 1],
            ['title' => 'Larangan Mutlak Membawa Anak Kecil & Balita', 'category' => 'Tamu & Keluarga', 'description' => 'Demi menjaga kekhidmatan sakral prosesi pengambilan sumpah dokter, ANAK KECIL / BALITA (di bawah usia 10 tahun) DILARANG KERAS memasuki ruang aula prosesi. Petugas keamanan berhak menolak akses masuk di pintu aula.', 'is_strict' => true, 'sort_order' => 2],
            ['title' => 'Standar Tata Busana Resmi Profesi Dokter (Dress Code)', 'category' => 'Pakaian', 'description' => 'Calon wisudawan dokter pria wajib mengenakan Pakaian Jas Sipil Lengkap (PSL) gelap berdasi, kemeja putih, dan sepatu pantofel formal. Calon wisudawati dokter mengenakan busana kebaya nasional rapi dan sopan. Tamu keluarga wajib berbusana batik formal atau pakaian rapi.', 'is_strict' => false, 'sort_order' => 3],
            ['title' => 'Ketentuan e-Ticket & Kuota Undangan Keluarga', 'category' => 'Tamu & Keluarga', 'description' => 'Setiap calon wisudawan hanya berhak membawa maksimal 2 (dua) orang pendamping keluarga. Pendamping wajib menunjukkan e-Ticket barcode digital atau cetak resmi yang dapat diunduh di dashboard.', 'is_strict' => false, 'sort_order' => 4],
            ['title' => 'Ketertiban Ruangan & Mode Senyap Perangkat HP', 'category' => 'Prosesi Aula', 'description' => 'Seluruh gawai / smartphone wajib dalam mode hening (silent mode). Selama prosesi pengucapan sumpah dokter dan lagu kebangsaan berlangsung, hadirin dilarang keluar masuk ruangan atau membuat kegaduhan.', 'is_strict' => true, 'sort_order' => 5],
            ['title' => 'Ketentuan Fotografer & Zona Karpet Merah', 'category' => 'Dokumentasi', 'description' => 'Hanya 3 fotografer resmi yang terdaftar dan memakai E-Badge Akreditasi Aula yang diizinkan mengambil foto di area panggung utama dan karpet merah. Tamu keluarga dilarang maju ke depan panggung saat prosesi berlangsung.', 'is_strict' => false, 'sort_order' => 6],
        ];
    }
}
