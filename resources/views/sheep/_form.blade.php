{{--
    PARTIAL FORM DOMBA
    Digunakan oleh create.blade.php dan edit.blade.php
--}}

{{-- (1) Baris 1: Eartag, Kategori, Tipe --}}
<div class="grid grid-cols-1 gap-6 md:grid-cols-3">
    <div>
        <x-input-label for="tag_number" :value="__('Eartag (Wajib)')" />
        <div class="flex mt-1 rounded-md shadow-sm">
            <span class="inline-flex items-center px-3 text-gray-500 border border-r-0 border-gray-300 rounded-l-md bg-gray-50 sm:text-sm">
                JAS-
            </span>
            <x-text-input id="tag_number" class="flex-1 block w-full rounded-none rounded-r-md" type="text" name="tag_number" :value="old('tag_number', $tagSuffix ?? '')" required autofocus placeholder="001" />
        </div>
        <x-input-error :messages="$errors->get('tag_number')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="category" :value="__('Kategori (Wajib)')" />
        <x-text-input id="category" class="block w-full mt-1" type="text" name="category" :value="old('category', $sheep->category ?? '')" required placeholder="Contoh: Pedaging" />
        <x-input-error :messages="$errors->get('category')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="type" :value="__('Tipe/Ras (Wajib)')" />
        <x-text-input id="type" class="block w-full mt-1" type="text" name="type" :value="old('type', $sheep->type ?? '')" required placeholder="Contoh: Garut" />
        <x-input-error :messages="$errors->get('type')" class="mt-2" />
    </div>
</div>

{{-- (2) Baris 2: LOKASI & KEMITRAAN (LOGIKA BARU) --}}
<div class="grid grid-cols-1 gap-6 p-4 mt-4 border rounded-md md:grid-cols-3 bg-gray-50"
     x-data="{ status: '{{ $isPartner ? 'Internal' : old('placement_status', $sheep->placement_status ?? 'Internal') }}' }">

    {{-- Pilihan Status (HANYA MUNCUL UNTUK ADMIN) --}}
    @if(!$isPartner)
        <div class="md:col-span-3">
            <label class="block mb-2 text-sm font-medium text-gray-700">Status Penempatan</label>
            <div class="flex gap-4">
                <label class="inline-flex items-center">
                    <input type="radio" name="placement_status" value="Internal" x-model="status" class="text-indigo-600 border-gray-300 focus:ring-indigo-500">
                    <span class="ml-2 text-gray-700">Internal (Kandang Sendiri)</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="radio" name="placement_status" value="Partner" x-model="status" class="text-indigo-600 border-gray-300 focus:ring-indigo-500">
                    <span class="ml-2 text-gray-700">Mitra (Titip Ternak / Gaduh)</span>
                </label>
            </div>
            <x-input-error :messages="$errors->get('placement_status')" class="mt-2" />
        </div>
    @else
        {{-- UNTUK MITRA: Otomatis set status Internal (agar validasi kandang jalan) --}}
        <input type="hidden" name="placement_status" value="Internal">
    @endif

    {{-- Dropdown Kandang (Muncul jika Internal / Selalu Muncul utk Mitra) --}}
    <div x-show="status === 'Internal'">
        <x-input-label for="shelter_id" :value="__('Pilih Kandang (Wajib)')" />
        <select id="shelter_id" name="shelter_id" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" onchange="if(typeof checkCapacity === 'function') checkCapacity(this.value)">
            <option value="">-- Pilih Kandang --</option>
            @foreach($shelters as $shelter)
                <option value="{{ $shelter->id }}" {{ old('shelter_id', $sheep->shelter_id ?? '') == $shelter->id ? 'selected' : '' }}>
                    {{ $shelter->name }}
                </option>
            @endforeach
        </select>
        <div id="capacity-info" class="hidden mt-2 text-sm"><span id="capacity-text"></span></div>
        <x-input-error :messages="$errors->get('shelter_id')" class="mt-2" />
    </div>

    {{-- Dropdown Mitra (HANYA MUNCUL UNTUK ADMIN jika pilih Partner) --}}
    @if(!$isPartner)
        <div x-show="status === 'Partner'" style="display: none;">
            <x-input-label for="partner_id" :value="__('Pilih Mitra (Wajib)')" />
            <select id="partner_id" name="partner_id" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">-- Pilih Nama Mitra --</option>
                @foreach($partners as $partner)
                    <option value="{{ $partner->id }}" {{ old('partner_id', $sheep->partner_id ?? '') == $partner->id ? 'selected' : '' }}>
                        {{ $partner->name }}
                    </option>
                @endforeach
            </select>
            <p class="mt-1 text-xs text-gray-500">Domba akan tercatat di lokasi mitra ini.</p>
            <x-input-error :messages="$errors->get('partner_id')" class="mt-2" />
        </div>
    @endif

</div>

{{-- (3) Baris 3: Fisik --}}
<div class="grid grid-cols-1 gap-6 mt-4 md:grid-cols-3">
    <div>
        <x-input-label for="gender" :value="__('Jenis Kelamin (Wajib)')" />
        <select id="gender" name="gender" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
            <option value="Jantan" {{ old('gender', $sheep->gender ?? '') == 'Jantan' ? 'selected' : '' }}>Jantan</option>
            <option value="Betina" {{ old('gender', $sheep->gender ?? '') == 'Betina' ? 'selected' : '' }}>Betina</option>
        </select>
        <x-input-error :messages="$errors->get('gender')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="date_of_birth" :value="__('Tgl Lahir (Wajib)')" />
        <x-text-input id="date_of_birth" class="block w-full mt-1" type="date" name="date_of_birth" :value="old('date_of_birth', isset($sheep->date_of_birth) ? $sheep->date_of_birth->format('Y-m-d') : '')" required />
        <x-input-error :messages="$errors->get('date_of_birth')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="birth_weight" :value="__('Bobot Lahir (KG)')" />
        <x-text-input id="birth_weight" class="block w-full mt-1" type="number" step="0.01" name="birth_weight" :value="old('birth_weight', $sheep->birth_weight ?? '')" />
        <x-input-error :messages="$errors->get('birth_weight')" class="mt-2" />
    </div>
</div>

{{-- (4) Baris 4: Silsilah --}}
<div class="grid grid-cols-1 gap-6 mt-4 md:grid-cols-2">
    <div>
        <x-input-label for="mother_id" :value="__('Induk Betina (Opsional)')" />
        <select id="mother_id" name="mother_id" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">Pilih Induk (Jika diketahui)</option>
            @foreach($potential_dams as $dam)
                <option value="{{ $dam->id }}" {{ old('mother_id', $sheep->mother_id ?? '') == $dam->id ? 'selected' : '' }}>
                    Eartag: {{ $dam->tag_number }}
                </option>
            @endforeach
        </select>
    </div>
    <div>
        <x-input-label for="father_id" :value="__('Induk Pejantan (Opsional)')" />
        <select id="father_id" name="father_id" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">Pilih Pejantan (Jika diketahui)</option>
            @foreach($potential_sires as $sire)
                <option value="{{ $sire->id }}" {{ old('father_id', $sheep->father_id ?? '') == $sire->id ? 'selected' : '' }}>
                    Eartag: {{ $sire->tag_number }}
                </option>
            @endforeach
        </select>
    </div>
</div>

{{-- (5) Baris 5: Harga & Bibit Unggul --}}
<div class="grid grid-cols-1 gap-6 mt-4 md:grid-cols-2">
    <div>
        <x-input-label for="purchase_price" :value="__('Harga Beli (Opsional)')" />
        <x-text-input id="purchase_price" class="block w-full mt-1" type="number" name="purchase_price" :value="old('purchase_price', $sheep->purchase_price ?? '')" />
    </div>
    <div class="flex items-center mt-6">
        <input type="hidden" name="is_pedigree" value="0">
        <input id="is_pedigree" name="is_pedigree" type="checkbox" value="1"
               {{ (old('is_pedigree', $sheep->is_pedigree ?? 0) == 1) ? 'checked' : '' }}
               class="text-indigo-600 border-gray-300 rounded shadow-sm focus:ring-indigo-500">
        <label for="is_pedigree" class="ml-2 text-sm text-gray-600">Bibit Unggul (Pedigree)</label>
    </div>
</div>

{{-- (6) Baris 6: Foto --}}
<div class="mt-4">
    <x-input-label for="photo" :value="__('Foto Domba (Opsional)')" />

    @if(isset($sheep) && $sheep->photo_path)
        <div class="mt-2 mb-2">
            <img src="{{ asset('storage/' . $sheep->photo_path) }}" alt="Foto saat ini" class="object-cover w-32 h-32 rounded-md">
            <p class="mt-1 text-xs text-gray-500">Foto saat ini (Upload baru untuk mengganti)</p>
        </div>
    @endif

    <x-text-input id="photo" class="block w-full p-2 mt-1 border" type="file" name="photo" />
    <x-input-error :messages="$errors->get('photo')" class="mt-2" />
</div>

{{-- (7) Baris 7: Deskripsi --}}
<div class="mt-4">
    <x-input-label for="description" :value="__('Deskripsi/Catatan (Opsional)')" />
    <textarea id="description" name="description" rows="4"
              class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $sheep->description ?? '') }}</textarea>
</div>

{{-- Tombol Submit --}}
<div class="flex items-center justify-end mt-6">
    <a href="{{ route('sheep.index') }}" class="mr-4 text-sm text-gray-600 hover:text-gray-900">
        Batal
    </a>
    <x-primary-button>
        {{ isset($sheep) ? __('Simpan Perubahan') : __('Simpan Data Domba') }}
    </x-primary-button>
</div>

{{-- JAVASCRIPT --}}
<script>
    function checkCapacity(shelterId) {
        // ... (Kode Javascript cek kapasitas yang lama tetap dipakai di sini) ...
        const infoDiv = document.getElementById('capacity-info');
        const infoText = document.getElementById('capacity-text');
        if (!shelterId) { infoDiv.classList.add('hidden'); return; }
        infoDiv.classList.remove('hidden');
        infoText.innerHTML = 'Memuat data...';

        fetch(`/shelters/${shelterId}/capacity`)
            .then(response => response.json())
            .then(data => {
                const isFull = data.remaining <= 0;
                const colorClass = isFull ? 'text-red-600 font-bold' : 'text-green-600';
                infoText.innerHTML = `Terisi: ${data.current}/${data.capacity} Ekor <span class="${colorClass}"> (Sisa: ${data.remaining})</span>${isFull ? '<br>⚠️ Kandang Penuh!' : ''}`;
            });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const initialShelterId = document.getElementById('shelter_id').value;
        if (initialShelterId) { checkCapacity(initialShelterId); }
    });
</script>
