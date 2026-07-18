@extends('layouts.landing')
@section('title', 'Informasi Pendaftaran | Super Seed Academy')

@section('content')

<section class="py-20 bg-slate-50 border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-black text-brand-navy mb-4">
            Informasi Pendaftaran
        </h1>

        <p class="text-slate-600 max-w-2xl mx-auto text-lg">
            Mari bergabung menjadi keluarga besar Super Seed Academy.
            Simak persyaratan pendaftaran di bawah ini.
        </p>
    </div>
</section>

<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">

            {{-- Informasi --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8">

                <h3 class="text-2xl font-bold text-brand-navy mb-8">
                    Yang Didapatkan
                </h3>

                <ul class="space-y-5">

                    <li class="flex items-start gap-4">
                        <i class="bi bi-check-circle-fill text-brand-blue text-lg mt-1"></i>
                        <span class="text-slate-600">
                            2 Set Jersey Latihan Resmi (Home & Away)
                        </span>
                    </li>

                    <li class="flex items-start gap-4">
                        <i class="bi bi-check-circle-fill text-brand-blue text-lg mt-1"></i>
                        <span class="text-slate-600">
                            1 Set Baju Bebas / Polo Shirt
                        </span>
                    </li>

                    <li class="flex items-start gap-4">
                        <i class="bi bi-check-circle-fill text-brand-blue text-lg mt-1"></i>
                        <span class="text-slate-600">
                            ID Card Akses Portal Wali Murid
                        </span>
                    </li>

                    <li class="flex items-start gap-4">
                        <i class="bi bi-check-circle-fill text-brand-blue text-lg mt-1"></i>
                        <span class="text-slate-600">
                            Latihan Rutin 3x Seminggu (Fasilitas Lapangan Premium)
                        </span>
                    </li>

                </ul>

                <div class="mt-10">
                    <a href="https://wa.me/628888061522"
                        class="w-full flex items-center justify-center gap-2 bg-brand-blue hover:bg-blue-700 text-white font-semibold py-4 rounded-xl transition duration-300">

                        <i class="bi bi-whatsapp text-lg"></i>

                        Daftar Via WhatsApp
                    </a>
                </div>

            </div>

            {{-- Persyaratan --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8">

                <h3 class="text-2xl font-bold text-brand-navy mb-8">
                    Persyaratan Dokumen
                </h3>

                <div class="space-y-5">

                    <div class="flex items-center gap-5 border border-slate-200 rounded-xl p-5">
                        <div class="w-11 h-11 rounded-full bg-brand-light text-brand-blue flex items-center justify-center font-bold">
                            1
                        </div>

                        <span class="text-slate-700 font-medium">
                            Fotokopi Akte Kelahiran (2 Lembar)
                        </span>
                    </div>

                    <div class="flex items-center gap-5 border border-slate-200 rounded-xl p-5">
                        <div class="w-11 h-11 rounded-full bg-brand-light text-brand-blue flex items-center justify-center font-bold">
                            2
                        </div>

                        <span class="text-slate-700 font-medium">
                            Fotokopi Kartu Keluarga (2 Lembar)
                        </span>
                    </div>

                    <div class="flex items-center gap-5 border border-slate-200 rounded-xl p-5">
                        <div class="w-11 h-11 rounded-full bg-brand-light text-brand-blue flex items-center justify-center font-bold">
                            3
                        </div>

                        <span class="text-slate-700 font-medium">
                            KIA dan Fotokopi Rapor (2 Lembar)
                        </span>
                    </div>

                </div>

            </div>

        </div>

    </div>
</section>

@endsection