<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Pencatatan Pemberian Pakan Harian') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if(session('error'))
                        <div class="p-4 mb-4 text-red-700 bg-red-100 rounded-md">{{ session('error') }}</div>
                    @endif
                    @if ($errors->any())
                        <div class="p-4 mb-4 text-red-700 bg-red-100 rounded-md">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('feeding-records.store') }}">
                        @csrf

                        {{-- HEADER RECORD --}}
                        <div class="grid grid-cols-1 gap-6 p-4 mb-8 border rounded-md md:grid-cols-3 bg-gray-50">
                            <div>
                                <x-input-label for="date" :value="__('Tanggal Pakan (Wajib)')" />
                                <x-text-input id="date" name="date" type="date" class="block w-full mt-1" value="{{ old('date', date('Y-m-d')) }}" required />
                            </div>
                            <div>
                                <x-input-label for="shelter_id" :value="__('Pilih Kandang (Wajib)')" />
                                <select id="shelter_id" name="shelter_id" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option value="">-- Pilih Kandang --</option>
                                    @foreach($shelters as $shelter)
                                        <option value="{{ $shelter->id }}" {{ old('shelter_id') == $shelter->id ? 'selected' : '' }}>
                                            {{ $shelter->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label :value="__('Petugas Pencatat')" />
                                <p class="mt-2 text-gray-600">{{ Auth::user()->name }}</p>
                            </div>
                        </div>

                        {{-- WAKTU Pemberian --}}
                        <h3 class="mb-3 text-lg font-semibold">Waktu Pemberian</h3>
                        <div class="grid grid-cols-1 gap-6 mb-8 md:grid-cols-2">
                            <div>
                                <x-input-label for="time_morning" :value="__('Waktu Pagi (Opsional)')" />
                                <x-text-input id="time_morning" name="time_morning" type="time" class="block w-full mt-1" value="{{ old('time_morning') }}" />
                            </div>
                            <div>
                                <x-input-label for="time_evening" :value="__('Waktu Sore (Opsional)')" />
                                <x-text-input id="time_evening" name="time_evening" type="time" class="block w-full mt-1" value="{{ old('time_evening') }}" />
                                <p class="mt-1 text-xs text-gray-500">Waktu sore harus setelah waktu pagi.</p>
                            </div>
                        </div>

                        {{-- DETAIL JENIS & JUMLAH PAKAN (DYNAMIC FIELDS) --}}
                        <h3 class="mb-3 text-lg font-semibold">Porsi Pakan (Pagi vs Sore)</h3>
                        <div id="feed-details-container" class="p-4 mb-8 space-y-4 border rounded-md">

                            {{-- Template untuk item pakan --}}
                            <div id="feed-item-template" class="grid items-center hidden grid-cols-7 gap-3 p-3 bg-white border rounded-md">
                                <div class="col-span-3">
                                    <label class="text-sm font-medium text-gray-700">Jenis Pakan</label>
                                    {{-- Tambahkan disabled agar tidak divalidasi/dikirim saat tersembunyi --}}
                                    <select class="block w-full mt-1 border-gray-300 rounded-md shadow-sm feed-id-input" required disabled>
                                        <option value="">Pilih Pakan</option>
                                        @foreach($feedTypes as $feed)
                                            {{-- PERUBAHAN: Menampilkan Harga dan Satuan di Label Dropdown --}}
                                            <option value="{{ $feed->id }}" data-unit="{{ $feed->unit }}">
                                                {{ $feed->name }} (Rp {{ number_format($feed->price_per_unit, 0, ',', '.') }} / {{ $feed->unit }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Porsi Pagi --}}
                                <div class="col-span-1">
                                    <label class="text-sm font-medium text-gray-700">Pagi</label>
                                    <div class="flex mt-1">
                                        {{-- Tambahkan disabled --}}
                                        <input type="number" step="0.01" min="0" class="block w-full border-gray-300 shadow-sm feed-morning-input rounded-l-md" placeholder="0.00" disabled>
                                        <span class="inline-flex items-center px-2 text-xs text-gray-500 border border-l-0 border-gray-300 feed-unit-display-morning rounded-r-md bg-gray-50">
                                            KG
                                        </span>
                                    </div>
                                </div>

                                {{-- Porsi Sore --}}
                                <div class="col-span-2">
                                    <label class="text-sm font-medium text-gray-700">Sore</label>
                                    <div class="flex mt-1">
                                        {{-- Tambahkan disabled --}}
                                        <input type="number" step="0.01" min="0" class="block w-full border-gray-300 shadow-sm feed-evening-input rounded-l-md" placeholder="0.00" disabled>
                                        <span class="inline-flex items-center px-2 text-xs text-gray-500 border border-l-0 border-gray-300 feed-unit-display-evening rounded-r-md bg-gray-50">
                                            KG
                                        </span>
                                    </div>
                                    <p class="text-[10px] text-gray-500 mt-1">Total: <span class="font-semibold total-quantity">0.00</span> <span class="unit-label">KG</span></p>
                                </div>

                                <div class="col-span-1 text-right">
                                    <button type="button" onclick="removeFeedItem(this)" class="text-sm text-red-500 hover:text-red-700">Hapus</button>
                                </div>
                            </div>

                            {{-- Container untuk menampung item yang sudah ditambahkan --}}
                            <div id="feed-items-list" class="space-y-4">
                                {{-- Item pakan akan ditambahkan di sini --}}

                                {{-- Tampilkan error khusus untuk field dinamis --}}
                                @error('feed_types')
                                    <p class="mt-2 text-sm text-red-500">Anda wajib mengisi kuantitas (pagi atau sore) minimal pada satu jenis pakan.</p>
                                @enderror
                                @foreach ($errors->keys() as $key)
                                    @if (str_starts_with($key, 'feed_types.'))
                                        <p class="mt-2 text-sm text-red-500">⚠️ Error pada input pakan: {{ $errors->first($key) }}</p>
                                    @endif
                                @endforeach
                            </div>

                            <button type="button" onclick="addFeedItem()" class="w-full px-4 py-2 mt-4 text-sm font-medium text-center text-indigo-700 border border-indigo-300 rounded-md shadow-sm bg-indigo-50 hover:bg-indigo-100">
                                + Tambah Jenis Pakan
                            </button>
                            <p class="mt-2 text-xs text-red-500" id="feed-error" style="display:none;">Mohon tambahkan minimal satu jenis pakan.</p>

                        </div>


                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('dashboard') }}" class="mr-4 text-sm text-gray-600 hover:text-gray-900">
                                Batal
                            </a>
                            <x-primary-button>
                                {{ __('Simpan Pencatatan Pakan') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <script>
        let feedItemCount = 0;

        // Helper function to update total display
        function updateTotal(itemDiv) {
            const morningInput = itemDiv.querySelector('.feed-morning-input');
            const eveningInput = itemDiv.querySelector('.feed-evening-input');
            const totalSpan = itemDiv.querySelector('.total-quantity');

            // Menggunakan toFixed(2) untuk menghindari masalah floating point
            const morning = parseFloat(morningInput.value) || 0;
            const evening = parseFloat(eveningInput.value) || 0;
            const total = (morning + evening).toFixed(2);

            totalSpan.textContent = total;
        }

        function updateUnitDisplay(selectElement) {
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            // Ambil data unit dari atribut data-unit pada option yang dipilih
            const unit = selectedOption.getAttribute('data-unit') || 'KG';

            const itemDiv = selectElement.closest('.grid');
            const unitDisplayMorning = itemDiv.querySelector('.feed-unit-display-morning');
            const unitDisplayEvening = itemDiv.querySelector('.feed-unit-display-evening');
            const totalDisplay = itemDiv.querySelector('.total-quantity');

            // Update label unit
            if (unitDisplayMorning) unitDisplayMorning.textContent = unit;
            if (unitDisplayEvening) unitDisplayEvening.textContent = unit;

            // Update unit text next to total
            const unitLabel = itemDiv.querySelector('.unit-label');
            if (unitLabel) unitLabel.textContent = unit;
        }

        function addFeedItem() {
            const template = document.getElementById('feed-item-template');
            const clone = template.cloneNode(true);

            // PERBAIKAN PENTING: Gunakan feedItemCount UNTUK ID/NAME dan *kemudian* tingkatkan nilainya
            const currentCount = feedItemCount++;

            clone.id = `feed-item-${currentCount}`;
            clone.classList.remove('hidden');

            const feedIdInput = clone.querySelector('.feed-id-input');
            const morningInput = clone.querySelector('.feed-morning-input');
            const eveningInput = clone.querySelector('.feed-evening-input');

            // HAPUS DISABLED AGAR INPUT BISA DIKIRIM & DVALIDASI
            feedIdInput.removeAttribute('disabled');
            morningInput.removeAttribute('disabled');
            eveningInput.removeAttribute('disabled');

            // Atur nama input agar dikirim sebagai array ke Laravel
            feedIdInput.name = `feed_types[${currentCount}][id]`;
            morningInput.name = `feed_types[${currentCount}][morning]`;
            eveningInput.name = `feed_types[${currentCount}][evening]`;

            // Atur event listener untuk update unit dan total
            feedIdInput.addEventListener('change', (e) => updateUnitDisplay(e.target));
            morningInput.addEventListener('input', () => updateTotal(clone));
            eveningInput.addEventListener('input', () => updateTotal(clone));

            // Tambahkan item ke list
            document.getElementById('feed-items-list').appendChild(clone);

            // Set unit display awal berdasarkan pilihan pertama
            updateUnitDisplay(feedIdInput);
            updateTotal(clone); // Hitung total awal (0)

            // feedItemCount sudah bertambah di awal fungsi (currentCount = feedItemCount++)
            document.getElementById('feed-error').style.display = 'none'; // Sembunyikan error jika ada item

            // Scroll ke item yang baru ditambahkan
            clone.scrollIntoView({ behavior: 'smooth' });
        }

        function removeFeedItem(button) {
            const itemDiv = button.closest('.grid');
            itemDiv.remove();

            // Tampilkan error jika tidak ada item tersisa
            if (document.getElementById('feed-items-list').childElementCount === 0) {
                document.getElementById('feed-error').style.display = 'block';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Logika untuk mengisi ulang OLD data (jika validasi gagal) bisa ditambahkan di sini
            if (document.getElementById('feed-items-list').childElementCount === 0) {
                document.getElementById('feed-error').style.display = 'block';
            }
        });
    </script>
</x-app-layout>
