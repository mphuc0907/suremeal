document.addEventListener("DOMContentLoaded", function () {
    // Lấy tất cả các combo-wrapper trên trang
    const comboWrappers = document.querySelectorAll(".combo-wrapper");

    comboWrappers.forEach(function (wrapper) {
        const inputComboProduct = wrapper.querySelector(".input-combo-product");
        const comboWrap = wrapper.querySelector(".combo-wrap");
        const comboSelects = wrapper.querySelectorAll(".combo-select");
        const inputValue = wrapper.querySelector(".input-value");

        // Xử lý khi bấm vào input-combo-product
        inputComboProduct.addEventListener("click", function (event) {
            // Đóng tất cả combo khác trước khi mở combo hiện tại
            document.querySelectorAll(".combo-wrap").forEach(function (otherComboWrap) {
                if (otherComboWrap !== comboWrap) {
                    otherComboWrap.classList.add("hidden");
                }
            });

            // Toggle hiển thị combo hiện tại
            comboWrap.classList.toggle("hidden");
            event.stopPropagation(); // Ngăn chặn sự kiện lan ra ngoài
        });

        // Xử lý khi chọn combo-select
        comboSelects.forEach(function (comboSelect) {
            comboSelect.addEventListener("click", function () {
                // Kiểm tra nếu combo-select có lớp 'disabled' thì không thực hiện gì
                if (comboSelect.classList.contains("disabled")) {
                    return;
                }

                // Lấy giá trị từ p và gán vào input-value
                const selectedValue = comboSelect.querySelector("p").textContent;
                inputValue.textContent = selectedValue;

                // Ẩn combo-wrap
                comboWrap.classList.add("hidden");
            });
        });
    });

    // Ẩn tất cả combo khi click bên ngoài
    document.addEventListener("click", function () {
        document.querySelectorAll(".combo-wrap").forEach(function (comboWrap) {
            comboWrap.classList.add("hidden");
        });
    });
});
