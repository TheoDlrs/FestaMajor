<?php

namespace Database\Seeders;

use App\Models\GalleryImage;
use App\Models\ProgramEvent;
use App\Models\Flyer;
use Illuminate\Database\Seeder;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        // Flyer Home
        Flyer::updateOrCreate(['title' => 'Flyer Principal (Original)'], [
            'image_url' => 'https://www.escapadeslr.com/img/agenda/1356-festa-major-saint-cyprien-2.jpg',
            'subtitle' => 'L\'Héritage',
            'headline' => 'Terre de Feu & d\'Or',
            'description' => 'La Festa Major n\'est pas un simple festival. C\'est le moment où Saint-Cyprien renoue avec ses racines. Des Correfocs qui illuminent les ruelles aux Sardanes sur le parvis, chaque instant est une célébration de notre identité catalane.',
            'quote_text' => 'La force de l\'unité.',
            'quote_author' => 'Devise Castellers',
            'is_active' => true
        ]);

        // Program
        $events = [
            [
                'title' => 'Esprits de la Forêt',
                'time' => 'Ven. 12 Sept. — 18h00',
                'description' => 'Spectacle sur échasses et déambulation lumineuse par la compagnie Les Vaguabondes.',
                'image_url' => 'https://lh3.googleusercontent.com/pw/AP1GczOAWp5pw7-CajDhjnO29DWWspZ2MHn8lq7R-DFG3l_FHxKEI1sqIhjiWKvHa-jOkYX6MkX3Fx0BhEzYxsXlnrge3g_HJLkmL8-DwXE0utYAwLqzkyQYti3ZUGt0TOpDCzYS2JSZl4AwcJw1VPzx2ZRX5g=w1143-h1714-s-no-gm?authuser=0',
                'is_featured' => false,
                'order' => 1,
            ],
            [
                'title' => 'Correfocs',
                'time' => 'Sam. 13 Sept. — 23h00',
                'description' => 'Spectacle pyrotechnique légendaire par la Infernal de la Vallalta sur la Place Desnoyer.',
                'image_url' => 'https://i.pinimg.com/236x/24/d0/c0/24d0c08a71b7e5eb7574f886c36b07a3.jpg',
                'is_featured' => true,
                'order' => 2,
            ],
            [
                'title' => 'Colles Geganteres',
                'time' => 'Dim. 14 Sept. — 11h00',
                'description' => 'Grand défilé des géants de Sant Hipòlit de la Salanca et de Perpinyà.',
                'image_url' => 'https://lh3.googleusercontent.com/pw/AP1GczPQ5JbftouxAbRx9Mr1R3k6-kKhM5zh4ZxtXT9o560VTyxRyL1bI2O-H5Kmn5O3TvmrWSSfmU5_gvzV6AQ-7E5SlACAkcmpVt76AP4hz7zbkTSw4c2y9XUomCDmC3gen1fYpqrw51dB2nL678eh2zjp2A=w1143-h1714-s-no-gm?authuser=0',
                'is_featured' => false,
                'order' => 3,
            ],
        ];

        foreach ($events as $event) {
            ProgramEvent::updateOrCreate(
                ['title' => $event['title']],
                $event
            );
        }

        // Gallery
        $images = [
            ['image_url' => 'https://lh3.googleusercontent.com/pw/AP1GczPPgFfSENf_Uno39tDM3hR6CI0nEqZnjRK9i9wdSiRJhIwCavS9oGdGn8biJsuFRGjwtigc0IqYcqmreCjiZ5H90nDJF9uaaeN3R_RDCuFejKkH2PiFTlswtCeJCBFpqcSBfC7YV4u_HN7mrdpm1quHpg=w2284-h1714-s-no-gm?authuser=0', 'is_featured' => true, 'order' => 1],
            ['image_url' => 'https://lh3.googleusercontent.com/pw/AP1GczOrK_hTziq-wGdsG2baPETF4Ns3B1kB9N5UXUHLmkBbV9HigVJopo7Oif4TdcZaZPWf7bwk25W7xsOdH3EBj2SFoMiyBh86U6VlPDQXmslnjftjdIFvdEAhqnPcZYZElD8uF_JgJKw-yB4cFL1ZGUrvGQ=w1292-h1714-s-no-gm?authuser=0', 'is_featured' => true, 'order' => 2],
            ['image_url' => 'https://lh3.googleusercontent.com/pw/AP1GczMh3b3pco-liyz7znaLa2uw3ZsXyLGm-SSk7JoLrZVsc5rU_0DVQs0TjmMALp_236_V1XjOAGxO-qswDeuhIhSn_qtNQyzZvFOPqX5JYfrUKr1BjSaOOLj26gJk7NUUEjAUUVEVfqHp6VhSNql8N6ECRQ=w2570-h1714-s-no-gm?authuser=0', 'is_featured' => false, 'order' => 3],
            ['image_url' => 'https://lh3.googleusercontent.com/pw/AP1GczOqb3mkV8h6dNCe8Ja9c1PI-sQfFqAm2v-ohVD7jtghkSfWLLvqyGCe3VVN_kdXx-V42tqyM2Lj1KzJA3JnLdx_pWrNM9y6lHJ98T9CTRh-zVBVNqahmZh0sb3dJ17Kpj6ieZyGwxaI96bfp7XqeIlGTQ=w1143-h1714-s-no-gm?authuser=0', 'is_featured' => false, 'order' => 4],
            ['image_url' => 'https://lh3.googleusercontent.com/pw/AP1GczP1N8_fQR7OIXHnKlNKA0wCqwLIOsAWkDIK7tQotI_idELzVOFxo6hQbUlb9Bb2PADYG6XfdeQuuWdsdl2Qg5YZg2ouidiESyNkGGF91L0NjyVJArEUyWU-qHAjsz-8WUjhWSIEL6khsE2zBrJNMZHg7Q=w2077-h1714-s-no-gm?authuser=0', 'is_featured' => false, 'order' => 5], // featured in original layout but span 2
            ['image_url' => 'https://lh3.googleusercontent.com/pw/AP1GczNIEbjZNyfMoCTTZlnZAPAYe-M7o39WxMGLqRZlf9EMkLykK4BVEYHvbFPAes-aPTdGDps1FFEFOex4JgCVjdQXS333jQVw7iCEPuF2DYhrNEJUzqBLxKLfnRIkPYqKf1RbetMH77uVIVPwmbVKmdaHEg=w2569-h1714-s-no-gm?authuser=0', 'is_featured' => false, 'order' => 6],
            ['image_url' => 'https://lh3.googleusercontent.com/pw/AP1GczPmZVLA3ff4MMEYV11h2T7seTuGL-J2IJDHeYgim2jFYhVu1WlLNkMpq_9T0eXBgIp5Xr1SFwJewHiJZ9eOh6_cxyOVtLmzvzuvFvdXqlhbcJ9cgNfnrRkcWfx1uOnbuuriiKNTxzrldP8yYxAebGCOUA=w1215-h1714-s-no-gm?authuser=0', 'is_featured' => false, 'order' => 7],
        ];

        foreach ($images as $img) {
            GalleryImage::updateOrCreate(
                ['image_url' => $img['image_url']],
                $img
            );
        }
    }
}
