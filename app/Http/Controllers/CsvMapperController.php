<?php

namespace App\Http\Controllers;

use App\Models\CsvMapper;
use App\Services\CsvMapperService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;

class CsvMapperController extends Controller
{
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'mappings' => 'required|array',
            'file_id'  => 'required|string',
        ]);

        $userId = 1;

        $existingMapper = CsvMapper::where('user_id', $userId)->first();

        CsvMapper::where('user_id', $userId)->update(['is_default' => false]);

        $mapper = CsvMapper::create([
            'user_id' => $userId,
            'column_mapping' => $request->mappings,
            'temp_file_path' => str_replace('private/private/', 'private/', $request->file_id),
            'is_default' => true,
        ]);

        return response()->json([
            'message' => 'Mapping saved successfully',
            'id' => $mapper->id,
        ]);
    }

  /*  public function fetch(): JsonResponse
    {
        try {
            $userId = 1;

            $mapper = DB::table('csv_mappers')
                ->where('user_id', $userId)
                ->select('column_mapping', 'temp_file_path')
                ->first();

            if (!$mapper || !isset($mapper->temp_file_path)) {
                return response()->json(['error' => 'Mapper not found or invalid structure.'], 404);
            }


            // Normalize the file path to avoid duplicate "private/"
            $filePath = preg_replace('/^private\//', '', $mapper->temp_file_path);

            if (!Storage::exists($filePath)) {
                return response()->json(['error' => 'CSV file not found.'], 404);
            }
            $csv = Reader::createFromPath(Storage::path($filePath), 'r');
            $csv->setHeaderOffset(0); // Use the first row as the header

            $rows = [];
            foreach ($csv as $record) {
                $mappedRow = [];
                foreach ($mapper as $csvIndex => $key) {
                    $mappedRow[$key] = $record[$csvIndex] ?? null;
                }
                $rows[] = $mappedRow;
            }

            return response()->json($rows);
        } catch (\Exception $e) {
            \Log::error('Exception occurred while processing the CSV file: ' . $e->getMessage());

            return response()->json(['error' => 'Failed to process the CSV file.'], 500);
        }
    }*/

    public function fetch(CsvMapperService $service): JsonResponse
    {
        try {
            $userId = 1; // Replace with actual user ID logic
            $data = $service->fetchMappedData($userId);
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}
