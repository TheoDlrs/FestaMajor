<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $urls = [
            [
                'loc' => route('home'),
                'lastmod' => date('c'), // Dynamique serait mieux si basé sur DB, mais Home change souvent
                'freq' => 'daily',
                'priority' => '1.0',
            ],
            [
                'loc' => route('boutique'),
                'lastmod' => date('c'),
                'freq' => 'daily', // Les stocks changent
                'priority' => '0.9',
            ],
            [
                'loc' => route('plan-festa'),
                'lastmod' => '2026-01-15T00:00:00+00:00',
                'freq' => 'monthly',
                'priority' => '0.7',
            ],
            [
                'loc' => route('mentions-legales'),
                'lastmod' => '2026-01-01T00:00:00+00:00',
                'freq' => 'yearly',
                'priority' => '0.3',
            ],
            [
                'loc' => route('confidentialite'),
                'lastmod' => '2026-01-01T00:00:00+00:00',
                'freq' => 'yearly',
                'priority' => '0.3',
            ],
            [
                'loc' => route('plan-du-site'),
                'lastmod' => '2026-01-15T00:00:00+00:00',
                'freq' => 'yearly',
                'priority' => '0.3',
            ],
        ];

        return response()->view('sitemap', [
            'urls' => $urls,
        ])->header('Content-Type', 'text/xml');
    }
}