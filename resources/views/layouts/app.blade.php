<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>{{ $title ?? 'Ruang Juang | Bimbel Persiapan Tes Kedinasan dan CPNS' }}</title>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    @vite(['resources/css/homepage.css', 'resources/js/homepage.js'])
    <link rel="shortcut icon" href="{{ asset('images/logorj.ico') }}" type="image/x-icon">
    {{-- PASTIKAN CSS AOS DIMUAT DI HEAD --}}
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" /> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> 
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* FIX: MENCEGAH SCROLLBAR HORIZONTAL PADA MOBILE */
        html, body {
            overflow-x: hidden; 
        }
    </style>

    <script>
        // ... (Semua script Snap.js Anda tetap sama) ...
        window.snapInitialized = false;
        window.pendingSnapToken = null;
        window.initializeMidtransSnap = function(snapToken, clientKey) {
            console.log('🔧 Initializing Midtrans Snap...');
            if (!snapToken || !clientKey) { console.error('❌ Missing Snap token or client key'); return; }
            window.pendingSnapToken = snapToken;
            if (typeof snap !== 'undefined') { executeSnapPayment(snapToken); } else { loadSnapJS(clientKey); }
        };
        function loadSnapJS(clientKey) {
            const scriptId = 'midtrans-snap-script';
            const oldScript = document.getElementById(scriptId);
            if (oldScript) { oldScript.remove(); }
            const script = document.createElement('script');
            script.id = scriptId;
            script.src = 'https://app.sandbox.midtrans.com/snap/snap.js';
            script.setAttribute('data-client-key', clientKey);
            script.onload = function() {
                console.log('✅ Snap.js loaded successfully');
                window.snapInitialized = true;
                if (window.pendingSnapToken) { executeSnapPayment(window.pendingSnapToken); }
            };
            script.onerror = function() { console.error('❌ Failed to load Snap.js'); showSnapError(); };
            document.head.appendChild(script);
        }
        function executeSnapPayment(snapToken) {
            console.log('💳 Executing Snap payment...');
            try {
                snap.pay(snapToken, {
                    onSuccess: function(result) { window.location.href = "{{ route('payment.finish') }}?order_id=" + result.order_id; },
                    onPending: function(result) { window.location.href = "{{ route('payment.pending') }}?order_id=" + result.order_id; },
                    onError: function(result) { window.location.href = "{{ route('payment.error') }}?order_id=" + result.order_id; },
                    onClose: function() { console.log('📱 Payment popup closed by user'); }
                });
            } catch (error) { console.error('💥 Snap execution error:', error); showSnapError(); }
        }
        function showSnapError() {
            const container = document.getElementById('snap-container');
            if (container) {
                container.innerHTML = `
                    <div class="text-center py-8">
                        <i class="fas fa-exclamation-triangle text-red-500 text-4xl mb-4"></i>
                        <p class="text-red-600 mb-4">Gagal memuat halaman pembayaran</p>
                        <button onclick="location.reload()" class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-primary-dark transition-colors">
                            <i class="fas fa-redo mr-2"></i> Refresh Halaman
                        </button>
                    </div>
                `;
            }
        }
    </script>
</head>
<body class="bg-white text-gray-800 font-poppins transition-colors duration-400 scroll-smooth">
    
    <nav class="fixed top-0 left-0 right-0 z-50 py-4 px-4 sm:px-6 bg-white/90 backdrop-blur-md border-b border-gray-200">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center">
                {{-- Logo mengarah ke root URL / halaman ini sendiri --}}
                <a href="{{ url('/') }}">
                    <img src="{{ asset('images/logorj.png') }}" alt="Logo RuangJuang" class="h-16">
                </a>
            </div>

            {{-- NAVIGASI DESKTOP --}}
            <div class="hidden md:flex space-x-8">
                {{-- Link Beranda mengarah ke root URL / halaman ini sendiri --}}
                <a href="{{ url('/') }}" class="nav-link font-medium text-gray-800 hover:text-primary transition-colors"><i class="fas fa-home"></i> Beranda</a>
                
                @auth
                {{-- Link untuk user yang sudah login --}}
                <a href="{{ route('tryout.index') }}" class="nav-link font-medium text-gray-800 hover:text-primary transition-colors"><i class="fas fa-file-alt"></i> Tryout</a>
                <a href="{{ route('bundle.index') }}" class="nav-link font-medium text-gray-800 hover:text-primary transition-colors"><i class="fas fa-file-alt"></i> Bundle</a>
                <a href="{{ route('tryout.my-tryouts') }}" class="nav-link font-medium text-gray-800 hover:text-primary transition-colors"><i class="fas fa-graduation-cap"></i> Tryoutku</a>
                
                <a href="{{ route('testimonials.index') }}" class="nav-link font-medium text-gray-800 hover:text-primary transition-colors">
                    <i class="fas fa-comments"></i> Testimoni
                </a>
                
                <a 
                    href="https://wa.me/6285769163218" 
                    target="_blank" 
                    class="nav-link font-medium text-gray-800 hover:text-primary transition-colors"
                >
                    <i class="fab fa-whatsapp"></i> MinRuJu
                </a>
                @else
                
                <!-- ================== PERBAIKAN 1 (Desktop) ================== -->
                {{-- Jika di Homepage, gunakan anchor #. Jika tidak, gunakan URL lengkap --}}
                @if (Request::is('/'))
                    <a href="#promo" class="nav-link font-medium text-gray-800 hover:text-primary transition-colors"><i class="fas fa-percent"></i> Promo</a>
                    <a href="#profil" class="nav-link font-medium text-gray-800 hover:text-primary transition-colors"><i class="fas fa-user-circle"></i> Profil</a>
                @else
                    <a href="{{ url('/#promo') }}" class="nav-link font-medium text-gray-800 hover:text-primary transition-colors"><i class="fas fa-percent"></i> Promo</a>
                    <a href="{{ url('/#profil') }}" class="nav-link font-medium text-gray-800 hover:text-primary transition-colors"><i class="fas fa-user-circle"></i> Profil</a>
                @endif
                <!-- ================== AKHIR PERBAIKAN 1 ================== -->
                
                <a href="{{ route('testimonials.index') }}" class="nav-link font-medium text-gray-800 hover:text-primary transition-colors">
                    <i class="fas fa-comments"></i> Testimoni
                </a>
                
                <a 
                    href="https://wa.me/6285769163218" 
                    target="_blank" 
                    class="nav-link font-medium text-gray-800 hover:text-primary transition-colors"
                >
                    <i class="fab fa-whatsapp"></i> MinRuJu
                </a>
                @endauth
                
            </div>

            <div class="flex items-center space-x-4">
                {{-- Tombol Keranjang DIHILANGKAN --}}

                @auth
                    {{-- Dropdown Profile User --}}
                    <div class="relative hidden md:block" id="profile-dropdown">
                        <button id="profile-button" class="flex items-center space-x-2 px-4 py-2 bg-primary text-white rounded-full font-medium hover:bg-primary-dark transition-colors">
                            <i class="fas fa-user"></i>
                            <span>{{ Auth::user()->name }}</span>
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        
                        <div id="dropdown-menu" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50 hidden">
                            <a href="{{ route('profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                <i class="fas fa-user-circle mr-2"></i>Profile
                            </a>
                            <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                            </a>
                            <a href="{{ route('transaction.history') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                <i class="fas fa-history mr-2"></i>History Transaksi
                            </a>
                            <a href="{{ route('rapor.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                <i class="fas fa-chart-line mr-2"></i>Rapor Saya
                            </a>
                            <div class="border-t border-gray-200 my-1"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 transition-colors">
                                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    {{-- Tombol Login/Daftar (Tamu) --}}
                    <div class="hidden md:flex items-center space-x-3">
                        <a href="{{ route('login') }}" class="px-4 py-2 text-primary border border-primary rounded-full font-medium hover:bg-primary hover:text-white transition-colors">
                            Login
                        </a>
                        <a href="{{ route('register') }}" class="px-4 py-2 bg-primary text-white rounded-full font-medium hover:bg-primary-dark transition-colors">
                            Daftar
                        </a>
                    </div>
                @endauth

                {{-- Tombol Hamburger Mobile --}}
                <button id="hamburger" class="hamburger md:hidden flex flex-col space-y-1.5 w-6 h-6 justify-center items-center">
                    <span class="hamburger-line block w-6 h-0.5 bg-gray-800 rounded"></span>
                    <span class="hamburger-line block w-6 h-0.5 bg-gray-800 rounded"></span>
                    <span class="hamburger-line block w-6 h-0.5 bg-gray-800 rounded"></span>
                </button>
            </div>
        </div>

        {{-- MOBILE MENU --}}
        <div id="mobileMenu" class="mobile-menu fixed top-16 left-0 w-64 h-screen bg-white shadow-lg md:hidden p-6 hidden overflow-y-auto">
            <div class="flex flex-col space-y-6">
                @auth
                    {{-- Menu User (Mobile) --}}
                    <div class="pb-4 border-b border-gray-200">
                        <p class="text-sm text-gray-500">Halo,</p>
                        <p class="font-semibold text-gray-800">{{ Auth::user()->name }}</p>
                    </div>
                    <a href="{{ route('profile') }}" class="nav-link font-medium hover:text-primary transition-colors flex items-center space-x-3">
                        <i class="fas fa-user-circle w-5"></i>
                        <span>Profile</span>
                    </a>
                    <a href="{{ route('dashboard') }}" class="nav-link font-medium hover:text-primary transition-colors flex items-center space-x-3">
                        <i class="fas fa-tachometer-alt w-5"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('transaction.history') }}" class="nav-link font-medium hover:text-primary transition-colors flex items-center space-x-3">
                        <i class="fas fa-history w-5"></i>
                        <span>History Transaksi</span>
                    </a>
                    <a href="{{ route('rapor.index') }}" class="nav-link font-medium hover:text-primary transition-colors flex items-center space-x-3">
                        <i class="fas fa-chart-line w-5"></i>
                        <span>Rapor Saya</span>
                    </a>
                    <div class="border-t border-gray-200 my-2"></div>
                @endauth

                <a href="{{ url('/') }}" class="nav-link font-medium hover:text-primary transition-colors flex items-center space-x-3">
                    <i class="fas fa-home w-5"></i>
                    <span>Beranda</span>
                </a>
                
                @auth
                    {{-- Menu User (Mobile) --}}
                    <a href="{{ route('tryout.index') }}" class="nav-link font-medium hover:text-primary transition-colors flex items-center space-x-3">
                        <i class="fas fa-file-alt w-5"></i>
                        <span>Tryout</span>
                    </a>
                    <a href="{{ route('bundle.index') }}" class="nav-link font-medium hover:text-primary transition-colors flex items-center space-x-3">
                        <i class="fas fa-file-alt w-5"></i>
                        <span>Bundle</span>
                    </a>
                    <a href="{{ route('tryout.my-tryouts') }}" class="nav-link font-medium hover:text-primary transition-colors flex items-center space-x-3">
                        <i class="fas fa-graduation-cap w-5"></i>
                        <span>Tryoutku</span>
                    </a>
                @else
                
                <!-- ================== PERBAIKAN 2 (Mobile) ================== -->
                @if (Request::is('/'))
                    <a href="#promo" class="nav-link font-medium hover:text-primary transition-colors flex items-center space-x-3">
                        <i class="fas fa-percent w-5"></i>
                        <span>Promo</span>
                    </a>
                    <a href="#profil" class="nav-link font-medium hover:text-primary transition-colors flex items-center space-x-3">
                        <i class="fas fa-user-circle w-5"></i>
                        <span>Profil</span>
                    </a>
                @else
                    <a href="{{ url('/#promo') }}" class="nav-link font-medium hover:text-primary transition-colors flex items-center space-x-3">
                        <i class="fas fa-percent w-5"></i>
                        <span>Promo</span>
                    </a>
                    <a href="{{ url('/#profil') }}" class="nav-link font-medium hover:text-primary transition-colors flex items-center space-x-3">
                        <i class="fas fa-user-circle w-5"></i>
                        <span>Profil</span>
                    </a>
                @endif
                <!-- ================== AKHIR PERBAIKAN 2 ================== -->

                @endauth
                
                <a href="{{ route('testimonials.index') }}" class="nav-link font-medium hover:text-primary transition-colors flex items-center space-x-3">
                    <i class="fas fa-comments w-5"></i>
                    <span>Testimoni</span>
                </a>
                
                <a 
                    href="https://wa.me/6285769163218" 
                    target="_blank"
                    class="nav-link font-medium hover:text-primary transition-colors flex items-center space-x-3"
                >
                    <i class="fab fa-whatsapp w-5"></i>
                    <span>MinRuJu</span>
                </a>
                
                @auth
                    {{-- Tombol Logout (Mobile) --}}
                    <div class="border-t border-gray-200 pt-4 space-y-3">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex items-center space-x-3 w-full text-red-600 hover:text-red-700 transition-colors">
                                <i class="fas fa-sign-out-alt w-5"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                @else
                    {{-- Tombol Login/Daftar (Mobile) --}}
                    <div class="border-t border-gray-200 pt-4 space-y-3">
                        <a href="{{ route('login') }}" class="block w-full text-center px-4 py-2 text-primary border border-primary rounded-full font-medium hover:bg-primary hover:text-white transition-colors">
                            Login
                        </a>
                        <a href="{{ route('register') }}" class="block w-full text-center px-4 py-2 bg-primary text-white rounded-full font-medium hover:bg-primary-dark transition-colors">
                            Daftar
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    {{ $slot }}

    <footer id="kontak" class="bg-gray-100 border-t border-gray-200 py-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-xl font-bold bg-gradient-to-r from-accent to-primary bg-clip-text text-transparent mb-4">Ruang Juang</h3>
                    <p class="text-gray-600 mb-4">Bimbingan belajar dan latihan Try Out untuk persiapan tes kedinasan dan CPNS, dengan metode terbaik serta materi dan soal yang selalu terupdate.</p>
                    <div class="flex space-x-4">
                        <a href="https://www.instagram.com/temanmuberjuang?igsh=YmNmZTBqOHVncjRp" class="text-gray-600 hover:text-primary transition-colors" target="_blank">
                            <i class="fab fa-instagram text-xl"></i>
                        </a>
                    </div>
                </div>

                <div>
                    <h4 class="font-semibold text-lg mb-4">Program</h4>
                    <ul class="space-y-2">
                        <!-- ================== PERBAIKAN 3 (Footer Program) ================== -->
                        @if (Request::is('/'))
                            <li><a href="#promo" class="nav-link text-gray-600 hover:text-primary transition-colors flex items-center"><i class="fas fa-file-alt w-4 mr-2"></i> Try Out SKD</a></li>
                            <li><a href="#promo" class="nav-link text-gray-600 hover:text-primary transition-colors flex items-center"><i class="fas fa-file-alt w-4 mr-2"></i> Bimbel Kedinasan</a></li>
                            <li><a href="#promo" class="nav-link text-gray-600 hover:text-primary transition-colors flex items-center"><i class="fas fa-file-alt w-4 mr-2"></i> Bimbel CPNS</a></li>
                            <li><a href="#promo" class="nav-link text-gray-600 hover:text-primary transition-colors flex items-center"><i class="fas fa-file-alt w-4 mr-2"></i> Kelas Intensif</a></li>
                        @else
                            <li><a href="{{ url('/#promo') }}" class="nav-link text-gray-600 hover:text-primary transition-colors flex items-center"><i class="fas fa-file-alt w-4 mr-2"></i> Try Out SKD</a></li>
                            <li><a href="{{ url('/#promo') }}" class="nav-link text-gray-600 hover:text-primary transition-colors flex items-center"><i class="fas fa-file-alt w-4 mr-2"></i> Bimbel Kedinasan</a></li>
                            <li><a href="{{ url('/#promo') }}" class="nav-link text-gray-600 hover:text-primary transition-colors flex items-center"><i class="fas fa-file-alt w-4 mr-2"></i> Bimbel CPNS</a></li>
                            <li><a href="{{ url('/#promo') }}" class="nav-link text-gray-600 hover:text-primary transition-colors flex items-center"><i class="fas fa-file-alt w-4 mr-2"></i> Kelas Intensif</a></li>
                        @endif
                        <!-- ================== AKHIR PERBAIKAN 3 ================== -->
                    </ul>
                </div>

                <div>
                    <h4 class="font-semibold text-lg mb-4">Bantuan</h4>
                    <ul class="space-y-2">
                        <li class="text-gray-600 hover:text-primary transition-colors">
                            <a href="https://www.instagram.com/temanmuberjuang?igsh=YmNmZTBqOHVncjRp" class="flex items-center hover:text-primary transition-colors" target="_blank"><i class="fab fa-instagram w-4 mr-2"></i> temanmuberjuang</a>
                        </li>
                        <li>
                            <a href="mailto:inruangjuang@gmail.com" class="text-gray-600 hover:text-primary transition-colors flex items-center"><i class="fas fa-envelope w-4 mr-2"></i>inruangjuang@gmail.com</a>
                        </li>
                        <li><a href="https://wa.me/6285769163218" class="text-gray-600 hover:text-primary transition-colors flex items-center" target="_blank"><i class="fab fa-whatsapp w-4 mr-2"></i>+62 857-6916-3218</a></li>
                        
                        <!-- ================== PERBAIKAN 4 (Footer Bantuan/FAQ) ================== -->
                        @if (Request::is('/'))
                            <li><a href="#faq" class="nav-link text-gray-600 hover:text-primary transition-colors flex items-center"><i class="fas fa-question-circle w-4 mr-2"></i>FAQ</a></li>
                        @else
                            <li><a href="{{ url('/#faq') }}" class="nav-link text-gray-600 hover:text-primary transition-colors flex items-center"><i class="fas fa-question-circle w-4 mr-2"></i>FAQ</a></li>
                        @endif
                        <!-- ================== AKHIR PERBAIKAN 4 ================== -->
                    </ul>
                </div>

                <div>
                    <h4 class="font-semibold text-lg mb-4">Kontak</h4>
                    <ul class="space-y-2">
                        <li>
                            <a href="https://www.instagram.com/temanmuberjuang?igsh=YmNmZTBqOHVncjRp" class="text-gray-600 hover:text-primary transition-colors flex items-center" target="_blank"><i class="fab fa-instagram w-4 mr-2"></i> temanmuberjuang</a>
                        </li>
                        <li class="flex items-center text-gray-600">
                            <i class="fas fa-map-marker-alt w-4 mr-2"></i> Jakarta, Indonesia
                        </li>
                        <li>
                            <a href="https://wa.me/6285769163218" class="text-gray-600 hover:text-primary transition-colors flex items-center" target="_blank">
                                <i class="fab fa-whatsapp w-4 mr-2"></i> +62 857-6916-3218
                            </a>
                        </li>
                        <li>
                            <a href="mailto:inruangjuang@gmail.com" class="text-gray-600 hover:text-primary transition-colors flex items-center"><i class="fas fa-envelope w-4 mr-2"></i>inruangjuang@gmail.com</a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-200 mt-8 pt-8 text-center text-gray-500 text-sm">
                <p>&copy; 2025 Ruang Juang. All rights reserved.</p>
            </div>
        </div>
    </footer>

    {{-- SCRIPTS: PENTING: AOS.js HARUS DIMUAT SEBELUM KODE YANG MEMANGGIL AOS.init() --}}
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
        // Inisialisasi AOS saat halaman dimuat pertama kali
        AOS.init(); 

        document.addEventListener('livewire:init', function () {
            console.log('🚀 Livewire initialized');
        });
        
        document.addEventListener('livewire:navigated', function () {
            console.log('🔄 Livewire navigated');
            
            // 🔥 SOLUSI UTAMA: Panggil AOS.init() lagi setelah Livewire memuat konten baru (navigasi)
            AOS.init();
            AOS.refresh(); // Tambahkan refresh untuk memastikan semua elemen diinisialisasi
            
            // Reset Snap state ketika navigasi terjadi
            window.snapInitialized = false;
            window.pendingSnapToken = null;
        });
        
        // Tambahkan event listener untuk me-refresh AOS setelah DOM content diload jika tidak ada Livewire navigation
        document.addEventListener('DOMContentLoaded', function() {
             AOS.refresh();
        });
    </script>
    
</body>
</html>