// Initializing the cart from localStorage
let cart = JSON.parse(localStorage.getItem("cart")) || [];

// Update the cart count in the navbar
function updateCartCount() {
  const cartCount = document.getElementById("cart-count");
  if (cartCount) {
    cartCount.textContent = cart.length;
  }
}

// Function to add an item to the cart
function addToCart(item) {
  const existingItem = cart.find((cartItem) => cartItem.name === item.name);

  if (existingItem) {
    existingItem.quantity += 1;
  } else {
    item.quantity = 1;
    cart.push(item);
  }

  localStorage.setItem("cart", JSON.stringify(cart));
  updateCartCount();
}

// Function to remove an item from the cart (decreasing the quantity)
function removeFromCart(itemName) {
  const item = cart.find((cartItem) => cartItem.name === itemName);

  if (item) {
    if (item.quantity > 1) {
      item.quantity -= 1; // Decrease the quantity by 1
    } else {
      cart = cart.filter((cartItem) => cartItem.name !== itemName); // If quantity is 1, remove item entirely
    }
  }

  localStorage.setItem("cart", JSON.stringify(cart));
  updateCartCount();
  getCartDetails(); // Update cart display after removal
}

// Function to get the cart details and display them on the cart page
function getCartDetails() {
  const cartDetailsContainer = document.getElementById("cart-details");
  const totalPriceContainer = document.getElementById("total");
  if (cartDetailsContainer) {
    cartDetailsContainer.innerHTML = ""; // Clear existing items

    let totalPrice = 0;
    cart.forEach((item) => {
      totalPrice += item.price * item.quantity;
      const itemElement = document.createElement("div");
      itemElement.classList.add("cart-item");
      itemElement.innerHTML = `
        <img src="../img/${item.image}" alt="${item.name}">
        <div class="cart-item-info">
          <h2>${item.name}</h2>
          <span>Price: $${item.price}</span>
          <span>Quantity: ${item.quantity}</span>
        </div>
        <div class="cart-item-remove">
          <a href="#" class="btn" onclick="removeFromCart('${item.name}')">Remove 1</a>
        </div>
      `;
      cartDetailsContainer.appendChild(itemElement);
    });

    totalPriceContainer.textContent = `$${totalPrice.toFixed(2)}`;
  }
}

// Initial setup when page loads
document.addEventListener("DOMContentLoaded", () => {
  updateCartCount();

  // If it's the cart page, display cart details
  if (window.location.pathname.includes("cart.html")) {
    getCartDetails();
  }
});