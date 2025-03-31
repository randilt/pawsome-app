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
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadCart();
        });
        
        function loadCart() {
            const cartContainer = document.getElementById('cart-container');
            const cartSummary = document.getElementById('cart-summary');
            const cartItems = JSON.parse(localStorage.getItem('userCart')) || [];
            
            console.log("Loading cart:", cartItems);
            
            if (cartItems.length === 0) {
                cartContainer.innerHTML = `
                    <div class="text-center py-8">
                        <p class="text-xl mb-4">Your cart is empty</p>
                        <a href="{{ route('products.index') }}" class="bg-primary text-white py-2 px-4 rounded-full hover:bg-opacity-90 transition duration-300">Start Shopping</a>
                    </div>
                `;
                cartSummary.innerHTML = '';
            } else {
                let cartHTML = '<div class="bg-white rounded-lg shadow-md overflow-hidden">';
                cartHTML += '<table id="cart-table" class="min-w-full divide-y divide-gray-200">';
                cartHTML += `
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                `;
                
                let totalPrice = 0;
                
                cartItems.forEach((item) => {
                    const itemTotal = parseFloat(item.price) * item.quantity;
                    totalPrice += itemTotal;
                    
                    cartHTML += `
                        <tr id="cart-item-${item.id}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <img class="h-10 w-10 rounded-full object-cover" src="${item.imageUrl || '/placeholder.svg'}" alt="${item.name}">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">${item.name}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">LKR ${parseFloat(item.price).toFixed(2)}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <button onclick="updateCartItem('${item.id}', ${item.quantity - 1})" class="bg-gray-200 text-gray-700 py-1 px-2 rounded-md hover:bg-gray-300 transition duration-300">-</button>
                                    <input type="number" value="${item.quantity}" min="1" class="cart-quantity-input mx-2 w-16 text-center border-gray-300 rounded-md" data-item-id="${item.id}" onchange="updateCartItem('${item.id}', this.value)">
                                    <button onclick="updateCartItem('${item.id}', ${item.quantity + 1})" class="bg-gray-200 text-gray-700 py-1 px-2 rounded-md hover:bg-gray-300 transition duration-300">+</button>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div id="item-subtotal-${item.id}" class="text-sm text-gray-900">LKR ${itemTotal.toFixed(2)}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button onclick="removeCartItem('${item.id}')" class="text-red-600 hover:text-red-900 remove-cart-item" data-item-id="${item.id}">Remove</button>
                            </td>
                        </tr>
                    `;
                });
                
                cartHTML += `
                    </tbody>
                </table>
                </div>
                `;
                
                cartContainer.innerHTML = cartHTML;
                
                cartSummary.innerHTML = `
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-2xl font-semibold">Cart Summary</h2>
                            <div id="cart-total" class="text-2xl font-bold text-primary">LKR ${totalPrice.toFixed(2)}</div>
                        </div>
                        <a href="{{ route('cart.checkout') }}" id="checkout-button" class="block w-full bg-primary text-white text-center py-3 px-6 rounded-md hover:bg-opacity-90 transition duration-300 mt-4" onclick="goToCheckout(event)">
                            Proceed to Checkout
                        </a>
                    </div>
                `;
            }
        }
        
        function goToCheckout(event) {
            event.preventDefault();
            console.log("Proceeding to checkout");
            
            const cartItems = JSON.parse(localStorage.getItem('userCart')) || [];
            if (cartItems.length === 0) {
                showNotification("Your cart is empty", "error");
                return;
            }
            
            // Navigate to checkout page
            window.location.href = "{{ route('cart.checkout') }}";
        }
        
        function updateCartItem(itemId, quantity) {
            let cart = JSON.parse(localStorage.getItem('userCart')) || [];
            
            // Find the item in the cart
            const itemIndex = cart.findIndex(item => item.id === itemId);
            
            if (itemIndex === -1) {
                showNotification("Item not found in cart.", "error");
                return;
            }
            
            // Update quantity
            quantity = parseInt(quantity);
            if (quantity <= 0) {
                return removeCartItem(itemId);
            }
            
            cart[itemIndex].quantity = quantity;
            
            // Save updated cart
            localStorage.setItem('userCart', JSON.stringify(cart));
            
            // Update UI
            const subtotal = cart[itemIndex].price * cart[itemIndex].quantity;
            document.getElementById(`item-subtotal-${itemId}`).textContent = `LKR ${subtotal.toFixed(2)}`;
            
            // Update total
            const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            document.getElementById("cart-total").textContent = `LKR ${total.toFixed(2)}`;
            
            // Update cart count
            updateCartCount();
            
            showNotification("Cart updated!", "success");
        }
        
        function removeCartItem(itemId) {
            let cart = JSON.parse(localStorage.getItem('userCart')) || [];
            
            // Filter out the item
            cart = cart.filter(item => item.id !== itemId);
            
            // Save updated cart
            localStorage.setItem('userCart', JSON.stringify(cart));
            
            // Update UI
            if (document.getElementById(`cart-item-${itemId}`)) {
                document.getElementById(`cart-item-${itemId}`).remove();
                
                // Update total
                const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
                if (document.getElementById("cart-total")) {
                    document.getElementById("cart-total").textContent = `LKR ${total.toFixed(2)}`;
                }
                
                // Show empty cart message if cart is empty
                if (cart.length === 0) {
                    loadCart(); // Reload the cart to show empty state
                }
            }
            
            // Update cart count
            updateCartCount();
            
            showNotification("Item removed from cart!", "success");
        }
        
        function updateCartCount() {
            const cart = JSON.parse(localStorage.getItem('userCart')) || [];
            const totalItems = cart.reduce((total, item) => total + item.quantity, 0);
            
            // Update all cart count elements
            document.querySelectorAll('.cart-count').forEach(element => {
                element.textContent = totalItems;
            });
        }
        
        function showNotification(message, type = 'success') {
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

