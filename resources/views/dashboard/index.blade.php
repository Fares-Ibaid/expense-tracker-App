@extends('layouts.app')

@section('content')
    <div class="space-y-6">

        {{-- Summary Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white p-4 shadow rounded">
                <h2 class="text-lg font-semibold">Total This Month</h2>
                <p class="text-2xl font-bold text-green-600">${{ number_format($total, 2) }}</p>
            </div>
            <div class="bg-white p-4 shadow rounded">
                <h2 class="text-lg font-semibold"># of Expenses</h2>
                <p class="text-2xl font-bold text-blue-600">{{ $count }}</p>
            </div>
        </div>

        {{-- Recent Expenses Table --}}
        <div class="bg-white p-4 shadow rounded">
            <h2 class="text-lg font-semibold mb-4">Recent Expenses</h2>
            <table class="w-full table-auto text-left">
                <thead>
                <tr class="border-b">
                    <th class="pb-2">Description</th>
                    <th class="pb-2">Category</th>
                    <th class="pb-2">Amount</th>
                    <th class="pb-2">Date</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($expenses as $expense)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-2">{{ $expense->description }}</td>
                        <td class="py-2">{{ $expense->category->name }}</td>
                        <td class="py-2">${{ number_format($expense->amount, 2) }}</td>
                        <td class="py-2">{{ $expense->date }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Upload CSV Section --}}
    <div class="bg-white p-4 shadow rounded mt-6">
        <h2 class="text-lg font-semibold mb-4">Upload Bank CSV</h2>

        @if (session('success'))
            <div class="bg-green-100 text-green-800 p-2 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('expenses.upload') }}" enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
                <label for="csv" class="block mb-1 font-medium">CSV File</label>
                <input type="file" name="csv" id="csv" required class="w-full border rounded p-2">
            </div>

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
                Upload CSV
            </button>
        </form>
    </div>
@endsection
