<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ __('Riwayat Pemberian Pakan') }}
            </h2>
            <a href="{{ route('feeding-records.create') }}">
                <x-primary-button class="justify-center w-full sm:w-auto">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    {{ __('Catat Pakan Baru') }}
                </x-primary-button>
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="flex items-center p-4 mb-6 text-green-700 border-l-4 border-green-500 rounded-md shadow-sm bg-green-50">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    {{ session('success') }}
                </div>
            @endif

            {{-- FILTER SECTION --}}
            <div class="p-5 mb-6 bg-white border border-gray-100 shadow-sm rounded-xl">
                <form method="GET" action="{{ route('feeding-records.index') }}">
                    <div class="grid items-end grid-cols-1 gap-4 md:grid-cols-4">
                        <div>
                            <x-input-label for="start_date" :value="__('Dari Tanggal')" />
                            <x-text-input id="start_date" name="start_date" type="date" class="w-full mt-1 text-sm" :value="request('start_date')" />
                        </div>
                        <div>
                            <x-input-label for="end_date" :value="__('Sampai Tanggal')" />
                            <x-text-input id="end_date" name="end_date" type="date" class="w-full mt-1 text-sm" :value="request('end_date')" />
                        </div>
                        <div>
                            <x-input-label for="shelter_id" :value="__('Filter Kandang')" />
                            <select name="shelter_id" id="shelter_id" class="w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Semua Kandang</option>
                                @if(isset($shelters))
                                    @foreach($shelters as $shelter)
                                        <option value="{{ $shelter->id }}" {{ request('shelter_id') == $shelter->id ? 'selected' : '' }}>
                                            {{ $shelter->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="flex gap-2">
                            <x-primary-button type="submit" class="justify-center w-full h-[38px]">
                                {{ __('Filter') }}
                            </x-primary-button>
                            @if(request()->hasAny(['start_date', 'end_date', 'shelter_id']))
                                <a href="{{ route('feeding-records.index') }}" class="inline-flex items-center px-3 py-2 text-xs font-semibold tracking-widest text-gray-700 uppercase transition duration-150 ease-in-out bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 active:bg-gray-300 focus:outline-none" title="Reset Filter">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            {{-- GRID LAYOUT --}}
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
                @forelse ($feedingRecords as $record)
                    <div class="relative flex flex-col overflow-hidden transition-shadow duration-200 bg-white border border-gray-100 shadow-sm rounded-xl hover:shadow-md group">

                        <a href="{{ route('feeding-records.show', $record) }}" class="flex-grow block">
                            {{-- Header --}}
                            <div class="flex items-center justify-between px-5 py-4 transition-colors border-b border-gray-100 bg-gray-50 group-hover:bg-gray-100">
                                <div>
                                    <div class="text-lg font-bold text-gray-800">
                                        {{ $record->date->format('d M Y') }}
                                    </div>
                                    <div class="mt-1 text-xs font-medium text-gray-500">
                                        {{ $record->date->diffForHumans() }}
                                    </div>
                                </div>
                                <span class="inline-flex items-center px-3 py-1 text-xs font-bold text-indigo-700 bg-indigo-100 border border-indigo-200 rounded-full">
                                    🏠 {{ $record->shelter->name }}
                                </span>
                            </div>

                            {{-- Body --}}
                            <div class="p-5">
                                <div class="grid h-full grid-cols-2 gap-4">
                                    {{-- (1) Inisialisasi variabel total biaya DI SINI --}}
                                    @php $totalCostPerCard = 0; @endphp

                                    {{-- Kolom Pagi --}}
                                    <div class="pr-2 border-r border-gray-200 border-dashed">
                                        <div class="flex items-center mb-3 text-orange-500">
                                            {{-- ICON MATAHARI --}}
                                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                            <span class="text-sm font-bold tracking-wide uppercase">Pagi</span>
                                            <span class="ml-auto text-xs font-mono bg-orange-50 text-orange-600 px-1.5 py-0.5 rounded">
                                                {{ $record->time_morning ? \Carbon\Carbon::parse($record->time_morning)->format('H:i') : '-' }}
                                            </span>
                                        </div>
                                        <ul class="space-y-2">
                                            @php $hasMorning = false; @endphp
                                            @foreach($record->feedTypes as $feed)
                                                @if($feed->pivot->quantity_morning > 0)
                                                    @php
                                                        $hasMorning = true;
                                                        // (2) Hitung biaya (qty pagi * harga) dan tambahkan ke total
                                                        $totalCostPerCard += ($feed->pivot->quantity_morning * ($feed->price_per_unit ?? 0));
                                                    @endphp
                                                    <li class="flex items-start justify-between text-sm text-gray-700">
                                                        <span class="w-16 text-xs text-gray-500 truncate" title="{{ $feed->name }}">{{ $feed->name }}</span>
                                                        {{-- MENAMPILKAN UNIT --}}
                                                        <span class="font-bold">
                                                            {{ (float)$feed->pivot->quantity_morning }}
                                                            <span class="text-[10px] font-normal text-gray-400">{{ $feed->unit }}</span>
                                                        </span>
                                                    </li>
                                                @endif
                                            @endforeach
                                            @if(!$hasMorning) <li class="py-2 text-xs italic text-center text-gray-400">- Tidak ada pakan -</li> @endif
                                        </ul>
                                    </div>

                                    {{-- Kolom Sore --}}
                                    <div class="pl-2">
                                        <div class="flex items-center mb-3 text-blue-600">
                                            {{-- ICON BULAN --}}
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                                            <span class="text-sm font-bold tracking-wide uppercase">Sore</span>
                                            <span class="ml-auto text-xs font-mono bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded">
                                                {{ $record->time_evening ? \Carbon\Carbon::parse($record->time_evening)->format('H:i') : '-' }}
                                            </span>
                                        </div>
                                        <ul class="space-y-2">
                                            @php $hasEvening = false; @endphp
                                            @foreach($record->feedTypes as $feed)
                                                @if($feed->pivot->quantity_evening > 0)
                                                    @php
                                                        $hasEvening = true;
                                                        // (3) Hitung biaya (qty sore * harga) dan tambahkan ke total
                                                        $totalCostPerCard += ($feed->pivot->quantity_evening * ($feed->price_per_unit ?? 0));
                                                    @endphp
                                                    <li class="flex items-start justify-between text-sm text-gray-700">
                                                        <span class="w-16 text-xs text-gray-500 truncate" title="{{ $feed->name }}">{{ $feed->name }}</span>
                                                        {{-- MENAMPILKAN UNIT --}}
                                                        <span class="font-bold">
                                                            {{ (float)$feed->pivot->quantity_evening }}
                                                            <span class="text-[10px] font-normal text-gray-400">{{ $feed->unit }}</span>
                                                        </span>
                                                    </li>
                                                @endif
                                            @endforeach
                                            @if(!$hasEvening) <li class="py-2 text-xs italic text-center text-gray-400">- Tidak ada pakan -</li> @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </a>

                        {{-- FOOTER KARTU (TOMBOL AKSI & CATAT KAS) --}}
                        <div class="z-10 flex items-center justify-between px-5 py-3 text-xs border-t border-gray-100 bg-gray-50">

                            {{-- (4) KIRI: Estimasi & Tombol Kas --}}
                            <div class="flex items-center gap-3">
                                <div class="flex flex-col">
                                    <span class="text-[10px] text-gray-400 uppercase font-bold tracking-wider">Estimasi Biaya</span>
                                    <span class="text-sm font-bold text-green-700">
                                        @if($totalCostPerCard > 0)
                                            Rp {{ number_format($totalCostPerCard, 0, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </span>
                                </div>

                                {{-- (5) TOMBOL CATAT KE KAS (Hanya jika ada biaya > 0) --}}
                                @if($totalCostPerCard > 0)
                                    <a href="{{ route('profit-loss.create', [
                                        'date' => $record->date->format('Y-m-d'),
                                        'type' => 'expense',
                                        'amount' => $totalCostPerCard,
                                        'category' => 'Pembelian Pakan',
                                        'shelter_id' => $record->shelter_id,
                                        'description' => 'Pakan Harian ' . $record->date->format('d M Y') . ' (' . $record->shelter->name . ')'
                                    ]) }}"
                                    class="flex items-center px-2 py-1 font-semibold text-green-700 transition-colors bg-green-100 border border-green-300 rounded shadow-sm hover:bg-green-200"
                                    title="Catat pengeluaran ini ke Buku Kas">
                                        <span class="mr-1">💸</span> Catat
                                    </a>
                                @endif
                            </div>

                            {{-- KANAN: Edit/Hapus --}}
                            <div class="flex items-center space-x-3">
                                <a href="{{ route('feeding-records.edit', $record) }}" class="font-semibold text-indigo-600 transition-colors hover:text-indigo-800 hover:underline">Edit</a>
                                <span class="text-gray-300">|</span>
                                <form action="{{ route('feeding-records.destroy', $record) }}" method="POST" onsubmit="return confirm('Hapus catatan pakan tanggal {{ $record->date->format('d M Y') }}?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="font-semibold text-red-500 transition-colors hover:text-red-700 hover:underline">Hapus</button>
                                </form>
                            </div>
                        </div>

                    </div>
                @empty
                    <div class="col-span-1 md:col-span-2 xl:col-span-3">
                         <div class="p-12 text-center bg-white border-2 border-gray-200 border-dashed shadow-sm rounded-xl">
                            <p class="mt-2 text-lg text-gray-500">Belum ada riwayat pakan.</p>
                            <div class="mt-6">
                                <a href="{{ route('feeding-records.create') }}">
                                    <x-primary-button>{{ __('Catat Pakan Pertama') }}</x-primary-button>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="mt-8">
                {{ $feedingRecords->links() }}
            </div>

        </div>
    </div>
</x-app-layout>
