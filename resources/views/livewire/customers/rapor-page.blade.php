<div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 py-12 mt-20">
<div class="container mx-auto px-4">

    <div class="text-center mb-10" data-aos="fade-up">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-3 leading-tight">
            Rapor Belajar Anda
        </h1>
        <p class="text-lg text-gray-600 max-w-2xl mx-auto">
            Pantau perkembangan skor Anda dari pengerjaan pertama setiap tryout.
        </p>
    </div>

    @if (!empty($reportData))
        <div class="max-w-7xl mx-auto bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden" 
             data-aos="fade-up" data-aos-delay="100">
            
            <div class="p-5 md:p-8 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Grafik Perkembangan Skor</h3>
                
                <div 
                    wire:ignore 
                    x-data="raporChart(
                        {{ json_encode($chartLabels) }}, 
                        {{ json_encode($chartSeries) }}
                    )"
                    x-init="initChart()"
                    class="mt-4"
                >
                    <div id="rapor-chart-container"></div>
                </div>

            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tryout Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Score</th>
                            
                            @foreach ($allCategoryNames as $categoryName)
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $categoryName }}</th>
                            @endforeach

                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($reportData as $data)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $data['title'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $data['tanggal']->isoFormat('D MMM YYYY') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-600 font-bold">{{ number_format($data['total_score'], 0, ',', '.') }}</td>
                                
                                @foreach ($allCategoryNames as $categoryName)
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-center">
                                        {{ number_format($data['categories'][$categoryName] ?? 0, 0, ',', '.') }}
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="text-center bg-white p-10 rounded-xl shadow-lg border border-gray-100" data-aos="fade-up">
            <i class="fas fa-chart-pie text-5xl mb-4 text-gray-400"></i>
            <h3 class="text-xl font-semibold text-gray-800 mb-2">Rapor Masih Kosong</h3>
            <p class="text-gray-600">
                Anda belum menyelesaikan pengerjaan pertama pada tryout manapun.
            </p>
            <a href="{{ route('tryout.my-tryouts') }}" 
               class="mt-6 inline-block bg-blue-600 text-white font-semibold py-3 px-6 rounded-lg shadow-md hover:bg-blue-700 transition-all duration-300">
                Mulai Kerjakan Tryout
            </a>
        </div>
    @endif

</div>

<script>
    function raporChart(labels, series) {
        return {
            initChart() {
                const options = {
                    series: series, // Ini sudah dalam format: [{name: 'Total Score', data: [...]}, {name: 'TWK', data: [...]}]
                    chart: {
                        height: 350,
                        type: 'line',
                        toolbar: { show: true },
                        zoom: { enabled: false }
                    },
                    colors: ['#008FFB', '#00E396', '#FEB019', '#FF4560', '#775DD0'], // Daftar warna
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        // [FIX] Disesuaikan agar cocok dengan jumlah series Anda (misal 1 Total + 3 Kategori = 4 series)
                        width: [4, 2, 2, 2], 
                        curve: 'smooth'
                    },

                    // ▼▼▼ TAMBAHAN BARU: Membuat titik/marker selalu terlihat ▼▼▼
                    markers: {
                        size: 4, // Ukuran titik default (selalu terlihat)
                        strokeColors: '#fff',
                        strokeWidth: 0, // Anda bisa set 1 atau 2 jika ingin ada border putih
                        hover: {
                            size: 7 // Ukuran saat di-hover (sedikit lebih besar)
                        }
                    },
                    // ▲▲▲ AKHIR TAMBAHAN ▲▲▲

                    title: {
                        text: 'Grafik Perkembangan Skor',
                        align: 'left'
                    },
                    xaxis: {
                        categories: labels,
                    },
                    yaxis: {
                        title: {
                            text: 'Nilai Skor'
                        },
                        min: 0
                        // Anda bisa set 'max: 550' jika ini skor SKD
                    },
                    tooltip: {
                        shared: true, // Tampilkan semua series saat hover
                        intersect: false,
                        y: {
                            formatter: function (y) {
                                if (typeof y !== "undefined") {
                                    return y.toFixed(0) + " poin";
                                }
                                return y;
                            }
                        }
                    },
                    legend: {
                        position: 'top',
                        horizontalAlign: 'center',
                        offsetY: 0
                    }
                };

                const chart = new ApexCharts(document.querySelector("#rapor-chart-container"), options);
                chart.render();
            }
        }
    }
</script>

</div>