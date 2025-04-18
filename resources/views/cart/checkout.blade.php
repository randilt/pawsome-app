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
                    
                    <button type="submit" class="w-full bg-blue-500 text-white py-3 px-6 rounded-md hover:bg-opacity-90 transition duration-300">Place Order</button>
                </form>
            </div>
        </div>
    </main>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log("Checkout page loaded");
            loadCheckout();
            
            // Handle form submission
            document.getElementById('checkout-form').addEventListener('submit', handleCheckout);
        });
        
        function loadCheckout() {
            const checkoutItems = document.getElementById('checkout-items');
            const checkoutTotal = document.getElementById('checkout-total');
            const cartItems = JSON.parse(localStorage.getItem('userCart')) || [];
            
            console.log("Loading checkout with items:", cartItems);
            
            // Redirect to cart if no items
            if (cartItems.length === 0) {
                console.log("No items in cart, redirecting to cart page");
                window.location.href = '{{ route('cart.index') }}';
                return;
            }
            
            let checkoutHTML = '';
            let totalPrice = 0;
            
            cartItems.forEach((item) => {
                const itemTotal = parseFloat(item.price) * item.quantity;
                totalPrice += itemTotal;
                
                checkoutHTML += `
                    <div class="flex justify-between items-center mb-2 pb-2 border-b border-gray-200">
                        <div class="flex items-center">
                            <img src="${item.imageUrl || '/placeholder.svg'}" alt="${item.name}" class="w-12 h-12 object-cover rounded-md mr-3">
                            <div>
                                <p class="font-medium">${item.name}</p>
                                <p class="text-sm text-gray-500">Qty: ${item.quantity}</p>
                            </div>
                        </div>
                        <span>LKR ${itemTotal.toFixed(2)}</span>
                    </div>
                `;
            });
            
            checkoutItems.innerHTML = checkoutHTML;
            checkoutTotal.innerHTML = `Total: <span class="text-primary">LKR ${totalPrice.toFixed(2)}</span>`;
        }
        
        async function handleCheckout(event) {
            event.preventDefault();
            console.log("Checkout form submitted");
            
            try {
                const formData = new FormData(event.target);
                const cartItems = JSON.parse(localStorage.getItem('userCart')) || [];
                
                if (cartItems.length === 0) {
                    showNotification('Your cart is empty', 'error');
                    return;
                }
                
                const payload = {
                    shipping_address: `${formData.get('address')}, ${formData.get('city')}`,
                    items: cartItems.map((item) => ({
                        product_id: item.id,
                        quantity: item.quantity
                    }))
                };
                
                console.log("Sending order payload:", payload);
                
                // Get CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                showNotification('Processing your order...', 'success');
                
                const response = await fetch('/api/orders', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });
                
                console.log("Order response status:", response.status);
                
                const responseData = await response.json();
                console.log("Order response data:", responseData);
                
                if (!response.ok) {
                    if (response.status === 401) {
                        showNotification('Please login to place an order', 'error');
                        setTimeout(() => {
                            window.location.href = '{{ route('login') }}';
                        }, 2000);
                        return;
                    }
                    throw new Error(responseData.error || 'Order creation failed');
                }
                
                // If we get here, the order was successfully placed
                // Clear cart and redirect to orders page
                localStorage.removeItem('userCart');
                showNotification('Order placed successfully!', 'success');
                
                setTimeout(() => {
                    window.location.href = '{{ route('home') }}';
                }, 2000);
                
            } catch (error) {
                console.error('Checkout error:', error);
                showNotification(error.message || 'An error occurred while placing the order', 'error');
            }
        }
        
        function showNotification(message, type = 'success') {
            console.log("Notification:", message, type);
            
            const notification = document.createElement("div");
            notification.className = `fixed top-4 right-4 px-6 py-3 rounded-md shadow-md z-50 ${
                type === "success" ? "bg-green-500" : "bg-red-500"
            } text-white`;
            notification.textContent = message;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.classList.add(
                    "opacity-0",
                    "transition-opacity",
                    "duration-500"
                );
                setTimeout(() => {
                    notification.remove();
                }, 500);
            }, 3000);
        }
    </script>
</x-app-layout>

