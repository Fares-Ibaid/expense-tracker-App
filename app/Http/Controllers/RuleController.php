<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Rule;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule as ValidationRule;

class RuleController extends Controller
{
    public  function  index()
    {
        $rules = Rule::with('category')->get();

        return response()->json($rules);

    }

    public  function  store(Request $request)
    {
        $validated = $request->validate([
            'field' =>  ['required', ValidationRule::in(['description', 'amount'])],
            'match_type' => ['required', ValidationRule::in(['contains', 'equals', 'regex'])],
            'value' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],

        ]);

        $rule  = Rule::create($validated);
        $rule->load('category');

        // Fetch uncategorized transactions
  $uncategorizedExpenses = Expense::whereNull('category_id')->get();

  foreach ($uncategorizedExpenses as $expense) {
      // Apply the rule to uncategorized expenses
      if ($this->matchesRule($expense, $rule)) {
          $expense->update(['category_id' => $rule->category_id]);
      }
  }
        return response()->json($rule,201);
    }

    public  function destroy($id)
    {
        $rule = Rule::findorFail($id);
        $rule->delete();

        return response()->json(['message'=>'Rule deleted successfully'],200);

    }

    public  function update(Request $request,$id)
    {

        $validated = $request->validate([
            'field' =>  ['sometimes', 'required', ValidationRule::in(['description', 'amount'])],
            'match_type' => ['sometimes', 'required', ValidationRule::in(['contains', 'equals', 'regex'])],
            'value' => ['sometimes', 'required', 'string', 'max:255'],
            'category_id' => ['sometimes', 'required', 'exists:categories,id'],

        ]);

        $rule = Rule::findorFail($id);

        $rule->update($validated);
        $rule->load('category');

        // Fetch uncategorized transactions
        $uncategorizedExpenses = Expense::whereNull('category_id')->get();

        foreach ($uncategorizedExpenses as $expense) {
            // Apply the updated rule to uncategorized expenses
            if ($this->matchesRule($expense, $rule)) {
                $expense->update(['category_id' => $rule->category_id]);
            }
        }


        return response()->json($rule,200);
    }

    /**
     * Check if a transaction matches the rule.
     */
    private function matchesRule($transaction, $rule)
    {
        switch ($rule->match_type) {
            case 'contains':
                return stripos($transaction->{$rule->field}, $rule->value) !== false;
            case 'equals':
                return $transaction->{$rule->field} === $rule->value;
            case 'regex':
                return preg_match("/{$rule->value}/", $transaction->{$rule->field});
            default:
                return false;
        }
    }
}
