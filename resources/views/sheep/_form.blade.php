{{--
    PARTIAL FORM DOMBA
    Digunakan oleh create.blade.php dan edit.blade.php
--}}

{{-- (1) Baris 1: Eartag, Kategori, Tipe --}}
<div class="grid grid-cols-1 gap-6 md:grid-cols-3">

    {{-- Input Eartag dengan Prefix Addon --}}
    <div>
        <x-input-label for="tag_number" :value="__('Eartag (Wajib)')" />
        <div class="flex mt-1 rounded-md shadow-sm">
            <span class="inline-flex items-center px-3 text-gray-500 border border-r-0 border-gray-300 rounded-l-md bg-gray-50 sm:text-sm">
                JAS-
            </span>
            {{-- $tagSuffix dikirim dari Controller saat Edit, kosong saat Create --}}
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

{{-- (2) Baris 2: Kandang (Dengan Realtime Capacity Check), Gender, Tgl Lahir, Bobot --}}
<div class="grid grid-cols-1 gap-6 mt-4 md:grid-cols-4">
    <div>
        <x-input-label for="shelter_id" :value="__('Kandang (Wajib)')" />
        <select id="shelter_id" name="shelter_id" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required onchange="checkCapacity(this.value)">
            <option value="">Pilih Kandang</option>
            @foreach($shelters as $shelter)
                <option value="{{ $shelter->id }}" {{ old('shelter_id', $sheep->shelter_id ?? '') == $shelter->id ? 'selected' : '' }}>
                    {{ $shelter->name }}
                </option>
            @endforeach
        </select>

        {{-- Tempat Menampilkan Info Kapasitas Realtime --}}
        <div id="capacity-info" class="hidden mt-2 text-sm">
            <span class="font-semibold">Status:</span>
            <span id="capacity-text">Loading...</span>
        </div>

        <x-input-error :messages="$errors->get('shelter_id')" class="mt-2" />
    </div>

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

{{-- (3) Baris 3: Silsilah --}}
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
        <x-input-error :messages="$errors->get('mother_id')" class="mt-2" />
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
        <x-input-error :messages="$errors->get('father_id')" class="mt-2" />
    </div>
</div>

{{-- (4) Baris 4: Harga & Bibit Unggul --}}
<div class="grid grid-cols-1 gap-6 mt-4 md:grid-cols-2">
    <div>
        <x-input-label for="purchase_price" :value="__('Harga Beli (Opsional)')" />
        <x-text-input id="purchase_price" class="block w-full mt-1" type="number" name="purchase_price" :value="old('purchase_price', $sheep->purchase_price ?? '')" />
        <x-input-error :messages="$errors->get('purchase_price')" class="mt-2" />
    </div>
    <div class="flex items-center mt-6">
        <input type="hidden" name="is_pedigree" value="0">
        <input id="is_pedigree" name="is_pedigree" type="checkbox" value="1"
               {{ (old('is_pedigree', $sheep->is_pedigree ?? 0) == 1) ? 'checked' : '' }}
               class="text-indigo-600 border-gray-300 rounded shadow-sm focus:ring-indigo-500">
        <label for="is_pedigree" class="ml-2 text-sm text-gray-600">Bibit Unggul (Pedigree)</label>
    </div>
</div>

{{-- (5) Baris 5: Foto --}}
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

{{-- (6) Baris 6: Deskripsi --}}
<div class="mt-4">
    <x-input-label for="description" :value="__('Deskripsi/Catatan (Opsional)')" />
    <textarea id="description" name="description" rows="4"
              class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $sheep->description ?? '') }}</textarea>
    <x-input-error :messages="$errors->get('description')" class="mt-2" />
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

{{-- JAVASCRIPT UNTUK REALTIME CAPACITY CHECK --}}
<script>
    function checkCapacity(shelterId) {
        const infoDiv = document.getElementById('capacity-info');
        const infoText = document.getElementById('capacity-text');

        if (!shelterId) {
            infoDiv.classList.add('hidden');
            return;
        }

        infoDiv.classList.remove('hidden');
        infoText.innerHTML = 'Memuat data...';
        infoText.className = 'text-gray-500';

        // Panggil API yang sudah kita buat di Controller
        fetch(`/shelters/${shelterId}/capacity`)
            .then(response => response.json())
            .then(data => {
                // Format tampilan: "Terisi: 5/20 (Sisa: 15)"
                const isFull = data.remaining <= 0;
                const colorClass = isFull ? 'text-red-600 font-bold' : 'text-green-600';

                infoText.innerHTML = `
                    Terisi: ${data.current}/${data.capacity} Ekor
                    <span class="${colorClass}"> (Sisa: ${data.remaining})</span>
                    ${isFull ? '<br>⚠️ Kandang Penuh!' : ''}
                `;
            })
            .catch(error => {
                console.error('Error:', error);
                infoText.innerHTML = 'Gagal memuat data kapasitas.';
                infoText.className = 'text-red-500';
            });
    }

    // Jalankan cek kapasitas saat halaman dimuat (jika sedang edit atau ada old input)
    document.addEventListener('DOMContentLoaded', function() {
        const initialShelterId = document.getElementById('shelter_id').value;
        if (initialShelterId) {
            checkCapacity(initialShelterId);
        }
    });
</script>
