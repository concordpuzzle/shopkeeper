<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Open Orders') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-6">
                @if(isset($error))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                        <span class="block sm:inline">{{ $error }}</span>
                    </div>
                @endif

                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Address</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Products</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($orders as $order)
                            <tr>
                                <td class="px-6 py-4">#{{ $order['id'] }}</td>
                                <td class="px-6 py-4">{{ $order['customer_name'] }}</td>
                                <td class="px-6 py-4">{{ $order['shipping_address'] }}</td>
                                <td class="px-6 py-4">${{ number_format((float)$order['total'], 2) }}</td>
                                <td class="px-6 py-4">
                                    @foreach($order['options'] as $item)
                                        <div>
                                            @if(isset($item['product_url']))
                                                <a href="{{ $item['product_url'] }}" class="text-blue-500 hover:underline" target="_blank">
                                                    {{ $item['product'] }}
                                                </a>
                                            @else
                                                {{ $item['product'] }}
                                            @endif
                                        </div>
                                    @endforeach
                                </td>
                                <td class="px-6 py-4">{{ \Carbon\Carbon::parse($order['date_created'])->format('M d, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Ordered List of Tasks -->
                <div class="mt-8">
                    <h3 class="text-lg font-semibold">Tasks:</h3>
                    <ol class="list-decimal list-inside mt-2">
                        <li>Print Sublimation Sheet</li>
                        <li>Print Sleeve Paper</li>
                        <li>Print Poster</li>
                        <li>Create and print shipping labels and branding labels, then place on mailers</li>
                        <li>Cut the posters & sleeves and fit to the boxes</li>
                        <li>Cut sublimation paper, tape to blanks</li>
                        <li>Press</li>
                        <li>Fill mailers and ship</li>
                    </ol>
                    <h4 class="mt-4 font-semibold">Addendum:</h4>
                    <ol class="list-decimal list-inside mt-2">
                        <li>Run inventory</li>
                        <li>Add expenses</li>
                        <li>Create puzzle images</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
