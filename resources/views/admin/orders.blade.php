@extends('layouts.app')

@section('title', 'Kelola Transaksi')

@section('content')
<div class="space-y-6">
    <!-- Header Panel -->
    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
        <h1 class="text-xl font-bold text-slate-900 tracking-tight">Kelola Transaksi</h1>
        <p class="text-sm text-slate-500 mt-1">Pantau status transaksi masuk, input resi pengiriman, dan kelola status pesanan</p>
    </div>

    <!-- Statistics Panel -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Sales Card -->
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Penjualan Selesai</span>
            <span class="text-lg font-bold text-slate-800 mt-2">Rp {{ number_format($totalSales, 0, ',', '.') }}</span>
        </div>
        
        <!-- Pending Card -->
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Belum Bayar</span>
            <span class="text-lg font-bold text-amber-600 mt-2">{{ $pendingCount }} Pesanan</span>
        </div>

        <!-- Shipped Card -->
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Sedang Dikirim</span>
            <span class="text-lg font-bold text-blue-600 mt-2">{{ $shippedCount }} Pesanan</span>
        </div>

        <!-- Completed Card -->
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Selesai</span>
            <span class="text-lg font-bold text-emerald-600 mt-2">{{ $completedCount }} Pesanan</span>
        </div>
    </div>

    <!-- Orders List -->
    <div class="space-y-4">
        @if($transactions->isEmpty())
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm text-center py-16 px-4">
                <div class="w-16 h-16 bg-slate-50 text-slate-400 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-slate-100">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                </div>
                <h3 class="text-base font-bold text-slate-800">Tidak Ada Transaksi</h3>
                <p class="text-sm text-slate-500 mt-1 max-w-sm mx-auto">Belum ada pesanan masuk dari pembeli.</p>
            </div>
        @else
            @foreach($transactions as $tx)
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <!-- Header -->
                    <div class="bg-slate-50/50 border-b border-slate-100 px-6 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-sm">
                        <div class="flex flex-wrap items-center gap-3">
                            <span class="font-bold text-slate-800">Transaksi #{{ $tx->id }}</span>
                            <span class="text-slate-400">•</span>
                            <span class="font-medium text-slate-600">Pelanggan: {{ $tx->user->name }} ({{ $tx->user->email }})</span>
                            <span class="text-slate-400">•</span>
                            <span class="text-slate-500">{{ $tx->created_at->format('d M Y, H:i') }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            {!! $tx->status_badge !!}
                            <span class="font-bold text-indigo-600">{{ $tx->formatted_total }}</span>
                        </div>
                    </div>

                    <!-- Details -->
                    <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Left: Items -->
                        <div class="md:col-span-2 space-y-3">
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Item Pesanan</h4>
                            <div class="space-y-2 text-sm">
                                @foreach($tx->items as $item)
                                    <div class="flex justify-between items-center">
                                        <div class="text-slate-700">
                                            <span class="font-semibold text-slate-800">{{ $item->product_name }}</span>
                                            <span class="text-slate-500">x{{ $item->quantity }}</span>
                                            <span class="text-slate-400 text-xs">({{ $item->weight }} gr/pc)</span>
                                        </div>
                                        <span class="font-semibold text-slate-800">{{ $item->formatted_subtotal }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Right: Shipping -->
                        <div class="space-y-3 border-t md:border-t-0 md:border-l border-slate-100 pt-4 md:pt-0 md:pl-6 text-sm">
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pengiriman & Tujuan</h4>
                            <div class="space-y-1.5 text-slate-600">
                                <p><span class="font-semibold text-slate-700">Tujuan:</span> {{ $tx->city }}, {{ $tx->province }}</p>
                                <p class="text-xs bg-slate-50 p-2 rounded-lg border border-slate-100 line-clamp-2">{{ $tx->address_details }}</p>
                                <p><span class="font-semibold text-slate-700">Kurir:</span> {{ $tx->courier }} ({{ $tx->service }})</p>
                                <p><span class="font-semibold text-slate-700">Ongkir:</span> {{ $tx->formatted_shipping_cost }}</p>
                                <p><span class="font-semibold text-slate-700">Berat Total:</span> {{ $tx->weight }} gram</p>
                            </div>
                        </div>
                    </div>

                    <!-- Footer / Actions -->
                    <div class="border-t border-slate-100 px-6 py-4 flex flex-col md:flex-row items-center justify-between gap-4 bg-slate-50/20">
                        <!-- Left: Resi details if present -->
                        <div class="text-sm">
                            @if($tx->tracking_number)
                                <span class="text-slate-500 font-medium">Nomor Resi: </span>
                                <span class="font-bold text-slate-800 font-mono bg-slate-100 px-2 py-0.5 rounded border border-slate-200">{{ $tx->tracking_number }}</span>
                            @else
                                <span class="text-slate-400 italic">Belum ada nomor resi / belum dikirim.</span>
                            @endif
                        </div>

                        <!-- Right: Form actions to change status -->
                        <div class="flex items-center gap-3 w-full md:w-auto justify-end">
                            <a href="{{ route('orders.invoice', $tx->id) }}" target="_blank"
                                class="inline-flex items-center justify-center gap-2 py-2 px-4 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 active:bg-slate-100 rounded-xl border border-slate-200 shadow-sm transition">
                                Cetak Invoice
                            </a>

                            <!-- Update status form -->
                            <form action="{{ route('admin.orders.status', $tx->id) }}" method="POST" class="flex flex-wrap items-center gap-2">
                                @csrf
                                @method('PATCH')
                                
                                <!-- Status select -->
                                <select name="status" class="status-select py-1.5 px-3 rounded-xl border border-slate-200 text-xs bg-white focus:outline-none focus:ring-1 focus:ring-indigo-500" data-txid="{{ $tx->id }}">
                                    <option value="belum_bayar" {{ $tx->status == 'belum_bayar' ? 'selected' : '' }}>Belum Bayar</option>
                                    <option value="dikirim" {{ $tx->status == 'dikirim' ? 'selected' : '' }}>Dikirim</option>
                                    <option value="selesai" {{ $tx->status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                </select>

                                <!-- Tracking Input, visible only when 'dikirim' is selected -->
                                <div id="tracking-container-{{ $tx->id }}" class="hidden">
                                    <input type="text" name="tracking_number" placeholder="Input No. Resi..." value="{{ $tx->tracking_number }}"
                                        class="py-1.5 px-3 rounded-xl border border-slate-200 text-xs w-36 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                </div>

                                <button type="submit" 
                                    class="py-1.5 px-4 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-xl shadow-sm transition">
                                    Perbarui
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const statusSelects = document.querySelectorAll('.status-select');
        
        statusSelects.forEach(select => {
            const txId = select.dataset.txid;
            const trackingContainer = document.getElementById(`tracking-container-${txId}`);
            const trackingInput = trackingContainer.querySelector('input');

            // Set initial state
            toggleTrackingInput(select.value, trackingContainer, trackingInput);

            // Add change listener
            select.addEventListener('change', function() {
                toggleTrackingInput(this.value, trackingContainer, trackingInput);
            });
        });

        function toggleTrackingInput(value, container, input) {
            if (value === 'dikirim') {
                container.classList.remove('hidden');
                input.required = true;
            } else {
                container.classList.add('hidden');
                input.required = false;
            }
        }
    });
</script>
@endsection