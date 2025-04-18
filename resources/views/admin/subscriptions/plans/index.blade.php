<x-admin-layout>
    <x-slot name="title">Subscription Plans</x-slot>
    <x-slot name="header">Subscription Plans</x-slot>
    
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
        <!-- Add/Edit Subscription Plan Form -->
        <div class="md:col-span-1">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-4 bg-gray-50 border-b">
                    <h2 class="text-lg font-medium" id="form-title">Add New Plan</h2>
                </div>
                <div class="p-6">
                    <form id="planForm" action="{{ route('admin.subscriptions.plans.store') }}" method="POST">
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
                            <textarea id="description" name="description" rows="3" required 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm 
                                focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">{{ old('description') }}</textarea>
                        </div>
                        
                        <div class="mb-4">
                            <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Price (LKR)</label>
                            <input type="number" id="price" name="price" value="{{ old('price') }}" min="0" step="0.01" required 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm 
                                focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        </div>
                        
                        <div class="mb-4">
                            <label for="duration_in_days" class="block text-sm font-medium text-gray-700 mb-1">Duration (Days)</label>
                            <input type="number" id="duration_in_days" name="duration_in_days" value="{{ old('duration_in_days') }}" min="1" required 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm 
                                focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Features</label>
                            <div id="features-container">
                                <div class="flex mb-2">
                                    <input type="text" name="features[]" placeholder="Add a feature" 
                                        class="flex-1 rounded-md border-gray-300 shadow-sm 
                                        focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                    <button type="button" onclick="addFeatureField()" class="ml-2 bg-gray-200 text-gray-700 px-3 py-1 rounded-md hover:bg-gray-300 transition duration-300">
                                        +
                                    </button>
                                </div>
                            </div>
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
                                Save Plan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Subscription Plans List -->
        <div class="md:col-span-2">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-4 bg-gray-50 border-b">
                    <h2 class="text-lg font-medium">Subscription Plans</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subscribers</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($plans as $plan)
                                <tr>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $plan->name }}</div>
                                        <div class="text-xs text-gray-500 truncate max-w-xs">{{ $plan->description }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        LKR {{ number_format($plan->price, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $plan->duration_in_days }} days
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $plan->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $plan->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $plan->customerSubscriptions->count() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button onclick="viewPlan({{ $plan->id }})" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                            View
                                        </button>
                                        <button onclick="editPlan({{ $plan->id }})" class="text-blue-600 hover:text-blue-900 mr-3">
                                            Edit
                                        </button>
                                        
                                        @if($plan->customerSubscriptions->count() == 0)
                                            <form action="{{ route('admin.subscriptions.plans.destroy', $plan->id) }}" method="POST" class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this plan?')">
                                                    Delete
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-gray-400 cursor-not-allowed" title="Cannot delete plan with active subscriptions">Delete</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">No subscription plans found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Plan Details Modal -->
    <div id="planModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-4 bg-gray-50 border-b flex justify-between items-center">
                <h3 class="text-lg font-medium" id="modal-title">Plan Details</h3>
                <button onclick="closePlanModal()" class="text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="p-6" id="modal-content">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function addFeatureField() {
            const container = document.getElementById('features-container');
            const div = document.createElement('div');
            div.className = 'flex mb-2';
            div.innerHTML = `
                <input type="text" name="features[]" placeholder="Add a feature" 
                    class="flex-1 rounded-md border-gray-300 shadow-sm 
                    focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                <button type="button" onclick="removeFeatureField(this)" class="ml-2 bg-red-200 text-red-700 px-3 py-1 rounded-md hover:bg-red-300 transition duration-300">
                    -
                </button>
            `;
            container.appendChild(div);
        }
        
        function removeFeatureField(button) {
            const div = button.parentElement;
            div.remove();
        }
        
        function viewPlan(id) {
            // Here you would usually fetch the plan details via AJAX
            // For simplicity, we'll use the data already on the page
            const plans = @json($plans);
            const plan = plans.find(p => p.id === id);
            
            if (plan) {
                let featuresHtml = '';
                if (plan.features && plan.features.length) {
                    featuresHtml = '<ul class="list-disc pl-5 mt-2">';
                    plan.features.forEach(feature => {
                        featuresHtml += `<li>${feature}</li>`;
                    });
                    featuresHtml += '</ul>';
                }
                
                document.getElementById('modal-title').innerText = plan.name;
                document.getElementById('modal-content').innerHTML = `
                    <div class="space-y-4">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Description</h4>
                            <p class="mt-1">${plan.description}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Price</h4>
                            <p class="mt-1 text-xl font-semibold">LKR ${parseFloat(plan.price).toFixed(2)}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Duration</h4>
                            <p class="mt-1">${plan.duration_in_days} days</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Features</h4>
                            ${featuresHtml || '<p class="mt-1 text-gray-400">No features specified</p>'}
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Status</h4>
                            <p class="mt-1">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full ${plan.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                    ${plan.is_active ? 'Active' : 'Inactive'}
                                </span>
                            </p>
                        </div>
                    </div>
                `;
                
                document.getElementById('planModal').classList.remove('hidden');
            }
        }
        
        function closePlanModal() {
            document.getElementById('planModal').classList.add('hidden');
        }
        
        function editPlan(id) {
            // Fetch plan data
            const plans = @json($plans);
            const plan = plans.find(p => p.id === id);
            
            if (!plan) return;
            
            // Update form title
            document.getElementById('form-title').innerText = 'Edit Subscription Plan';
            
            // Update form action and method
            const form = document.getElementById('planForm');
            form.action = `/admin/subscriptions/plans/${id}`;
            
            // Add method field
            const methodField = document.getElementById('method-field');
            methodField.innerHTML = `<input type="hidden" name="_method" value="PUT">`;
            
            // Fill form fields
            document.getElementById('name').value = plan.name;
            document.getElementById('description').value = plan.description;
            document.getElementById('price').value = plan.price;
            document.getElementById('duration_in_days').value = plan.duration_in_days;
            
            // Set active checkbox
            const activeCheckbox = document.querySelector('input[name="is_active"]');
            activeCheckbox.checked = plan.is_active;
            
            // Clear existing feature fields
            const featuresContainer = document.getElementById('features-container');
            featuresContainer.innerHTML = '';
            
            // Add feature fields
            if (plan.features && plan.features.length) {
                plan.features.forEach((feature, index) => {
                    const div = document.createElement('div');
                    div.className = 'flex mb-2';
                    
                    if (index === 0) {
                        div.innerHTML = `
                            <input type="text" name="features[]" value="${feature}" 
                                class="flex-1 rounded-md border-gray-300 shadow-sm 
                                focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                            <button type="button" onclick="addFeatureField()" class="ml-2 bg-gray-200 text-gray-700 px-3 py-1 rounded-md hover:bg-gray-300 transition duration-300">
                                +
                            </button>
                        `;
                    } else {
                        div.innerHTML = `
                            <input type="text" name="features[]" value="${feature}" 
                                class="flex-1 rounded-md border-gray-300 shadow-sm 
                                focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                            <button type="button" onclick="removeFeatureField(this)" class="ml-2 bg-red-200 text-red-700 px-3 py-1 rounded-md hover:bg-red-300 transition duration-300">
                                -
                            </button>
                        `;
                    }
                    
                    featuresContainer.appendChild(div);
                });
            } else {
                // Add empty feature field if none exist
                const div = document.createElement('div');
                div.className = 'flex mb-2';
                div.innerHTML = `
                    <input type="text" name="features[]" placeholder="Add a feature" 
                        class="flex-1 rounded-md border-gray-300 shadow-sm 
                        focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                    <button type="button" onclick="addFeatureField()" class="ml-2 bg-gray-200 text-gray-700 px-3 py-1 rounded-md hover:bg-gray-300 transition duration-300">
                        +
                    </button>
                `;
                featuresContainer.appendChild(div);
            }
            
            // Show cancel button
            document.getElementById('cancelEdit').classList.remove('hidden');
            
            // Scroll to form
            form.scrollIntoView({ behavior: 'smooth' });
        }
        
        function resetForm() {
            // Reset form title
            document.getElementById('form-title').innerText = 'Add New Plan';
            
            // Reset form action and method
            const form = document.getElementById('planForm');
            form.action = "{{ route('admin.subscriptions.plans.store') }}";
            
            // Clear method field
            document.getElementById('method-field').innerHTML = '';
            
            // Clear form fields
            form.reset();
            
            // Reset feature fields
            const featuresContainer = document.getElementById('features-container');
            featuresContainer.innerHTML = `
                <div class="flex mb-2">
                    <input type="text" name="features[]" placeholder="Add a feature" 
                        class="flex-1 rounded-md border-gray-300 shadow-sm 
                        focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                    <button type="button" onclick="addFeatureField()" class="ml-2 bg-gray-200 text-gray-700 px-3 py-1 rounded-md hover:bg-gray-300 transition duration-300">
                        +
                    </button>
                </div>
            `;
            
            // Hide cancel button
            document.getElementById('cancelEdit').classList.add('hidden');
        }
        
        // Close modal when clicking outside of it
        document.getElementById('planModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closePlanModal();
            }
        });
    </script>
    @endpush
</x-admin-layout>