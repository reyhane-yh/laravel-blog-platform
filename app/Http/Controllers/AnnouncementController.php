<?php

namespace App\Http\Controllers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class AnnouncementController extends Controller
{
    public function __invoke()

    {
        // Make a request to the API
        $response = Http::get('https://api.sokanacademy.com/api/announcements/blog-index-header');

        // Check if the response is successful
        if ($response->successful()) {
            $data = $response->json()['data'];

            // Format the data
            $formattedData = $this->format(collect($data));

            return response()->json($formattedData);
        }

        // Response failed
        return response()->json([
            'message' => 'Failed to fetch data'],
            $response->status());
    }

    private function format(Collection $data): Collection
    {
        return $data->map(function ($item) {
            // Get the 'all' subarray for each item
            return $item['all'];
            // Group items by category_name
        })->groupBy('category_name')
            ->map(function ($group) {
            return $group->map(function ($item) {
                return [
                    $item['title'] => $item['views_count']
                ];
            });
        });
    }

}
