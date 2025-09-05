<?php

namespace App\Http\Controllers;
use App\Models\Expense;
use App\Models\Category;
use App\Traits\Filterable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use League\Csv\Reader;
use League\Csv\Statement;

class ExpenseUploadController extends Controller
{
    use Filterable ;
    // hard-coded category rules
    private array $categoryRules = [
        'EDEKA' => 'Lebensmittel',
        'DM' => 'Essen & Trinken',
        'Stadtwerke' => 'Strom',
        'Benzin' => 'Transport',
        'RSG Group GmbH' => 'Abonnements',
    ];

    public function upload(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'csv' => ['required', 'file', 'mimes:csv,txt'],
        ]);


        // retrieve date ,amount , user_id
        $existingExpenses = Expense::select('date', 'amount', 'description')->get();

        // reformat to map array for quick searching
        $existingExpensesMap = $existingExpenses->map(function ($expense) {
            return [
                'date' => $expense->date,
                'amount' => $expense->amount,
                'description' => $expense->description,
            ];
        })->toArray();

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
                // toDo - handle year 20xx vs 19xx Bug here
                $buchungstag = trim($row['Buchungstag']);
                // Check if the year is two digits and prepend "20"
                if (preg_match('/^\d{2}\.\d{2}\.\d{2}$/', $buchungstag)) {
                    $buchungstag = preg_replace('/(\d{2}\.\d{2}\.)(\d{2})$/', '${1}20${2}', $buchungstag);
                }

                $date = Carbon::createFromFormat('d.m.Y', $buchungstag)->toDateString();
            } catch (\Exception $e) {
                $date = null;
            }

            $csvRow = [
                'date'        => $date,
                'amount'      => $this->parseAmount($row['Betrag']),
                'description' => trim($row['Beguenstigter/Zahlungspflichtiger']),
            ];

            // use map to check if the row already exists
            $isDuplicated = collect($existingExpensesMap)->contains($csvRow);
            $parsed[] = array_merge($csvRow, [
                'category' => $this->autoCategorize($row['Beguenstigter/Zahlungspflichtiger']),
                'duplicated' => $isDuplicated,
            ]);
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
        return (float)$clean;
    }

    public function save(Request $request): \Illuminate\Http\JsonResponse
    {
        $data = $request->validate([
            'expenses' => ['required', 'array'],
            'expenses.*.date' => ['required', 'date_format:Y-m-d'],
            'expenses.*.description' => ['required', 'string', 'max:255'],
            'expenses.*.amount' => ['required', 'numeric'],
            'expenses.*.duplicated' => ['required', 'boolean'],
        ]);

        // Filter out duplicated expenses
        $nonDuplicatedExpenses = array_filter($data['expenses'], function ($item) {
            return !$item['duplicated'];
        });

        // hard codede user_id
        $user_id = 1;
        // if the validation passes, you can save the expenses to the database
        foreach ($nonDuplicatedExpenses as $item) {
            Expense::create([
                'user_id' => $user_id,
                'date' => $item['date'],
                'description' => $item['description'],
                'amount' => $item['amount'],
                // Map category
                'category_id' => Category::where('name', $this->autoCategorize($item['description']))->value('id')
            ]);
        }

        return response()->json(['message' => 'Expenses saved successfully'], 201);
    }

    private function autoCategorize(string $description): string
    {
        $desc = strtolower($description);

        foreach ($this->categoryRules as $keyword => $category) {
            if (str_contains($desc, strtolower($keyword))) {
                return $category;
            }
        }

        return 'Uncategorized';
    }

    public  function summaryByCategory (Request $request)
    {
        $query = Expense::query();

        // Apply filters using the trait
        $this->applyFilters($request, $query);

        $data = $query->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->get();

        return response()->json($data);
    }
}
