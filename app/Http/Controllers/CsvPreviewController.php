<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CsvPreviewController extends Controller
{
    public function preview(Request $request){

        // Validate file input
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        // Store file temporarily
        $file = $request->file('file');
        $filename = 'csv_' . Str::uuid() . '.csv';
        $file->move(storage_path('app/temp_csv_uploads'), $filename);
        $filePath = 'temp_csv_uploads/' . $filename;


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
