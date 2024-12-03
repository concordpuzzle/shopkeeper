<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Products') }}
            </h2>
            <a href="{{ route('products.create') }}" class="bg-[#b71540] hover:bg-[#9c1238] text-black px-4 py-2 rounded">
                Add New Product
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                @foreach($products as $product)
                    <div class="mb-8 border-b pb-6" 
                         x-data="productData({{ json_encode([
                             'materials' => $product->materials,
                             'sale_price' => $product->sale_price ?? 0,
                             'productId' => $product->id,
                             'wooCategoryId' => $product->woo_category_id ?? '',
                             'wooCategoryName' => $product->woo_category_name ?? ''
                         ]) }})"
                    >
                        <div class="flex justify-between items-center mb-4">
                            <div class="flex items-center gap-4">
                                <h3 class="text-lg font-bold">{{ $product->title }}</h3>
                                <div class="relative">
                                    <select 
                                        x-model="wooCategoryId"
                                        @change="saveWooCategory()"
                                        class="block appearance-none bg-white border border-gray-300 hover:border-gray-400 px-4 py-2 pr-8 rounded leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    >
                                        <option value="">Select WooCommerce Category</option>
                                        <template x-for="category in wooCategories" :key="category.id">
                                            <option :value="category.id" 
                                                    x-text="category.name"
                                                    :selected="category.id == wooCategoryId">
                                            </option>
                                        </template>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="flex gap-6 items-center">
                                <div class="text-sm">
                                    <span class="font-semibold">Cost Per Item:</span>
                                    <span x-text="'$' + totalCost.toFixed(2)"></span>
                                </div>
                                <div class="text-sm flex items-center gap-2">
                                    <span class="font-semibold">Sale Price:</span>
                                    <span>$</span>
                                    <input type="number" 
                                           x-model.number="sale_price" 
                                           @change="saveSalePrice()"
                                           class="border-0 focus:ring-2 focus:ring-blue-500 rounded px-2 py-1 w-24"
                                           step="0.01"
                                           min="0">
                                </div>
                                <div class="text-sm">
                                    <span class="font-semibold">Profit Per Item:</span>
                                    <span x-text="'$' + profit.toFixed(2)"
                                          :class="{ 'text-green-600': profit > 0, 'text-red-600': profit < 0 }">
                                    </span>
                                </div>
                                <div class="text-sm">
                                    <span class="font-semibold">Margin:</span>
                                    <span x-text="margin.toFixed(1) + '%'"
                                          :class="{ 'text-green-600': margin > 0, 'text-red-600': margin < 0 }">
                                    </span>
                                </div>
                            </div>
                        </div>

                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Material</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Inventory</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price ($)</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Source</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <template x-for="material in materials" :key="material.id">
                                    <tr>
                                        <td class="px-6 py-4">
                                            <input type="text" 
                                                   x-model="material.name"
                                                   @change="saveMaterial(material)"
                                                   class="border-0 focus:ring-2 focus:ring-blue-500 rounded px-2 py-1 w-full">
                                        </td>
                                        <td class="px-6 py-4">
                                            <input type="number" 
                                                   x-model.number="material.inventory_count"
                                                   @change="saveMaterial(material)"
                                                   class="border-0 focus:ring-2 focus:ring-blue-500 rounded px-2 py-1 w-24">
                                        </td>
                                        <td class="px-6 py-4">
                                            <input type="number" 
                                                   x-model.number="material.price_per"
                                                   @change="saveMaterial(material)"
                                                   class="border-0 focus:ring-2 focus:ring-blue-500 rounded px-2 py-1 w-24"
                                                   step="0.01"
                                                   min="0">
                                        </td>
                                        <td class="px-6 py-4">
                                            <input type="text" 
                                                   x-model="material.source"
                                                   @change="saveMaterial(material)"
                                                   class="border-0 focus:ring-2 focus:ring-blue-500 rounded px-2 py-1 w-full">
                                        </td>
                                        <td class="px-6 py-4">
                                            <template x-if="material.saved">
                                                <span class="text-green-500">✓ Saved</span>
                                            </template>
                                            <template x-if="material.error">
                                                <span class="text-red-500">Failed to save</span>
                                            </template>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>

                        <button @click="addMaterial()" class="mt-4 bg-[#b71540] hover:bg-[#9c1238] text-black font-bold py-2 px-4 rounded">
                            Add New Material
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('productData', (data) => ({
                materials: data.materials,
                sale_price: data.sale_price,
                productId: data.productId,
                wooCategoryId: data.wooCategoryId,
                wooCategoryName: data.wooCategoryName,
                wooCategories: [],
                
                async init() {
                    await this.fetchWooCategories();
                    if (this.wooCategoryId) {
                        console.log('Initial category:', this.wooCategoryId);
                    }
                },
                
                async fetchWooCategories() {
                    try {
                        const response = await fetch('/api/woo-categories');
                        if (!response.ok) throw new Error('Failed to fetch categories');
                        this.wooCategories = await response.json();
                    } catch (error) {
                        console.error('Failed to fetch WooCommerce categories:', error);
                    }
                },
                
                async saveWooCategory() {
                    try {
                        const selectedCategory = this.wooCategories.find(c => c.id == this.wooCategoryId);
                        const categoryName = selectedCategory ? selectedCategory.name : null;
                        
                        console.log('Saving category:', {
                            id: this.wooCategoryId,
                            name: categoryName
                        });

                        const response = await fetch(`/products/${this.productId}/woo-category`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ 
                                woo_category_id: this.wooCategoryId,
                                woo_category_name: categoryName
                            })
                        });
                        
                        if (!response.ok) {
                            const errorData = await response.json();
                            throw new Error(errorData.message || 'Failed to save category');
                        }

                        const data = await response.json();
                        console.log('Category saved successfully:', data);
                        
                        this.wooCategoryName = categoryName;
                        
                    } catch (error) {
                        console.error('Failed to save WooCommerce category:', error);
                        alert('Failed to save category: ' + error.message);
                    }
                },
                
                get totalCost() {
                    return this.materials.reduce((sum, material) => 
                        sum + (Number(material.price_per) || 0), 0);
                },
                
                get profit() {
                    return this.sale_price - this.totalCost;
                },
                
                get margin() {
                    if (this.sale_price <= 0) return 0;
                    return (this.profit / this.sale_price) * 100;
                },
                
                addMaterial() {
                    this.materials.push({
                        id: Date.now(), // Temporary ID
                        name: '',
                        inventory_count: 0,
                        price_per: 0.00,
                        source: '',
                        isNew: true,
                        saved: false,
                        error: false
                    });
                },
                
                async saveMaterial(material) {
                    try {
                        // Use the isNew flag to determine the request type
                        const isNewMaterial = material.isNew === true;
                        const url = isNewMaterial 
                            ? `/products/${this.productId}/materials` 
                            : `/materials/${material.id}`;
                        
                        const method = isNewMaterial ? 'POST' : 'PATCH';
                        
                        console.log(`${isNewMaterial ? 'Creating' : 'Updating'} material:`, material);

                        const response = await fetch(url, {
                            method: method,
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                name: material.name,
                                inventory_count: material.inventory_count,
                                price_per: material.price_per,
                                source: material.source
                            })
                        });
                        
                        if (!response.ok) throw new Error('Failed to save');
                        
                        const savedData = await response.json();
                        
                        // Update the material with the saved data
                        if (isNewMaterial) {
                            // Replace the temporary material with the saved one
                            const index = this.materials.findIndex(m => m.id === material.id);
                            if (index !== -1) {
                                // Keep the same array reference but update all properties
                                Object.assign(this.materials[index], savedData.material);
                                // Remove the isNew flag
                                delete this.materials[index].isNew;
                            }
                        }
                        
                        material.saved = true;
                        material.error = false;
                        setTimeout(() => {
                            material.saved = false;
                        }, 2000);
                    } catch (error) {
                        console.error('Save failed:', error);
                        material.error = true;
                        material.saved = false;
                        setTimeout(() => {
                            material.error = false;
                        }, 2000);
                    }
                },
                
                async saveSalePrice() {
                    try {
                        const response = await fetch(`/products/${this.productId}/sale-price`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ sale_price: this.sale_price })
                        });
                        
                        if (!response.ok) throw new Error('Failed to save');
                    } catch (error) {
                        console.error('Save failed:', error);
                    }
                }
            }));
        });
    </script>
    @endpush
</x-app-layout>
