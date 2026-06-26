@extends('layouts.app')

@section('title', 'Checkout Pembayaran')

@section('content')
<div class="space-y-6">
    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
        <h1 class="text-xl font-bold text-slate-900 tracking-tight">Checkout Pembayaran</h1>
        <p class="text-sm text-slate-500 mt-1">Lengkapi alamat pengiriman dan pilih opsi pengiriman untuk menyelesaikan pesanan Anda</p>
    </div>

    <form action="{{ route('checkout.store') }}" method="POST" id="checkout-form" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        @csrf
        
        <!-- Input Address Column -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white p-8 rounded-2xl border border-slate-100 shadow-sm space-y-6">
                <h2 class="text-base font-bold text-slate-800 border-b border-slate-100 pb-3">Alamat Pengiriman</h2>

                <!-- Address detail textarea -->
                <div>
                    <label for="address_details" class="block text-sm font-semibold text-slate-700 mb-1.5">Alamat Lengkap</label>
                    <textarea name="address_details" id="address_details" rows="3" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-600/20 focus:border-indigo-600 transition placeholder-slate-400 text-sm"
                        placeholder="Tuliskan nama jalan, nomor rumah, RT/RW, dan patokan..."></textarea>
                </div>

                <!-- Dropdowns: Province -> City -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="province_select" class="block text-sm font-semibold text-slate-700 mb-1.5">Provinsi Tujuan</label>
                        <select id="province_select" required
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-600/20 focus:border-indigo-600 transition text-sm bg-white">
                            <option value="">Pilih Provinsi...</option>
                            @foreach($provinces as $prov)
                                <option value="{{ $prov['province_id'] }}">{{ $prov['province'] }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="province" id="province_name">
                    </div>

                    <div>
                        <label for="city_select" class="block text-sm font-semibold text-slate-700 mb-1.5">Kota / Kabupaten Tujuan</label>
                        <select id="city_select" required disabled
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-600/20 focus:border-indigo-600 transition text-sm bg-white disabled:bg-slate-50 disabled:text-slate-400">
                            <option value="">Pilih Kota...</option>
                        </select>
                        <input type="hidden" name="city" id="city_name">
                        <input type="hidden" name="destination_city_id" id="destination_city_id">
                    </div>
                </div>

                <!-- Courier -> Service selection -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="courier_select" class="block text-sm font-semibold text-slate-700 mb-1.5">Kurir Pengiriman</label>
                        <select name="courier" id="courier_select" required disabled
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-600/20 focus:border-indigo-600 transition text-sm bg-white disabled:bg-slate-50 disabled:text-slate-400">
                            <option value="">Pilih Kurir...</option>
                            <option value="jne">JNE (Jalur Nugraha Ekakurir)</option>
                            <option value="pos">POS Indonesia</option>
                            <option value="tiki">TIKI (Citra Van Titipan Kilat)</option>
                        </select>
                    </div>

                    <div>
                        <label for="service_select" class="block text-sm font-semibold text-slate-700 mb-1.5">Layanan / Tarif Ongkir</label>
                        <select name="service_cost_selected" id="service_select" required disabled
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-600/20 focus:border-indigo-600 transition text-sm bg-white disabled:bg-slate-50 disabled:text-slate-400">
                            <option value="">Pilih Layanan...</option>
                        </select>
                        <input type="hidden" name="service" id="service_name">
                        <input type="hidden" name="shipping_cost" id="shipping_cost_val">
                    </div>
                </div>
            </div>

            <!-- Items summary overview -->
            <div class="bg-white p-8 rounded-2xl border border-slate-100 shadow-sm space-y-4">
                <h2 class="text-base font-bold text-slate-800 border-b border-slate-100 pb-3">Rincian Barang</h2>
                <div class="divide-y divide-slate-100">
                    @foreach($cartItems as $item)
                        <div class="py-3 flex items-center justify-between gap-4 first:pt-0 last:pb-0">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-slate-50 border border-slate-200 overflow-hidden flex-shrink-0 flex items-center justify-center">
                                    @if($item['product']->image_path)
                                        <img src="{{ asset($item['product']->image_path) }}" alt="{{ $item['product']->name }}" class="w-full h-full object-cover">
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-slate-400">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                        </svg>
                                    @endif
                                </div>
                                <div>
                                    <h4 class="font-semibold text-slate-800 text-sm">{{ $item['product']->name }}</h4>
                                    <p class="text-xs text-slate-500 mt-0.5">{{ $item['quantity'] }} pcs x Rp {{ number_format($item['product']->price, 0, ',', '.') }}</p>
                                </div>
                            </div>
                            <span class="text-sm font-semibold text-slate-800">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Checkout Summary Column -->
        <div class="lg:col-span-1">
            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm space-y-6 sticky top-24">
                <h2 class="text-base font-bold text-slate-800">Ringkasan Pembayaran</h2>

                <!-- Subtotals -->
                <div class="space-y-3.5 text-sm text-slate-600 border-b border-slate-100 pb-4">
                    <div class="flex justify-between">
                        <span>Subtotal Belanja</span>
                        <span class="font-semibold text-slate-800">Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Berat Total</span>
                        <span class="font-semibold text-slate-800">
                            @if($totalWeight >= 1000)
                                {{ ($totalWeight / 1000) }} kg
                            @else
                                {{ $totalWeight }} gram
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span>Ongkos Kirim</span>
                        <span class="font-semibold text-slate-800" id="shipping_cost_display">Pilih alamat & kurir</span>
                    </div>
                </div>

                <!-- Grand Total -->
                <div class="flex justify-between text-base font-bold text-slate-800">
                    <span>Total Pembayaran</span>
                    <span id="grand_total_display">Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
                </div>

                <!-- Warnings/Loading info -->
                <div id="cost_loading_info" class="text-xs text-indigo-600 font-medium hidden flex items-center gap-1.5">
                    <svg class="animate-spin h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Menghitung ongkos kirim real-time...
                </div>

                <button type="submit" id="submit-btn" disabled
                    class="w-full py-3 px-4 inline-flex items-center justify-center text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-xl shadow-sm disabled:bg-slate-100 disabled:text-slate-400 transition">
                    Konfirmasi & Buat Pesanan
                </button>
                
                <a href="{{ route('cart.index') }}" 
                    class="w-full py-3 px-4 inline-flex items-center justify-center text-sm font-semibold text-slate-600 hover:text-slate-800 hover:bg-slate-50 rounded-xl transition">
                    Kembali ke Keranjang
                </a>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const provinceSelect = document.getElementById('province_select');
        const provinceNameInput = document.getElementById('province_name');
        
        const citySelect = document.getElementById('city_select');
        const cityNameInput = document.getElementById('city_name');
        const destinationCityIdInput = document.getElementById('destination_city_id');
        
        const courierSelect = document.getElementById('courier_select');
        const serviceSelect = document.getElementById('service_select');
        const serviceNameInput = document.getElementById('service_name');
        
        const shippingCostValInput = document.getElementById('shipping_cost_val');
        const shippingCostDisplay = document.getElementById('shipping_cost_display');
        const grandTotalDisplay = document.getElementById('grand_total_display');
        
        const costLoadingInfo = document.getElementById('cost_loading_info');
        const submitBtn = document.getElementById('submit-btn');

        const subtotal = {{ $totalPrice }};
        const totalWeight = {{ $totalWeight }};

        // 1. Province selection triggers City load
        provinceSelect.addEventListener('change', function() {
            const provinceId = this.value;
            const provinceText = this.options[this.selectedIndex].text;
            provinceNameInput.value = provinceText;

            // Reset downstream select elements
            citySelect.innerHTML = '<option value="">Pilih Kota...</option>';
            citySelect.disabled = true;
            courierSelect.selectedIndex = 0;
            courierSelect.disabled = true;
            resetServiceSelect();

            if (!provinceId) return;

            // Fetch cities via AJAX
            fetch(`/api/rajaongkir/cities/${provinceId}`)
                .then(response => response.json())
                .then(cities => {
                    cities.forEach(city => {
                        const option = document.createElement('option');
                        option.value = city.city_id;
                        option.textContent = `${city.type} ${city.city_name}`;
                        option.dataset.name = `${city.type} ${city.city_name}`;
                        citySelect.appendChild(option);
                    });
                    citySelect.disabled = false;
                })
                .catch(error => {
                    console.error('Error fetching cities:', error);
                    alert('Gagal memuat kota. Silakan coba lagi.');
                });
        });

        // 2. City selection triggers Courier activation
        citySelect.addEventListener('change', function() {
            const cityId = this.value;
            const cityName = this.options[this.selectedIndex].dataset.name;
            
            destinationCityIdInput.value = cityId;
            cityNameInput.value = cityName;

            // Reset downstream
            courierSelect.selectedIndex = 0;
            resetServiceSelect();

            if (cityId) {
                courierSelect.disabled = false;
            } else {
                courierSelect.disabled = true;
            }
        });

        // 3. Courier selection triggers cost calculation
        courierSelect.addEventListener('change', function() {
            const courier = this.value;
            const destinationCityId = destinationCityIdInput.value;

            resetServiceSelect();
            if (!courier || !destinationCityId) return;

            // Show loading
            costLoadingInfo.classList.remove('hidden');

            // POST to calculate shipping cost
            fetch('/api/rajaongkir/cost', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    destination: destinationCityId,
                    weight: totalWeight,
                    courier: courier
                })
            })
            .then(response => response.json())
            .then(results => {
                costLoadingInfo.classList.add('hidden');
                
                if (results && results.length > 0) {
                    const courierData = results[0];
                    const costs = courierData.costs;
                    
                    if (costs.length > 0) {
                        costs.forEach(costInfo => {
                            const option = document.createElement('option');
                            const costValue = costInfo.cost[0].value;
                            const etd = costInfo.cost[0].etd;
                            
                            // Value: name|cost
                            option.value = `${costInfo.service}|${costValue}`;
                            option.textContent = `${costInfo.service} - Rp ${costValue.toLocaleString('id-ID')} (${etd})`;
                            option.dataset.cost = costValue;
                            option.dataset.service = costInfo.service;
                            
                            serviceSelect.appendChild(option);
                        });
                        serviceSelect.disabled = false;
                    } else {
                        alert('Tidak ada opsi pengiriman tersedia untuk kurir ini.');
                    }
                }
            })
            .catch(error => {
                costLoadingInfo.classList.add('hidden');
                console.error('Error calculating cost:', error);
                alert('Gagal memuat tarif ongkir. Silakan coba lagi.');
            });
        });

        // 4. Service selection updates price details
        serviceSelect.addEventListener('change', function() {
            const selectedVal = this.value;
            
            if (!selectedVal) {
                resetPricingDisplay();
                submitBtn.disabled = true;
                return;
            }

            const parts = selectedVal.split('|');
            const serviceName = parts[0];
            const shippingCost = parseInt(parts[1]);

            serviceNameInput.value = serviceName;
            shippingCostValInput.value = shippingCost;

            // Update displays
            shippingCostDisplay.textContent = `Rp ${shippingCost.toLocaleString('id-ID')}`;
            shippingCostDisplay.classList.remove('text-slate-500');
            shippingCostDisplay.classList.add('text-slate-800', 'font-semibold');

            const grandTotal = subtotal + shippingCost;
            grandTotalDisplay.textContent = `Rp ${grandTotal.toLocaleString('id-ID')}`;
            
            submitBtn.disabled = false;
        });

        function resetServiceSelect() {
            serviceSelect.innerHTML = '<option value="">Pilih Layanan...</option>';
            serviceSelect.disabled = true;
            resetPricingDisplay();
            submitBtn.disabled = true;
        }

        function resetPricingDisplay() {
            shippingCostDisplay.textContent = 'Pilih alamat & kurir';
            shippingCostDisplay.className = 'font-semibold text-slate-500';
            grandTotalDisplay.textContent = `Rp ${subtotal.toLocaleString('id-ID')}`;
            shippingCostValInput.value = '';
            serviceNameInput.value = '';
        }
    });
</script>
@endsection