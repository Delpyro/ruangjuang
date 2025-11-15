<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TinyMceController extends Controller
{
    /**
     * Menangani upload gambar dari TinyMCE.
     * Endpoint ini wajib mengembalikan JSON dengan key 'location' (URL gambar).
     */
    public function uploadImage(Request $request)
    {
        // 1. Validasi: TinyMCE mengirim file dengan key 'file'
        $request->validate([
            // Memastikan file ada, berupa gambar, format diizinkan, dan ukuran maksimum 5MB
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:5000', 
        ]);

        if ($request->hasFile('file')) {
            try {
                $file = $request->file('file');
                
                // 2. Simpan file ke direktori 'public/uploads/tryouts' pada disk 'public'
                // Pastikan Anda telah menjalankan 'php artisan storage:link'
                $path = $file->store('uploads/tryouts', 'public');
                
                // 3. Mengembalikan JSON dengan key 'location'
                // asset('storage/' . $path) akan menghasilkan URL publik untuk file yang disimpan.
                return response()->json(['location' => asset('storage/' . $path)]);

            } catch (\Exception $e) {
                // Tangani error saat penyimpanan (misal: permission denied)
                // Kembalikan response error 500
                return response()->json(['error' => 'Upload failed: ' . $e->getMessage()], 500);
            }
        }

        // Jika tidak ada file yang terlampir (seharusnya terdeteksi oleh validasi, tapi ini sebagai fallback)
        return response()->json(['error' => 'No file uploaded.'], 400);
    }
}