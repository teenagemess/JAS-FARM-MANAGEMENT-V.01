<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ __('Daftar Domba') }}
            </h2>
            <a href="{{ route('sheep.create') }}">
                <x-primary-button>{{ __('+ Tambah Domba') }}</x-primary-button>
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="p-4 mb-4 text-green-700 bg-green-100 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            {{-- === FILTER & SEARCH BAR (MANUAL SUBMIT) === --}}
            <div class="p-4 mb-6 bg-white rounded-lg shadow-sm">
                {{-- Hapus ID form karena tidak lagi dipakai JS --}}
                <form method="GET" action="{{ route('sheep.index') }}">
                    <div class="grid items-end grid-cols-1 gap-4 md:grid-cols-5">

                        {{-- 1. Search Eartag --}}
                        <div class="col-span-1 md:col-span-2">
                            <x-input-label for="search" :value="__('Cari Eartag')" />
                            <x-text-input id="search" name="search" type="text" class="w-full mt-1"
                                placeholder="JAS-001 atau 001"
                                :value="request('search')" />
                        </div>

                        {{-- 2. Filter Gender --}}
                        <div>
                            <x-input-label for="gender" :value="__('Filter Gender')" />
                            <select name="gender" id="gender" class="w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Semua Gender</option>
                                <option value="Jantan" {{ request('gender') == 'Jantan' ? 'selected' : '' }}>Jantan</option>
                                <option value="Betina" {{ request('gender') == 'Betina' ? 'selected' : '' }}>Betina</option>
                            </select>
                        </div>

                        {{-- 3. Filter Kandang --}}
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

                        {{-- 4. Tombol Action (Cari & Reset) --}}
                        <div class="flex space-x-2">
                            {{-- Tombol Cari Dikembalikan --}}
                            <x-primary-button type="submit" class="justify-center w-full">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                {{ __('Cari') }}
                            </x-primary-button>

                            @if(request()->hasAny(['search', 'gender', 'shelter_id']))
                                <a href="{{ route('sheep.index') }}" class="inline-flex items-center justify-center w-full px-4 py-2 text-xs font-semibold tracking-widest text-center text-gray-700 uppercase transition duration-150 ease-in-out bg-gray-200 border border-transparent rounded-md hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                    Reset
                                </a>
                            @endif
                        </div>

                    </div>
                </form>
            </div>
            {{-- === AKHIR FILTER === --}}

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">

                @forelse ($sheep as $s)
                    @php
                        // 1. LOGIKA KESEHATAN (VERSI LEBIH STABIL)
                        $latestRecord = $s->healthRecords
                            ->sort(function ($a, $b) {
                                // Gunakan Carbon::parse agar aman baik itu string maupun objek date
                                $dateA = \Carbon\Carbon::parse($a->record_date)->timestamp;
                                $dateB = \Carbon\Carbon::parse($b->record_date)->timestamp;

                                // Jika tanggal sama persis, gunakan ID terbesar (inputan terakhir)
                                if ($dateA === $dateB) {
                                    return $b->id - $a->id;
                                }

                                // Urutkan tanggal dari yang paling baru (DESC)
                                return $dateB - $dateA;
                            })
                            ->first();

                        $activeSickness = null;

                        // 2. Cek Status
                        // Tag hanya muncul jika ada record DAN statusnya BUKAN 'Completed' atau 'Sembuh / Selesai'
                        if ($latestRecord && !in_array($latestRecord->status, ['Completed', 'Sembuh / Selesai'])) {
                            $activeSickness = $latestRecord;
                        }

                        // 3. Logika Kehamilan
                        $activePregnancy = null;
                        if ($s->gender === 'Betina') {
                            $activePregnancy = $s->asDamReproductionRecords
                                ->where('status', 'Pregnant')
                                ->first();
                        }

                        // 4. Border Kartu
                        $cardBorderClass = '';
                        if ($activeSickness) {
                            $cardBorderClass = 'border-2 border-red-400 shadow-red-200';
                        } elseif ($activePregnancy) {
                            $cardBorderClass = 'border-2 border-purple-400 shadow-purple-200';
                        }
                    @endphp

                    <a href="{{ route('sheep.show', $s) }}" class="block">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg h-full flex flex-col transition-transform transform hover:scale-105 {{ $cardBorderClass }}">

                            {{-- Kontainer Gambar --}}
                            <div class="relative">
                                <img
                                    class="object-cover w-full h-56"
                                    src="{{ $s->photo_path ? asset('storage/' . $s->photo_path) : 'https://placehold.co/600x400/e2e8f0/9ca3af?text=' . urlencode($s->tag_number) }}"
                                    alt="Foto Domba {{ $s->tag_number }}"
                                    onerror="this.onerror=null; this.src='https://placehold.co/600x400/e2e8f0/9ca3af?text=Image+Error';"
                                >

                                {{-- AREA BADGE --}}
                                <div class="absolute flex flex-col items-end gap-1 top-2 right-2">
                                    @if($s->is_pedigree)
                                        <span class="bg-yellow-500 text-yellow-900 text-[10px] font-bold px-2 py-1 rounded shadow-md border border-yellow-600">
                                            UNGGUL
                                        </span>
                                    @endif

                                    {{-- Badge Kesehatan (Hanya muncul jika SAKIT) --}}
                                    @if($activeSickness)
                                        <span class="{{ $activeSickness->badge_style }} text-[10px] font-bold px-2 py-1 rounded shadow-md animate-pulse border border-white/20 text-right">
                                            {{ $activeSickness->status }}
                                            <span class="block text-[9px] opacity-90 font-normal mt-0.5">
                                                {{ Str::limit($activeSickness->diagnosis, 12) }}
                                                ({{ \Carbon\Carbon::parse($activeSickness->record_date)->format('d/m') }})
                                            </span>
                                        </span>
                                    @endif

                                    {{-- Badge Kehamilan --}}
                                    @if($activePregnancy)
                                        <span class="bg-purple-600 text-white text-[10px] font-bold px-2 py-1 rounded shadow-md border border-purple-400 text-right">
                                            HAMIL
                                            <span class="block text-[9px] opacity-90 font-normal mt-0.5">
                                                HPL: {{ \Carbon\Carbon::parse($activePregnancy->expected_delivery_date)->format('d M') }}
                                                ({{ \Carbon\Carbon::parse($activePregnancy->expected_delivery_date)->diffForHumans() }})
                                            </span>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Konten Teks --}}
                            <div class="flex-grow p-6 text-gray-900">
                                <h3 class="mb-1 text-lg font-bold">{{ $s->tag_number }}</h3>
                                <p class="flex items-center mb-2 text-sm text-gray-600">
                                    <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                                    {{ $s->shelter->name ?? 'Belum ada kandang' }}
                                </p>
                                <hr class="my-2">
                                <div class="space-y-1 text-sm text-gray-700">
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Umur:</span>
                                        <span class="font-medium">{{ $s->date_of_birth->diffForHumans(null, true) }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-500">Gender:</span>
                                        @if($s->gender == 'Jantan')
                                            <span class="flex items-center font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full border border-blue-100 text-xs">
                                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 5L13.6 10.4"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 5h-5"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 5v5"></path></svg>
                                                Jantan
                                            </span>
                                        @else
                                            <span class="flex items-center font-bold text-pink-500 bg-pink-50 px-2 py-0.5 rounded-full border border-pink-100 text-xs">
                                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9a5 5 0 1 0 0 10 5 5 0 0 0 0-10z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14v7"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 18h6"></path></svg>
                                                Betina
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Tipe:</span>
                                        <span class="font-medium">{{ $s->type }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="col-span-1 py-16 text-center border-2 border-gray-300 border-dashed rounded-lg sm:col-span-2 lg:col-span-3 xl:col-span-4 bg-gray-50">
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

    {{-- Script untuk Debounce dihapus agar tidak hot reload --}}
</x-app-layout>
