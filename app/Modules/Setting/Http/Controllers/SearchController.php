<?php

namespace App\Modules\Setting\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Setting\Services\SearchService;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    protected $searchService;

    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    /**
     * Global search endpoint
     */
    public function search(Request $request)
    {
        $query = $request->input('q', '');

        // Minimum 2 characters required
        if (strlen($query) < 2) {
            return response()->json([
                'success' => false,
                'message' => 'Please enter at least 2 characters',
                'results' => []
            ]);
        }

        // Get search results
        $results = $this->searchService->search($query, auth()->id());

        return response()->json([
            'success' => true,
            'query' => $query,
            'results' => $results,
            'total' => $results['total']
        ]);
    }
}
