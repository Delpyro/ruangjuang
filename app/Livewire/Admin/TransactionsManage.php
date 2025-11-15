<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Transaction;
use Livewire\WithPagination;
use App\Services\MidtransService; 
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf; // Import PDF facade

class TransactionsManage extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    // --- Properti untuk Modal Detail ---
    public $showModal = false;
    public ?Transaction $selectedTransaction = null; 

    // --- Properti untuk Filtering & Searching ---
    public $search = '';
    public $filterStatus = 'all'; // 'all', 'success', 'pending', 'failed'
    public $filterMonth = 'all';  // Properti untuk filter bulan

    // Hubungkan ke query string URL
    protected $queryString = [
        'search' => ['except' => ''],
        'filterStatus' => ['except' => 'all', 'as' => 'status'],
        'filterMonth' => ['except' => 'all', 'as' => 'month'],
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterStatus()
    {
        $this->resetPage();
    }
    
    public function updatedFilterMonth()
    {
        $this->resetPage();
    }

    /**
     * Membuat Query Builder untuk Transaksi berdasarkan filter saat ini.
     */
    protected function getBaseTransactionsQuery()
    {
        return Transaction::query()
            ->with(['user:id,name', 'tryout:id,title', 'bundle:id,title'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('order_id', 'like', '%' . $this->search . '%')
                        ->orWhere('payment_method', 'like', '%' . $this->search . '%')
                        ->orWhereHas('user', function ($uq) {
                            $uq->where('name', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->filterStatus, function ($query) {
                if ($this->filterStatus === 'success') {
                    $query->success();
                } elseif ($this->filterStatus === 'pending') {
                    $query->pending();
                } elseif ($this->filterStatus === 'failed') {
                    $query->failed();
                }
            })
            ->when($this->filterMonth && $this->filterMonth !== 'all', function ($query) {
                try {
                    $date = Carbon::createFromFormat('Y-m', $this->filterMonth);
                    $query->whereYear('created_at', $date->year)
                        ->whereMonth('created_at', $date->month);
                } catch (\Exception $e) {
                    // Abaikan jika formatnya tidak valid
                }
            })
            ->orderBy('created_at', 'desc');
    }

    /**
     * Render komponen.
     */
    public function render()
    {
        $transactions = $this->getBaseTransactionsQuery()->paginate(10); 

        // Buat daftar bulan untuk dropdown filter
        $months = Transaction::select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month_year'))
            ->whereNotNull('created_at')
            ->groupBy('month_year')
            ->orderBy('month_year', 'desc')
            ->limit(12)
            ->get()
            ->map(function ($item) {
                $date = Carbon::createFromFormat('Y-m', $item->month_year);
                return [
                    'value' => $item->month_year,
                    'label' => $date->isoFormat('MMMM YYYY')
                ];
            });

        return view('livewire.admin.transactions-manage', [
            'transactions' => $transactions,
            'months' => $months,
        ])->layout('layouts.admin');
    }

    /**
     * Mengambil data terfilter dan mengekspornya ke format PDF.
     */
    public function exportToPdf()
    {
        // 1. Ambil data dengan query yang sama (tanpa pagination)
        $transactions = $this->getBaseTransactionsQuery()->get();
        
        // 2. Tentukan nama file
        $fileName = 'transactions_export';
        if ($this->filterStatus !== 'all') {
            $fileName .= '_' . $this->filterStatus;
        }
        if ($this->filterMonth !== 'all') {
            $fileName .= '_' . $this->filterMonth;
        }
        $fileName .= '_' . Carbon::now()->format('Ymd_His') . '.pdf';

        // 3. Muat view yang didedikasikan untuk PDF dengan data transaksi
        $pdf = PDF::loadView('pdf.transactions-report', [
            'transactions' => $transactions,
            'filterStatus' => $this->filterStatus,
            'filterMonth' => $this->filterMonth,
        ]);

        // 4. Download PDF
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, $fileName);
    }

    /**
     * Membuka modal untuk melihat detail transaksi.
     */
    public function openModal($id)
    {
        $this->selectedTransaction = Transaction::with(['user', 'tryout', 'bundle'])->find($id);
        $this->showModal = true;
    }

    /**
     * Menutup modal.
     */
    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedTransaction = null; 
    }

    /**
     * Sinkronisasi status transaksi dengan Midtrans.
     */
    public function syncStatus($orderId, MidtransService $midtransService)
    {
        if (!$orderId) {
            session()->flash('error', 'Order ID tidak ditemukan.');
            return;
        }

        try {
            $statusResult = $midtransService->getStatus($orderId);

            if ($statusResult['success']) {
                $midtransService->handleNotification((array)$statusResult['response']);
                session()->flash('success', 'Status transaksi ' . $orderId . ' berhasil disinkronkan.');
            } else {
                $transaction = Transaction::where('order_id', $orderId)->first();
                if ($transaction && $transaction->isPending()) {
                    $transaction->update(['status' => 'expire']);
                    session()->flash('success', 'Transaksi ' . $orderId . ' ditandai sebagai expired (404).');
                } else {
                    session()->flash('error', 'Gagal sinkronisasi: ' . $statusResult['error']);
                }
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal sinkronisasi: ' . $e->getMessage());
        }
    }
}