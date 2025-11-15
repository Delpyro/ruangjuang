<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title; // <-- 1. TAMBAHKAN BARIS INI
use Livewire\Volt\Component;
use App\Models\User;

new #[Layout('layouts.guest', ['vite_assets' => ['resources/css/auth.css', 'resources/js/auth/forgot-password.js']])]
#[Title('Lupa Password')] // <-- 2. TAMBAHKAN BARIS INI
class extends Component
{
    public string $email = '';
    public string $phone_number = '';

    // <-- UBAH: Nama fungsi diubah agar lebih sesuai
    public function validateAndRedirect(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
            'phone_number' => ['required', 'string', 'numeric'],
        ]);

        $user = User::where('email', $this->email)->first();

        // Ganti 'phone_number' jika nama kolom di database Anda berbeda
        if (!$user || $user->phone_number != $this->phone_number) {
            $this->addError('email', __('Kombinasi email dan nomor WhatsApp tidak valid.'));
            return;
        }

        // --- SEMUA LOGIKA DI BAWAH INI DIUBAH ---

        // 1. Jika data cocok, buat token reset password secara manual
        $token = Password::broker()->createToken($user);

        // 2. Buat URL untuk halaman reset password
        // (Ini mengasumsikan Anda menggunakan route 'password.reset' standar Laravel)
        $resetUrl = route('password.reset', [
            'token' => $token,
            'email' => $user->email,
        ]);

        // 3. Alihkan (redirect) user ke halaman reset password
        // 'navigate: true' akan menggunakan perutean SPA Livewire (jika diaktifkan)
        $this->redirect($resetUrl, navigate: true);

        /*
        // Kode lama yang mengirim email tidak lagi digunakan
        $status = Password::sendResetLink(
            $this->only('email')
        );

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));
            return;
        }

        $this->reset('email', 'phone_number');
        session()->flash('status', __($status));
        */
    }
}; ?>

<!-- 👇 Saya rapikan div di sini agar konsisten dengan file login/register Anda 👇 -->
<div class="py-4">
    <h2 class="text-4xl font-bold text-primary-dark mb-2">Lupa Password</h2>
    
    <p class="text-gray-600 mb-10">Masukkan email dan nomor WhatsApp Anda untuk verifikasi.</p>
    
    <x-auth-session-status class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg border border-green-200" :status="session('status')" />
    
    <form wire:submit="validateAndRedirect" class="space-y-6">
        
        <div class="space-y-2">
            <label class="block text-gray-700 font-medium" for="email">
                <i class="fas fa-envelope text-primary-light mr-2"></i>Email
            </label>
            <div class="relative">
                <input 
                    wire:model="email"
                    id="email" 
                    type="email" 
                    placeholder="Masukkan alamat email" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-light transition duration-300"
                    required
                    autofocus
                >
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-600 text-sm" />
        </div>
        
        <div class="space-y-2">
            <label class="block text-gray-700 font-medium" for="phone_number">
                <i class="fas fa-phone text-primary-light mr-2"></i>Nomor WhatsApp
            </label>
            <div class="relative">
                <input 
                    wire:model="phone_number"
                    id="phone_number" 
                    type="text" 
                    placeholder="Contoh: 08123456789" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-light transition duration-300"
                    required
                >
            </div>
            <x-input-error :messages="$errors->get('phone_number')" class="mt-2 text-red-600 text-sm" />
        </div>
        
        <button 
            type="submit" 
            class="w-full bg-primary-light hover:bg-primary-dark text-white font-bold py-3 px-4 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 flex items-center justify-center"
        >
            <i class="fas fa-check-circle mr-2"></i> Verifikasi dan Lanjutkan
        </button>
    </form>
    
    <div class="mt-8 text-center">
        <p class="text-gray-600">
            <a href="{{ route('login') }}" class="text-primary-light hover:text-primary-dark font-medium transition duration-300" wire:navigate><i class="fas fa-arrow-left mr-2"></i> Kembali ke halaman Login</a>
        </p>
    </div>
</div>