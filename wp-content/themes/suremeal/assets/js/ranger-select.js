let rangeMin = 50; // Khoảng cách tối thiểu giữa min và max trong giá trị
const range = document.querySelector(".range-selected");
const rangeInput = document.querySelectorAll(".range-input input");
const rangePrice = document.querySelectorAll(".range-price input");
const tooltipMin = document.getElementById("tooltip-min");
const tooltipMax = document.getElementById("tooltip-max");

function updateTooltips() {
    const minValue = parseInt(rangeInput[0].value);
    const maxValue = parseInt(rangeInput[1].value);

    tooltipMin.textContent = minValue + "$";
    tooltipMax.textContent = maxValue + "$";

    // Tính toán vị trí phần trăm cho tooltip
    const minPercentage = (minValue / rangeInput[0].max) * 100;
    const maxPercentage = (maxValue / rangeInput[1].max) * 100;

    // Cập nhật vị trí tooltip
    tooltipMin.style.left = `calc(${minPercentage}% + 10px)`;
    tooltipMax.style.left = `calc(${maxPercentage}% - 10px)`;
}

rangeInput.forEach((input) => {
    input.addEventListener("input", (e) => {
        let minRange = parseInt(rangeInput[0].value);
        let maxRange = parseInt(rangeInput[1].value);

        // Đảm bảo khoảng cách tối thiểu
        if (maxRange - minRange < rangeMin) {
            if (e.target.classList.contains("min")) {
                rangeInput[0].value = maxRange - rangeMin;
            } else {
                rangeInput[1].value = minRange + rangeMin;
            }
        }

        // Cập nhật giá trị sau khi điều chỉnh
        minRange = parseInt(rangeInput[0].value);
        maxRange = parseInt(rangeInput[1].value);

        // Cập nhật thanh hiển thị
        range.style.left = (minRange / rangeInput[0].max) * 100 + "%";
        range.style.right = 100 - (maxRange / rangeInput[1].max) * 100 + "%";

        // Đồng bộ giá trị hiển thị trong input
        rangePrice[0].value = minRange;
        rangePrice[1].value = maxRange;

        updateTooltips();
    });
});

rangePrice.forEach((input) => {
    input.addEventListener("input", (e) => {
        let minPrice = parseInt(rangePrice[0].value);
        let maxPrice = parseInt(rangePrice[1].value);

        // Đảm bảo khoảng cách tối thiểu
        if (maxPrice - minPrice >= rangeMin && maxPrice <= rangeInput[1].max) {
            if (e.target.name === "min") {
                minPrice = Math.min(minPrice, maxPrice - rangeMin);
                rangeInput[0].value = minPrice;
                range.style.left = (minPrice / rangeInput[0].max) * 100 + "%";
            } else {
                maxPrice = Math.max(maxPrice, minPrice + rangeMin);
                rangeInput[1].value = maxPrice;
                range.style.right = 100 - (maxPrice / rangeInput[1].max) * 100 + "%";
            }

            rangePrice[0].value = minPrice;
            rangePrice[1].value = maxPrice;

            updateTooltips();
        }
    });
});

// Cập nhật tooltip ban đầu
updateTooltips();
