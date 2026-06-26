@extends('layouts.app')

@section('title', 'Masuk ke Akun Anda')

@section('content')
<div class="max-w-md mx-auto my-12">
    <div class="bg-white p-8 rounded-2xl border border-slate-100 shadow-sm">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Selamat Datang Kembali</h1>
            <p class="text-sm text-slate-500 mt-2">Silakan masuk untuk melanjutkan transaksi atau pengelolaan toko Anda</p>
        </div>

        <form action="{{ route('login') }}" method="POST" class="space-y-5">
            @csrf
            
            <div>
                <label for="email" class="block text-sm font-semibold text-slate-700 mb-1.5">Alamat Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-600/20 focus:border-indigo-600 transition placeholder-slate-400 text-sm"
                    placeholder="nama@email.com">
                @error('email')
                    <p class="text-xs text-red-500 mt-1.5 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-semibold text-slate-700 mb-1.5">Password</label>
                <input type="password" name="password" id="password" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-600/20 focus:border-indigo-600 transition placeholder-slate-400 text-sm"
                    placeholder="Masukkan password Anda">
                @error('password')
                    <p class="text-xs text-red-500 mt-1.5 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between text-sm">
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded text-indigo-600 border-slate-300 focus:ring-indigo-500">
                    <span class="text-slate-600">Ingat Saya</span>
                </label>
            </div>

            <button type="submit" 
                class="w-full py-3 px-4 inline-flex items-center justify-center text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-xl shadow-sm transition">
                Masuk ke Akun
            </button>
        </form>

        <div class="text-center mt-6 pt-6 border-t border-slate-100">
            <p class="text-sm text-slate-500">
                Belum punya akun? 
                <a href="{{ route('register') }}" class="font-semibold text-indigo-600 hover:text-indigo-700 transition">Daftar Sekarang</a>
            </p>
        </div>
    </div>
</div>
@endsection