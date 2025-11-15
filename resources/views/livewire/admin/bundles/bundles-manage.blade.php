<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-bold text-gray-800">Manajemen Bundles</h2>
        <a href="{{ route('admin.bundles.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200 shadow-md flex items-center">
            {{-- Feather: plus -> Font Awesome: fa-plus --}}
            <i class="fa-solid fa-plus w-5 h-5 mr-2"></i> Tambah Bundle Baru
        </a>
    </div>

    {{-- Notifikasi --}}
    @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg flex items-center">
            {{-- Font Awesome: fa-circle-check --}}
            <i class="fa-solid fa-circle-check w-5 h-5 mr-3"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg flex items-center">
            {{-- Font Awesome: fa-circle-exclamation --}}
            <i class="fa-solid fa-circle-exclamation w-5 h-5 mr-3"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- Filter dan Pencarian --}}
    <div class="mb-4 bg-white p-4 rounded-lg shadow-sm flex space-x-4">
        <input
            wire:model.live.debounce.300ms="search"
            type="text"
            placeholder="Cari berdasarkan Judul atau Slug..."
            class="flex-1 border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
        >

        <select wire:model.live="perPage" class="border border-gray-300 rounded-lg px-4 py-2">
            <option value="10">10 per halaman</option>
            <option value="25">25 per halaman</option>
            <option value="50">50 per halaman</option>
        </select>

        <select wire:model.live="status" class="border border-gray-300 rounded-lg px-4 py-2">
            <option value="">Semua Status</option>
            <option value="1">Aktif</option>
            <option value="0">Nonaktif</option>
        </select>
    </div>

    {{-- Tabel Bundles --}}
    <div class="bg-white shadow-lg rounded-xl overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Asli (Rp)</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Jual (Rp)</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Tryout</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($bundles as $bundle)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $bundle->title }}
                            <div class="text-xs text-gray-500 mt-1">Slug: {{ $bundle->slug }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ number_format($bundle->price, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold
                            @if($bundle->discount > 0) text-green-600 @else text-gray-900 @endif">
                            {{ number_format($bundle->final_price, 0, ',', '.') }}
                            @if($bundle->discount > 0)
                                <span class="text-xs text-red-500 ml-1">(-{{ $bundle->discount_percentage }}%)</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $bundle->tryouts_count }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if ($bundle->is_active)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Aktif
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                            <a href="{{ route('admin.bundles.edit', $bundle) }}" class="text-blue-600 hover:text-blue-900 px-3 py-2 rounded-md hover:bg-blue-50 inline-flex items-center transition duration-200">
                                {{-- Feather: edit -> Font Awesome: fa-pen-to-square --}}
                                <i class="fa-solid fa-pen-to-square w-4 h-4 inline-block mr-1"></i> Edit
                            </a>
                            <button
                                wire:click="deleteBundle({{ $bundle->id }})"
                                wire:confirm="Anda yakin ingin menghapus bundle ini?"
                                class="text-red-600 hover:text-red-900 px-3 py-2 rounded-md hover:bg-red-50 inline-flex items-center transition duration-200"
                            >
                                {{-- Feather: trash-2 -> Font Awesome: fa-trash-can --}}
                                <i class="fa-solid fa-trash-can w-4 h-4 inline-block mr-1"></i> Hapus
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            Tidak ada data bundle yang ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $bundles->links() }}
    </div>
</div>