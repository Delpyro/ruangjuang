<?php

namespace App\Livewire\Admin\Bundles;

use Livewire\Component;
use App\Models\Bundle;
use App\Models\Tryout;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BundlesCreate extends Component
{
    // Properti Form
    public $title;
    public $slug;
    public $description;
    public $price = 0;
    public $discount = 0;
    public $is_active = true;
    public $expired_at;
    
    // Properti untuk menampung ID Tryout yang dipilih
    public $selected_tryout_ids = []; 
    
    // Properti untuk search
    public $search = '';
    
    // Properti untuk select all
    public $selectAll = false;

    protected $rules = [
        'title' => 'required|string|max:255',
        'slug' => 'required|string|max:255|unique:bundles,slug',
        'description' => 'nullable|string',
        'price' => 'required|integer|min:0',
        // 'lte:price' sudah menangani diskon tidak boleh melebihi harga
        'discount' => 'nullable|integer|min:0|lte:price', 
        'is_active' => 'boolean',
        'expired_at' => 'nullable|date|after:today',
        'selected_tryout_ids' => 'required|array|min:1',
        'selected_tryout_ids.*' => 'exists:tryouts,id',
    ];

    protected $messages = [
        'title.required' => 'Judul bundle wajib diisi.',
        'slug.required' => 'Slug wajib diisi.',
        'slug.unique' => 'Slug sudah digunakan, coba judul lain.',
        'price.required' => 'Harga bundle wajib diisi.',
        'price.min' => 'Harga tidak boleh negatif.',
        'selected_tryout_ids.required' => 'Bundle harus menyertakan minimal satu Tryout.',
        'selected_tryout_ids.min' => 'Bundle harus menyertakan minimal satu Tryout.',
        'discount.lte' => 'Diskon tidak boleh melebihi harga Bundle.',
        'expired_at.after' => 'Tanggal kedaluwarsa harus setelah hari ini.',
    ];
    
    // Perbaikan #1: mount() tidak diperlukan jika loadTryouts() dipanggil di render()
    // public function mount() {} 

    public function loadTryouts()
    {
        $query = Tryout::active();
        
        if ($this->search) {
            $query->where('title', 'like', '%' . $this->search . '%');
        }
        
        return $query->select('id', 'title', 'price')->get();
    }

    // Perbaikan #1: updatedSearch() dikosongkan karena 'wire:model.live' akan memicu render()
    public function updatedSearch()
    {
        // Livewire akan memanggil render() secara otomatis, tidak perlu tindakan tambahan di sini.
    }

    // Auto-generate slug ketika title diubah
    public function updatedTitle($value)
    {
        if (!empty($value)) {
            $this->slug = Str::slug($value);
        }
    }
    
    // Perbaikan #4: Tambahkan validasi real-time untuk expired_at
    public function updatedExpiredAt()
    {
        $this->validateOnly('expired_at');
    }

    // Handle select all
    public function updatedSelectAll($value)
    {
        // Perbaikan #2: loadTryouts() dipanggil untuk mengambil ID yang difilter.
        if ($value) {
            $this->selected_tryout_ids = $this->loadTryouts()->pluck('id')->toArray();
        } else {
            $this->selected_tryout_ids = [];
        }
    }

    // Reset select all ketika selected items berubah
    public function updatedSelectedTryoutIds()
    {
        $allTryoutsCount = $this->loadTryouts()->count();
        $selectedCount = count($this->selected_tryout_ids);
        
        $this->selectAll = $selectedCount === $allTryoutsCount && $allTryoutsCount > 0;
    }

    // Validasi diskon real-time
    public function updatedDiscount($value)
    {
        // Gunakan validateOnly untuk memanfaatkan rule 'lte:price'
        $this->validateOnly('discount');
    }

    // Validasi harga real-time (juga memicu validasi diskon)
    public function updatedPrice($value)
    {
        // Perlu juga memicu validasi diskon jika harga diubah
        $this->validateOnly('price');
        $this->validateOnly('discount');
    }

    public function store()
    {
        $this->validate();

        // Perbaikan #3: Validasi tambahan manual dihapus karena sudah tercakup oleh $this->validate() dan rule 'lte:price'.
        /*
        if ($this->discount > $this->price) {
            $this->addError('discount', 'Diskon tidak boleh melebihi harga Bundle');
            return;
        }
        */

        try {
            $bundle = Bundle::create([
                'title' => $this->title,
                'slug' => $this->slug,
                'description' => $this->description,
                'price' => $this->price,
                // Menggunakan ?? 0 untuk memastikan int, meskipun validasi harusnya sudah menjamin.
                'discount' => $this->discount ?? 0, 
                'is_active' => $this->is_active,
                // Menggunakan Carbon::parse hanya jika $this->expired_at tidak null
                'expired_at' => $this->expired_at ? Carbon::parse($this->expired_at) : null,
            ]);
            
            // Melampirkan relasi many-to-many
            $bundle->tryouts()->attach($this->selected_tryout_ids);

            session()->flash('success', 'Bundle baru berhasil dibuat!');
            return redirect()->route('admin.bundles.index'); 

        } catch (\Exception $e) {
            // Log the error for debugging: \Log::error($e->getMessage());
            session()->flash('error', 'Terjadi kesalahan saat menyimpan bundle: ' . $e->getMessage());
        }
    }

    // Computed property untuk harga akhir (CamelCase)
    public function getFinalPriceProperty()
    {
        return max(0, $this->price - $this->discount);
    }

    // Hitung total harga jika beli individual (CamelCase)
    public function getTotalIndividualPriceProperty()
    {
        if (empty($this->selected_tryout_ids)) {
            return 0;
        }
        
        // Memastikan Query berjalan
        return Tryout::whereIn('id', $this->selected_tryout_ids)->sum('price');
    }

    // Hitung persentase hemat (CamelCase)
    public function getSavingsPercentageProperty()
    {
        $totalIndividual = $this->totalIndividualPrice; // Menggunakan properti komputasi lain
        if ($totalIndividual > 0 && $this->finalPrice > 0) {
            $savings = $totalIndividual - $this->finalPrice;
            return round(($savings / $totalIndividual) * 100, 2);
        }
        return 0;
    }

    // Hitung jumlah tryout yang dipilih (CamelCase)
    public function getSelectedTryoutsCountProperty()
    {
        return count($this->selected_tryout_ids);
    }

    // Cek apakah ada hemat (CamelCase)
    public function getHasSavingsProperty()
    {
        return $this->totalIndividualPrice > $this->finalPrice; // Menggunakan properti komputasi lain
    }

    // Format expired date untuk input datetime-local
    public function getFormattedExpiredAtProperty()
    {
        if ($this->expired_at) {
            // Livewire/Blade akan menggunakan nilai dari $this->expired_at,
            // dan browser yang akan memformatnya. Getter ini mungkin tidak digunakan
            // jika wire:model sudah memegang nilai datetime-local yang valid.
            // Biarkan saja untuk jaga-jaga.
            return Carbon::parse($this->expired_at)->format('Y-m-d\TH:i');
        }
        return null;
    }

    public function render()
    {
        // Perbaikan #1: Data tryouts diambil di render()
        $tryouts = $this->loadTryouts();

        return view('livewire.admin.bundles.bundles-create', [
            'tryouts' => $tryouts,
        ])->layout('layouts.admin');
    }
}