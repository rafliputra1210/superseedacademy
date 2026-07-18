@extends('layouts.landing')
@section('title', 'Jadwal Latihan | Superseed Academy')

@section('content')
<section class="py-20 bg-slate-50 border-b border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center fade-in">
        <h1 class="text-3xl md:text-5xl font-black text-brand-navy mb-4 tracking-tight">Jadwal Latihan</h1>
        <p class="text-slate-600 text-lg max-w-2xl mx-auto">
            Disiplin adalah kunci. Lihat jadwal latihan terstruktur untuk semua kelompok umur akademi.
        </p>
    </div>
</section>

<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 fade-in">
        <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-brand-navy text-white text-sm uppercase tracking-wider">
                            <th class="py-4 px-6 font-semibold">Kelompok Umur</th>
                            <th class="py-4 px-6 font-semibold">Hari</th>
                            <th class="py-4 px-6 font-semibold">Waktu</th>
                            <th class="py-4 px-6 font-semibold">Lokasi</th>
                            <th class="py-4 px-6 font-semibold">Pelatih Utama</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($schedules as $schedule)
                        <tr class="hover:bg-slate-50 transition-colors group">
                            <td class="py-4 px-6">
                                <span class="inline-block px-3 py-1 bg-brand-light text-brand-blue rounded-full font-bold text-sm">
                                    {{ $schedule->kelompok_usia ?? 'Umum' }}
                                </span>
                            </td>
                            <td class="py-4 px-6 font-medium text-slate-700">
                                <i class="bi bi-calendar-event text-brand-blue mr-2"></i> {{ $schedule->hari ?? '-' }}
                            </td>
                            <td class="py-4 px-6 text-slate-600">
                                <i class="bi bi-clock text-brand-gray mr-2"></i> {{ $schedule->waktu ?? '-' }}
                            </td>
                            <td class="py-4 px-6 text-slate-600">
                                {{ $schedule->lokasi ?? 'Lapangan Utama' }}
                            </td>
                            <td class="py-4 px-6 font-medium text-brand-navy">
                                {{ $schedule->coach->nama ?? 'Tim Pelatih' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-500">
                                Jadwal latihan belum tersedia saat ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-8 bg-brand-light rounded-xl p-6 border border-blue-100 flex items-start gap-4">
            <i class="bi bi-info-circle-fill text-brand-blue text-2xl mt-1"></i>
            <div>
                <h4 class="font-bold text-brand-navy mb-1">Informasi Kehadiran</h4>
                <p class="text-slate-600 text-sm leading-relaxed">
                    Siswa diharapkan hadir 15 menit sebelum sesi latihan dimulai. Pemantauan absensi akan dicatat langsung oleh pelatih dan dapat dilihat secara real-time melalui Portal Wali Murid.
                </p>
            </div>
        </div>
    </div>
</section>
@endsection