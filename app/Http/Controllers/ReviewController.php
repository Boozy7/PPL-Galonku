<?php

namespace App\Http\Controllers;

use App\Models\Depot;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Simpan ulasan baru dan perbarui rata-rata rating depot
     */
    public function store(Request $request, int $depotId)
    {
        $depot = Depot::findOrFail($depotId);

        $validated = $request->validate([
            'user_name' => 'required|string|max:100',
            'rating' => 'required|numeric|min:1|max:5',
            'clarity_rating' => 'nullable|integer|min:1|max:5',
            'taste_rating' => 'nullable|integer|min:1|max:5',
            'cleanliness_rating' => 'nullable|integer|min:1|max:5',
            'service_rating' => 'nullable|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ]);

        Review::create([
            'depot_id' => $depot->id,
            'user_name' => $validated['user_name'],
            'rating' => (int)$validated['rating'],
            'clarity_rating' => $validated['clarity_rating'] ?? (int)$validated['rating'],
            'taste_rating' => $validated['taste_rating'] ?? (int)$validated['rating'],
            'cleanliness_rating' => $validated['cleanliness_rating'] ?? (int)$validated['rating'],
            'service_rating' => $validated['service_rating'] ?? (int)$validated['rating'],
            'comment' => $validated['comment'],
            'is_verified_purchase' => true,
        ]);

        // Hitung ulang rating & jumlah ulasan depot secara otomatis
        $avgRating = Review::where('depot_id', $depot->id)->avg('rating');
        $count = Review::where('depot_id', $depot->id)->count();

        $depot->update([
            'rating' => round($avgRating, 1),
            'review_count' => $count,
        ]);

        return redirect()->route('depots.show', $depot->slug)
            ->with('review_success', 'Ulasan higienitas dan penilaian air Anda berhasil dikirim!');
    }
}
