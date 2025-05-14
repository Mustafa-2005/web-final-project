function addToCart(item) {
  const cart = JSON.parse(localStorage.getItem("cart")) || [];

  const existingItem = cart.find((cartItem) =>
    cartItem.id === item.id && cartItem.type === item.type
  );

  if (existingItem) {
    existingItem.quantity += 1;
  } else {
    item.quantity = 1;
    cart.push(item);
  }

  localStorage.setItem("cart", JSON.stringify(cart));
  updateCartCount();
}
