<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
class PlacesController extends Controller
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.google_maps.api_key');
    }

    /**
     * Search for places using Google Maps Places API
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function searchPlaces(Request $request): JsonResponse
    {
        $query = $request->input('query');
        $type = $request->input('type', ''); // Optional: filter by place type
        $radius = $request->input('radius', 5000); // Default radius in meters
        $location = $request->input('location', ''); // Optional: lat,lng

        if (empty($query)) {
            return response()->json([
                'error' => 'Query parameter is required'
            ], 400);
        }

        $url = 'https://maps.googleapis.com/maps/api/place/textsearch/json';
        $params = [
            'query' => $query,
            'key' => $this->apiKey,
            'radius' => $radius,
        ];

        if (!empty($type)) {
            $params['type'] = $type;
        }

        if (!empty($location)) {
            $params['location'] = $location;
        }

        try {
            $response = Http::get($url, $params);

            if ($response->successful()) {
                return response()->json([
                    'status' => 'success',
                    'data' => $response->json()['results']
                ]);
            } else {
                return response()->json([
                    'error' => 'Failed to fetch places from Google Maps API',
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

   
     public function calculateDistance(Request $request)
    {
        $origin = $request->query('origin');       // "28.6139,77.2090"
        $destination = $request->query('destination'); // "28.4595,77.0266"

        if (!$origin || !$destination) {
            return response()->json(['error' => 'Origin and Destination required'], 400);
        }

        $apiKey = config('services.google_maps.api_key');
        // config('services.google_maps.api_key')

        $response = Http::get("https://maps.googleapis.com/maps/api/directions/json", [
            'origin' => $origin,
            'destination' => $destination,
            'key' => $apiKey,
        ]);

        $data = $response->json();

        if (!empty($data['routes'])) {
            $distance = $data['routes'][0]['legs'][0]['distance']['text'];
            $duration = $data['routes'][0]['legs'][0]['duration']['text'];

            return response()->json([
                'distance' => $distance,
                'duration' => $duration,
            ]);
        }

        return response()->json(['error' => 'Route not found'], 404);
    }
     public function getPlaceDetails(Request $request, string $placeId): JsonResponse
    {
        if (empty($placeId)) {
            return response()->json([
                'error' => 'Place ID is required'
            ], 400);
        }

        $url = 'https://maps.googleapis.com/maps/api/place/details/json';
        $params = [
            'place_id' => $placeId,
            'key' => $this->apiKey,
            'fields' => 'name,formatted_address,geometry,place_id,types,photos,rating,reviews'
        ];

        try {
            $response = Http::get($url, $params);

            $responseData = $response->json();

            // Log the full response for debugging
            Log::info('Google Maps API Response', ['response' => $responseData]);

            // Check if the response has a status field
            if (isset($responseData['status'])) {
                if ($responseData['status'] === 'OK') {
                    return response()->json([
                        'status' => 'success',
                        'data' => $responseData['result'] ?? []
                    ]);
                } elseif ($responseData['status'] === 'ZERO_RESULTS') {
                    return response()->json([
                        'error' => 'No place found for the provided place_id'
                    ], 404);
                } else {
                    return response()->json([
                        'error' => 'Failed to fetch place details from Google Maps API',
                        'details' => [
                            'status' => $responseData['status'],
                            'error_message' => $responseData['error_message'] ?? 'Unknown error'
                        ]
                    ], 400);
                }
            }

            // Fallback if status is missing
            return response()->json([
                'error' => 'Unexpected response from Google Maps API',
                'details' => $responseData
            ], 500);

        } catch (\Exception $e) {
            Log::error('Error fetching place details', ['exception' => $e->getMessage()]);
            return response()->json([
                'error' => 'An error occurred while fetching place details',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}