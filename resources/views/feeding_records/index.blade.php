<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ __('Riwayat Pemberian Pakan Harian') }}
            </h2>
            <a href="{{ route('feeding-records.create') }}">
                <x-primary-button>{{ __('+ Catat Pakan') }}</x-primary-button>
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

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Tanggal</th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Kandang</th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Waktu Pagi/Sore</th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Detail Pakan (Jenis & Jumlah)</th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Dicatat Oleh</th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($feedingRecords as $record)
                                    <tr>
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap">
                                            {{ $record->date->format('d M Y') }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                            {{ $record->shelter->name }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                            Pagi: {{ $record->time_morning ? \Carbon\Carbon::parse($record->time_morning)->format('H:i') : '-' }}<br>
                                            Sore: {{ $record->time_evening ? \Carbon\Carbon::parse($record->time_evening)->format('H:i') : '-' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            <ul class="space-y-1 list-disc list-inside">
                                                @foreach($record->feedTypes as $feed)
                                                    @php
                                                        $morning = $feed->pivot->quantity_morning ?? 0;
                                                        $evening = $feed->pivot->quantity_evening ?? 0;
                                                        $total = $morning + $evening;
                                                    @endphp
                                                    <li>
                                                        <span class="font-medium text-gray-900">{{ $feed->name }}</span> ({{ $feed->unit }})
                                                        <span class="block text-[10px] text-gray-600 mt-0.5">
                                                            Pagi: <span class="font-semibold">{{ number_format($morning, 2) }}</span> |
                                                            Sore: <span class="font-semibold">{{ number_format($evening, 2) }}</span> |
                                                            Total: <span class="font-bold text-indigo-600">{{ number_format($total, 2) }}</span>
                                                        </span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                            {{ $record->user->name }}
                                        </td>
                                        <td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">
                                            <div class="flex justify-end space-x-2">
                                                <a href="{{ route('feeding-records.edit', $record) }}" class="font-bold text-indigo-600 hover:text-indigo-900">Edit</a>

                                                <form action="{{ route('feeding-records.destroy', $record) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus catatan ini?');" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">Belum ada catatan pemberian pakan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $feedingRecords->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
