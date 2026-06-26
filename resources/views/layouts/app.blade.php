<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50 text-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'IndoOngkir') - Toko UMKM & Ongkir Real-Time</title>
    
    <!-- Google Fonts: Plus Jakarta Sans for Premium Aesthetic -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS compilation via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        /* Custom subtle scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
    @yield('styles')
</head>
<body class="flex flex-col min-h-screen">

    <!-- Top Navigation Bar -->
    <header class="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-slate-100">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-8">
                <!-- Logo -->
                <a href="{{ route('shop') }}" class="flex items-center gap-2 text-xl font-bold tracking-tight text-indigo-600 hover:opacity-90 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124l-.208-3.32a1.5 1.5 0 0 0-1.492-1.406h-2.128m2.2 7.75h-2.2m-7.44-8.25h11.666m-11.666 0H4.5m10.5 0V3h-4.5v11.25M4.5 9h15" />
                    </svg>
                    <span>Indo<span class="text-slate-800">Ongkir</span></span>
                </a>

                <!-- Shop Link (Hidden for Admin) -->
                @if(!Auth::check() || !Auth::user()->isAdmin())
                    <a href="{{ route('shop') }}" class="hidden sm:inline-flex items-center text-sm font-medium text-slate-600 hover:text-indigo-600 transition">
                        Katalog Produk
                    </a>
                @endif
            </div>

            <!-- Navigation Actions -->
            <div class="flex items-center gap-4">
                @if(Auth::check())
                    <!-- Logged In navigation -->
                    @if(Auth::user()->isAdmin())
                        <!-- Admin Links -->
                        <span class="hidden md:inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200">
                            Mode Admin
                        </span>
                        <a href="{{ route('admin.products.index') }}" class="text-sm font-medium text-slate-600 hover:text-indigo-600 transition {{ Request::is('admin/products*') ? 'text-indigo-600 font-semibold' : '' }}">
                            Kelola Produk
                        </a>
                        <a href="{{ route('admin.orders') }}" class="text-sm font-medium text-slate-600 hover:text-indigo-600 transition {{ Request::is('admin/orders*') ? 'text-indigo-600 font-semibold' : '' }}">
                            Kelola Transaksi
                        </a>
                    @else
                        <!-- Buyer Links -->
                        <a href="{{ route('buyer.orders') }}" class="text-sm font-medium text-slate-600 hover:text-indigo-600 transition {{ Request::is('buyer/orders*') ? 'text-indigo-600 font-semibold' : '' }}">
                            Pesanan Saya
                        </a>
                    @endif

                    <!-- User Dropdown/Menu -->
                    <div class="relative flex items-center gap-3 pl-3 border-l border-slate-200">
                        <div class="text-right hidden sm:block">
                            <p class="text-xs font-semibold text-slate-800">{{ Auth::user()->name }}</p>
                            <p class="text-[10px] text-slate-500 capitalize">{{ Auth::user()->role }}</p>
                        </div>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition" title="Log Out">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                                </svg>
                            </button>
                        </form>
                    </div>
                @else
                    <!-- Guest Navigation -->
                    <a href="{{ route('login') }}" class="text-sm font-medium text-slate-600 hover:text-indigo-600 transition">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-xl shadow-sm hover:shadow transition">
                        Daftar
                    </a>
                @endif

                <!-- Cart Button (Hidden for Admin) -->
                @if(!Auth::check() || !Auth::user()->isAdmin())
                    <a href="{{ route('cart.index') }}" class="relative p-2.5 text-slate-600 hover:text-indigo-600 hover:bg-slate-50 rounded-xl transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5.5 h-5.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                        </svg>
                        @php
                            $cartCount = array_sum(session('cart', []));
                        @endphp
                        @if($cartCount > 0)
                            <span class="absolute -top-1 -right-1 inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold leading-none text-white bg-indigo-600 rounded-full border-2 border-white">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </a>
                @endif
            </div>
        </nav>
    </header>

    <!-- Main Content Area -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Alerts/Flash Messages -->
        @if(session('success'))
            <div class="mb-6 p-4 rounded-xl border border-emerald-100 bg-emerald-50 text-emerald-800 flex items-center gap-3 animate-fade-in shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-emerald-500 flex-shrink-0">
                    <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd" />
                </svg>
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 rounded-xl border border-red-100 bg-red-50 text-red-800 flex items-center gap-3 animate-fade-in shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-red-500 flex-shrink-0">
                    <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25Zm-1.72 6.97a.75.75 0 1 0-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 1 0 1.06 1.06L12 13.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L13.06 12l1.72-1.72a.75.75 0 1 0-1.06-1.06L12 10.94l-1.72-1.72Z" clip-rule="evenodd" />
                </svg>
                <span class="text-sm font-medium">{{ session('error') }}</span>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-100 py-6 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-4">
            <p class="text-sm text-slate-500">
                &copy; {{ date('Y') }} IndoOngkir. Dibuat untuk Proyek Pemrograman Web.
            </p>
            <div class="flex items-center gap-4 text-xs font-semibold text-slate-400">
                <span>Mhs 1: Auth & CRUD Produk</span>
                <span>•</span>
                <span>Mhs 2: Keranjang & RajaOngkir API</span>
                <span>•</span>
                <span>Mhs 3: Transaksi & Invoice PDF</span>
            </div>
        </div>
    </footer>

    @yield('scripts')
</body>
</html>