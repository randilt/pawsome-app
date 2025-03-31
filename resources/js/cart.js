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
    fetch(`/cart/add/${productId}`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
        },
        body: JSON.stringify({ quantity: quantity }),
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                updateCartCount();
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
            showNotification("An error occurred. Please try again.", "error");
        });
}

// Update cart item quantity
function updateCartItem(itemId, quantity) {
    fetch(`/cart/update/${itemId}`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
        },
        body: JSON.stringify({ quantity: quantity }),
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                // Update subtotal and total
                document.getElementById(
                    `item-subtotal-${itemId}`
                ).textContent = `LKR ${data.subtotal}`;
                document.getElementById(
                    "cart-total"
                ).textContent = `LKR ${data.total}`;

                showNotification("Cart updated!", "success");
            } else {
                showNotification(
                    data.message || "Failed to update cart.",
                    "error"
                );
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            showNotification("An error occurred. Please try again.", "error");
        });
}

// Remove item from cart
function removeCartItem(itemId) {
    fetch(`/cart/remove/${itemId}`, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
        },
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                // Remove item row from table
                document.getElementById(`cart-item-${itemId}`).remove();

                // Update total
                document.getElementById(
                    "cart-total"
                ).textContent = `LKR ${data.total}`;

                // Update cart count
                updateCartCount();

                // Show empty cart message if cart is empty
                if (data.count === 0) {
                    document
                        .getElementById("cart-table")
                        .classList.add("hidden");
                    document
                        .getElementById("empty-cart")
                        .classList.remove("hidden");
                    document
                        .getElementById("checkout-button")
                        .classList.add("hidden");
                }

                showNotification("Item removed from cart!", "success");
            } else {
                showNotification(
                    data.message || "Failed to remove item from cart.",
                    "error"
                );
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            showNotification("An error occurred. Please try again.", "error");
        });
}

// Update cart count in header
function updateCartCount() {
    fetch("/cart", {
        headers: {
            "X-Requested-With": "XMLHttpRequest",
        },
    })
        .then((response) => response.json())
        .then((data) => {
            const cartCountElements = document.querySelectorAll(".cart-count");
            cartCountElements.forEach((element) => {
                element.textContent = data.count;
            });
        })
        .catch((error) => {
            console.error("Error:", error);
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
