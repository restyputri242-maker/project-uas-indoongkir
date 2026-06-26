@extends('layouts.app')

@section('title', 'Keranjang Belanja')

@section('content')
<div class="space-y-6">
    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
        <h1 class="text-xl font-bold text-slate-900 tracking-tight">Keranjang Belanja</h1>
        <p class="text-sm text-slate-500 mt-1">Kelola item pilihan Anda sebelum melakukan pembayaran</p>
    </div>

    @if(empty($cartItems))
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm text-center py-16 px-4">
            <div class="w-16 h-16 bg-slate-50 text-slate-400 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-slate-100">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                </svg>
            </div>
            <h3 class="text-base font-bold text-slate-800">Keranjang Anda Kosong</h3>
            <p class="text-sm text-slate-500 mt-1 max-w-sm mx-auto">Anda belum menambahkan produk apapun ke keranjang belanja.</p>
            <a href="{{ route('shop') }}" class="mt-4 inline-flex items-center justify-center py-2.5 px-6 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-xl shadow-sm transition">
                Mulai Belanja &rarr;
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Items Column -->
            <div class="lg:col-span-2 space-y-4">
                @foreach($cartItems as $item)
                    @php $product = $item['product']; @endphp
                    <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <!-- Image -->
                            <div class="w-16 h-16 rounded-xl bg-slate-100 border border-slate-200 overflow-hidden flex-shrink-0 flex items-center justify-center">
                                @if($product->image_path)
                                    <img src="{{ asset($product->image_path) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-slate-400">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                    </svg>
                                @endif
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-800">{{ $product->name }}</h3>
                                <p class="text-xs text-slate-500 mt-0.5">Berat: {{ $product->formatted_weight }} | Stok: {{ $product->stock }}</p>
                                <p class="text-sm font-semibold text-slate-800 mt-1.5">{{ $product->formatted_price }}</p>
                            </div>
                        </div>

                        <!-- Actions & Qty -->
                        <div class="flex items-center justify-between sm:justify-end w-full sm:w-auto gap-6 border-t sm:border-0 pt-4 sm:pt-0">
                            <!-- Update Quantity Form -->
                            <form action="{{ route('cart.update', $product->id) }}" method="POST" class="flex items-center border border-slate-200 rounded-xl overflow-hidden bg-slate-50">
                                @csrf
                                @method('PUT')
                                <button type="submit" name="quantity" value="{{ $item['quantity'] - 1 }}" 
                                    class="px-3 py-1.5 text-slate-500 hover:bg-slate-100 hover:text-slate-800 font-semibold transition"
                                    {{ $item['quantity'] <= 1 ? 'disabled' : '' }}>
                                    -
                                </button>
                                <span class="px-4 text-sm font-semibold text-slate-800">{{ $item['quantity'] }}</span>
                                <button type="submit" name="quantity" value="{{ $item['quantity'] + 1 }}" 
                                    class="px-3 py-1.5 text-slate-500 hover:bg-slate-100 hover:text-slate-800 font-semibold transition"
                                    {{ $item['quantity'] >= $product->stock ? 'disabled' : '' }}>
                                    +
                                </button>
                            </form>

                            <!-- Remove Form -->
                            <form action="{{ route('cart.destroy', $product->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-xl transition" title="Hapus Item">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Summary Column -->
            <div class="lg:col-span-1">
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm space-y-6 sticky top-24">
                    <h2 class="text-base font-bold text-slate-800">Ringkasan Pesanan</h2>
                    
                    <div class="space-y-3.5 text-sm text-slate-600 border-b border-slate-100 pb-4">
                        <div class="flex justify-between">
                            <span>Subtotal Barang</span>
                            <span class="font-semibold text-slate-800">Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Total Berat</span>
                            <span class="font-semibold text-slate-800">
                                @if($totalWeight >= 1000)
                                    {{ ($totalWeight / 1000) }} kg
                                @else
                                    {{ $totalWeight }} gram
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="flex justify-between text-base font-bold text-slate-800">
                        <span>Total Belanja</span>
                        <span>Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
                    </div>

                    <a href="{{ route('checkout') }}" 
                        class="w-full py-3 px-4 inline-flex items-center justify-center text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-xl shadow-sm hover:shadow transition">
                        Lanjut ke Checkout
                    </a>
                    
                    <a href="{{ route('shop') }}" 
                        class="w-full py-3 px-4 inline-flex items-center justify-center text-sm font-semibold text-slate-600 hover:text-slate-800 hover:bg-slate-50 rounded-xl transition">
                        Kembali Belanja
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection