@extends('layouts.app')

@section('title', 'Katalog Toko UMKM')

@section('content')
<div class="space-y-12">
    <!-- Hero / Banner Section -->
    <div class="relative bg-gradient-to-r from-indigo-50 to-slate-50 border border-slate-100 rounded-3xl p-8 sm:p-12 overflow-hidden shadow-sm">
        <div class="relative z-10 max-w-xl space-y-4">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800">
                Toko UMKM Lokal
            </span>
            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900 leading-none">
                Belanja Mudah, <br>
                <span class="text-indigo-600">Ongkir Real-Time.</span>
            </h1>
            <p class="text-slate-500 text-sm sm:text-base leading-relaxed">
                Temukan produk lokal pilihan berkualitas terbaik dengan kalkulasi biaya pengiriman otomatis ke seluruh Indonesia menggunakan RajaOngkir.
            </p>
        </div>
        
        <!-- Decorative subtle background shape -->
        <div class="absolute right-0 bottom-0 translate-x-12 translate-y-12 w-64 h-64 rounded-full bg-indigo-100/50 filter blur-3xl -z-10"></div>
    </div>

    <!-- Product Catalog Section -->
    <div class="space-y-6">
        <div>
            <h2 class="text-xl font-bold text-slate-800 tracking-tight">Katalog Produk</h2>
            <p class="text-sm text-slate-500 mt-1">Daftar produk terbaik yang siap dikirim hari ini</p>
        </div>

        @if($products->isEmpty())
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm text-center py-16 px-4">
                <div class="w-16 h-16 bg-slate-50 text-slate-400 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-slate-100">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72M6.75 18h3.5a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75h-3.5a.75.75 0 00-.75.75v3.75c0 .414.336.75.75.75z" />
                    </svg>
                </div>
                <h3 class="text-base font-bold text-slate-800">Katalog Kosong</h3>
                <p class="text-sm text-slate-500 mt-1 max-w-sm mx-auto">Saat ini belum ada produk yang siap dijual di toko.</p>
            </div>
        @else
            <!-- Grid container -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                @foreach($products as $product)
                    <div class="bg-white rounded-2xl border border-slate-100 hover:border-slate-200 shadow-sm hover:shadow transition-all duration-300 flex flex-col justify-between overflow-hidden group">
                        <!-- Product Image Area -->
                        <div class="relative aspect-square w-full bg-slate-50 flex items-center justify-center border-b border-slate-50 overflow-hidden">
                            @if($product->image_path)
                                <img src="{{ asset($product->image_path) }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.2" stroke="currentColor" class="w-12 h-12 text-slate-300">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                </svg>
                            @endif

                            <!-- Weight Tag -->
                            <span class="absolute top-3 left-3 inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-black/60 backdrop-blur-md text-white">
                                {{ $product->formatted_weight }}
                            </span>
                        </div>

                        <!-- Card Body details -->
                        <div class="p-5 flex-grow flex flex-col justify-between gap-4">
                            <div class="space-y-1">
                                <h3 class="font-bold text-slate-800 text-base line-clamp-1 group-hover:text-indigo-600 transition">{{ $product->name }}</h3>
                                <p class="text-xs text-slate-400 font-medium">Stok: {{ $product->stock }} pcs</p>
                                <p class="text-xs text-slate-500 line-clamp-2 mt-1.5 leading-relaxed">{{ $product->description ?? 'Tidak ada deskripsi.' }}</p>
                            </div>

                            <div class="flex items-center justify-between gap-3 pt-3 border-t border-slate-50">
                                <span class="text-base font-extrabold text-slate-900">{{ $product->formatted_price }}</span>

                                <!-- Add to Cart Button -->
                                <form action="{{ route('cart.add', $product->id) }}" method="POST" class="flex items-center gap-1.5">
                                    @csrf
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" 
                                        class="inline-flex items-center justify-center p-2.5 text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-xl shadow-sm hover:shadow transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection