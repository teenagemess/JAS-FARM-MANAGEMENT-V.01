<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Edit Pemberian Pakan: ') . $feedingRecord->date->format('d M Y') }}
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

                    <form method="POST" action="{{ route('feeding-records.update', $feedingRecord) }}">
                        @csrf
                        @method('PUT')

                        {{-- HEADER RECORD --}}
                        <div class="grid grid-cols-1 gap-6 p-4 mb-8 border rounded-md md:grid-cols-3 bg-gray-50">
                            <div>
                                <x-input-label for="date" :value="__('Tanggal Pakan (Wajib)')" />
                                <x-text-input id="date" name="date" type="date" class="block w-full mt-1" value="{{ old('date', $feedingRecord->date->format('Y-m-d')) }}" required />
                            </div>
                            <div>
                                <x-input-label for="shelter_id" :value="__('Pilih Kandang (Wajib)')" />
                                <select id="shelter_id" name="shelter_id" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option value="">-- Pilih Kandang --</option>
                                    @foreach($shelters as $shelter)
                                        <option value="{{ $shelter->id }}" {{ old('shelter_id', $feedingRecord->shelter_id) == $shelter->id ? 'selected' : '' }}>
                                            {{ $shelter->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label :value="__('Petugas Pencatat')" />
                                <p class="mt-2 text-gray-600">{{ $feedingRecord->user->name }}</p>
                            </div>
                        </div>

                        {{-- WAKTU Pemberian --}}
                        <h3 class="mb-3 text-lg font-semibold">Waktu Pemberian</h3>
                        <div class="grid grid-cols-1 gap-6 mb-8 md:grid-cols-2">
                            <div>
                                <x-input-label for="time_morning" :value="__('Waktu Pagi (Opsional)')" />
                                {{-- PERBAIKAN: Gunakan Carbon::parse() --}}
                                <x-text-input id="time_morning" name="time_morning" type="time" class="block w-full mt-1"
                                    value="{{ old('time_morning', $feedingRecord->time_morning ? \Carbon\Carbon::parse($feedingRecord->time_morning)->format('H:i') : '') }}" />
                            </div>
                            <div>
                                <x-input-label for="time_evening" :value="__('Waktu Sore (Opsional)')" />
                                {{-- PERBAIKAN: Gunakan Carbon::parse() --}}
                                <x-text-input id="time_evening" name="time_evening" type="time" class="block w-full mt-1"
                                    value="{{ old('time_evening', $feedingRecord->time_evening ? \Carbon\Carbon::parse($feedingRecord->time_evening)->format('H:i') : '') }}" />
                            </div>
                        </div>

                        {{-- DETAIL JENIS & JUMLAH PAKAN --}}
                        <h3 class="mb-3 text-lg font-semibold">Porsi Pakan (Pagi vs Sore)</h3>
                        <div id="feed-details-container" class="p-4 mb-8 space-y-4 border rounded-md">

                            {{-- Template Item (Hidden) --}}
                            <div id="feed-item-template" class="grid items-center hidden grid-cols-7 gap-3 p-3 bg-white border rounded-md">
                                <div class="col-span-3">
                                    <label class="text-sm font-medium text-gray-700">Jenis Pakan</label>
                                    <select class="block w-full mt-1 border-gray-300 rounded-md shadow-sm feed-id-input" required disabled>
                                        <option value="">Pilih Pakan</option>
                                        @foreach($feedTypes as $feed)
                                            <option value="{{ $feed->id }}" data-unit="{{ $feed->unit }}">{{ $feed->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-span-1">
                                    <label class="text-sm font-medium text-gray-700">Pagi</label>
                                    <div class="flex mt-1">
                                        <input type="number" step="0.01" min="0" class="block w-full border-gray-300 shadow-sm feed-morning-input rounded-l-md" placeholder="0.00" disabled>
                                        <span class="inline-flex items-center px-2 text-xs text-gray-500 border border-l-0 border-gray-300 feed-unit-display-morning rounded-r-md bg-gray-50">KG</span>
                                    </div>
                                </div>
                                <div class="col-span-2">
                                    <label class="text-sm font-medium text-gray-700">Sore</label>
                                    <div class="flex mt-1">
                                        <input type="number" step="0.01" min="0" class="block w-full border-gray-300 shadow-sm feed-evening-input rounded-l-md" placeholder="0.00" disabled>
                                        <span class="inline-flex items-center px-2 text-xs text-gray-500 border border-l-0 border-gray-300 feed-unit-display-evening rounded-r-md bg-gray-50">KG</span>
                                    </div>
                                    <p class="text-[10px] text-gray-500 mt-1">Total: <span class="font-semibold total-quantity">0.00</span> KG</p>
                                </div>
                                <div class="col-span-1 text-right">
                                    <button type="button" onclick="removeFeedItem(this)" class="text-sm text-red-500 hover:text-red-700">Hapus</button>
                                </div>
                            </div>

                            {{-- Container Item --}}
                            <div id="feed-items-list" class="space-y-4">
                                {{-- Item akan diisi oleh JavaScript --}}
                            </div>

                            <button type="button" onclick="addFeedItem()" class="w-full px-4 py-2 mt-4 text-sm font-medium text-center text-indigo-700 border border-indigo-300 rounded-md shadow-sm bg-indigo-50 hover:bg-indigo-100">
                                + Tambah Jenis Pakan
                            </button>
                            <p class="mt-2 text-xs text-red-500" id="feed-error" style="display:none;">Mohon tambahkan minimal satu jenis pakan.</p>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('feeding-records.index') }}" class="mr-4 text-sm text-gray-600 hover:text-gray-900">
                                Batal
                            </a>
                            <x-primary-button>
                                {{ __('Update Pencatatan Pakan') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <script>
        let feedItemCount = 0;

        // Data existing dari Controller (JSON)
        const existingFeeds = @json($feedingRecord->feedTypes);

        function updateTotal(itemDiv) {
            const morningInput = itemDiv.querySelector('.feed-morning-input');
            const eveningInput = itemDiv.querySelector('.feed-evening-input');
            const totalSpan = itemDiv.querySelector('.total-quantity');

            const morning = parseFloat(morningInput.value) || 0;
            const evening = parseFloat(eveningInput.value) || 0;
            const total = (morning + evening).toFixed(2);

            totalSpan.textContent = total;
        }

        function updateUnitDisplay(selectElement) {
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            const unit = selectedOption.getAttribute('data-unit') || 'KG';

            const itemDiv = selectElement.closest('.grid');
            const unitDisplayMorning = itemDiv.querySelector('.feed-unit-display-morning');
            const unitDisplayEvening = itemDiv.querySelector('.feed-unit-display-evening');
            const totalDisplay = itemDiv.querySelector('.total-quantity');

            if (unitDisplayMorning) unitDisplayMorning.textContent = unit;
            if (unitDisplayEvening) unitDisplayEvening.textContent = unit;
            if (totalDisplay && totalDisplay.parentElement) {
                const parent = totalDisplay.parentElement;
                parent.innerHTML = `Total: <span class="font-semibold total-quantity">${totalDisplay.textContent}</span> ${unit}`;
            }
        }

        function addFeedItem(data = null) {
            const template = document.getElementById('feed-item-template');
            const clone = template.cloneNode(true);

            const currentCount = feedItemCount++;
            clone.id = `feed-item-${currentCount}`;
            clone.classList.remove('hidden');

            const feedIdInput = clone.querySelector('.feed-id-input');
            const morningInput = clone.querySelector('.feed-morning-input');
            const eveningInput = clone.querySelector('.feed-evening-input');

            feedIdInput.removeAttribute('disabled');
            morningInput.removeAttribute('disabled');
            eveningInput.removeAttribute('disabled');

            feedIdInput.name = `feed_types[${currentCount}][id]`;
            morningInput.name = `feed_types[${currentCount}][morning]`;
            eveningInput.name = `feed_types[${currentCount}][evening]`;

            feedIdInput.addEventListener('change', (e) => updateUnitDisplay(e.target));
            morningInput.addEventListener('input', () => updateTotal(clone));
            eveningInput.addEventListener('input', () => updateTotal(clone));

            // JIKA ADA DATA (EDIT MODE), ISI NILAINYA
            if (data) {
                feedIdInput.value = data.id;
                morningInput.value = data.pivot.quantity_morning;
                eveningInput.value = data.pivot.quantity_evening;
            }

            document.getElementById('feed-items-list').appendChild(clone);

            updateUnitDisplay(feedIdInput);
            updateTotal(clone);

            document.getElementById('feed-error').style.display = 'none';
        }

        function removeFeedItem(button) {
            const itemDiv = button.closest('.grid');
            itemDiv.remove();
            if (document.getElementById('feed-items-list').childElementCount === 0) {
                document.getElementById('feed-error').style.display = 'block';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Muat data yang sudah ada
            if (existingFeeds && existingFeeds.length > 0) {
                existingFeeds.forEach(feed => {
                    addFeedItem(feed);
                });
            } else {
                // Jika kosong (misal error validasi redirect), tampilkan pesan error atau 1 baris kosong
                if (document.getElementById('feed-items-list').childElementCount === 0) {
                    document.getElementById('feed-error').style.display = 'block';
                }
            }
        });
    </script>
</x-app-layout>
