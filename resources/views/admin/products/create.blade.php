@extends('layouts.app')

@section('title', 'Tambah Produk Baru')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Header Panel -->
    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4">
        <a href="{{ route('admin.products.index') }}" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-50 rounded-xl transition">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Tambah Produk Baru</h1>
            <p class="text-sm text-slate-500 mt-1">Masukkan rincian produk yang akan ditambahkan ke katalog toko</p>
        </div>
    </div>

    <!-- Form Panel -->
    <div class="bg-white p-8 rounded-2xl border border-slate-100 shadow-sm">
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div>
                <label for="name" class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Produk</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-600/20 focus:border-indigo-600 transition placeholder-slate-400 text-sm"
                    placeholder="Contoh: Kemeja Batik Premium">
                @error('name')
                    <p class="text-xs text-red-500 mt-1.5 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-semibold text-slate-700 mb-1.5">Deskripsi Produk</label>
                <textarea name="description" id="description" rows="4"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-600/20 focus:border-indigo-600 transition placeholder-slate-400 text-sm"
                    placeholder="Tuliskan spesifikasi, ukuran, atau rincian produk lainnya...">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-xs text-red-500 mt-1.5 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div>
                    <label for="price" class="block text-sm font-semibold text-slate-700 mb-1.5">Harga (Rp)</label>
                    <input type="number" name="price" id="price" value="{{ old('price') }}" required min="0"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-600/20 focus:border-indigo-600 transition placeholder-slate-400 text-sm"
                        placeholder="Contoh: 150000">
                    @error('price')
                        <p class="text-xs text-red-500 mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="stock" class="block text-sm font-semibold text-slate-700 mb-1.5">Stok Produk</label>
                    <input type="number" name="stock" id="stock" value="{{ old('stock') }}" required min="0"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-600/20 focus:border-indigo-600 transition placeholder-slate-400 text-sm"
                        placeholder="Contoh: 25">
                    @error('stock')
                        <p class="text-xs text-red-500 mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="weight" class="block text-sm font-semibold text-slate-700 mb-1.5">Berat (Gram)</label>
                    <input type="number" name="weight" id="weight" value="{{ old('weight') }}" required min="1"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-600/20 focus:border-indigo-600 transition placeholder-slate-400 text-sm"
                        placeholder="Contoh: 350 (untuk 0.35kg)">
                    @error('weight')
                        <p class="text-xs text-red-500 mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="image" class="block text-sm font-semibold text-slate-700 mb-1.5">Foto Produk</label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-200 border-dashed rounded-xl hover:border-indigo-400 transition cursor-pointer relative">
                    <div class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-slate-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="flex text-sm text-slate-600">
                            <label for="image" class="relative cursor-pointer bg-white rounded-md font-semibold text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                <span>Unggah file</span>
                                <input id="image" name="image" type="file" class="sr-only" accept="image/*" onchange="previewImage(this)">
                            </label>
                            <p class="pl-1">atau seret dan lepas</p>
                        </div>
                        <p class="text-xs text-slate-500">PNG, JPG, JPEG, WEBP hingga 2MB</p>
                    </div>
                </div>
                
                <!-- Image Preview Container -->
                <div id="image-preview-container" class="mt-4 hidden">
                    <p class="text-xs font-semibold text-slate-500 mb-2">Pratinjau Foto:</p>
                    <div class="w-32 h-32 rounded-xl border border-slate-200 overflow-hidden bg-slate-50">
                        <img id="image-preview" src="#" alt="Pratinjau" class="w-full h-full object-cover">
                    </div>
                </div>

                @error('image')
                    <p class="text-xs text-red-500 mt-1.5 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-4 flex gap-4">
                <a href="{{ route('admin.products.index') }}" 
                    class="w-1/2 py-3 px-4 inline-flex items-center justify-center text-sm font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 active:bg-slate-300 rounded-xl transition">
                    Batalkan
                </a>
                <button type="submit" 
                    class="w-1/2 py-3 px-4 inline-flex items-center justify-center text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-xl shadow-sm transition">
                    Simpan Produk
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function previewImage(input) {
        const previewContainer = document.getElementById('image-preview-container');
        const preview = document.getElementById('image-preview');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.src = e.target.result;
                previewContainer.classList.remove('hidden');
            }
            
            reader.readAsDataURL(input.files[0]);
        } else {
            preview.src = '#';
            previewContainer.classList.add('hidden');
        }
    }
</script>
@endsection