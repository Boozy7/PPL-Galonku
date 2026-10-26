<?php

namespace App\Http\Controllers;

use App\Models\Depot;
use Illuminate\Http\Request;

class DepotController extends Controller
{
    /**
     * Display a listing of depots with search, filters, and coordinates for Leaflet map.
     */
    public function index(Request $request)
    {
        $query = Depot::with(['products']);

        // Search by name, address, or district
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('district', 'like', "%{$search}%")
                  ->orWhere('tagline', 'like', "%{$search}%");
            });
        }

        // District filter
        if ($district = $request->input('district')) {
            if ($district !== 'all') {
                $query->where('district', $district);
            }
        }

        // Dinkes SLHS Certification filter
        if ($request->boolean('certified')) {
            $query->where('is_certified', true);
        }

        // Water product type filter (ro, mineral, alkaline)
        if ($type = $request->input('type')) {
            if ($type !== 'all') {
                $query->whereHas('products', function ($q) use ($type) {
                    $q->where('type', $type);
                });
            }
        }

        // Sorting
        $sort = $request->input('sort', 'rating_desc');
        if ($sort === 'rating_desc') {
            $query->orderByDesc('rating')->orderByDesc('review_count');
        } elseif ($sort === 'rating_asc') {
            $query->orderBy('rating');
        } elseif ($sort === 'name_asc') {
            $query->orderBy('name');
        }

        $depots = $query->get();

        // Optional: If user passed lat & lng, calculate distance for each depot
        $userLat = $request->input('lat');
        $userLng = $request->input('lng');

        if ($userLat && $userLng) {
            $depots = $depots->map(function ($depot) use ($userLat, $userLng) {
                $depot->distance_km = $this->haversineGreatCircleDistance(
                    (float)$userLat,
                    (float)$userLng,
                    (float)$depot->lat,
                    (float)$depot->lng
                );
                return $depot;
            });

            if ($sort === 'distance_asc') {
                $depots = $depots->sortBy('distance_km')->values();
            }
        }

        // Unique districts for filter dropdown
        $districts = Depot::distinct()->pluck('district')->sort()->values();

        // Prepare map pins payload
        $mapDepots = $depots->map(function ($d) {
            $lowestPrice = $d->products->min('price') ?? 0;
            return [
                'id' => $d->id,
                'slug' => $d->slug,
                'name' => $d->name,
                'tagline' => $d->tagline,
                'address' => $d->address,
                'district' => $d->district,
                'lat' => (float)$d->lat,
                'lng' => (float)$d->lng,
                'rating' => (float)$d->rating,
                'review_count' => $d->review_count,
                'is_certified' => (bool)$d->is_certified,
                'slhs_number' => $d->slhs_number,
                'tds_ppm' => $d->tds_ppm,
                'ph_level' => $d->ph_level,
                'ecoli_status' => $d->ecoli_status,
                'cover_image' => $d->cover_image,
                'lowest_price' => $lowestPrice,
                'distance_km' => $d->distance_km ?? null,
                'show_url' => route('depots.show', $d->slug),
                'order_url' => route('orders.create', $d->id),
            ];
        });

        return view('depots.index', [
            'depots' => $depots,
            'mapDepots' => $mapDepots,
            'districts' => $districts,
            'currentSearch' => $search,
            'currentDistrict' => $district,
            'currentCertified' => $request->boolean('certified'),
            'currentType' => $type,
            'currentSort' => $sort,
            'userLat' => $userLat,
            'userLng' => $userLng,
        ]);
    }

    /**
     * Display single depot details (Dinkes certificate, lab test, water products, reviews).
     */
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

    /**
     * API endpoint returning depots for dynamic AJAX map queries.
     */
    public function apiDepots(Request $request)
    {
        $userLat = $request->input('lat');
        $userLng = $request->input('lng');

        $depots = Depot::with('products')->get()->map(function ($d) use ($userLat, $userLng) {
            $distance = null;
            if ($userLat && $userLng) {
                $distance = $this->haversineGreatCircleDistance(
                    (float)$userLat,
                    (float)$userLng,
                    (float)$d->lat,
                    (float)$d->lng
                );
            }
            return [
                'id' => $d->id,
                'slug' => $d->slug,
                'name' => $d->name,
                'district' => $d->district,
                'lat' => (float)$d->lat,
                'lng' => (float)$d->lng,
                'rating' => (float)$d->rating,
                'is_certified' => (bool)$d->is_certified,
                'slhs_number' => $d->slhs_number,
                'tds_ppm' => $d->tds_ppm,
                'ph_level' => $d->ph_level,
                'cover_image' => $d->cover_image,
                'lowest_price' => $d->products->min('price') ?? 0,
                'distance_km' => $distance,
                'show_url' => route('depots.show', $d->slug),
                'order_url' => route('orders.create', $d->id),
            ];
        });

        return response()->json($depots);
    }

    /**
     * Calculates the great-circle distance between two points in kilometers.
     */
    private function haversineGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371)
    {
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return round($angle * $earthRadius, 2);
    }
}
