<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Admin
        User::create([
            'name' => 'Admin IndoOngkir',
            'email' => 'admin@indoongkir.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // 2. Seed Buyer
        User::create([
            'name' => 'Buyer IndoOngkir',
            'email' => 'buyer@indoongkir.com',
            'password' => Hash::make('password'),
            'role' => 'buyer',
        ]);

        // 3. Seed Products
        $products = [
            [
                'name' => 'Kaos Polos Cotton Combed 30s',
                'description' => 'Bahan katun berkualitas tinggi, dingin, menyerap keringat, nyaman untuk aktivitas sehari-hari.',
                'price' => 59000,
                'stock' => 50,
                'weight' => 200, // 200 grams
                'image_path' => null,
            ],
            [
                'name' => 'Kemeja Flanel Premium',
                'description' => 'Kemeja flanel lengan panjang dengan motif kotak-kotak modern. Bahan tebal namun lembut.',
                'price' => 125000,
                'stock' => 25,
                'weight' => 350, // 350 grams
                'image_path' => null,
            ],
            [
                'name' => 'Jaket Hoodie Oversize',
                'description' => 'Hoodie rajut tebal dengan potongan oversized unisex. Dilengkapi saku depan dan tudung kepala.',
                'price' => 189000,
                'stock' => 15,
                'weight' => 600, // 600 grams
                'image_path' => null,
            ],
            [
                'name' => 'Sepatu Sneakers Canvas',
                'description' => 'Sepatu sneakers kasual dari bahan kanvas premium dengan sol karet antiselip. Cocok untuk kuliah atau hang out.',
                'price' => 299000,
                'stock' => 10,
                'weight' => 850, // 850 grams
                'image_path' => null,
            ],
            [
                'name' => 'Tas Ransel Waterproof',
                'description' => 'Tas ransel berkapasitas besar dengan lapisan kedap air. Dilengkapi slot laptop 15.6 inci.',
                'price' => 145000,
                'stock' => 20,
                'weight' => 500, // 500 grams
                'image_path' => null,
            ],
            [
                'name' => 'Celana Chino Slim Fit',
                'description' => 'Celana chino stretch dengan potongan slim fit. Nyaman dipakai formal maupun kasual.',
                'price' => 110000,
                'stock' => 30,
                'weight' => 400, // 400 grams
                'image_path' => null,
            ],
        ];

        foreach ($products as $prod) {
            Product::create($prod);
        }
    }
}