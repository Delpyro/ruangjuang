<?php
namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;

class DashboardManage extends Component
{
    public $userCount;
    public $recentUsers;

    public function mount()
    {
        $this->userCount = User::count();
        $this->recentUsers = User::latest()->take(5)->get();
    }

    public function render()
    {
        return view('livewire.admin.dashboard-manage')
            ->layout('layouts.admin'); 
    }
}