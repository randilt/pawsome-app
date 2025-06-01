document.addEventListener("DOMContentLoaded", () => {
    // Update cart count on page load
    updateCartCount();

    // Add to cart buttons
    const addToCartButtons = document.querySelectorAll(".add-to-cart-btn");
    addToCartButtons.forEach((button) => {
        button.addEventListener("click", function (e) {
            e.preventDefault();
            const productId = this.getAttribute("data-product-id");
            const quantityInput = document.querySelector(
                `input[name="quantity"][data-product-id="${productId}"]`
            );
            const quantity = quantityInput ? quantityInput.value : 1;

            // Collect any variant options
            const options = {};
            const variantSelects = document.querySelectorAll(".variant-select");
            variantSelects.forEach((select) => {
                if (select.value) {
                    options[select.name] = select.value;
                }
            });

            addToCart(productId, quantity, options);
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

    // Quantity increment/decrement buttons
    const incrementButtons = document.querySelectorAll(".quantity-increment");
    const decrementButtons = document.querySelectorAll(".quantity-decrement");

    incrementButtons.forEach((button) => {
        button.addEventListener("click", function (e) {
            e.preventDefault();
            const input = this.parentElement.querySelector(
                'input[type="number"]'
            );
            const max = parseInt(input.getAttribute("max")) || 999;
            const current = parseInt(input.value) || 0;
            if (current < max) {
                input.value = current + 1;
                input.dispatchEvent(new Event("change"));
            }
        });
    });

    decrementButtons.forEach((button) => {
        button.addEventListener("click", function (e) {
            e.preventDefault();
            const input = this.parentElement.querySelector(
                'input[type="number"]'
            );
            const min = parseInt(input.getAttribute("min")) || 1;
            const current = parseInt(input.value) || 0;
            if (current > min) {
                input.value = current - 1;
                input.dispatchEvent(new Event("change"));
            }
        });
    });
});

// Add product to cart via server-side
function addToCart(productId, quantity, options = {}) {
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content");

    if (!csrfToken) {
        showNotification(
            "Security token not found. Please refresh the page.",
            "error"
        );
        return;
    }

    // Disable the button to prevent double-clicks
    const button = document.querySelector(`[data-product-id="${productId}"]`);
    if (button) {
        button.disabled = true;
        button.textContent = "Adding...";
    }

    fetch(`/cart/add/${productId}`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken,
            "X-Requested-With": "XMLHttpRequest",
            Accept: "application/json",
        },
        body: JSON.stringify({
            quantity: parseInt(quantity),
            options: options,
        }),
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
        })
        .finally(() => {
            // Re-enable the button
            if (button) {
                button.disabled = false;
                button.textContent = "Add to Cart";
            }
        });
}

// Update cart item quantity via server-side
function updateCartItem(itemId, quantity) {
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content");

    if (!csrfToken) {
        showNotification(
            "Security token not found. Please refresh the page.",
            "error"
        );
        return;
    }

    fetch(`/cart/update/${itemId}`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken,
            "X-Requested-With": "XMLHttpRequest",
            Accept: "application/json",
        },
        body: JSON.stringify({
            quantity: parseInt(quantity),
            _method: "PUT",
        }),
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                // Update UI elements
                if (document.getElementById(`item-subtotal-${itemId}`)) {
                    document.getElementById(
                        `item-subtotal-${itemId}`
                    ).textContent = `LKR ${
                        data.item_subtotal
                            ? data.item_subtotal.toFixed(2)
                            : "0.00"
                    }`;
                }

                if (document.getElementById("cart-total")) {
                    document.getElementById("cart-total").textContent = `LKR ${
                        data.cart_total ? data.cart_total.toFixed(2) : "0.00"
                    }`;
                }

                updateCartCount();
                showNotification("Cart updated!", "success");
            } else {
                showNotification(
                    data.message || "Failed to update cart.",
                    "error"
                );
                // Revert the input value if update failed
                location.reload();
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            showNotification("An error occurred. Please try again.", "error");
            location.reload();
        });
}

// Remove item from cart via server-side
function removeCartItem(itemId) {
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content");

    if (!csrfToken) {
        showNotification(
            "Security token not found. Please refresh the page.",
            "error"
        );
        return;
    }

    if (!confirm("Are you sure you want to remove this item from cart?")) {
        return;
    }

    fetch(`/cart/remove/${itemId}`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken,
            "X-Requested-With": "XMLHttpRequest",
            Accept: "application/json",
        },
        body: JSON.stringify({
            _method: "DELETE",
        }),
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                // Remove the item row from UI
                const itemRow = document.getElementById(`cart-item-${itemId}`);
                if (itemRow) {
                    itemRow.remove();
                }

                // Update total
                if (document.getElementById("cart-total")) {
                    document.getElementById("cart-total").textContent = `LKR ${
                        data.cart_total ? data.cart_total.toFixed(2) : "0.00"
                    }`;
                }

                // Check if cart is empty
                const remainingItems =
                    document.querySelectorAll('[id^="cart-item-"]');
                if (remainingItems.length === 0) {
                    location.reload(); // Reload to show empty cart message
                }

                updateCartCount();
                showNotification("Item removed from cart!", "success");
            } else {
                showNotification(
                    data.message || "Failed to remove item.",
                    "error"
                );
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            showNotification("An error occurred. Please try again.", "error");
        });
}

// Update cart count in header via server-side
function updateCartCount() {
    fetch("/cart/data", {
        method: "GET",
        headers: {
            "X-Requested-With": "XMLHttpRequest",
            Accept: "application/json",
        },
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                // Update all cart count elements
                document.querySelectorAll(".cart-count").forEach((element) => {
                    element.textContent = data.data.count || 0;
                });
            }
        })
        .catch((error) => {
            console.error("Error fetching cart data:", error);
            // Fallback to localStorage for compatibility
            updateCartCountFromLocalStorage();
        });
}

// Fallback function for cart count (maintains backward compatibility)
function updateCartCountFromLocalStorage() {
    const cart = JSON.parse(localStorage.getItem("userCart")) || [];
    const totalItems = cart.reduce(
        (total, item) => total + (item.quantity || 0),
        0
    );

    document.querySelectorAll(".cart-count").forEach((element) => {
        element.textContent = totalItems;
    });
}

// Show notification function (unchanged)
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

// Checkout handling
document.addEventListener("DOMContentLoaded", function () {
    const checkoutForm = document.getElementById("checkout-form");
    if (checkoutForm) {
        checkoutForm.addEventListener("submit", function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            const csrfToken = document
                .querySelector('meta[name="csrf-token"]')
                ?.getAttribute("content");

            const submitButton = this.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.textContent = "Processing...";
            }

            fetch("/cart/checkout", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": csrfToken,
                    "X-Requested-With": "XMLHttpRequest",
                    Accept: "application/json",
                },
                body: formData,
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        showNotification(
                            "Order placed successfully!",
                            "success"
                        );
                        if (data.redirect_url) {
                            setTimeout(() => {
                                window.location.href = data.redirect_url;
                            }, 1000);
                        }
                    } else {
                        showNotification(
                            data.message || "Failed to place order.",
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
                })
                .finally(() => {
                    if (submitButton) {
                        submitButton.disabled = false;
                        submitButton.textContent = "Place Order";
                    }
                });
        });
    }
});
