<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Product') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <form action="{{ route('products.store') }}" method="POST" x-data="{ materials: [{}] }">
                    @csrf
                    
                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Product Title</label>
                        <input type="text" 
                               name="title" 
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                               value="{{ old('title') }}">
                        @error('title')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Materials</label>
                        
                        <!-- Headers -->
                        <div class="flex gap-4 mb-2 px-2">
                            <div class="flex-1 font-semibold text-gray-600">Material Name</div>
                            <div class="w-32 font-semibold text-gray-600">Inventory</div>
                            <div class="w-32 font-semibold text-gray-600">Price ($)</div>
                            <div class="flex-1 font-semibold text-gray-600">Source</div>
                            <div class="w-20"></div>
                        </div>

                        <!-- Material Rows -->
                        <template x-for="(material, index) in materials" :key="index">
                            <div class="flex gap-4 mb-4">
                                <div class="flex-1">
                                    <input type="text" 
                                           x-model="material.name" 
                                           :name="'materials['+index+'][name]'" 
                                           placeholder="e.g., Red Thread"
                                           class="shadow border rounded w-full py-2 px-3 text-gray-700">
                                    @error('materials.*.name')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="w-32">
                                    <input type="number" 
                                           x-model="material.inventory_count" 
                                           :name="'materials['+index+'][inventory_count]'" 
                                           placeholder="0"
                                           class="shadow border rounded w-full py-2 px-3 text-gray-700">
                                    @error('materials.*.inventory_count')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="w-32">
                                    <input type="number" 
                                           step="0.01" 
                                           x-model="material.price_per" 
                                           :name="'materials['+index+'][price_per]'" 
                                           placeholder="0.00"
                                           class="shadow border rounded w-full py-2 px-3 text-gray-700">
                                    @error('materials.*.price_per')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="flex-1">
                                    <input type="text" 
                                           x-model="material.source" 
                                           :name="'materials['+index+'][source]'" 
                                           placeholder="e.g., Local Store"
                                           class="shadow border rounded w-full py-2 px-3 text-gray-700">
                                    @error('materials.*.source')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="w-20">
                                    <button type="button" 
                                            @click="materials = materials.filter((m, i) => i !== index)" 
                                            class="btn-primary px-3 py-2 rounded transition"
                                            :disabled="materials.length === 1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                        
                        <button type="button" 
                                @click="materials.push({ name: '', inventory_count: '', price_per: '', source: '' })" 
                                class="btn-primary px-4 py-2 rounded transition">
                            Add Another Material
                        </button>
                    </div>

                    <div class="flex justify-end mt-6">
                        <a href="{{ route('products.index') }}" 
                           class="btn-primary px-6 py-2 rounded mr-4 transition">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="btn-primary px-6 py-2 rounded transition">
                            Create Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
