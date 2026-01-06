<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Detail Domba: ') }} {{ $sheep->tag_number }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="p-4 mb-4 text-green-700 bg-green-100 rounded-md">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="p-4 mb-4 text-red-700 bg-red-100 rounded-md">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

                {{-- KOLOM KIRI: INFO UTAMA & REPRODUKSI (2/3 Lebar) --}}
                <div class="space-y-6 lg:col-span-2">

                    {{-- 1. KARTU INFO UTAMA & QR --}}
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">

                            {{-- LOGIKA STATUS --}}
                            @php
                                // 1. Status Kesehatan Aktif
                                $activeSickness = $sheep->healthRecords
                                    ->sort(function ($a, $b) {
                                        if ($a->record_date == $b->record_date) {
                                            return $b->id - $a->id;
                                        }
                                        return strtotime($b->record_date) - strtotime($a->record_date);
                                    })
                                    ->first();

                                if ($activeSickness && in_array($activeSickness->status, ['Completed', 'Sembuh / Selesai'])) {
                                    $activeSickness = null;
                                }

                                // 2. Status Kehamilan
                                $activePregnancy = null;
                                if ($sheep->gender === 'Betina') {
                                    $activePregnancy = $sheep->asDamReproductionRecords
                                        ->where('status', 'Pregnant')
                                        ->first();
                                }
                            @endphp

                            <div class="flex items-start justify-between mb-4">
                                <div class="flex flex-col gap-2">
                                    <h3 class="text-2xl font-bold">Eartag: {{ $sheep->tag_number }}</h3>

                                    <div class="flex flex-wrap gap-2">
                                        {{-- (BARU) BADGE STATUS KEMITRAAN --}}
                                        @if($sheep->placement_status === 'Partner')
                                            @if($sheep->partner_id)
                                                <span class="inline-flex items-center px-3 py-1 text-xs font-bold text-blue-900 bg-blue-100 border border-blue-200 rounded-full shadow-sm">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                                    Mitra: {{ $sheep->partner->name ?? 'Unknown' }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-3 py-1 text-xs font-bold text-yellow-800 bg-yellow-100 border border-yellow-200 rounded-full shadow-sm">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    Menunggu Konfirmasi Mitra
                                                </span>
                                            @endif
                                        @endif

                                        @if($sheep->is_pedigree)
                                            <span class="inline-block px-3 py-1 text-xs font-bold text-yellow-900 bg-yellow-500 rounded-full shadow-md">
                                                Bibit Unggul
                                            </span>
                                        @endif
                                        @if($activeSickness)
                                            <span class="{{ $activeSickness->badge_style }} inline-block px-3 py-1 text-xs font-bold rounded-full shadow-md animate-pulse">
                                                {{ $activeSickness->status }}: {{ Str::limit($activeSickness->diagnosis, 15) }}
                                            </span>
                                        @endif
                                        @if($activePregnancy)
                                            <span class="inline-block px-3 py-1 text-xs font-bold text-white bg-purple-600 border border-purple-400 rounded-full shadow-md">
                                                HAMIL (HPL: {{ \Carbon\Carbon::parse($activePregnancy->expected_delivery_date)->diffForHumans() }})
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex space-x-2">
                                    {{-- Tombol PDF --}}
                                    <a href="{{ route('sheep.print', $sheep) }}" target="_blank" class="flex items-center px-3 py-1 text-sm font-bold text-white bg-red-600 border border-red-700 rounded hover:bg-red-700">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                        Cetak Kartu
                                    </a>

                                    <button onclick="openQrModal()" class="px-3 py-1 text-sm font-bold text-gray-700 bg-gray-100 border rounded hover:bg-gray-200">
                                        QR Code
                                    </button>

                                    <a href="{{ route('sheep.edit', $sheep) }}">
                                        <x-secondary-button>Edit Data</x-secondary-button>
                                    </a>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <div>
                                     <img
                                        class="object-cover w-full rounded-lg shadow-md"
                                        src="{{ $sheep->photo_path ? asset('storage/' . $sheep->photo_path) : 'https://placehold.co/600x450/e2e8f0/9ca3af?text=' . urlencode($sheep->tag_number) }}"
                                        alt="Foto Domba {{ $sheep->tag_number }}"
                                        onerror="this.onerror=null; this.src='https://placehold.co/600x450/e2e8f0/9ca3af?text=Image+Not+Found';"
                                    >
                                </div>
                                <div class="space-y-3">
                                    {{-- UPDATE INFO LOKASI/KANDANG --}}
                                    <p>
                                        <strong>Lokasi:</strong>
                                        @if($sheep->placement_status === 'Partner' && $sheep->partner_id)
                                            <span class="font-semibold text-indigo-700">{{ $sheep->partner->name }}</span>
                                            @if($sheep->shelter)
                                                <span class="text-gray-600">({{ $sheep->shelter->name }})</span>
                                            @else
                                                <span class="italic text-gray-400">(Belum masuk kandang)</span>
                                            @endif
                                        @elseif($sheep->placement_status === 'Partner')
                                            <span class="italic text-yellow-600">Sedang proses penempatan ke Mitra...</span>
                                        @else
                                            {{ $sheep->shelter->name ?? 'Belum ada kandang' }}
                                        @endif
                                    </p>

                                    <p><strong>Jenis Kelamin:</strong> {{ $sheep->gender }}</p>
                                    <p><strong>Bobot Lahir:</strong> {{ $sheep->birth_weight ? $sheep->birth_weight . ' kg' : '-' }}</p>
                                    <p><strong>Umur:</strong> {{ $sheep->date_of_birth->diffForHumans(null, true) }} (Lahir: {{ $sheep->date_of_birth->format('d M Y') }})</p>
                                    <p><strong>Kategori:</strong> {{ $sheep->category }}</p>
                                    <p><strong>Tipe/Ras:</strong> {{ $sheep->type }}</p>
                                    <hr>
                                    <p><strong>Harga Beli:</strong> Rp {{ number_format($sheep->purchase_price, 0, ',', '.') }}</p>
                                    <p><strong>Deskripsi:</strong> {{ $sheep->description ?? '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 2. RIWAYAT REPRODUKSI (KHUSUS BETINA) --}}
                    @if($sheep->gender === 'Betina')
                        <div class="overflow-hidden bg-white border-l-4 border-purple-500 shadow-sm sm:rounded-lg">
                            <div class="p-6 text-gray-900">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-bold text-purple-900">Riwayat Reproduksi</h3>
                                    <a href="{{ route('sheep.reproduction-records.create', $sheep) }}">
                                        <x-primary-button class="text-xs bg-purple-600 hover:bg-purple-700">+ Catat Kawin</x-primary-button>
                                    </a>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-purple-50">
                                            <tr>
                                                <th class="px-4 py-2 text-xs font-medium text-left text-gray-500 uppercase">Tgl Kawin</th>
                                                <th class="px-4 py-2 text-xs font-medium text-left text-gray-500 uppercase">Pejantan</th>
                                                <th class="px-4 py-2 text-xs font-medium text-left text-gray-500 uppercase">Keterangan Tanggal</th>
                                                <th class="px-4 py-2 text-xs font-medium text-left text-gray-500 uppercase">Status</th>
                                                <th class="px-4 py-2 text-xs font-medium text-right text-gray-500 uppercase">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            @forelse($sheep->asDamReproductionRecords()->with('sire')->orderBy('mating_date', 'desc')->orderBy('id', 'desc')->get() as $repro)
                                                <tr>
                                                    <td class="px-4 py-2 text-sm text-gray-900">{{ \Carbon\Carbon::parse($repro->mating_date)->format('d M Y') }}</td>
                                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $repro->sire->tag_number ?? '-' }}</td>
                                                    <td class="px-4 py-2 text-sm text-gray-900">
                                                        @if($repro->status == 'Delivered' && $repro->actual_delivery_date)
                                                            <span class="font-bold text-green-700">Lahir: {{ $repro->actual_delivery_date->format('d M Y') }}</span>
                                                            <br><span class="text-xs text-gray-500">({{ $repro->offspring_count }} anak)</span>
                                                        @elseif($repro->status == 'Pregnant')
                                                            HPL: {{ $repro->expected_delivery_date ? $repro->expected_delivery_date->format('d M Y') : '-' }}
                                                            <br><span class="text-xs font-bold text-purple-600">({{ $repro->expected_delivery_date ? $repro->expected_delivery_date->diffForHumans() : '' }})</span>
                                                        @else
                                                            HPL: {{ $repro->expected_delivery_date ? $repro->expected_delivery_date->format('d M Y') : '-' }}
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-2 text-sm">
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $repro->badge_style }}">
                                                            {{ $repro->status }}
                                                        </span>
                                                    </td>
                                                    <td class="px-4 py-2 text-sm text-right">
                                                        <div class="flex justify-end space-x-2">
                                                            <a href="{{ route('reproduction-records.edit', $repro) }}" class="font-bold text-indigo-600 hover:text-indigo-900">Update</a>
                                                            <form action="{{ route('reproduction-records.destroy', $repro) }}" method="POST" onsubmit="return confirm('Hapus data ini?');" class="inline">
                                                                @csrf @method('DELETE')
                                                                <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="px-4 py-4 text-sm text-center text-gray-500">Belum ada riwayat reproduksi.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- 3. RIWAYAT TIMBANGAN --}}
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-bold">Riwayat Pertumbuhan (Timbangan)</h3>
                                <a href="{{ route('sheep.weights.create', $sheep) }}">
                                    <x-primary-button class="text-xs">+ Timbang</x-primary-button>
                                </a>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-xs font-medium text-left text-gray-500 uppercase">Tanggal</th>
                                            <th class="px-4 py-2 text-xs font-medium text-left text-gray-500 uppercase">Berat (KG)</th>
                                            <th class="px-4 py-2 text-xs font-medium text-left text-gray-500 uppercase">Catatan</th>
                                            <th class="px-4 py-2 text-xs font-medium text-right text-gray-500 uppercase">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @forelse($weightRecords as $record)
                                            <tr>
                                                <td class="px-4 py-2 text-sm text-gray-900">{{ \Carbon\Carbon::parse($record->weighing_date)->format('d M Y') }}</td>
                                                <td class="px-4 py-2 text-sm font-bold text-gray-900">{{ $record->weight }} kg</td>
                                                <td class="px-4 py-2 text-sm text-gray-500">{{ $record->notes ?? '-' }}</td>
                                                <td class="px-4 py-2 text-sm text-right">
                                                    <form action="{{ route('weights.destroy', $record) }}" method="POST" onsubmit="return confirm('Hapus data timbangan ini?');" class="inline">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="px-4 py-4 text-sm text-center text-gray-500">Belum ada data timbangan.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4">{{ $weightRecords->links() }}</div>
                        </div>
                    </div>

                    {{-- 4. SILSILAH & KELUARGA --}}
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <h3 class="mb-4 text-lg font-bold text-center">Silsilah & Keluarga</h3>

                            @php
                                $children = $sheep->gender === 'Jantan' ? $sheep->offspringAsFather : $sheep->offspringAsMother;
                                $siblings = collect();
                                if ($sheep->father_id || $sheep->mother_id) {
                                    $siblings = \App\Models\Sheep::where('id', '!=', $sheep->id)
                                        ->where(function($query) use ($sheep) {
                                            if ($sheep->father_id) $query->orWhere('father_id', $sheep->father_id);
                                            if ($sheep->mother_id) $query->orWhere('mother_id', $sheep->mother_id);
                                        })
                                        ->get();
                                }
                            @endphp

                            {{-- TABEL ORANG TUA --}}
                            <h4 class="pb-1 mb-2 text-base font-semibold text-center border-b">Orang Tua</h4>
                            <div class="mb-6 overflow-x-auto border rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-xs font-medium text-left text-gray-500 uppercase">Peran</th>
                                            <th class="px-4 py-2 text-xs font-medium text-left text-gray-500 uppercase">Eartag</th>
                                            <th class="px-4 py-2 text-xs font-medium text-left text-gray-500 uppercase">Jenis Kelamin</th>
                                            <th class="px-4 py-2 text-xs font-medium text-left text-gray-500 uppercase">Tanggal Lahir</th>
                                            <th class="px-4 py-2 text-xs font-medium text-left text-gray-500 uppercase">Ibu</th>
                                            <th class="px-4 py-2 text-xs font-medium text-left text-gray-500 uppercase">Bapak</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr>
                                            <td class="px-4 py-2 text-sm font-semibold text-purple-700">Ibu (Dam)</td>
                                            <td class="px-4 py-2 text-sm font-bold text-indigo-600">
                                                @if($sheep->mother) <a href="{{ route('sheep.show', $sheep->mother) }}">{{ $sheep->mother->tag_number }}</a> @else - @endif
                                            </td>
                                            <td class="px-4 py-2 text-sm text-gray-500">Betina</td>
                                            <td class="px-4 py-2 text-sm text-gray-500">{{ $sheep->mother ? $sheep->mother->date_of_birth->format('Y-m-d') : '-' }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-500">{{ $sheep->mother?->mother?->tag_number ?? '-' }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-500">{{ $sheep->mother?->father?->tag_number ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="px-4 py-2 text-sm font-semibold text-blue-700">Bapak (Sire)</td>
                                            <td class="px-4 py-2 text-sm font-bold text-indigo-600">
                                                @if($sheep->father) <a href="{{ route('sheep.show', $sheep->father) }}">{{ $sheep->father->tag_number }}</a> @else - @endif
                                            </td>
                                            <td class="px-4 py-2 text-sm text-gray-500">Jantan</td>
                                            <td class="px-4 py-2 text-sm text-gray-500">{{ $sheep->father ? $sheep->father->date_of_birth->format('Y-m-d') : '-' }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-500">{{ $sheep->father?->mother?->tag_number ?? '-' }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-500">{{ $sheep->father?->father?->tag_number ?? '-' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            {{-- TABEL SAUDARA --}}
                            <h4 class="pb-1 mb-2 text-base font-semibold text-center border-b">Saudara</h4>
                            <div class="mb-6 overflow-x-auto border rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-xs font-medium text-left text-gray-500 uppercase">Eartag</th>
                                            <th class="px-4 py-2 text-xs font-medium text-left text-gray-500 uppercase">Jenis Kelamin</th>
                                            <th class="px-4 py-2 text-xs font-medium text-left text-gray-500 uppercase">Tanggal Lahir</th>
                                            <th class="px-4 py-2 text-xs font-medium text-left text-gray-500 uppercase">Ibu</th>
                                            <th class="px-4 py-2 text-xs font-medium text-left text-gray-500 uppercase">Bapak</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @forelse($siblings as $sib)
                                            <tr>
                                                <td class="px-4 py-2 text-sm font-bold text-indigo-600"><a href="{{ route('sheep.show', $sib) }}">{{ $sib->tag_number }}</a></td>
                                                <td class="px-4 py-2 text-sm text-gray-500">{{ $sib->gender }}</td>
                                                <td class="px-4 py-2 text-sm text-gray-500">{{ $sib->date_of_birth->format('Y-m-d') }}</td>
                                                <td class="px-4 py-2 text-sm text-gray-500">{{ $sib->mother?->tag_number ?? '-' }}</td>
                                                <td class="px-4 py-2 text-sm text-gray-500">{{ $sib->father?->tag_number ?? '-' }}</td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="5" class="px-4 py-4 text-sm text-center text-gray-500">Tidak ada data saudara.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            {{-- TABEL ANAK --}}
                            <h4 class="pb-1 mb-2 text-base font-semibold text-center border-b">Anak (Offspring)</h4>
                            <div class="overflow-x-auto border rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-xs font-medium text-left text-gray-500 uppercase">Eartag</th>
                                            <th class="px-4 py-2 text-xs font-medium text-left text-gray-500 uppercase">Jenis Kelamin</th>
                                            <th class="px-4 py-2 text-xs font-medium text-left text-gray-500 uppercase">Tanggal Lahir</th>
                                            <th class="px-4 py-2 text-xs font-medium text-left text-gray-500 uppercase">Ibu</th>
                                            <th class="px-4 py-2 text-xs font-medium text-left text-gray-500 uppercase">Bapak</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @forelse($children as $child)
                                            <tr>
                                                <td class="px-4 py-2 text-sm font-bold text-indigo-600"><a href="{{ route('sheep.show', $child) }}">{{ $child->tag_number }}</a></td>
                                                <td class="px-4 py-2 text-sm text-gray-500">{{ $child->gender }}</td>
                                                <td class="px-4 py-2 text-sm text-gray-500">{{ $child->date_of_birth->format('Y-m-d') }}</td>
                                                <td class="px-4 py-2 text-sm text-gray-500">{{ $child->mother?->tag_number ?? '-' }}</td>
                                                <td class="px-4 py-2 text-sm text-gray-500">{{ $child->father?->tag_number ?? '-' }}</td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="5" class="px-4 py-4 text-sm text-center text-gray-500">Belum ada data anak.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- KOLOM KANAN --}}
                <div class="space-y-6 lg:col-span-1">

                    {{-- KARTU REKOMENDASI HARGA --}}
                    <div class="overflow-hidden bg-white border border-indigo-200 shadow-lg sm:rounded-lg">
                        <div class="p-6 bg-gradient-to-br from-indigo-50 to-white">
                            <h3 class="flex items-center mb-2 text-lg font-bold text-indigo-900">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Analisis Harga Jual
                            </h3>
                            <p class="mb-4 text-xs text-gray-500">Estimasi untung rugi berdasarkan modal.</p>

                            <div class="mb-4">
                                <span class="block text-sm font-medium text-gray-500 uppercase">Rekomendasi Harga</span>
                                <span class="block text-3xl font-extrabold text-green-600">
                                    Rp {{ number_format($priceData['rekomendasi'], 0, ',', '.') }}
                                </span>
                            </div>

                            <div class="pt-3 space-y-2 text-sm border-t">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Nilai Daging ({{ $priceData['berat_kg'] }}kg)</span>
                                    <span class="font-semibold">Rp {{ number_format($priceData['nilai_daging'], 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Total Modal (Beli+Biaya)</span>
                                    <span class="font-semibold text-red-500">Rp {{ number_format($priceData['total_modal'], 0, ',', '.') }}</span>
                                </div>
                                @if($priceData['bonus'] > 0)
                                    <div class="flex justify-between text-green-600">
                                        <span>Bonus Bibit Unggul</span>
                                        <span class="font-bold">+ Rp {{ number_format($priceData['bonus'], 0, ',', '.') }}</span>
                                    </div>
                                @endif
                                @if($priceData['penalti'] > 0)
                                    <div class="flex justify-between text-red-600">
                                        <span>Penalti Sakit</span>
                                        <span class="font-bold">- Rp {{ number_format($priceData['penalti'], 0, ',', '.') }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- STATUS KESEHATAN (Tidak Berubah) --}}
                    <div class="overflow-hidden bg-white border-t-4 border-red-500 shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <h3 class="mb-4 text-lg font-bold">Status Kesehatan</h3>
                            <a href="{{ route('sheep.health-records.create', $sheep) }}" class="block w-full px-4 py-2 mb-4 font-bold text-center text-white bg-red-600 rounded hover:bg-red-700">
                                + Lapor Sakit / Penanganan
                            </a>
                            <div class="space-y-4">
                                @forelse($healthRecords as $health)
                                    <div class="border rounded-md p-3 {{ $health->card_style }}">
                                        <div class="flex items-start justify-between">
                                            <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($health->record_date)->format('d M Y') }}</span>
                                            <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded {{ $health->badge_style }}">
                                                {{ $health->status }}
                                            </span>
                                        </div>
                                        <h4 class="mt-2 text-sm font-bold">{{ $health->diagnosis }}</h4>
                                        <div class="flex flex-wrap gap-1 mt-1">
                                            @foreach($health->symptoms as $sym)
                                                <span class="text-[10px] bg-white border border-gray-300 px-1 rounded text-gray-600">{{ $sym->name }}</span>
                                            @endforeach
                                        </div>
                                        <p class="mt-2 text-xs italic text-gray-600">
                                            "{{ Str::limit($health->treatment_details, 50) }}"
                                        </p>
                                        <div class="flex justify-end mt-2">
                                            <form action="{{ route('health-records.destroy', $health) }}" method="POST" onsubmit="return confirm('Hapus riwayat sakit ini?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-xs text-red-500 hover:text-red-700">Hapus</button>
                                            </form>
                                        </div>
                                    </div>
                                @empty
                                    <p class="py-4 text-sm italic text-center text-gray-500">Tidak ada riwayat penyakit. Domba sehat!</p>
                                @endforelse
                            </div>
                            <div class="mt-4 text-xs">{{ $healthRecords->links() }}</div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- MODAL POPUP QR CODE --}}
    <div id="qrModal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
        <div class="w-full max-w-sm p-6 text-center bg-white rounded-lg shadow-lg">
            <h3 class="mb-4 text-lg font-bold">QR Code Domba: {{ $sheep->tag_number }}</h3>

            {{-- Container QR Code --}}
            <div id="qr-container" class="flex justify-center p-4 mb-4 bg-white border border-gray-200 rounded">
                {{-- Generate QR Code (SVG) --}}
                {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(200)->generate(route('sheep.show', $sheep)) !!}
            </div>

            <p class="mb-4 text-sm text-gray-500">Scan untuk membuka profil domba ini.</p>

            {{-- Tombol Aksi Modal --}}
            <div class="flex justify-center gap-2">
                {{-- Tombol Download PNG --}}
                <button onclick="downloadQrImage()" class="flex items-center gap-1 px-4 py-2 text-white transition-colors bg-green-600 rounded hover:bg-green-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Download
                </button>

                {{-- Tombol Tutup --}}
                <button onclick="closeQrModal()" class="px-4 py-2 text-white transition-colors bg-indigo-600 rounded hover:bg-indigo-700">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    {{-- SCRIPT: MODAL & DOWNLOAD QR --}}
    <script>
        function openQrModal() {
            document.getElementById('qrModal').classList.remove('hidden');
        }
        function closeQrModal() {
            document.getElementById('qrModal').classList.add('hidden');
        }

        function downloadQrImage() {
            // Ambil elemen SVG dari dalam container
            const svg = document.querySelector('#qr-container svg');

            if (!svg) {
                alert('QR Code belum dimuat.');
                return;
            }

            // Buat Canvas untuk konversi
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            const img = new Image();

            // Serialisasi SVG ke String XML
            const data = (new XMLSerializer()).serializeToString(svg);

            // Buat Blob dari data SVG
            const svgBlob = new Blob([data], {type: 'image/svg+xml;charset=utf-8'});
            const url = URL.createObjectURL(svgBlob);

            img.onload = function() {
                // Canvas size: 200x200 for QR + extra space for text
                const qrSize = 200;
                const textHeight = 40;
                canvas.width = qrSize;
                canvas.height = qrSize + textHeight; // Increase height

                // Fill background white
                ctx.fillStyle = "white";
                ctx.fillRect(0, 0, canvas.width, canvas.height);

                // Draw QR Code
                ctx.drawImage(img, 0, 0, qrSize, qrSize);

                // Add Text (Eartag)
                ctx.font = "bold 20px Arial";
                ctx.fillStyle = "black";
                ctx.textAlign = "center";
                // Position text: center horizontal, below QR vertical
                ctx.fillText("{{ $sheep->tag_number }}", canvas.width / 2, qrSize + 25);

                // Download link
                const a = document.createElement('a');
                a.download = 'QR_Domba_{{ $sheep->tag_number }}.png';
                a.href = canvas.toDataURL('image/png');
                a.click();

                URL.revokeObjectURL(url);
            };

            img.src = url;
        }
    </script>
</x-app-layout>
