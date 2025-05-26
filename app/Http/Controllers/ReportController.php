<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    public function index()
    {
        // List all files
        $files = Storage::disk('local')->files('weekly_reports');

        $baseUrl = url('api/reports');

        $fileUrls = array_map(function ($file) use ($baseUrl) {
            return [
                'filename' => basename($file),
                'url' => "{$baseUrl}/" . basename($file)
            ];
        }, $files);


        return response()->json($fileUrls);
    }

    public function download($filename)
    {
        // Check if the file exists
        if (Storage::disk('local')->exists("weekly_reports/$filename")) {
            // Download the file
            return Storage::disk('local')
                ->download("weekly_reports/$filename");
        }

        return response()->json([
            'message' => 'File not found'],
            404);
    }
}
