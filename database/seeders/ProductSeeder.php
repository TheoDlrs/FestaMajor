<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'T-shirt Édition 2026',
                'description' => '100% Coton Bio',
                'price' => 10.00,
                'stock' => 50,
                'image_url' => 'https://ih1.redbubble.net/image.4996960737.9337/ssrco,classic_tee,mens,fafafa:cace72,front_alt,square_product,600x600.jpg',
                'has_sizes' => true,
            ],
            [
                'name' => 'Foulard "Festa"',
                'description' => 'L\'accessoire indispensable',
                'price' => 6.00,
                'stock' => 47,
                'image_url' => 'products/S6vTQO5DcTx3gjhRZDw7FTq7eY4s2mCxunofTjGp.png',
                'has_sizes' => false,
            ],
            [
                'name' => 'Gobelet Collector Festa 2026',
                'description' => 'Réutilisable & Souvenir',
                'price' => 1.00,
                'stock' => 48,
                'image_url' => 'products/vG2bWvPi2bGwK6XEnwYebkI1JcwHSYqafv9c6bMy.png',
                'has_sizes' => false,
            ],
            [
                'name' => 'Tote Bag Festa',
                'description' => 'Le sac le plus beau !',
                'price' => 6.00,
                'stock' => 50,
                'image_url' => 'products/gIv3cwj1mSXuluCwzOga6u0kb0XaJb7ykMDXyYzM.png',
                'has_sizes' => false,
            ],
            [
                'name' => 'Eventail Festa ',
                'description' => 'Meilleur éventail de la région ! ',
                'price' => 6.50,
                'stock' => 50,
                'image_url' => 'products/0WhIsgEYGdTO6GHkYB2eKmeWYckqF7hfcp88VhZ6.png',
                'has_sizes' => false,
            ],
            [
                'name' => 'Carte Postale',
                'description' => '',
                'price' => 1.00,
                'stock' => 50,
                'image_url' => 'https://images.unsplash.com/photo-1572509018340-d99c74673673?q=80&w=800&auto=format&fit=crop',
                'has_sizes' => false,
            ],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['name' => $product['name']],
                $product
            );
        }
    }
}
