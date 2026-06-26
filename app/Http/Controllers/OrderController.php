<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Services\RajaOngkirService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderController extends Controller
{
    protected RajaOngkirService $rajaOngkir;

    public function __construct(RajaOngkirService $rajaOngkir)
    {
        $this->rajaOngkir = $rajaOngkir;
    }

    /**
     * Show checkout page with cart contents and provinces.
     */
    public function checkout(): View|RedirectResponse
    {
        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja Anda kosong.');
        }

        $cartItems = [];
        $totalPrice = 0;
        $totalWeight = 0;

        $products = Product::whereIn('id', array_keys($cart))->get();

        foreach ($products as $product) {
            $quantity = $cart[$product->id];
            if ($product->stock < $quantity) {
                return redirect()->route('cart.index')->with('error', "Stok produk {$product->name} tidak mencukupi.");
            }

            if ($quantity > 0) {
                $cartItems[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'subtotal' => $product->price * $quantity,
                ];
                $totalPrice += $product->price * $quantity;
                $totalWeight += $product->weight * $quantity;
            }
        }

        $provinces = $this->rajaOngkir->getProvinces();

        return view('buyer.checkout', compact('cartItems', 'totalPrice', 'totalWeight', 'provinces'));
    }

    /**
     * Create the transaction and items.
     */
    public function storeCheckout(Request $request): RedirectResponse
    {
        $request->validate([
            'address_details' => ['required', 'string'],
            'province' => ['required', 'string'],
            'city' => ['required', 'string'],
            'courier' => ['required', 'string', 'in:jne,pos,tiki'],
            'service_cost_selected' => ['required', 'string'], // format: service|cost
        ]);

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('shop')->with('error', 'Keranjang Anda kosong.');
        }

        // Parse courier service and cost
        $serviceParts = explode('|', $request->service_cost_selected);
        $serviceName = $serviceParts[0];
        $shippingCost = (int) $serviceParts[1];

        // Begin Transaction
        DB::beginTransaction();

        try {
            $products = Product::whereIn('id', array_keys($cart))->get();
            $subtotal = 0;
            $totalWeight = 0;
            $itemsData = [];

            foreach ($products as $product) {
                $qty = $cart[$product->id];
                if ($product->stock < $qty) {
                    throw new \Exception("Stok produk {$product->name} tidak mencukupi.");
                }

                // Reduce stock
                $product->decrement('stock', $qty);

                $subtotal += $product->price * $qty;
                $totalWeight += $product->weight * $qty;

                $itemsData[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $qty,
                    'weight' => $product->weight,
                ];
            }

            // Create Transaction
            $transaction = Transaction::create([
                'user_id' => Auth::id(),
                'status' => 'belum_bayar',
                'weight' => $totalWeight,
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'total' => $subtotal + $shippingCost,
                'courier' => strtoupper($request->courier),
                'service' => $serviceName,
                'province' => $request->province,
                'city' => $request->city,
                'address_details' => $request->address_details,
            ]);

            // Create Transaction Items
            foreach ($itemsData as $item) {
                $item['transaction_id'] = $transaction->id;
                TransactionItem::create($item);
            }

            DB::commit();

            // Clear Cart
            session()->forget('cart');

            return redirect()->route('buyer.orders')->with('success', 'Pesanan Anda berhasil dibuat! Silakan lakukan pembayaran.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('cart.index')->with('error', 'Terjadi kesalahan saat membuat pesanan: ' . $e->getMessage());
        }
    }

    /**
     * Display order history for Buyer.
     */
    public function buyerOrders(): View
    {
        $transactions = Transaction::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('buyer.orders', compact('transactions'));
    }

    /**
     * Display order management for Admin.
     */
    public function adminOrders(): View
    {
        $transactions = Transaction::with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate some simple dashboard stats
        $totalSales = Transaction::where('status', 'selesai')->sum('total');
        $pendingCount = Transaction::where('status', 'belum_bayar')->count();
        $shippedCount = Transaction::where('status', 'dikirim')->count();
        $completedCount = Transaction::where('status', 'selesai')->count();

        return view('admin.orders', compact('transactions', 'totalSales', 'pendingCount', 'shippedCount', 'completedCount'));
    }

    /**
     * Update order status (Admin).
     */
    public function updateStatus(Request $request, Transaction $transaction): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'in:belum_bayar,dikirim,selesai'],
            'tracking_number' => ['required_if:status,dikirim', 'nullable', 'string', 'max:255'],
        ]);

        $updateData = ['status' => $request->status];

        if ($request->status === 'dikirim') {
            $updateData['tracking_number'] = $request->tracking_number;
        } elseif ($request->status === 'selesai' && $transaction->status === 'belum_bayar') {
            // If admin directly completes it, or it was shipped already
            $updateData['tracking_number'] = $transaction->tracking_number ?? 'Bypass';
        }

        $transaction->update($updateData);

        return back()->with('success', 'Status transaksi berhasil diperbarui.');
    }

    /**
     * Show print-friendly invoice page.
     */
    public function invoice(Transaction $transaction): View
    {
        // Security check: Only the buyer who placed the order or an Admin can print
        if (Auth::id() !== $transaction->user_id && !Auth::user()->isAdmin()) {
            abort(403, 'Anda tidak memiliki hak akses untuk mencetak invoice ini.');
        }

        $transaction->load('items', 'user');

        return view('orders.invoice', compact('transaction'));
    }
}