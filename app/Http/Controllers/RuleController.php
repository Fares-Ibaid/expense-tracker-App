<?php

namespace App\Http\Controllers;

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
        //dd('Store method reached!', $request->all());

        $validated = $request->validate([
            'field' =>  ['required', ValidationRule::in(['description', 'amount'])],
            'match_type' => ['required', ValidationRule::in(['contains', 'equals', 'regex'])],
            'value' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],

        ]);

        $rule  = Rule::create($validated);

        dd($rule);

        return response()->json($rule,201);
    }

    public  function destroy()
    {

    }
}
