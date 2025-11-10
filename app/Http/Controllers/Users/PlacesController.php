<?php

namespace App\Http\Controllers\Users;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class PlacesController extends Controller
{
    protected $mapboxToken;

    public function __construct()
    {
        $this->mapboxToken = config('services.mapbox.access_token');
    }

    /**
     * Search for places using Mapbox Geocoding API (Forward Geocoding)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function searchPlaces(Request $request): JsonResponse
    {
        $query = $request->input('query');
        $types = $request->input('types', 'place,address,street'); // Default types
        $limit = $request->input('limit', 10); // Default limit
        $proximity = $request->input('proximity', ''); // Optional: lon,lat
        $country = $request->input('country', 'in'); // Default to India

        if (empty($query)) {
            return response()->json([
                'error' => 'Query parameter is required'
            ], 400);
        }

        $url = "https://api.mapbox.com/search/geocode/v6/forward";
        $params = [
            'q' => $query,
            'access_token' => $this->mapboxToken,
            'limit' => $limit,
            'types' => $types,
            'country' => $country,
            'autocomplete' => 'true',
        ];

        if (!empty($proximity)) {
            $params['proximity'] = $proximity;
        }

        try {
            $response = Http::get($url, $params);

            if ($response->successful()) {
                $data = $response->json();
                return response()->json([
                    'status' => 'success',
                    'data' => $data['features'] ?? []  // Return GeoJSON features
                ]);
            } else {
                return response()->json([
                    'error' => 'Failed to fetch places from Mapbox API',
                    'details' => $response->json()
                ], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while fetching places',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get place details using Mapbox Geocoding API (Forward with mapbox_id)
     *
     * @param Request $request
     * @param string $placeId (mapbox_id)
     * @return JsonResponse
     */
    public function getPlaceDetails(Request $request, string $placeId): JsonResponse
    {
        if (empty($placeId)) {
            return response()->json([
                'error' => 'Place ID (mapbox_id) is required'
            ], 400);
        }

        $url = "https://api.mapbox.com/search/geocode/v6/forward";
        $params = [
            'q' => $placeId,
            'access_token' => $this->mapboxToken,
            'limit' => 1,
        ];

        try {
            $response = Http::get($url, $params);

            $responseData = $response->json();

            Log::info('Mapbox API Response', ['response' => $responseData]);

            if (isset($responseData['features']) && count($responseData['features']) > 0) {
                return response()->json([
                    'status' => 'success',
                    'data' => $responseData['features'][0]  // Return the first feature
                ]);
            } else {
                return response()->json([
                    'error' => 'No place found for the provided place_id'
                ], 404);
            }
        } catch (\Exception $e) {
            Log::error('Error fetching place details', ['exception' => $e->getMessage()]);
            return response()->json([
                'error' => 'An error occurred while fetching place details',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate distance and duration using Mapbox Directions API
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function calculateDistance(Request $request): JsonResponse
    {
        $origin = $request->query('origin');       // "lon,lat" e.g., "77.2090,28.6139"
        $destination = $request->query('destination'); // "77.0266,28.4595"

        if (!$origin || !$destination) {
            return response()->json(['error' => 'Origin and Destination required'], 400);
        }

        // Format coordinates for Mapbox: lon1,lat1;lon2,lat2
        $coordinates = str_replace(',', ',', $origin) . ';' . str_replace(',', ',', $destination);  // Ensure format lon,lat;lon,lat

        $url = "https://api.mapbox.com/directions/v5/mapbox/driving/{$coordinates}";
        $params = [
            'access_token' => $this->mapboxToken,
        ];

        try {
            $response = Http::get($url, $params);
            $data = $response->json();

            if (!empty($data['routes'])) {
                $distance = $data['routes'][0]['distance'] / 1000;  // meters to km
                $duration = $data['routes'][0]['duration'] / 60;    // seconds to minutes

                return response()->json([
                    'distance' => round($distance, 2) . ' km',
                    'duration' => round($duration) . ' min',
                ]);
            }

            return response()->json(['error' => 'Route not found'], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while calculating distance',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}