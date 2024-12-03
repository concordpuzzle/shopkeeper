<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Expenses') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-6">
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                <!-- Category Management -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Add Category</h3>
                    <form action="{{ route('expense-categories.store') }}" method="POST" class="flex gap-4 items-end">
                        @csrf
                        <div>
                            <label for="category_name" class="block text-sm font-medium text-gray-700">Category Name</label>
                            <input type="text" name="name" id="category_name" class="mt-1 block w-full" required>
                        </div>
                        <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            Add Category
                        </button>
                    </form>
                </div>

                <!-- Add Expense Form -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Add Expense</h3>
                    <form action="{{ route('expenses.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label for="date" class="block text-sm font-medium text-gray-700">Date</label>
                                <input type="date" name="date" id="date" class="mt-1 block w-full" required>
                            </div>
                            <div>
                                <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                                <select name="expense_category_id" id="category" class="mt-1 block w-full" required>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="amount" class="block text-sm font-medium text-gray-700">Amount</label>
                                <input type="number" name="amount" id="amount" step="0.01" class="mt-1 block w-full" required>
                            </div>
                            <div>
                                <label for="scan_url" class="block text-sm font-medium text-gray-700">Scan URL</label>
                                <input type="url" name="scan_url" id="scan_url" class="mt-1 block w-full">
                            </div>
                        </div>
                        <div>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Add Expense
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Expenses Table -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Expenses List</h3>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Scan</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($expenses as $expense)
                                <tr>
                                    <td class="px-6 py-4">{{ $expense->date->format('M d, Y') }}</td>
                                    <td class="px-6 py-4">{{ $expense->category->name }}</td>
                                    <td class="px-6 py-4">${{ number_format($expense->amount, 2) }}</td>
                                    <td class="px-6 py-4">
                                        @if($expense->scan_url)
                                            <a href="{{ $expense->scan_url }}" target="_blank" class="text-blue-500 hover:underline">View Scan</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
