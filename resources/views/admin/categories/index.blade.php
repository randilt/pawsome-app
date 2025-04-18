<x-admin-layout>
    <x-slot name="title">Category Management</x-slot>
    <x-slot name="header">Category Management</x-slot>
    
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
            <p>{{ session('error') }}</p>
        </div>
    @endif
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Add/Edit Category Form -->
        <div class="md:col-span-1">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-4 bg-gray-50 border-b">
                    <h2 class="text-lg font-medium" id="form-title">Add New Category</h2>
                </div>
                <div class="p-6">
                    <form id="categoryForm" action="{{ route('admin.categories.store') }}" method="POST">
                        @csrf
                        <div id="method-field"></div>
                        
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" required 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm 
                                focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        </div>
                        
                        <div class="mb-4">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea id="description" name="description" rows="3" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm 
                                focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">{{ old('description') }}</textarea>
                        </div>
                        
                        <div class="mb-4">
                            <label for="image_url" class="block text-sm font-medium text-gray-700 mb-1">Image URL</label>
                            <input type="text" id="image_url" name="image_url" value="{{ old('image_url') }}" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm 
                                focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        </div>
                        
                        <div class="mb-4">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="is_active" value="1" checked 
                                    class="rounded border-gray-300 text-primary shadow-sm focus:border-primary 
                                    focus:ring focus:ring-primary focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">Active</span>
                            </label>
                        </div>
                        
                        <div class="flex justify-end space-x-2">
                            <button type="button" id="cancelEdit" onclick="resetForm()" class="hidden bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 transition duration-300">
                                Cancel
                            </button>
                            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition duration-300">
                                Save Category
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Categories List -->
        <div class="md:col-span-2">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-4 bg-gray-50 border-b">
                    <h2 class="text-lg font-medium">Categories List</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Products</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($categories as $category)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            @if($category->image_url)
                                                <img src="{{ $category->image_url }}" alt="{{ $category->name }}" class="h-10 w-10 rounded-full mr-3 object-cover">
                                            @else
                                                <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center mr-3">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $category->name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-500 line-clamp-2">{{ $category->description ?? 'No description' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $category->products->count() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button onclick="editCategory({{ $category->id }}, '{{ $category->name }}', '{{ $category->description }}', '{{ $category->image_url }}', {{ $category->is_active ? 'true' : 'false' }})" class="text-blue-600 hover:text-blue-900 mr-3">
                                            Edit
                                        </button>
                                        
                                        @if($category->products->count() == 0)
                                            <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this category?')">
                                                    Delete
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-gray-400 cursor-not-allowed" title="Cannot delete category with products">Delete</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">No categories found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function editCategory(id, name, description, imageUrl, isActive) {
            // Update form title
            document.getElementById('form-title').innerText = 'Edit Category';
            
            // Update form action and method
            const form = document.getElementById('categoryForm');
            form.action = `/admin/categories/${id}`;
            
            // Add method field
            const methodField = document.getElementById('method-field');
            methodField.innerHTML = `<input type="hidden" name="_method" value="PUT">`;
            
            // Fill form fields
            document.getElementById('name').value = name;
            document.getElementById('description').value = description || '';
            document.getElementById('image_url').value = imageUrl || '';
            
            // Set active checkbox
            const activeCheckbox = document.querySelector('input[name="is_active"]');
            activeCheckbox.checked = isActive;
            
            // Show cancel button
            document.getElementById('cancelEdit').classList.remove('hidden');
            
            // Scroll to form
            form.scrollIntoView({ behavior: 'smooth' });
        }
        
        function resetForm() {
            // Reset form title
            document.getElementById('form-title').innerText = 'Add New Category';
            
            // Reset form action and method
            const form = document.getElementById('categoryForm');
            form.action = "{{ route('admin.categories.store') }}";
            
            // Clear method field
            document.getElementById('method-field').innerHTML = '';
            
            // Clear form fields
            form.reset();
            
            // Hide cancel button
            document.getElementById('cancelEdit').classList.add('hidden');
        }
    </script>
    @endpush
</x-admin-layout>