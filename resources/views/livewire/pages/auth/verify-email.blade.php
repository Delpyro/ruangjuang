<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title; // <-- 1. TAMBAHKAN BARIS INI
use Livewire\Volt\Component;

new #[Layout('layouts.guest', ['vite_assets' => ['resources/css/auth.css', 'resources/js/auth/verify.js']])]
#[Title('Verifikasi Email')] // <-- 2. TAMBAHKAN BARIS INI
class extends Component
{
    /**
     * Send an email verification notification to the user.
     */
    public function sendVerification(): void
    {
        if (Auth::user()->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);

            return;
        }

        Auth::user()->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<div>
    <div class="mb-4 text-sm text-gray-600">
        {{-- {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }} --}}
        {{-- 👇 SAYA SESUAIKAN KE BAHASA INDONESIA 👇 --}}
        {{ __('Terima kasih telah mendaftar! Sebelum memulai, dapatkah Anda memverifikasi alamat email Anda dengan mengeklik tautan yang baru saja kami kirimkan melalui email kepada Anda? Jika Anda tidak menerima email tersebut, kami dengan senang hati akan mengirimkan email baru.') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-600">
            {{-- {{ __('A new verification link has been sent to the email address you provided during registration.') }} --}}
            {{-- 👇 SAYA SESUAIKAN KE BAHASA INDONESIA 👇 --}}
            {{ __('Tautan verifikasi baru telah dikirim ke alamat email yang Anda berikan saat pendaftaran.') }}
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <x-primary-button wire:click="sendVerification">
            {{-- {{ __('Resend Verification Email') }} --}}
            {{-- 👇 SAYA SESUAIKAN KE BAHASA INDONESIA 👇 --}}
            {{ __('Kirim Ulang Email Verifikasi') }}
        </x-primary-button>

        <button wire:click="logout" type="submit" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            {{-- {{ __('Log Out') }} --}}
            {{-- 👇 SAYA SESUAIKAN KE BAHASA INDONESIA 👇 --}}
            {{ __('Keluar') }}
        </button>
    </div>
</div>