<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ __('Detail Pemberian Pakan') }}
            </h2>
            <a href="{{ route('feeding-records.index') }}">
                <x-secondary-button>{{ __('Kembali') }}</x-secondary-button>
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">

                {{-- HEADER DETAIL --}}
                <div class="flex flex-col gap-4 px-6 py-5 border-b border-gray-200 bg-gray-50 md:flex-row md:justify-between md:items-center">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">
                            {{ $feedingRecord->date->format('l, d F Y') }}
                        </h3>
                        <p class="text-sm text-gray-500">
                            Dicatat oleh: {{ $feedingRecord->user->name }} • {{ $feedingRecord->created_at->diffForHumans() }}
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('feeding-records.edit', $feedingRecord) }}">
                            <x-primary-button>{{ __('Edit') }}</x-primary-button>
                        </a>
                        <form action="{{ route('feeding-records.destroy', $feedingRecord) }}" method="POST" onsubmit="return confirm('Hapus catatan ini?');">
                            @csrf
                            @method('DELETE')
                            <x-danger-button>{{ __('Hapus') }}</x-danger-button>
                        </form>
                    </div>
                </div>

                <div class="p-6 text-gray-900">

                    {{-- INFO UTAMA --}}
                    <div class="grid grid-cols-1 gap-6 mb-8 md:grid-cols-3">
                        <div class="p-4 border border-indigo-100 rounded-lg bg-indigo-50">
                            <span class="block text-xs font-bold tracking-wide text-indigo-500 uppercase">Lokasi Kandang</span>
                            <span class="block mt-1 text-xl font-bold text-indigo-900">{{ $feedingRecord->shelter->name }}</span>
                        </div>
                        <div class="p-4 border border-orange-100 rounded-lg bg-orange-50">
                            <span class="block text-xs font-bold tracking-wide text-orange-500 uppercase">Waktu Pagi</span>
                            <span class="block mt-1 text-xl font-bold text-orange-900">
                                {{ $feedingRecord->time_morning ? \Carbon\Carbon::parse($feedingRecord->time_morning)->format('H:i') : '-' }}
                            </span>
                        </div>
                        <div class="p-4 border border-blue-100 rounded-lg bg-blue-50">
                            <span class="block text-xs font-bold tracking-wide text-blue-500 uppercase">Waktu Sore</span>
                            <span class="block mt-1 text-xl font-bold text-blue-900">
                                {{ $feedingRecord->time_evening ? \Carbon\Carbon::parse($feedingRecord->time_evening)->format('H:i') : '-' }}
                            </span>
                        </div>
                    </div>

                    {{-- TABEL DETAIL PAKAN --}}
                    <h4 class="pb-2 mb-4 text-lg font-bold text-gray-800 border-b">Rincian Pakan</h4>
                    <div class="overflow-x-auto">
                        <table class="min-w-full border divide-y divide-gray-200 rounded-lg">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Jenis Pakan</th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Porsi Pagi</th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Porsi Sore</th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Total Qty</th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">Estimasi Biaya</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @php $grandTotalCost = 0; @endphp
                                @foreach($feedingRecord->feedTypes as $feed)
                                    @php
                                        $morning = $feed->pivot->quantity_morning ?? 0;
                                        $evening = $feed->pivot->quantity_evening ?? 0;
                                        $totalQty = $morning + $evening;
                                        $cost = $totalQty * ($feed->price_per_unit ?? 0);
                                        $grandTotalCost += $cost;
                                    @endphp
                                    <tr>
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap">
                                            {{ $feed->name }}
                                            <span class="block text-xs font-normal text-gray-500">Satuan: {{ $feed->unit }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                            {{ number_format($morning, 2) }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                            {{ number_format($evening, 2) }}
                                        </td>
                                        <td class="px-6 py-4 text-sm font-bold text-gray-900 whitespace-nowrap bg-gray-50">
                                            {{ number_format($totalQty, 2) }} {{ $feed->unit }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-right text-gray-900 whitespace-nowrap">
                                            @if($feed->price_per_unit)
                                                Rp {{ number_format($cost, 0, ',', '.') }}
                                                <span class="block text-xs text-gray-400">@ Rp {{ number_format($feed->price_per_unit, 0, ',', '.') }}</span>
                                            @else
                                                <span class="italic text-gray-400">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            @if($grandTotalCost > 0)
                                <tfoot class="bg-gray-100">
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 font-bold text-right text-gray-700">Total Estimasi Biaya Harian:</td>
                                        <td class="px-6 py-4 text-lg font-bold text-right text-green-700">
                                            Rp {{ number_format($grandTotalCost, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
