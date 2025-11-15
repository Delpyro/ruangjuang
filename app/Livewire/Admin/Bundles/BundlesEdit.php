<?php

namespace App\Livewire\Admin\Bundles;

use Livewire\Component;
use App\Models\Bundle;
use App\Models\Tryout;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class BundlesEdit extends Component
{
    public Bundle $bundle;

    public $title;
    public $slug;
    public $description; // Properti untuk TinyMCE
    public $price = 0;
    public $discount = 0;
    public $is_active = true;
    public $expired_at;
    
    public $selected_tryout_ids = []; 
    public $search = '';
    public $selectAll = false;

    protected function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'slug' => ['required', 'string', 'max:255', Rule::unique('bundles', 'slug')->ignore($this->bundle->id)],
            'description' => 'nullable|string', // Pastikan ini nullable atau required sesuai kebutuhan
            'price' => 'required|integer|min:0',
            'discount' => 'nullable|integer|min:0|lte:price', 
            'is_active' => 'boolean',
            'expired_at' => 'nullable|date|after:today',
            'selected_tryout_ids' => 'required|array|min:1',
            'selected_tryout_ids.*' => 'exists:tryouts,id',
        ];
    }

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
    
    public function mount(Bundle $bundle) 
    {
        $this->bundle = $bundle; 
        
        $this->title = $bundle->title;
        $this->slug = $bundle->slug;
        $this->description = $bundle->description; // Pastikan properti ini diisi dari model
        $this->price = $bundle->price;
        $this->discount = $bundle->discount;
        $this->is_active = $bundle->is_active;
        
        $this->expired_at = $bundle->expired_at 
            ? Carbon::parse($bundle->expired_at)->format('Y-m-d\TH:i') 
            : null;

        $this->selected_tryout_ids = $bundle->tryouts()->pluck('tryouts.id')->toArray();
    }

    public function loadTryouts()
    {
        $query = Tryout::active();
        
        if ($this->search) {
            $query->where('title', 'like', '%' . $this->search . '%');
        }

        if (!empty($this->selected_tryout_ids)) {
             $query->orWhereIn('id', $this->selected_tryout_ids);
        }
        
        return $query->select('id', 'title', 'price')->get();
    }

    public function updatedTitle($value)
    {
        if (!empty($value)) {
            $this->slug = Str::slug($value);
        }
    }
    
    public function updatedExpiredAt()
    {
        $this->validateOnly('expired_at');
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selected_tryout_ids = $this->loadTryouts()->pluck('id')->toArray();
        } else {
            $this->selected_tryout_ids = []; 
        }
    }

    public function updatedSelectedTryoutIds()
    {
        $allTryoutsCount = $this->loadTryouts()->count();
        $selectedCount = count($this->selected_tryout_ids);
        
        $this->selectAll = $selectedCount === $allTryoutsCount && $allTryoutsCount > 0;
    }

    public function updatedDiscount()
    {
        $this->validateOnly('discount');
    }

    public function updatedPrice()
    {
        $this->validateOnly('price');
        $this->validateOnly('discount');
    }

    public function update()
    {
        $this->validate($this->rules()); 

        try {
            $this->bundle->update([
                'title' => $this->title,
                'slug' => $this->slug,
                'description' => $this->description,
                'price' => $this->price,
                'discount' => $this->discount ?? 0, 
                'is_active' => $this->is_active,
                'expired_at' => $this->expired_at ? Carbon::parse($this->expired_at) : null,
            ]);
            
            $this->bundle->tryouts()->sync($this->selected_tryout_ids);

            session()->flash('success', 'Bundle **' . $this->title . '** berhasil diperbarui!');
            return redirect()->route('admin.bundles.index'); 

        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan saat memperbarui bundle: ' . $e->getMessage());
        }
    }

    public function getFinalPriceProperty()
    {
        return max(0, $this->price - $this->discount);
    }

    public function getTotalIndividualPriceProperty()
    {
        if (empty($this->selected_tryout_ids)) {
            return 0;
        }
        
        return Tryout::whereIn('id', $this->selected_tryout_ids)->sum('price');
    }

    public function getSavingsPercentageProperty()
    {
        $totalIndividual = $this->totalIndividualPrice; 
        $finalPrice = $this->finalPrice;

        if ($totalIndividual > 0 && $finalPrice > 0) {
            $savings = $totalIndividual - $finalPrice;
            return round(($savings / $totalIndividual) * 100, 2);
        }
        return 0;
    }

    public function getSelectedTryoutsCountProperty()
    {
        return count($this->selected_tryout_ids);
    }

    public function getHasSavingsProperty()
    {
        return $this->totalIndividualPrice > $this->finalPrice;
    }

    public function render()
    {
        $tryouts = $this->loadTryouts();

        return view('livewire.admin.bundles.bundles-edit', [
            'tryouts' => $tryouts,
        ])->layout('layouts.admin');
    }
}