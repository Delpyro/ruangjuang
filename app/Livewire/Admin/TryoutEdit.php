<?php

namespace App\Livewire\Admin;

use App\Models\Tryout;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class TryoutEdit extends Component
{
    use WithFileUploads;

    public $tryout;
    public $title, $slug, $is_hots, $duration, $content, $quote, $price, $discount, $is_active;
    public $image;
    public $currentImage;

    public function mount($id)
    {
        $this->tryout = Tryout::findOrFail($id);
        $this->title = $this->tryout->title;
        $this->slug = $this->tryout->slug;
        $this->is_hots = $this->tryout->is_hots;
        $this->duration = $this->tryout->duration;
        $this->content = $this->tryout->content;
        $this->quote = $this->tryout->quote;
        $this->price = $this->tryout->price;
        $this->discount = $this->tryout->discount;
        $this->is_active = $this->tryout->is_active;
        $this->currentImage = $this->tryout->image;
    }

    protected function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:tryouts,slug,' . $this->tryout->id,
            'is_hots' => 'boolean',
            'duration' => 'nullable|integer|min:1',
            'content' => 'required|string',
            'quote' => 'nullable|string',
            'price' => 'required|integer|min:0',
            'discount' => 'nullable|integer|min:0|max:' . $this->price,
            'is_active' => 'boolean',
            'image' => 'nullable|image|max:2048',
        ];
    }

    public function updatedTitle($value)
    {
        if (!$this->tryout->slug || $this->tryout->slug === Str::slug($this->tryout->title)) {
            $this->slug = Str::slug($value);
        }
    }

    public function updatedPrice($value)
    {
        if ($this->discount > $value) {
            $this->discount = $value;
        }
    }

    public function update()
    {
        $this->validate();

        $imagePath = $this->currentImage;
        if ($this->image) {
            // Hapus gambar lama jika ada
            if ($this->currentImage) {
                Storage::disk('public')->delete($this->currentImage);
            }
            $imagePath = $this->image->store('tryouts', 'public');
        }

        $this->tryout->update([
            'title' => $this->title,
            'slug' => $this->slug,
            'is_hots' => $this->is_hots,
            'duration' => $this->duration,
            'content' => $this->content,
            'quote' => $this->quote,
            'price' => $this->price,
            'discount' => $this->discount,
            'is_active' => $this->is_active,
            'image' => $imagePath,
        ]);

        session()->flash('success', 'Tryout berhasil diperbarui.');
        return redirect()->route('admin.tryouts.index');
    }

    public function removeImage()
    {
        if ($this->currentImage) {
            Storage::disk('public')->delete($this->currentImage);
            $this->tryout->update(['image' => null]);
            $this->currentImage = null;
            $this->image = null;
        }
    }

    public function render()
    {
        return view('livewire.admin.tryout-edit')
            ->layout('layouts.admin');
    }
}