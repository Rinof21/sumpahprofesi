<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventSchedule extends Model
{
    protected $fillable = [
        'period_id',
        'start_time',
        'end_time',
        'activity_name',
        'person_in_charge',
        'notes',
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
            'sort_order' => 'integer',
        ];
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(OathPeriod::class, 'period_id');
    }

    /**
     * Get default schedule template customized for specific study program.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function getDefaultTemplateForProdi(?string $code): array
    {
        $code = strtoupper(trim($code ?? 'KED'));

        if ($code === 'APT' || str_contains($code, 'APT') || str_contains($code, 'FARM')) {
            return [
                ['start_time' => '07:30', 'end_time' => '08:15', 'activity_name' => 'Registrasi & Presensi Calon Apoteker Baru & Pengurus IAI', 'person_in_charge' => 'Sie Kesekretariatan Apoteker', 'notes' => 'Pintu masuk foyer aula utama, validasi presensi dan selempang kelulusan.', 'sort_order' => 1],
                ['start_time' => '08:15', 'end_time' => '08:30', 'activity_name' => 'Pembukaan Acara & Masuknya Dewan Pengambil Sumpah Apoteker', 'person_in_charge' => 'MC & Protokoler Fakultas', 'notes' => 'Hadirin dimohon berdiri menyambut Pimpinan Sidang Terbuka & Pengurus IAI.', 'sort_order' => 2],
                ['start_time' => '08:30', 'end_time' => '08:45', 'activity_name' => 'Menyanyikan Indonesia Raya, Hymne UNTAN, & Mars Ikatan Apoteker Indonesia', 'person_in_charge' => 'Paduan Suara Mahasiswa Farmasi & Seluruh Hadirin', 'notes' => 'Posisi siap sempurna, dipandu oleh dirigen paduan suara.', 'sort_order' => 3],
                ['start_time' => '08:45', 'end_time' => '09:00', 'activity_name' => 'Pembukaan Sidang Pengambilan Sumpah & Janji Apoteker oleh Dekan', 'person_in_charge' => 'Dekan Fakultas Kedokteran UNTAN', 'notes' => 'Pengetukan palu sidang resmi pembuka dan sambutan pengantar.', 'sort_order' => 4],
                ['start_time' => '09:00', 'end_time' => '09:45', 'activity_name' => 'Prosesi Pengambilan Lafal Sumpah & Janji Apoteker Indonesia bersama Rohaniwan', 'person_in_charge' => 'Ketua Program Studi Profesi Apoteker & Rohaniwan Lintas Agama', 'notes' => 'Pengucapan lafal sumpah apoteker dipandu rohaniwan sesuai agama masing-masing.', 'sort_order' => 5],
                ['start_time' => '09:45', 'end_time' => '10:15', 'activity_name' => 'Penyerahan Surat Tanda Registrasi Apoteker (STRA), Sertifikat Kompetensi, & Medali', 'person_in_charge' => 'Dekan FK UNTAN & Ketua Pengurus Daerah IAI Kalbar', 'notes' => 'Penyerahan dokumen legalitas izin praktik kefarmasian dan medali kelulusan.', 'sort_order' => 6],
                ['start_time' => '10:15', 'end_time' => '10:35', 'activity_name' => 'Pesan & Kesan dari Perwakilan Apoteker Baru', 'person_in_charge' => 'Perwakilan Wisudawan Apoteker Terbaik', 'notes' => 'Pidato perwakilan angkatan lulusan apoteker baru.', 'sort_order' => 7],
                ['start_time' => '10:35', 'end_time' => '11:00', 'activity_name' => 'Sambutan Dekan FK UNTAN & Arahan Kode Etik Ketua PD IAI Kalimantan Barat', 'person_in_charge' => 'Dekan FK UNTAN & Ketua Pengurus Daerah IAI Kalbar', 'notes' => 'Amanat Kode Etik Apoteker Indonesia dan tanggung jawab pelayanan kefarmasian.', 'sort_order' => 8],
                ['start_time' => '11:00', 'end_time' => '11:15', 'activity_name' => 'Doa Bersama Lintas Agama & Penutupan Sidang Resmi Sumpah Apoteker', 'person_in_charge' => 'Perwakilan Rohaniwan & Ketua Senat', 'notes' => 'Doa penutup dan pengetukan palu penutupan sidang sumpah apoteker.', 'sort_order' => 9],
                ['start_time' => '11:15', 'end_time' => '11:45', 'activity_name' => 'Sesi Foto Bersama Apoteker Baru & Jajaran Pengurus Daerah IAI', 'person_in_charge' => 'Sie Dokumentasi & Fotografer Akreditasi', 'notes' => 'Foto bersama di panggung utama bersama dosen farmasi dan keluarga pendamping.', 'sort_order' => 10],
            ];
        }

        if ($code === 'NRS' || $code === 'NS' || str_contains($code, 'NERS') || str_contains($code, 'KEP')) {
            return [
                ['start_time' => '07:30', 'end_time' => '08:15', 'activity_name' => 'Registrasi, Presensi Calon Ners Baru, & Gladi Penataan Barisan', 'person_in_charge' => 'Sie Kesekretariatan Ners', 'notes' => 'Pintu masuk foyer aula, pengecekan kelengkapan kap perawat & pin PPNI.', 'sort_order' => 1],
                ['start_time' => '08:15', 'end_time' => '08:30', 'activity_name' => 'Pembukaan Acara & Masuknya Senat Prosesi Angkat Sumpah Ners', 'person_in_charge' => 'MC & Protokoler Fakultas', 'notes' => 'Hadirin dimohon berdiri menyambut Pimpinan Sidang Senat & DPW PPNI.', 'sort_order' => 2],
                ['start_time' => '08:30', 'end_time' => '08:45', 'activity_name' => 'Menyanyikan Indonesia Raya, Hymne UNTAN, & Mars PPNI', 'person_in_charge' => 'Paduan Suara Mahasiswa Keperawatan & Seluruh Hadirin', 'notes' => 'Posisi siap sempurna, dipandu oleh dirigen.', 'sort_order' => 3],
                ['start_time' => '08:45', 'end_time' => '09:00', 'activity_name' => 'Pembukaan Sidang Angkat Sumpah Ners oleh Dekan FK UNTAN', 'person_in_charge' => 'Dekan Fakultas Kedokteran UNTAN', 'notes' => 'Pemukulan palu sidang dan sambutan pembuka rapat senat terbuka.', 'sort_order' => 4],
                ['start_time' => '09:00', 'end_time' => '09:45', 'activity_name' => 'Prosesi Sakral Pengambilan Lafal Sumpah & Janji Ners Indonesia bersama Rohaniwan', 'person_in_charge' => 'Ketua Program Studi Profesi Ners & Rohaniwan Lintas Agama', 'notes' => 'Pengucapan sumpah janji ners dipandu rohaniwan sesuai keyakinan.', 'sort_order' => 5],
                ['start_time' => '09:45', 'end_time' => '10:15', 'activity_name' => 'Tradisi Pemasangan Pin PPNI, Pemasangan Kap (Ners Wanita), & Penyerahan Medali', 'person_in_charge' => 'Dekan FK UNTAN & Ketua DPW PPNI Kalbar', 'notes' => 'Tradisi sakral profesi perawat dan penyerahan sertifikat kompetensi ners.', 'sort_order' => 6],
                ['start_time' => '10:15', 'end_time' => '10:35', 'activity_name' => 'Pesan & Kesan dari Perwakilan Ners Baru', 'person_in_charge' => 'Perwakilan Wisudawan Ners Berprestasi', 'notes' => 'Pidato perwakilan angkatan lulusan profesi ners.', 'sort_order' => 7],
                ['start_time' => '10:35', 'end_time' => '11:00', 'activity_name' => 'Sambutan Dekan FK UNTAN & Amanat Profesi Keperawatan Ketua DPW PPNI Kalbar', 'person_in_charge' => 'Dekan FK UNTAN & Ketua DPW PPNI Kalbar', 'notes' => 'Arahan etika keperawatan, caring, dan tanggung jawab profesi ners.', 'sort_order' => 8],
                ['start_time' => '11:00', 'end_time' => '11:15', 'activity_name' => 'Doa Bersama Lintas Agama & Penutupan Sidang Senat Sumpah Ners', 'person_in_charge' => 'Perwakilan Rohaniwan & Ketua Senat', 'notes' => 'Doa bersama penutup dan pengetukan palu penutupan sidang senat.', 'sort_order' => 9],
                ['start_time' => '11:15', 'end_time' => '11:45', 'activity_name' => 'Sesi Foto Bersama Ners Baru, Pengurus PPNI, & Dosen Keperawatan', 'person_in_charge' => 'Sie Dokumentasi & Fotografer Akreditasi', 'notes' => 'Foto bersama di panggung utama aula bersama pimpinan dan keluarga.', 'sort_order' => 10],
            ];
        }

        // Default: KED (Profesi Dokter)
        return [
            ['start_time' => '07:30', 'end_time' => '08:15', 'activity_name' => 'Registrasi & Presensi Calon Dokter Baru & Tamu Undangan', 'person_in_charge' => 'Sie Kesekretariatan & Registrasi', 'notes' => 'Pintu masuk barat foyer aula, penyerahan name tag dokter & presensi digital.', 'sort_order' => 1],
            ['start_time' => '08:15', 'end_time' => '08:30', 'activity_name' => 'Pembukaan Acara & Masuknya Pimpinan Sidang Senat Terbuka', 'person_in_charge' => 'MC & Protokoler Fakultas', 'notes' => 'Seluruh hadirin berdiri menyambut Dewan Senat & Dekanat FK UNTAN.', 'sort_order' => 2],
            ['start_time' => '08:30', 'end_time' => '08:45', 'activity_name' => 'Menyanyikan Indonesia Raya & Hymne Universitas Tanjungpura', 'person_in_charge' => 'Paduan Suara Mahasiswa & Seluruh Hadirin', 'notes' => 'Posisi siap sempurna, dipandu oleh dirigen paduan suara.', 'sort_order' => 3],
            ['start_time' => '08:45', 'end_time' => '09:00', 'activity_name' => 'Pembukaan Rapat Terbuka Senat oleh Dekan FK UNTAN', 'person_in_charge' => 'Dekan Fakultas Kedokteran UNTAN', 'notes' => 'Pemukulan palu sidang 3 kali dan sambutan pembuka rapat senat.', 'sort_order' => 4],
            ['start_time' => '09:00', 'end_time' => '09:45', 'activity_name' => 'Prosesi Sakral Pengambilan Lafal Sumpah Dokter Indonesia bersama Rohaniwan', 'person_in_charge' => 'Ketua Prodi Profesi Dokter & Rohaniwan Lintas Agama', 'notes' => 'Calon dokter mengucapkan sumpah dokter dipandu rohaniwan sesuai agama.', 'sort_order' => 5],
            ['start_time' => '09:45', 'end_time' => '10:15', 'activity_name' => 'Penandatanganan Berita Acara & Pengalungan Medali Profesi Dokter', 'person_in_charge' => 'Dekan FK UNTAN & Ketua Ikatan Dokter Indonesia (IDI) Wilayah Kalbar', 'notes' => 'Penyerahan piagam sumpah dokter dan kartu tanda anggota IDI.', 'sort_order' => 6],
            ['start_time' => '10:15', 'end_time' => '10:35', 'activity_name' => 'Pesan & Kesan dari Perwakilan Dokter Baru', 'person_in_charge' => 'Perwakilan Wisudawan Dokter Terbaik', 'notes' => 'Pidato perwakilan angkatan lulusan dokter baru.', 'sort_order' => 7],
            ['start_time' => '10:35', 'end_time' => '11:00', 'activity_name' => 'Sambutan Dekan FK UNTAN & Arahan Etika Profesi Ketua IDI Wilayah Kalbar', 'person_in_charge' => 'Dekan FK UNTAN & Ketua Pengurus Wilayah IDI Kalbar', 'notes' => 'Amanat Kode Etik Kedokteran Indonesia (KODEKI) dan sumpah dokter.', 'sort_order' => 8],
            ['start_time' => '11:00', 'end_time' => '11:15', 'activity_name' => 'Doa Bersama Lintas Agama & Penutupan Sidang Senat Sumpah Dokter', 'person_in_charge' => 'Perwakilan Rohaniwan & Ketua Senat', 'notes' => 'Doa penutup dan pengetukan palu penutupan sidang senat.', 'sort_order' => 9],
            ['start_time' => '11:15', 'end_time' => '11:45', 'activity_name' => 'Sesi Foto Bersama Dokter Baru & Ramah Tamah', 'person_in_charge' => 'Sie Dokumentasi & 3 Fotografer Akreditasi', 'notes' => 'Foto bersama di panggung utama bersama IDI, senat, dan keluarga pendamping.', 'sort_order' => 10],
        ];
    }
}
