@extends('layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto mt-10">
        <h2 class="text-2xl font-bold mb-4">Add Expense</h2>

        @if(session('success'))
            <div class="mb-4 p-2 bg-green-100 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('expenses.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium">Description</label>
                <input type="text" name="description" class="w-full border rounded px-3 py-2" required>
            </div>

            <div>
                <label class="block text-sm font-medium">Amount</label>
                <input type="number" name="amount" step="0.01" class="w-full border rounded px-3 py-2" required>
            </div>

            <div>
                <label class="block text-sm font-medium">Category</label>
                <select name="category_id" class="w-full border rounded px-3 py-2">
                    <option value="">-- Select Category --</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium">Date</label>
                <input type="date" name="date" class="w-full border rounded px-3 py-2" required>
            </div>

      <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-pink-700">
          Save Expense
      </button>
        </form>

        <!-- showing Expenses  -->

        <h2 class="text-2xl font-bold mt-10 mb-4">Expenses Table</h2>
        <table class="w-full border-collapse border border-gray-300">
            <thead>
            <tr class="bg-gray-200">
                <th class="border border-gray-300 px-4 py-2">Description</th>
                <th class="border border-gray-300 px-4 py-2">Amount</th>
                <th class="border border-gray-300 px-4 py-2">Category</th>
                <th class="border border-gray-300 px-4 py-2">Date</th>
                <th class="border border-gray-300 px-4 py-2">Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($expenses as $expense)
                <tr>
                    <td class="border border-gray-300 px-4 py-2">{{ $expense->description }}</td>
                    <td class="border border-gray-300 px-4 py-2">{{ number_format($expense->amount, 2) }}</td>
                    <td class="border border-gray-300 px-4 py-2">{{ $expense->category->name }}</td>
                    <td class="border border-gray-300 px-4 py-2">{{ $expense->date }}</td>
                    <td class="border border-gray-300 px-4 py-2">
                        <button class="text-blue-600 hover:underline">Edit</button>
                        <button class="text-red-600 hover:underline">Delete</button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center border border-gray-300 px-4 py-2">No expenses found.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection
