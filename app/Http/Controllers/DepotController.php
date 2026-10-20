<?php

namespace App\Http\Controllers;

use App\Models\Depot;
use Illuminate\Http\Request;

class DepotController extends Controller
{
    public function show(string $slug)
    {
        $query = Depot::with(['products', 'reviews' => function ($q) {
            $q->orderByDesc('created_at');
        }])->where('slug', $slug);

        if (is_numeric($slug)) {
            $query->orWhere('id', $slug);
        }

        $depot = $query->firstOrFail();

        return view('depots.show', [
            'depot' => $depot,
        ]);
    }
}
