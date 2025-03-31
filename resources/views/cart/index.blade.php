<x-app-layout>
    <x-slot name="title">My Cart</x-slot>
    
    <main class="container mx-auto px-4 py-8">
        <h1 class="text-3xl md:text-5xl font-extralight font-chewy mb-8">My Cart</h1>
        
        <div id="cart-container">
           <!-- Cart items will be dynamically inserted here -->
        </div>
        
        <div id="cart-summary" class="mt-8">
           <!-- Cart summary will be inserted here -->
        </div>
    </main>
    
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadCart();
            updateCartCount();
        });
        
        function loadCart() {
            const cartContainer = document.getElementById('cart-container');
            const cartSummary = document.getElementById('cart-summary');
            const cartItems = JSON.parse(localStorage.getItem('userCart')) || [];
            
            if (cartItems.length === 0) {
                cartContainer.innerHTML = `
                    <div class="text-center py-8">
                        <p class="text-xl mb-4">Your cart is empty</p>
                        <a href="{{ route('products.index') }}" class="bg-primary text-white py-2 px-4 rounded-full hover:bg-opacity-90 transition duration-300">Start Shopping</a>
                    </div>
                `;
                cartSummary.innerHTML = '';
            } else {
                let cartHTML = '';
                let totalPrice = 0;
                
                cartItems.forEach((item) => {
                    const itemTotal = parseFloat(item.price) * item.quantity;
                    totalPrice += itemTotal;
                    
                    cartHTML += `
                        <div class="flex flex-col md:flex-row justify-between items-center bg-white p-4 rounded-lg shadow-md mb-4">
                            <div class="flex flex-col md:flex-row items-center mb-4 md:mb-0">
                                <img src="${item.imageUrl || 'https://via.placeholder.com/100x100'}" alt="${item.name}" class="w-24 h-24 object-cover rounded-md mr-4 mb-4 md:mb-0">
                                <div>
                                    <h2 class="text-lg font-semibold">${item.name}</h2>
                                    <p class="text-gray-600">${item.description}</p>
                                    <p class="text-primary font-semibold">LKR ${parseFloat(item.price).toFixed(2)} x ${item.quantity}</p>
                                </div>
                            </div>
                            <div class="flex space-x-4">
                                <button onclick="updateItemQuantity('${item.id}', ${item.quantity - 1})" class="bg-gray-200 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-300 transition duration-300">-</button>
                                <button onclick="updateItemQuantity('${item.id}', ${item.quantity + 1})" class="bg-gray-200 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-300 transition duration-300">+</button>
                                <button onclick="removeFromCart('${item.id}')" class="bg-accent text-white py-2 px-4 rounded-md hover:bg-opacity-90 transition duration-300">Remove</button>
                            </div>
                        </div>
                    `;
                });
                
                cartContainer.innerHTML = cartHTML;
                cartSummary.innerHTML = `
                    <div class="bg-white p-4 rounded-lg shadow-md">
                        <h2 class="text-2xl font-semibold mb-4">Cart Summary</h2>
                        <p class="text-xl">Total: <span class="font-bold text-primary">LKR ${totalPrice.toFixed(2)}</span></p>
                        <a href="{{ route('cart.checkout') }}">
                            <button class="w-full bg-primary text-white py-3 px-6 rounded-md hover:bg-opacity-90 transition duration-300 mt-4">Proceed to Checkout</button>
                        </a>
                    </div>
                `;
            }
        }
        
        function removeFromCart(itemId) {
            let cartItems = JSON.parse(localStorage.getItem('userCart')) || [];
            cartItems = cartItems.filter((item) => item.id !== itemId);
            localStorage.setItem('userCart', JSON.stringify(cartItems));
            loadCart();
            updateCartCount();
        }
        
        function updateItemQuantity(itemId, newQuantity) {
            if (newQuantity< 1) {
                removeFromCart(itemId);
                return;
            }
            
            let cartItems = JSON.parse(localStorage.getItem('userCart')) || [];
            const itemIndex = cartItems.findIndex((item) => item.id === itemId);
            
            if (itemIndex !== -1) {
                cartItems[itemIndex].quantity = newQuantity;
                localStorage.setItem('userCart', JSON.stringify(cartItems));
                loadCart();
                updateCartCount();
            }
        }
        
        function updateCartCount() {
            const userCart = JSON.parse(localStorage.getItem('userCart')) || [];
            const cartCount = userCart.reduce((total, item) => total + item.quantity, 0);
            
            const cartCountElement = document.querySelector('.cart-count');
            if (cartCountElement) {
                cartCountElement.textContent = cartCount;
            }
        }
    </script>
    @endpush
</x-app-layout>

