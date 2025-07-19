
function updateQuantity(change) {
    const quantityInput = document.getElementById("quantityInput");
    const hiddenQuantity = document.getElementById("hiddenQuantity");
    let currentValue = parseInt(quantityInput.value) || 0;

    // Update the quantity
    currentValue += change;

    // Prevent negative values
    if (currentValue < 1) {
      currentValue = 1;
    }

    quantityInput.value = currentValue;
    hiddenQuantity.value = currentValue;
  }
