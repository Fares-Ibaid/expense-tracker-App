<?php

namespace App\Http\Controllers;
use App\Models\Expense;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use League\Csv\Reader;
use League\Csv\Statement;
use Illuminate\Support\Facades\Log;

class ExpenseUploadController extends Controller
{
    public function upload(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'csv' => ['required', 'file', 'mimes:csv,txt'],
        ]);


        $path = $request->file('csv')->getRealPath();
        $raw = file_get_contents($path);
        $utf8 = mb_convert_encoding($raw, 'UTF-8', 'ISO-8859-1');

        $csv = Reader::createFromString($utf8);
        $csv->setDelimiter(';');
        $csv->setHeaderOffset(0);

        $statement = new Statement();
        $records = $statement->process($csv);

        $parsed = [];

        foreach ($records as $row) {
            if (
                !isset($row['Buchungstag']) ||
                !isset($row['Beguenstigter/Zahlungspflichtiger']) ||
                !isset($row['Betrag'])
            ) {
                continue;
            }

            try {
                $date = Carbon::createFromFormat('d.m.Y', trim($row['Buchungstag']))->toDateString();
            } catch (\Exception $e) {
                $date = null;
            }

            $parsed[] = [
                'date' => $date,
                'description' => trim($row['Beguenstigter/Zahlungspflichtiger']),
                'amount' => $this->parseAmount($row['Betrag']),
            ];
        }

        return response()->json([
            'message' => 'Parsed successfully',
            'data' => $parsed,
        ]);
    }

    private function parseAmount($raw)
    {
        // Convert European format: "1.234,56" → 1234.56
        $clean = str_replace('.', '', $raw); // remove thousands
        $clean = str_replace(',', '.', $clean); // swap decimal
        return (float) $clean;
    }

    public function save(Request $request): \Illuminate\Http\JsonResponse
    {
        //dd($request->all());
        $data = $request->validate([
            'expenses' => ['required', 'array'],
            'expenses.*.date' => ['required', 'date_format:Y-m-d'],
            'expenses.*.description' => ['required', 'string', 'max:255'],
            'expenses.*.amount' => ['required', 'numeric'],
        ]);

        // hard codede user_id
        $user_id = 1;
        // if the validation passes, you can save the expenses to the database
        foreach ($data['expenses'] as $item) {

            Expense::create([
                'user_id' => $user_id,
                'date' => $item['date'],
                'description' => $item['description'],
                'amount' => $item['amount'],
                'category_id' => null // ← will auto-match later
            ]);
        }

        return response()->json(['message' => 'Expenses saved successfully'], 201) ;

    }
}
