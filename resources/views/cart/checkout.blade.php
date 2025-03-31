<x-app-layout>
    <x-slot name="title">Checkout</x-slot>
    
    <main class="container mx-auto px-4 py-8">
        <h1 class="text-3xl md:text-5xl font-extralight font-chewy mb-8">Checkout</h1>
        
        <div id="checkout-container" class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-2xl font-semibold mb-4">Order Summary</h2>
                <div id="checkout-items"></div>
                <div id="checkout-total" class="mt-4 text-xl font-bold"></div>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-2xl font-semibold mb-4">Shipping Information</h2>
                <form id="checkout-form" class="space-y-4">
                    @csrf
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                        <input type="text" id="name" name="name" value="{{ auth()->user()->name ?? '' }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" id="email" name="email" value="{{ auth()->user()->email ?? '' }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                    </div>
                    
                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                        <input type="text" id="address" name="address" value="{{ auth()->user()->address ?? '' }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                    </div>
                    
                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700">City</label>
                        <input type="text" id="city" name="city" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                    </div>
                    
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                        <input type="tel" id="phone" name="phone" value="{{ auth()->user()->phone ?? '' }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Payment Method</label>
                        <div class="mt-2">
                            <label class="inline-flex items-center">
                                <input type="radio" class="form-radio" name="paymentMethod" value="cashOnDelivery" checked>
                                <span class="ml-2">Cash on Delivery</span>
                            </label>
                        </div>
                    </div>
                    
                    <button type="submit" class="w-full bg-primary text-white py-3 px-6 rounded-md hover:bg-opacity-90 transition duration-300">Place Order</button>
                </form>
            </div>
        </div>
    </main>
    
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadCheckout();
        });
        
        function loadCheckout() {
            const checkoutItems = document.getElementById('checkout-items');
            const checkoutTotal = document.getElementById('checkout-total');
            const cartItems = JSON.parse(localStorage.getItem('userCart')) || [];
            
            // Redirect to cart if no items
            if (cartItems.length === 0) {
                window.location.href = '{{ route('cart.index') }}';
                return;
            }
            
            let checkoutHTML = '';
            let totalPrice = 0;
            
            cartItems.forEach((item) => {
                const itemTotal = parseFloat(item.price) * item.quantity;
                totalPrice += itemTotal;
                
                checkoutHTML += `
                    <div class="flex justify-between items-center mb-2">
                        <span>${item.name} x ${item.quantity}</span>
                        <span>LKR ${itemTotal.toFixed(2)}</span>
                    </div>
                `;
            });
            
            checkoutItems.innerHTML = checkoutHTML;
            checkoutTotal.innerHTML = `Total: LKR ${totalPrice.toFixed(2)}`;
            
            const checkoutForm = document.getElementById('checkout-form');
            checkoutForm.addEventListener('submit', handleCheckout);
        }
        
        async function handleCheckout(event) {
            event.preventDefault();
            
            try {
                const formData = new FormData(event.target);
                const cartItems = JSON.parse(localStorage.getItem('userCart')) || [];
                
                const payload = {
                    shipping_address: `${formData.get('address')}, ${formData.get('city')}`,
                    items: cartItems.map((item) => ({
                        product_id: parseInt(item.id),
                        quantity: item.quantity,
                    })),
                };
                
                // Get CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                const response = await fetch('{{ route('orders.store') }}', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(payload),
                });
                
                if (!response.ok) {
                    const error = await response.json();
                    if (response.status === 401) {
                        alert('Please login to place an order');
                        window.location.href = '{{ route('login') }}';
                        return;
                    }
                    throw new Error(error.error || 'Order creation failed');
                }
                
                // Clear cart and redirect to orders page
                localStorage.removeItem('userCart');
                window.location.href = '{{ route('orders.index') }}';
                
            } catch (error) {
                console.error('Checkout error:', error);
                alert(error.message || 'An error occurred while placing the order');
            }
        }
    </script>
    @endpush
</x-app-layout>

