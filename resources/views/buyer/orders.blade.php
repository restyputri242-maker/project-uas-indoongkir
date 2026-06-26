@extends('layouts.app')

@section('title', 'Pesanan Saya')

@section('content')
<div class="space-y-6">
    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
        <h1 class="text-xl font-bold text-slate-900 tracking-tight">Pesanan Saya</h1>
        <p class="text-sm text-slate-500 mt-1">Pantau status pembayaran, pengiriman, dan riwayat belanja Anda</p>
    </div>

    @if($transactions->isEmpty())
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm text-center py-16 px-4">
            <div class="w-16 h-16 bg-slate-50 text-slate-400 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-slate-100">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-.621-.504-1.125-1.125-1.125H9.75M3 16.25V3.375C3 2.615 3.615 2 4.375 2h11.25c.76 0 1.375.615 1.375 1.375v14.5c0 .76-.615 1.375-1.375 1.375H4.375A1.375 1.375 0 013 16.25z" />
                </svg>
            </div>
            <h3 class="text-base font-bold text-slate-800">Belum Ada Transaksi</h3>
            <p class="text-sm text-slate-500 mt-1 max-w-sm mx-auto">Anda belum pernah melakukan pembelian produk apapun.</p>
            <a href="{{ route('shop') }}" class="mt-4 inline-flex items-center justify-center py-2.5 px-6 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-xl shadow-sm transition">
                Mulai Belanja &rarr;
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($transactions as $tx)
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <!-- Tx Header -->
                    <div class="bg-slate-50/50 border-b border-slate-100 px-6 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-sm">
                        <div class="flex flex-wrap items-center gap-3">
                            <span class="font-bold text-slate-800">ID Transaksi: #{{ $tx->id }}</span>
                            <span class="text-slate-400">•</span>
                            <span class="text-slate-500">{{ $tx->created_at->format('d M Y, H:i') }}</span>
                            <span class="text-slate-400">•</span>
                            {!! $tx->status_badge !!}
                        </div>
                        <div class="font-semibold text-indigo-600">
                            {{ $tx->formatted_total }}
                        </div>
                    </div>

                    <!-- Tx Body -->
                    <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Left: Items -->
                        <div class="md:col-span-2 space-y-3">
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Item Pembelian</h4>
                            <div class="space-y-2">
                                @foreach($tx->items as $item)
                                    <div class="flex justify-between items-center text-sm">
                                        <div class="text-slate-700">
                                            <span class="font-semibold text-slate-800">{{ $item->product_name }}</span>
                                            <span class="text-slate-500">x{{ $item->quantity }}</span>
                                        </div>
                                        <span class="font-semibold text-slate-800">{{ $item->formatted_subtotal }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Right: Shipping -->
                        <div class="space-y-3 border-t md:border-t-0 md:border-l border-slate-100 pt-4 md:pt-0 md:pl-6 text-sm">
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Informasi Pengiriman</h4>
                            
                            <div class="space-y-2 text-slate-600">
                                <p><span class="font-semibold text-slate-700">Tujuan:</span> {{ $tx->city }}, {{ $tx->province }}</p>
                                <p class="text-xs bg-slate-50 p-2 rounded-lg border border-slate-100 line-clamp-2">{{ $tx->address_details }}</p>
                                <p><span class="font-semibold text-slate-700">Kurir:</span> {{ $tx->courier }} ({{ $tx->service }})</p>
                                <p><span class="font-semibold text-slate-700">Ongkir:</span> {{ $tx->formatted_shipping_cost }}</p>
                                
                                @if($tx->tracking_number)
                                    <div class="mt-3 bg-indigo-50/50 p-2.5 rounded-xl border border-indigo-100 text-indigo-900 flex flex-col gap-1">
                                        <span class="text-[10px] font-bold text-indigo-500 uppercase tracking-wider">Nomor Resi / Pelacakan</span>
                                        <span class="font-bold text-sm tracking-wide">{{ $tx->tracking_number }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Tx Footer actions -->
                    <div class="border-t border-slate-100 px-6 py-4 flex flex-col sm:flex-row items-center sm:justify-between gap-4 bg-slate-50/20">
                        <div class="text-xs text-slate-500">
                            Total Berat: {{ $tx->weight >= 1000 ? ($tx->weight / 1000) . ' kg' : $tx->weight . ' gram' }}
                        </div>
                        
                        <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
                            <!-- Cetak invoice -->
                            <a href="{{ route('orders.invoice', $tx->id) }}" target="_blank"
                                class="inline-flex items-center justify-center gap-2 py-2 px-4 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 active:bg-slate-100 rounded-xl border border-slate-200 shadow-sm transition">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-slate-400">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.617 0-1.11-.497-1.12-1.115L6.25 18m11.41 0H6.25m11.41-3.321c.542-.053 1.077-.113 1.607-.179a1.125 1.125 0 0 0 .97-1.118V6.187a1.125 1.125 0 0 0-.97-1.118 48.003 48.003 0 0 0-30.7 0c-.54.067-.97.525-.97 1.118v5.204c0 .547.4.1 1.002.983a1.125 1.125 0 0 0 .97 1.118c.53.066 1.065.126 1.607.179m12.3-8.818v2.25M9 7.5v2.25m-3-2.25h12" />
                                </svg>
                                Cetak Invoice
                            </a>

                            <!-- If status is dikirim, buyer can complete the order -->
                            @if($tx->status === 'dikirim')
                                <form action="{{ route('admin.orders.status', $tx->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="selesai">
                                    <button type="submit" 
                                        class="inline-flex items-center justify-center py-2 px-4 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-xl shadow-sm transition">
                                        Pesanan Selesai / Diterima
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection