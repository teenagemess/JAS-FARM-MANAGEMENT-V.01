<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
            <div class="flex items-center gap-3">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    {{ __('Daftar Domba') }}
                </h2>
                <span class="inline-flex items-center px-3 py-1 text-xs font-bold text-indigo-700 border border-indigo-200 rounded-full bg-indigo-50">
                    {{ $sheep->total() }} Ekor
                </span>
            </div>
            <a href="{{ route('sheep.create') }}">
                <x-primary-button>{{ __('+ Tambah Domba') }}</x-primary-button>
            </a>
        </div>
    </x-slot>

    {{-- INJECT SERVICE HARGA --}}
    @inject('priceService', 'App\Services\PriceRecommenderService')

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="p-4 mb-4 text-green-700 bg-green-100 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            {{-- === FILTER & SEARCH BAR === --}}
            <div class="p-4 mb-6 bg-white rounded-lg shadow-sm">
                <form id="filter-form" method="GET" action="{{ route('sheep.index') }}">
                    <div class="grid items-end grid-cols-1 gap-4 md:grid-cols-5">
                        <div class="col-span-1 md:col-span-2">
                            <x-input-label for="search" :value="__('Cari Eartag')" />
                            <x-text-input id="search" name="search" type="text" class="w-full mt-1"
                                placeholder="JAS-001" :value="request('search')" />
                        </div>
                        <div>
                            <x-input-label for="gender" :value="__('Filter Gender')" />
                            <select name="gender" id="gender" class="w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Semua Gender</option>
                                <option value="Jantan" {{ request('gender') == 'Jantan' ? 'selected' : '' }}>Jantan</option>
                                <option value="Betina" {{ request('gender') == 'Betina' ? 'selected' : '' }}>Betina</option>
                            </select>
                        </div>
                        <div>
                            <x-input-label for="shelter_id" :value="__('Filter Kandang')" />
                            <select name="shelter_id" id="shelter_id" class="w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Semua Kandang</option>
                                @foreach($shelters as $shelter)
                                    <option value="{{ $shelter->id }}" {{ request('shelter_id') == $shelter->id ? 'selected' : '' }}>
                                        {{ $shelter->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex space-x-2">
                            <x-primary-button type="submit" class="justify-center w-full">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                {{ __('Cari') }}
                            </x-primary-button>
                            @if(request()->hasAny(['search', 'gender', 'shelter_id']))
                                <a href="{{ route('sheep.index') }}" class="inline-flex items-center justify-center w-full px-4 py-2 text-xs font-semibold tracking-widest text-center text-gray-700 uppercase transition duration-150 ease-in-out bg-gray-200 border border-transparent rounded-md hover:bg-gray-300 active:bg-gray-400">
                                    Reset
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">

                @forelse ($sheep as $s)
                    @php
                        // 1. LOGIKA KESEHATAN
                        $latestRecord = $s->healthRecords
                            ->sort(function ($a, $b) {
                                $dateA = \Carbon\Carbon::parse($a->record_date)->timestamp;
                                $dateB = \Carbon\Carbon::parse($b->record_date)->timestamp;
                                if ($dateA === $dateB) { return $b->id - $a->id; }
                                return $dateB - $dateA;
                            })->first();

                        $activeSickness = ($latestRecord && !in_array($latestRecord->status, ['Completed', 'Sembuh / Selesai'])) ? $latestRecord : null;

                        // 2. LOGIKA KEHAMILAN
                        $activePregnancy = null;
                        if ($s->gender === 'Betina') {
                            $activePregnancy = $s->asDamReproductionRecords->where('status', 'Pregnant')->first();
                        }

                        // 3. LOGIKA HARGA & BERAT (Panggil Service)
                        $analysis = $priceService->calculate($s);
                        $latestWeight = $analysis['berat_kg'];
                        $priceRecommendation = $analysis['rekomendasi'];

                        // 4. BORDER CARD
                        $cardBorderClass = 'border border-gray-200';
                        if ($activeSickness) {
                            $cardBorderClass = 'border-2 border-red-400 shadow-red-200';
                        } elseif ($activePregnancy) {
                            $cardBorderClass = 'border-2 border-purple-400 shadow-purple-200';
                        }
                        // PERUBAHAN: Logic border biru untuk Mitra dihapus, sehingga kartu mitra menggunakan border default (abu-abu)
                    @endphp

                    {{-- CARD CONTAINER --}}
                    <div class="flex flex-col h-full overflow-hidden transition-transform transform bg-white shadow-sm hover:scale-105 sm:rounded-lg {{ $cardBorderClass }}">

                        {{-- LINK UTAMA (Gambar & Info) --}}
                        <a href="{{ route('sheep.show', $s) }}" class="block">
                            <div class="relative">
                                <img
                                    class="object-cover w-full h-48"
                                    src="{{ $s->photo_path ? asset('storage/' . $s->photo_path) : 'https://placehold.co/600x400/e2e8f0/9ca3af?text=' . urlencode($s->tag_number) }}"
                                    alt="Foto {{ $s->tag_number }}"
                                    onerror="this.onerror=null; this.src='https://placehold.co/600x400/e2e8f0/9ca3af?text=No+Image';"
                                >

                                {{-- BADGES (Pojok Kanan Atas) --}}
                                <div class="absolute flex flex-col items-end gap-1 top-2 right-2">
                                    {{-- (BARU) Badge Mitra - Minimalis --}}
                                    @if($s->placement_status === 'Partner')
                                        <span class="flex items-center gap-1 bg-blue-50 text-blue-700 border border-blue-200 text-[10px] font-bold px-2 py-0.5 rounded shadow-sm">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                            MITRA
                                        </span>
                                    @endif

                                    @if($s->is_pedigree)
                                        <span class="bg-yellow-100 text-yellow-800 border border-yellow-200 text-[10px] font-bold px-2 py-0.5 rounded shadow-sm">UNGGUL</span>
                                    @endif

                                    @if($activeSickness)
                                        <span class="bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded shadow-sm">SAKIT</span>
                                    @endif

                                    @if($activePregnancy)
                                        <span class="bg-purple-500 text-white text-[10px] font-bold px-2 py-0.5 rounded shadow-sm">HAMIL</span>
                                    @endif
                                </div>

                                {{-- BADGE BERAT & HARGA (Pojok Kiri Bawah Gambar) --}}
                                <div class="absolute bottom-0 left-0 w-full p-2 bg-gradient-to-t from-black/70 to-transparent">
                                    <div class="flex items-end justify-between text-white">
                                        <div>
                                            <p class="text-[10px] opacity-80">Berat Terakhir</p>
                                            <p class="text-lg font-bold leading-none">{{ $latestWeight }} <span class="text-xs font-normal">kg</span></p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-[10px] opacity-80">Est. Harga</p>
                                            <p class="text-sm font-bold leading-none text-green-300">Rp {{ number_format($priceRecommendation, 0, ',', '.') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="text-lg font-bold text-gray-900">{{ $s->tag_number }}</h3>

                                    {{-- GENDER ICON --}}
                                    @if($s->gender == 'Jantan')
                                        <span class="flex items-center px-2 py-0.5 text-xs font-bold text-blue-600 border border-blue-100 rounded-full bg-blue-50">
                                            <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 5L13.6 10.4"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 5h-5"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 5v5"></path></svg>
                                            Jantan
                                        </span>
                                    @else
                                        <span class="flex items-center px-2 py-0.5 text-xs font-bold text-pink-600 border border-pink-100 rounded-full bg-pink-50">
                                            <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9a5 5 0 1 0 0 10 5 5 0 0 0 0-10z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14v7"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 18h6"></path></svg>
                                            Betina
                                        </span>
                                    @endif
                                </div>

                                <div class="flex items-center mb-1 text-xs text-gray-500">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                                    @if($s->placement_status === 'Partner')
                                        Mitra: {{ $s->partner->name ?? 'Unknown' }}
                                    @else
                                        {{ $s->shelter->name ?? 'Belum ada kandang' }}
                                    @endif
                                </div>
                                <div class="flex items-center text-xs text-gray-500">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    {{ $s->date_of_birth->diffForHumans(null, true) }}
                                </div>
                            </div>
                        </a>

                        {{-- FOOTER KARTU: QUICK ACTIONS --}}
                        <div class="flex items-center border-t border-gray-100 divide-x divide-gray-100 bg-gray-50">
                            {{-- Tombol Timbang --}}
                            <a href="{{ route('sheep.weights.create', $s) }}" class="flex items-center justify-center flex-1 py-3 text-xs font-bold text-gray-600 transition hover:bg-gray-100 hover:text-indigo-600" title="Input Timbangan">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path></svg>
                                Timbang
                            </a>

                            {{-- Tombol Lapor Sakit --}}
                            <a href="{{ route('sheep.health-records.create', $s) }}" class="flex items-center justify-center flex-1 py-3 text-xs font-bold text-gray-600 transition hover:bg-red-50 hover:text-red-600" title="Lapor Sakit">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                Lapor Sakit
                            </a>

                            {{-- Tombol Edit (Icon Only) --}}
                            <a href="{{ route('sheep.edit', $s) }}" class="flex items-center justify-center w-10 py-3 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600" title="Edit Data Domba">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="py-16 text-center border-2 border-gray-300 border-dashed rounded-lg col-span-full bg-gray-50">
                        <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        <p class="mt-2 text-lg text-gray-500">Tidak ada data domba yang ditemukan.</p>
                        @if(request()->hasAny(['search', 'gender', 'shelter_id']))
                            <a href="{{ route('sheep.index') }}" class="inline-block mt-2 font-medium text-indigo-600 hover:underline">
                                Reset Pencarian
                            </a>
                        @else
                            <a href="{{ route('sheep.create') }}" class="inline-block mt-4">
                                <x-primary-button>{{ __('+ Input Domba Pertama Anda') }}</x-primary-button>
                            </a>
                        @endif
                    </div>
                @endforelse

            </div>

            <div class="mt-8">
                {{ $sheep->links() }}
            </div>

        </div>
    </div>
</x-app-layout>
