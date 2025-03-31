document.addEventListener("DOMContentLoaded", () => {
    // Update cart count on page load
    updateCartCount();

    // Add to cart buttons
    const addToCartButtons = document.querySelectorAll(".add-to-cart-btn");
    addToCartButtons.forEach((button) => {
        button.addEventListener("click", function (e) {
            e.preventDefault();
            const productId = this.getAttribute("data-product-id");
            const quantity =
                document.querySelector(
                    `input[name="quantity"][data-product-id="${productId}"]`
                )?.value || 1;

            addToCart(productId, quantity);
        });
    });

    // Update cart item quantity
    const quantityInputs = document.querySelectorAll(".cart-quantity-input");
    quantityInputs.forEach((input) => {
        input.addEventListener("change", function () {
            const itemId = this.getAttribute("data-item-id");
            const quantity = this.value;

            updateCartItem(itemId, quantity);
        });
    });

    // Remove cart item
    const removeButtons = document.querySelectorAll(".remove-cart-item");
    removeButtons.forEach((button) => {
        button.addEventListener("click", function (e) {
            e.preventDefault();
            const itemId = this.getAttribute("data-item-id");

            removeCartItem(itemId);
        });
    });
});

// Add product to cart
function addToCart(productId, quantity) {
    // First, try to use localStorage for client-side cart
    const cart = JSON.parse(localStorage.getItem("userCart")) || [];

    // Find product details from the page or fetch from API
    let productName, productPrice, productImage;

    // If we're on the product page, get details from the page
    if (document.querySelector("h1")) {
        productName = document.querySelector("h1").textContent.trim();
        productPrice =
            document
                .querySelector(".text-primary.font-bold")
                ?.textContent.trim()
                .replace("LKR ", "")
                .replace(",", "") || "0";
        productImage =
            document.getElementById("main-image")?.getAttribute("src") || "";
        const productDescription =
            document.querySelector(".text-gray-600")?.textContent.trim() || "";

        // Create product object
        const product = {
            id: productId,
            name: productName,
            price: Number.parseFloat(productPrice),
            imageUrl: productImage,
            description: productDescription,
            quantity: Number.parseInt(quantity),
        };

        // Check if product already exists in cart
        const existingProductIndex = cart.findIndex(
            (item) => item.id === productId
        );

        if (existingProductIndex !== -1) {
            // Update quantity if product already exists
            cart[existingProductIndex].quantity += Number.parseInt(quantity);
        } else {
            // Add new product to cart
            cart.push(product);
        }

        // Save updated cart to localStorage
        localStorage.setItem("userCart", JSON.stringify(cart));

        // Update cart count
        updateCartCount();

        // Show notification
        showNotification("Product added to cart!", "success");
    } else {
        // If not on product page, fetch product details from server
        fetch(`/api/products/${productId}`, {
            headers: {
                "Content-Type": "application/json",
                "X-Requested-With": "XMLHttpRequest",
            },
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    const product = {
                        id: productId,
                        name: data.data.name,
                        price: Number.parseFloat(data.data.price),
                        imageUrl: data.data.image_url || "/placeholder.svg",
                        description: data.data.description,
                        quantity: Number.parseInt(quantity),
                    };

                    // Check if product already exists in cart
                    const existingProductIndex = cart.findIndex(
                        (item) => item.id === productId
                    );

                    if (existingProductIndex !== -1) {
                        // Update quantity if product already exists
                        cart[existingProductIndex].quantity +=
                            Number.parseInt(quantity);
                    } else {
                        // Add new product to cart
                        cart.push(product);
                    }

                    // Save updated cart to localStorage
                    localStorage.setItem("userCart", JSON.stringify(cart));

                    // Update cart count
                    updateCartCount();

                    // Show notification
                    showNotification("Product added to cart!", "success");
                } else {
                    showNotification(
                        data.message || "Failed to add product to cart.",
                        "error"
                    );
                }
            })
            .catch((error) => {
                console.error("Error:", error);
                showNotification(
                    "An error occurred. Please try again.",
                    "error"
                );
            });
    }
}

// Update cart item quantity
function updateCartItem(itemId, quantity) {
    const cart = JSON.parse(localStorage.getItem("userCart")) || [];

    // Find the item in the cart
    const itemIndex = cart.findIndex((item) => item.id === itemId);

    if (itemIndex === -1) {
        showNotification("Item not found in cart.", "error");
        return;
    }

    // Update quantity
    cart[itemIndex].quantity = Number.parseInt(quantity);

    // Remove item if quantity is 0 or less
    if (cart[itemIndex].quantity <= 0) {
        return removeCartItem(itemId);
    }

    // Save updated cart
    localStorage.setItem("userCart", JSON.stringify(cart));

    // Update UI
    if (document.getElementById(`item-subtotal-${itemId}`)) {
        const subtotal = cart[itemIndex].price * cart[itemIndex].quantity;
        document.getElementById(
            `item-subtotal-${itemId}`
        ).textContent = `LKR ${subtotal.toFixed(2)}`;

        // Update total
        const total = cart.reduce(
            (sum, item) => sum + item.price * item.quantity,
            0
        );
        document.getElementById(
            "cart-total"
        ).textContent = `LKR ${total.toFixed(2)}`;
    }

    // Update cart count
    updateCartCount();

    showNotification("Cart updated!", "success");
}

// Remove item from cart
function removeCartItem(itemId) {
    let cart = JSON.parse(localStorage.getItem("userCart")) || [];

    // Filter out the item
    cart = cart.filter((item) => item.id !== itemId);

    // Save updated cart
    localStorage.setItem("userCart", JSON.stringify(cart));

    // Update UI
    if (document.getElementById(`cart-item-${itemId}`)) {
        document.getElementById(`cart-item-${itemId}`).remove();

        // Update total
        const total = cart.reduce(
            (sum, item) => sum + item.price * item.quantity,
            0
        );
        if (document.getElementById("cart-total")) {
            document.getElementById(
                "cart-total"
            ).textContent = `LKR ${total.toFixed(2)}`;
        }

        // Show empty cart message if cart is empty
        if (
            cart.length === 0 &&
            document.getElementById("cart-table") &&
            document.getElementById("empty-cart")
        ) {
            document.getElementById("cart-table").classList.add("hidden");
            document.getElementById("empty-cart").classList.remove("hidden");

            if (document.getElementById("checkout-button")) {
                document
                    .getElementById("checkout-button")
                    .classList.add("hidden");
            }
        }
    }

    // Update cart count
    updateCartCount();

    showNotification("Item removed from cart!", "success");
}

// Update cart count in header
function updateCartCount() {
    const cart = JSON.parse(localStorage.getItem("userCart")) || [];
    const totalItems = cart.reduce((total, item) => total + item.quantity, 0);

    // Update all cart count elements
    document.querySelectorAll(".cart-count").forEach((element) => {
        element.textContent = totalItems;
    });
}

// Show notification
function showNotification(message, type = "success") {
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
