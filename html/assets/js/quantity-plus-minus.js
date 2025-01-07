document.querySelectorAll(".counter").forEach(counter => {
    const decrementButton = counter.querySelector(".decrement");
    const incrementButton = counter.querySelector(".increment");
    const quantityInput = counter.querySelector(".quantity");

    decrementButton.addEventListener("click", () => {
        let currentValue = parseInt(quantityInput.value);
        if (currentValue > 1) {
            quantityInput.value = currentValue - 1;
        }
    });

    incrementButton.addEventListener("click", () => {
        let currentValue = parseInt(quantityInput.value);
        if (currentValue < 100) { // Giới hạn tối đa là 100
            quantityInput.value = currentValue + 1;
        }
    });
});
