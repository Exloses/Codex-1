<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FaqController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('Storefront/Faq', [
            'faqs' => Faq::query()
                ->where('is_active', true)
                ->when($request->filled('category'), fn ($query) => $query->where('category', $request->string('category')))
                ->when($request->filled('language'), fn ($query) => $query->where('language', $request->string('language')))
                ->orderBy('sort_order')
                ->get(),
        ]);
    }
}
