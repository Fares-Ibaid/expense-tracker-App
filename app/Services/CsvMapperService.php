<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;

class CsvMapperService
{
    public function fetchMappedData(int $userId)
    {
        $mapper = DB::table('csv_mappers')
            ->where('user_id', $userId)
            ->select('column_mapping', 'temp_file_path')
            ->first();


        $mapper->column_mapping = json_decode($mapper->column_mapping, true);

        if (!$mapper || !isset($mapper->temp_file_path)) {
            throw new \Exception('Mapper not found or invalid structure.');
        }

        $filePath = preg_replace('/^private\//', '', $mapper->temp_file_path);

        if (!Storage::exists($filePath)) {
            throw new \Exception('CSV file not found.');
        }

        $csv = Reader::createFromPath(Storage::path($filePath), 'r');
        $csv->setHeaderOffset(0);


        $rows = [];
        dd($mapper->column_mapping);
        foreach ($csv as $record) {
            $mappedRow = [];
            foreach ($mapper->column_mapping as $csvIndex => $key) {
                $mappedRow[$key] = $record[$csvIndex] ?? null;
            }
            $rows[] = $mappedRow;
        }
      //  dd($rows);

        return $rows;
    }
}
