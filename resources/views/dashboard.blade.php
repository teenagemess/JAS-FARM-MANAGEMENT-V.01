<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __($dashboardTitle ?? 'Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">

            {{-- BAGIAN 1: KARTU STATISTIK (KPI) --}}
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">

                {{-- Total Domba --}}
                <div class="p-6 overflow-hidden bg-white border-l-4 border-indigo-500 shadow-sm sm:rounded-lg">
                    <div class="flex items-center">
                        <div class="p-3 mr-4 text-indigo-500 bg-indigo-100 rounded-full">
                            <span class="text-2xl">🐑</span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">{{ Auth::user()->role === 'admin' ? 'Total Populasi' : 'Domba Kelolaan' }}</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $totalSheep }} <span class="text-xs font-normal text-gray-400">Ekor</span></p>
                        </div>
                    </div>
                </div>

                {{-- Domba Sakit --}}
                <div class="p-6 overflow-hidden bg-white border-l-4 border-red-500 shadow-sm sm:rounded-lg">
                    <div class="flex items-center">
                        <div class="p-3 mr-4 text-red-500 bg-red-100 rounded-full">
                            <span class="text-2xl">🩺</span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Perlu Perawatan</p>
                            <p class="text-2xl font-bold text-red-600">{{ $activeSicknessCount }} <span class="text-xs font-normal text-gray-400">Ekor</span></p>
                        </div>
                    </div>
                </div>

                {{-- Domba Hamil --}}
                <div class="p-6 overflow-hidden bg-white border-l-4 border-purple-500 shadow-sm sm:rounded-lg">
                    <div class="flex items-center">
                        <div class="p-3 mr-4 text-purple-500 bg-purple-100 rounded-full">
                            <span class="text-2xl">🤰</span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Sedang Bunting</p>
                            <p class="text-2xl font-bold text-purple-600">{{ $pregnantCount }} <span class="text-xs font-normal text-gray-400">Indukan</span></p>
                        </div>
                    </div>
                </div>

                {{-- Saldo Bulan Ini --}}
                <div class="p-6 overflow-hidden bg-white border-l-4 shadow-sm sm:rounded-lg {{ $balanceThisMonth >= 0 ? 'border-green-500' : 'border-orange-500' }}">
                    <div class="flex items-center">
                        <div class="p-3 mr-4 rounded-full {{ $balanceThisMonth >= 0 ? 'bg-green-100 text-green-500' : 'bg-orange-100 text-orange-500' }}">
                            <span class="text-2xl">💰</span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Saldo Bulan Ini</p>
                            <p class="text-lg font-bold {{ $balanceThisMonth >= 0 ? 'text-green-600' : 'text-orange-600' }}">
                                Rp {{ number_format($balanceThisMonth, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- BAGIAN 2: GRAFIK STATISTIK (UPDATE GRID) --}}
            {{-- Mengubah grid menjadi 3 kolom agar muat untuk Pie Chart --}}
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

                {{-- Grafik Keuangan (Lebar 2 kolom di layar besar) --}}
                <div class="p-4 overflow-hidden bg-white shadow-sm sm:rounded-lg lg:col-span-2">
                    <h3 class="mb-4 font-bold text-gray-700">📊 Arus Kas Tahun Ini</h3>
                    <div class="relative w-full h-64">
                        <canvas id="financialChart"></canvas>
                    </div>
                </div>

                {{-- Grafik Komposisi Pengeluaran (BARU - Pie Chart) --}}
                <div class="p-4 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <h3 class="mb-4 font-bold text-gray-700">🍰 Komposisi Pengeluaran</h3>
                    <div class="relative w-full h-64">
                        <canvas id="expensePieChart"></canvas>
                    </div>
                </div>

                {{-- Grafik Pertumbuhan --}}
                <div class="p-4 overflow-hidden bg-white shadow-sm sm:rounded-lg lg:col-span-3">
                    <h3 class="mb-4 font-bold text-gray-700">📈 Tren Populasi Domba Baru</h3>
                    <div class="relative w-full h-64">
                        <canvas id="growthChart"></canvas>
                    </div>
                </div>

            </div>

            {{-- BAGIAN 3: PERINGATAN & DAFTAR --}}
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

                {{-- KOLOM KIRI --}}
                <div class="space-y-6">
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="flex items-center justify-between p-4 border-b border-gray-100 bg-red-50">
                            <h3 class="font-bold text-red-800">⚠️ Perlu Perawatan (Sakit)</h3>
                            <a href="{{ route('sheep.index', ['filter_health' => 'sick']) }}" class="text-xs text-red-600 hover:underline">Lihat Semua</a>
                        </div>
                        <div class="p-4">
                            @forelse($sickSheepList as $health)
                                <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                                    <div class="flex items-center">
                                        <div class="ml-3">
                                            <p class="text-sm font-bold text-gray-900">
                                                <a href="{{ route('sheep.show', $health->sheep_id) }}" class="hover:text-indigo-600">
                                                    {{ $health->sheep->tag_number }}
                                                </a>
                                            </p>
                                            <p class="text-xs text-gray-500">{{ $health->sheep->shelter->name ?? '-' }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            {{ $health->diagnosis }}
                                        </span>
                                        <p class="text-[10px] text-gray-400 mt-1">{{ \Carbon\Carbon::parse($health->record_date)->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @empty
                                <p class="py-4 text-sm text-center text-gray-500">Alhamdulillah, tidak ada domba sakit.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="flex items-center justify-between p-4 border-b border-gray-100 bg-purple-50">
                            <h3 class="font-bold text-purple-800">🤰 Estimasi Kelahiran Terdekat</h3>
                            <a href="#" class="text-xs text-purple-600 hover:underline">Lihat Semua</a>
                        </div>
                        <div class="p-4">
                            @forelse($pregnantSheepList as $repro)
                                <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                                    <div>
                                        <p class="text-sm font-bold text-gray-900">
                                            <a href="{{ route('sheep.show', $repro->female_sheep_id) }}" class="hover:text-indigo-600">
                                                {{ $repro->dam->tag_number }}
                                            </a>
                                        </p>
                                        <p class="text-xs text-gray-500">{{ $repro->dam->shelter->name ?? '-' }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-bold text-purple-600">
                                            {{ \Carbon\Carbon::parse($repro->expected_delivery_date)->format('d M Y') }}
                                        </p>
                                        <p class="text-[10px] text-gray-500">
                                            {{ \Carbon\Carbon::parse($repro->expected_delivery_date)->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>
                            @empty
                                <p class="py-4 text-sm text-center text-gray-500">Tidak ada data kehamilan aktif.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- KOLOM KANAN --}}
                <div class="overflow-hidden bg-white shadow-sm h-fit sm:rounded-lg">
                    <div class="flex items-center justify-between p-4 border-b border-gray-100 bg-gray-50">
                        <h3 class="font-bold text-gray-800">💸 Transaksi Terakhir</h3>
                        @if(Auth::user()->role === 'admin')
                            <a href="{{ route('profit-loss.index') }}" class="text-xs text-indigo-600 hover:underline">Buka Buku Kas</a>
                        @endif
                    </div>
                    <div class="p-4">
                        @forelse($recentTransactions as $transaction)
                            <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $transaction->category }}</p>
                                    <p class="text-xs text-gray-500">{{ $transaction->date->format('d M') }} • {{ Str::limit($transaction->description, 20) }}</p>
                                </div>
                                <span class="text-sm font-bold {{ $transaction->type == 'income' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $transaction->type == 'income' ? '+' : '-' }} Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                </span>
                            </div>
                        @empty
                            <p class="py-4 text-sm text-center text-gray-500">Belum ada transaksi.</p>
                        @endforelse

                        <div class="pt-4 mt-4 border-t border-gray-100">
                            <a href="{{ route('profit-loss.create') }}">
                                <x-primary-button class="justify-center w-full text-xs">
                                    {{ __('+ Catat Transaksi Baru') }}
                                </x-primary-button>
                            </a>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

    {{-- CHART.JS DARI LOCAL --}}
    <script src="{{ asset('js/chart.min.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const sheepData = @json(array_values(!empty($monthlySheep) ? $monthlySheep : array_fill(0, 12, 0)));
            const incomeData = @json(array_values(!empty($monthlyIncome) ? $monthlyIncome : array_fill(0, 12, 0)));
            const expenseData = @json(array_values(!empty($monthlyExpense) ? $monthlyExpense : array_fill(0, 12, 0)));

            // Data untuk Pie Chart
            const expenseLabels = @json($expenseLabels ?? []);
            const expenseTotals = @json($expenseTotals ?? []);

            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

            // 1. Grafik Keuangan (Bar)
            const financialCanvas = document.getElementById('financialChart');
            if (financialCanvas) {
                new Chart(financialCanvas, {
                    type: 'bar',
                    data: {
                        labels: months,
                        datasets: [
                            {
                                label: 'Pemasukan',
                                data: incomeData,
                                backgroundColor: 'rgba(34, 197, 94, 0.6)',
                                borderColor: 'rgba(34, 197, 94, 1)',
                                borderWidth: 1
                            },
                            {
                                label: 'Pengeluaran',
                                data: expenseData,
                                backgroundColor: 'rgba(239, 68, 68, 0.6)',
                                borderColor: 'rgba(239, 68, 68, 1)',
                                borderWidth: 1
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return 'Rp ' + new Intl.NumberFormat('id-ID', { notation: "compact" }).format(value);
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // 2. Grafik Komposisi Pengeluaran (Pie - BARU)
            const pieCanvas = document.getElementById('expensePieChart');
            if (pieCanvas && expenseLabels.length > 0) {
                new Chart(pieCanvas, {
                    type: 'doughnut',
                    data: {
                        labels: expenseLabels,
                        datasets: [{
                            data: expenseTotals,
                            backgroundColor: [
                                '#F87171', '#60A5FA', '#34D399', '#FBBF24', '#A78BFA'
                            ],
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 10 } } },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let value = context.parsed;
                                        return ' Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                    }
                                }
                            }
                        }
                    }
                });
            } else if (pieCanvas) {
                // Tampilkan pesan kosong jika tidak ada data
                // (Optional: handle empty state visualization)
            }

            // 3. Grafik Pertumbuhan (Line)
            const growthCanvas = document.getElementById('growthChart');
            if (growthCanvas) {
                new Chart(growthCanvas, {
                    type: 'line',
                    data: {
                        labels: months,
                        datasets: [{
                            label: 'Domba Baru (Ekor)',
                            data: sheepData,
                            borderColor: 'rgba(79, 70, 229, 1)',
                            backgroundColor: 'rgba(79, 70, 229, 0.1)',
                            tension: 0.3,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
                    }
                });
            }
        });
    </script>
</x-app-layout>
