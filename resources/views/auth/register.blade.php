@extends('layouts.app')

@section('title', 'Daftar Akun Baru')

@section('content')
<div class="max-w-md mx-auto my-12">
    <div class="bg-white p-8 rounded-2xl border border-slate-100 shadow-sm">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Daftar Akun Baru</h1>
            <p class="text-sm text-slate-500 mt-2">Buat akun untuk mulai berbelanja atau mengelola toko UMKM Anda</p>
        </div>

        <form action="{{ route('register') }}" method="POST" class="space-y-5">
            @csrf
            
            <div>
                <label for="name" class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Lengkap</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-600/20 focus:border-indigo-600 transition placeholder-slate-400 text-sm"
                    placeholder="Nama lengkap Anda">
                @error('name')
                    <p class="text-xs text-red-500 mt-1.5 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-semibold text-slate-700 mb-1.5">Alamat Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-600/20 focus:border-indigo-600 transition placeholder-slate-400 text-sm"
                    placeholder="nama@email.com">
                @error('email')
                    <p class="text-xs text-red-500 mt-1.5 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="role" class="block text-sm font-semibold text-slate-700 mb-1.5">Daftar Sebagai</label>
                <select name="role" id="role" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-600/20 focus:border-indigo-600 transition text-sm bg-white">
                    <option value="buyer" {{ old('role') == 'buyer' ? 'selected' : '' }}>Pembeli (Mencari Produk & Hitung Ongkir)</option>
                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin (Kelola Produk & Transaksi)</option>
                </select>
                @error('role')
                    <p class="text-xs text-red-500 mt-1.5 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-semibold text-slate-700 mb-1.5">Password</label>
                <input type="password" name="password" id="password" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-600/20 focus:border-indigo-600 transition placeholder-slate-400 text-sm"
                    placeholder="Minimal 8 karakter">
                @error('password')
                    <p class="text-xs text-red-500 mt-1.5 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-semibold text-slate-700 mb-1.5">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-600/20 focus:border-indigo-600 transition placeholder-slate-400 text-sm"
                    placeholder="Ulangi password Anda">
            </div>

            <button type="submit" 
                class="w-full py-3 px-4 inline-flex items-center justify-center text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-xl shadow-sm transition">
                Daftar Akun Baru
            </button>
        </form>

        <div class="text-center mt-6 pt-6 border-t border-slate-100">
            <p class="text-sm text-slate-500">
                Sudah punya akun? 
                <a href="{{ route('login') }}" class="font-semibold text-indigo-600 hover:text-indigo-700 transition">Masuk di sini</a>
            </p>
        </div>
    </div>
</div>
@endsection