<?php

namespace App\Http\Controllers;

use App\Services\CsvMapperService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CsvPreviewController extends Controller
{
   /* public function preview(Request $request){

        // Validate file input
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        // Store file temporarily
        $file = $request->file('file');
        $filename = 'csv_' . Str::uuid() . '.csv';
        $file->move(storage_path('app/private/temp_csv_uploads'), $filename);
        $filePath = 'private/temp_csv_uploads/' . $filename;

        $fullPath = storage_path('app/' . $filePath);

        if (!file_exists($fullPath)) {
            return response()->json(['error' => "File not found at: $fullPath"], 500);
        }

        // Open file and read header + preview rows
        $handle = fopen($fullPath, 'r');
        if (!$handle) {
            return response()->json(['error' => 'Could not open file'], 500);
        }

        $headers = fgetcsv($handle, 0, ';'); // Specify semicolon delimiter if needed

        $preview = [];
        $maxRows = 5;
        while (($row = fgetcsv($handle, 0, ';')) !== false && count($preview) < $maxRows) {
            $preview[] = $row;
        }
        fclose($handle);

        // Return preview data and file identifier
        return response()->json([
            'file_id' => $filePath,
            'headers' => $headers,
            'preview' => $preview,
        ]);
    }*/
    public function preview(Request $request, CsvMapperService $csvMapperService)
    {
        // Validate file input
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        // Check if a default mapper exists for the user
        $userId = 1; // Replace with actual user ID logic
        $mapperExists = DB::table('csv_mappers')->where('user_id', $userId)->exists();

        if ($mapperExists) {
            try {
                // Fetch mapped data using the service
                $data = $csvMapperService->fetchMappedData($userId);
                return response()->json($data);
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }

        // Store file temporarily
        $file = $request->file('file');
        $filename = 'csv_' . Str::uuid() . '.csv';
        $file->move(storage_path('app/private/temp_csv_uploads'), $filename);
        $filePath = 'private/temp_csv_uploads/' . $filename;

        $fullPath = storage_path('app/' . $filePath);

        if (!file_exists($fullPath)) {
            return response()->json(['error' => "File not found at: $fullPath"], 500);
        }

        // Open file and read header + preview rows
        $handle = fopen($fullPath, 'r');
        if (!$handle) {
            return response()->json(['error' => 'Could not open file'], 500);
        }

        $headers = fgetcsv($handle, 0, ';'); // Specify semicolon delimiter if needed

        $preview = [];
        $maxRows = 5;
        while (($row = fgetcsv($handle, 0, ';')) !== false && count($preview) < $maxRows) {
            $preview[] = $row;
        }
        fclose($handle);

        // Return preview data and file identifier
        return response()->json([
            'file_id' => $filePath,
            'headers' => $headers,
            'preview' => $preview,
        ]);
    }
}
