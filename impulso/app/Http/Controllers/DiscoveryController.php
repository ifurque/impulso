<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class DiscoveryController extends Controller
{
    public function index(Request $request)
    {
        $query = Business::query()->where('is_public', true)
            ->when($request->category, fn ($q, $category) => $q->where('category', $category));

        $businesses = $query->latest()->withCount('products')->withCount(['reviews as visible_reviews_count' => fn ($q) => $q->where('is_visible', true)])->withAvg(['reviews as visible_rating' => fn ($q) => $q->where('is_visible', true)], 'rating')->with('posts')->get();

        if ($request->filled('search')) {
            $search = $this->normalize($request->string('search')->toString());
            $businesses = $businesses->filter(fn ($business) => $this->matchesSearch($business, $search));
        }

        $page = LengthAwarePaginator::resolveCurrentPage();
        $businesses = new LengthAwarePaginator(
            $businesses->forPage($page, 9)->values(),
            $businesses->count(),
            9,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath(), 'query' => $request->query()]
        );
        
        $categories = Business::where('is_public', true)->distinct('category')->pluck('category')->sort();
        $palettes = BusinessController::PUBLIC_PALETTES;
        
        if ($request->ajax() || $request->boolean('ajax')) {
            return response()->json([
                'html' => view('partials.business-grid', compact('businesses'))->render(),
                'pagination' => (string) $businesses->render()
            ]);
        }
        
        return view('discover', compact('businesses', 'categories', 'palettes'));
    }

    private function matchesSearch(Business $business, string $search): bool
    {
        if ($search === '') {
            return true;
        }

        $name = $this->normalize($business->name);
        $description = $this->normalize($business->description ?? '');
        $words = preg_split('/\s+/', $name.' '.$description, -1, PREG_SPLIT_NO_EMPTY);

        foreach ($words as $word) {
            if (Str::startsWith($word, $search)) {
                return true;
            }

            if (strlen($search) > 2 && levenshtein($search, substr($word, 0, strlen($search))) <= 1) {
                return true;
            }
        }

        return false;
    }

    private function normalize(string $value): string
    {
        return Str::lower(Str::ascii(trim($value)));
    }
}