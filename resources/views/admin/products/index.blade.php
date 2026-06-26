@extends('layouts.app')

@section('title', 'Kelola Produk')

@section('content')
<div class="space-y-6">
    <!-- Header Panel -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Daftar Produk</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola stok, harga, dan rincian produk toko UMKM Anda</p>
        </div>
        <a href="{{ route('admin.products.create') }}" 
            class="inline-flex items-center justify-center gap-2 py-2.5 px-4 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-xl shadow-sm transition">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Tambah Produk Baru
        </a>
    </div>

    <!-- Products Table Panel -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        @if($products->isEmpty())
            <div class="text-center py-16 px-4">
                <div class="w-16 h-16 bg-slate-50 text-slate-400 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-slate-100">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                    </svg>
                </div>
                <h3 class="text-base font-bold text-slate-800">Tidak ada produk</h3>
                <p class="text-sm text-slate-500 mt-1 max-w-sm mx-auto">Silakan tambahkan produk baru untuk mengisi katalog toko UMKM Anda.</p>
                <a href="{{ route('admin.products.create') }}" class="mt-4 inline-flex items-center text-sm font-semibold text-indigo-600 hover:text-indigo-700 transition">
                    Tambah produk sekarang &rarr;
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-xs font-bold text-slate-500 uppercase tracking-wider">
                            <th class="py-4 px-6">Produk</th>
                            <th class="py-4 px-6 text-right">Harga</th>
                            <th class="py-4 px-6 text-center">Stok</th>
                            <th class="py-4 px-6 text-center">Berat</th>
                            <th class="py-4 px-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @foreach($products as $product)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-4 px-6 flex items-center gap-4">
                                    <!-- Photo Thumbnail -->
                                    <div class="w-12 h-12 rounded-xl bg-slate-100 border border-slate-200 overflow-hidden flex-shrink-0 flex items-center justify-center">
                                        @if($product->image_path)
                                            <img src="{{ asset($product->image_path) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-slate-400">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                            </svg>
                                        @endif
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-slate-800">{{ $product->name }}</h4>
                                        <p class="text-xs text-slate-500 line-clamp-1 mt-0.5">{{ $product->description ?? 'Tidak ada deskripsi.' }}</p>
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-right font-medium text-slate-800">
                                    {{ $product->formatted_price }}
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-lg text-xs font-semibold {{ $product->stock > 5 ? 'bg-indigo-50 text-indigo-700' : ($product->stock > 0 ? 'bg-amber-50 text-amber-700' : 'bg-red-50 text-red-700') }}">
                                        {{ $product->stock }} pcs
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-center text-slate-600 font-medium">
                                    {{ $product->formatted_weight }}
                                </td>
                                <td class="py-4 px-6">
                                    <div class="flex items-center justify-center gap-2">
                                        <!-- Edit button -->
                                        <a href="{{ route('admin.products.edit', $product->id) }}" 
                                            class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-slate-50 rounded-lg transition" title="Edit Produk">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                                            </svg>
                                        </a>

                                        <!-- Delete button -->
                                        <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition" title="Hapus Produk">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection