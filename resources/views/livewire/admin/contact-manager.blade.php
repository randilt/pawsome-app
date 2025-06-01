<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">Contact Management</h1>
        <div class="text-sm text-gray-500">
            Total: {{ $this->contacts->total() }} contacts
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <input type="text" 
                       id="search"
                       wire:model.live="search" 
                       placeholder="Search by name, email, or subject..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select id="status" 
                        wire:model.live="statusFilter"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="responded">Responded</option>
                    <option value="resolved">Resolved</option>
                </select>
            </div>

            <div class="flex items-end">
                <button wire:click="$refresh" 
                        class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition-colors">
                    Refresh
                </button>
            </div>
        </div>
    </div>

    <!-- Contacts Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($this->contacts as $contact)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $contact->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $contact->email }}</div>
                                    @if($contact->phone)
                                        <div class="text-sm text-gray-500">{{ $contact->phone }}</div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $contact->subject }}</div>
                                <div class="text-sm text-gray-500">{{ Str::limit($contact->message, 60) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                    @if($contact->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($contact->status === 'responded') bg-blue-100 text-blue-800
                                    @else bg-green-100 text-green-800 @endif">
                                    {{ ucfirst($contact->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $contact->created_at->format('M d, Y') }}
                                <div class="text-xs">{{ $contact->created_at->format('h:i A') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <button wire:click="viewContact({{ $contact->id }})" 
                                        class="text-blue-600 hover:text-blue-900">View</button>
                                
                                <div class="inline-block">
                                    <select wire:change="updateStatus({{ $contact->id }}, $event.target.value)" 
                                            class="text-xs border-gray-300 rounded">
                                        <option value="">Change Status</option>
                                        <option value="pending" {{ $contact->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="responded" {{ $contact->status === 'responded' ? 'selected' : '' }}>Responded</option>
                                        <option value="resolved" {{ $contact->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                                    </select>
                                </div>
                                
                                <button wire:click="deleteContact({{ $contact->id }})" 
                                        wire:confirm="Are you sure you want to delete this contact?"
                                        class="text-red-600 hover:text-red-900">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                No contacts found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-3 border-t border-gray-200">
            {{ $this->contacts->links() }}
        </div>
    </div>

    <!-- Contact Detail Modal -->
    @if($showModal && $selectedContact)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>
                
                <div class="relative inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full sm:p-6">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Contact Details</h3>
                        <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Contact Information -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Name</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $selectedContact->name }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Email</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $selectedContact->email }}</p>
                            </div>
                            
                            @if($selectedContact->phone)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Phone</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $selectedContact->phone }}</p>
                                </div>
                            @endif
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Subject</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $selectedContact->subject }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Status</label>
                                <span class="mt-1 inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                    @if($selectedContact->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($selectedContact->status === 'responded') bg-blue-100 text-blue-800
                                    @else bg-green-100 text-green-800 @endif">
                                    {{ ucfirst($selectedContact->status) }}
                                </span>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Submitted</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $selectedContact->created_at->format('M d, Y h:i A') }}</p>
                            </div>
                            
                            @if($selectedContact->responded_at)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Responded</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $selectedContact->responded_at->format('M d, Y h:i A') }}</p>
                                </div>
                            @endif
                        </div>

                        <!-- Message and Notes -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Message</label>
                                <div class="mt-1 p-3 border border-gray-300 rounded-md bg-gray-50">
                                    <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $selectedContact->message }}</p>
                                </div>
                            </div>
                            
                            <div>
                                <label for="adminNotes" class="block text-sm font-medium text-gray-700">Admin Notes</label>
                                <textarea id="adminNotes"
                                          wire:model="adminNotes"
                                          rows="6"
                                          class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                          placeholder="Add internal notes about this contact..."></textarea>
                                <div class="mt-2">
                                    <button wire:click="saveNotes" 
                                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                                        Save Notes
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Actions -->
                    <div class="mt-6 flex justify-between">
                        <div class="space-x-2">
                            <button wire:click="updateStatus({{ $selectedContact->id }}, 'responded')" 
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                                Mark as Responded
                            </button>
                            <button wire:click="updateStatus({{ $selectedContact->id }}, 'resolved')" 
                                    class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                                Mark as Resolved
                            </button>
                        </div>
                        
                        <button wire:click="closeModal" 
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>