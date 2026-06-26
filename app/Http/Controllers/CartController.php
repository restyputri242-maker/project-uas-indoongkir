<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    /**
     * Display the shopping cart.
     */
    public function index(): View
    {
        $cart = session()->get('cart', []);
        $cartItems = [];
        $totalPrice = 0;
        $totalWeight = 0;

        if (!empty($cart)) {
            $products = Product::whereIn('id', array_keys($cart))->get();

            foreach ($products as $product) {
                $quantity = $cart[$product->id];
                // Limit quantity to stock
                if ($quantity > $product->stock) {
                    $quantity = $product->stock;
                    $cart[$product->id] = $quantity;
                }

                if ($quantity > 0) {
                    $subtotal = $product->price * $quantity;
                    $subweight = $product->weight * $quantity;
                    
                    $cartItems[] = [
                        'product' => $product,
                        'quantity' => $quantity,
                        'subtotal' => $subtotal,
                        'subweight' => $subweight,
                    ];

                    $totalPrice += $subtotal;
                    $totalWeight += $subweight;
                }
            }
            
            session()->put('cart', $cart); // save possibly adjusted quantities
        }

        return view('buyer.cart', compact('cartItems', 'totalPrice', 'totalWeight'));
    }

    /**
     * Add a product to the cart.
     */
    public function add(Request $request, Product $product): RedirectResponse
    {
        $quantity = $request->input('quantity', 1);

        if ($product->stock <= 0) {
            return back()->with('error', 'Produk ini sedang habis.');
        }

        $cart = session()->get('cart', []);

        // If product already exists in cart, increment quantity
        if (isset($cart[$product->id])) {
            $newQty = $cart[$product->id] + $quantity;
            if ($newQty > $product->stock) {
                return back()->with('error', 'Jumlah melebihi stok yang tersedia.');
            }
            $cart[$product->id] = $newQty;
        } else {
            if ($quantity > $product->stock) {
                return back()->with('error', 'Jumlah melebihi stok yang tersedia.');
            }
            $cart[$product->id] = $quantity;
        }

        session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('success', 'Produk ditambahkan ke keranjang belanja.');
    }

    /**
     * Update quantity of a product in the cart.
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $quantity = (int) $request->input('quantity', 1);

        if ($quantity <= 0) {
            return $this->remove($product);
        }

        if ($quantity > $product->stock) {
            return back()->with('error', 'Jumlah melebihi stok yang tersedia.');
        }

        $cart = session()->get('cart', []);
        $cart[$product->id] = $quantity;
        session()->put('cart', $cart);

        return back()->with('success', 'Jumlah produk berhasil diperbarui.');
    }

    /**
     * Remove a product from the cart.
     */
    public function remove(Product $product): RedirectResponse
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$product->id])) {
            unset($cart[$product->id]);
            session()->put('cart', $cart);
        }

        return back()->with('success', 'Produk dihapus dari keranjang.');
    }
}