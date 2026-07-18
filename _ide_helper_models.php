<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property string $judul
 * @property string $konten
 * @property string $tanggal
 * @property int $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Announcement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Announcement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Announcement query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Announcement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Announcement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Announcement whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Announcement whereJudul($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Announcement whereKonten($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Announcement whereTanggal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Announcement whereUpdatedAt($value)
 */
	class Announcement extends \Eloquent {}
}

namespace App\Models{
/**
 * @method bool|null delete()
 * @property int $id
 * @property int|null $user_id
 * @property string $nama
 * @property string|null $nomor_punggung
 * @property string|null $tanggal_lahir
 * @property string|null $posisi_bermain
 * @property string|null $alamat
 * @property string|null $nomor_wa
 * @property string|null $nomor_wa_ortu
 * @property string|null $foto
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Athlete newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Athlete newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Athlete query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Athlete whereAlamat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Athlete whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Athlete whereFoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Athlete whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Athlete whereNama($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Athlete whereNomorPunggung($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Athlete whereNomorWa($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Athlete whereNomorWaOrtu($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Athlete wherePosisiBermain($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Athlete whereTanggalLahir($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Athlete whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Athlete whereUserId($value)
 */
	class Athlete extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $athlete_id
 * @property string $tanggal
 * @property string $status
 * @property string|null $kode_barcode
 * @property string|null $foto_bukti
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Athlete $athlete
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereAthleteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereFotoBukti($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereKodeBarcode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereTanggal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereUpdatedAt($value)
 */
	class Attendance extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nama
 * @property string|null $alamat
 * @property string|null $nomor_wa
 * @property string $status_lisensi
 * @property string|null $detail_lisensi
 * @property string|null $referensi
 * @property string|null $foto
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coach newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coach newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coach query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coach whereAlamat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coach whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coach whereDetailLisensi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coach whereFoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coach whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coach whereNama($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coach whereNomorWa($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coach whereReferensi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coach whereStatusLisensi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coach whereUpdatedAt($value)
 */
	class Coach extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $athlete_id
 * @property string|null $bulan_tagihan
 * @property string $tanggal
 * @property string $jenis
 * @property string $kategori
 * @property string|null $keterangan
 * @property numeric $nominal
 * @property numeric $saldo_akhir
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Athlete|null $athlete
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Finance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Finance newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Finance query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Finance whereAthleteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Finance whereBulanTagihan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Finance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Finance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Finance whereJenis($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Finance whereKategori($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Finance whereKeterangan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Finance whereNominal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Finance whereSaldoAkhir($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Finance whereTanggal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Finance whereUpdatedAt($value)
 */
	class Finance extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $athlete_id
 * @property string $periode
 * @property array<array-key, mixed>|null $daftar_nilai
 * @property string|null $progres_skill
 * @property string|null $prestasi
 * @property string|null $catatan_pelatih
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Athlete $athlete
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereAthleteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereCatatanPelatih($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereDaftarNilai($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report wherePeriode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report wherePrestasi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereProgresSkill($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereUpdatedAt($value)
 */
	class Report extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $kelompok_usia
 * @property string $hari
 * @property string $waktu
 * @property string $lokasi
 * @property int|null $coach_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Coach|null $coach
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Schedule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Schedule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Schedule query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Schedule whereCoachId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Schedule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Schedule whereHari($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Schedule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Schedule whereKelompokUsia($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Schedule whereLokasi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Schedule whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Schedule whereWaktu($value)
 */
	class Schedule extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $username
 * @property string $password
 * @property string $role
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Athlete> $athletes
 * @property-read int|null $athletes_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUsername($value)
 */
	class User extends \Eloquent {}
}

