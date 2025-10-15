<?php

namespace App\Http\Controllers;
use App\Models\Expense;
use App\Models\Category;
use App\Models\Rule;
use App\Traits\Filterable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use League\Csv\Exception;
use League\Csv\InvalidArgument;
use League\Csv\Reader;
use League\Csv\Statement;
use League\Csv\SyntaxError;

class ExpenseUploadController extends Controller
{
    use Filterable ;

    /**
     * @throws InvalidArgument
     * @throws SyntaxError
     * @throws Exception
     */
    public function upload(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'csv' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        $userId = 1; // Replace with your user ID
        $parsed = [];
        $this->checkIfTheUserHasAnyCategoriesOrRules($userId);
        $existingExpensesMap = $this->retrievealreadySavedExpenses();
        $records = $this->processCsv($request);

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
            // the flag themselves is set during the upload process ( Parser )
        $nonDuplicatedExpenses = array_filter($data['expenses'], function ($item) {
            return !$item['duplicated'];
        });

        // hard codede user_id
        $user_id = 1;

        // toDo - give the user a way to decide what to do with duplicates rows
        // if the validation passes, you can save the expenses to the database
        foreach ($nonDuplicatedExpenses as $item) {
            Expense::create([
                'user_id' => $user_id,
                'date' => $item['date'],
                'description' => $item['description'],
                'amount' => $item['amount'],
                // Map category
                // toDo- make the mapper to by dynamic
                'category_id' => Category::where('name', $this->autoCategorize($item['description']))->value('id')
            ]);
        }

        return response()->json(['message' => 'Expenses saved successfully'], 201);
    }

    private function autoCategorize(string $description): string
    {
        $desc = strtolower($description);

        // Fetch rules from the database
        $rules = Rule::with('category')->get();

        foreach ($rules as $rule) {
            $ruleValue = strtolower($rule->value);

            switch ($rule->match_type) {
                case 'equals':
                    if ($desc === $ruleValue) {
                        return $rule->category->name;
                    }
                    break;

                case 'contains':
                    if (str_contains($desc, $ruleValue)) {
                        return $rule->category->name;
                    }
                    break;

                case 'regex':
                    if (preg_match("/{$ruleValue}/i", $desc)) {
                        return $rule->category->name;
                    }
                    break;
            }
        }

        return 'Uncategorized';
    }

    public  function summaryByCategory (Request $request)
    {
        $query = Expense::query();

        // Apply filters using the trait
        $this->applyFilters($request, $query);

       // Join the categories table if category is a foreign key
        // Check if no category filter exists
        if (!$request->filled('category')) {
            $data = $query->join('categories', 'expenses.category_id', '=', 'categories.id')
                ->selectRaw('categories.name as category, SUM(expenses.amount) as total')
                ->groupBy('categories.name')
                ->get();
            Log::info('Request data:', $request->all());
         //   dd($request->all());

        } else {
            // If a category filter exists, filter by the provided category and group by it
            $data = $query->join('categories', 'expenses.category_id', '=', 'categories.id')
                ->where('categories.name', $request->input('category'))
                ->selectRaw('categories.name as category, SUM(expenses.amount) as total')
                ->groupBy('categories.name')
                ->get();
        }
       //dd($data);

        return response()->json($data);
    }

    private function seedDefaultCategoriesAndRules($userId) : void
    {

        $categoryRules = [
            'EDEKA' => 'Lebensmittel',
            'DM' => 'Essen & Trinken',
            'Stadtwerke' => 'Strom',
            'Benzin' => 'Transport',
            'RSG Group GmbH - Mcfit' => 'Abonnements',
        ];

        $categories = [];

        // Extract unique categories from the array
        foreach ($categoryRules as $rule => $category) {
            if (!in_array($category, $categories)) {
                $categories[] = $category;
            }
        }

        // Create categories and rules
        foreach ($categories as $categoryName) {
            $createdCategory = Category::create([
                'name' => $categoryName,
                'user_id' => $userId,
            ]);

            // Create rules for the category
            foreach ($categoryRules as $rule => $category) {
                if ($category === $categoryName) {
                    Rule::create([
                        'name' => $rule,
                        'category_id' => $createdCategory->id,
                        'value' => $rule,
                        'field' => 'description',
                        'match_type' => 'contains',
                        'user_id' => $userId,
                    ]);
                }
            }
        }
    }

    /**
     * @param int $userId
     * @return void
     */
    public function checkIfTheUserHasAnyCategoriesOrRules(int $userId): void
    {
// Check if the user has any categories or rules
        $hasCategories = Category::where('user_id', $userId)->exists();
        $hasRules = Rule::where('user_id', $userId)->exists();

        if (!$hasCategories && !$hasRules) {
            $this->seedDefaultCategoriesAndRules($userId);
        }
    }

    /**
     * @return array
     */
    public function retrievealreadySavedExpenses() : array
    {
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
        return $existingExpensesMap;
    }

    /**
     * @param Request $request
     * @return \League\Csv\TabularDataReader
     * @throws \League\Csv\Exception
     * @throws \League\Csv\InvalidArgument
     * @throws \League\Csv\SyntaxError
     */
    public function processCsv(Request $request): \League\Csv\TabularDataReader
    {
        $path = $request->file('csv')->getRealPath();
        $raw = file_get_contents($path);
        $utf8 = mb_convert_encoding($raw, 'UTF-8', 'ISO-8859-1');

        $csv = Reader::createFromString($utf8);
        $csv->setDelimiter(';');
        $csv->setHeaderOffset(0);

        $statement = new Statement();
        $records = $statement->process($csv); // returning a TabularDataReader containing the parsed rows.
        return $records;
    }
}

