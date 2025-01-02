// Chọn tất cả các phần tử có class "quantity-choose"
const quantityItems = document.querySelectorAll('.quantity-choose');

// Thêm sự kiện click cho từng phần tử
quantityItems.forEach(item => {
    item.addEventListener('click', () => {
        // Xóa class 'active' khỏi tất cả các phần tử
        quantityItems.forEach(i => i.classList.remove('active'));

        // Thêm class 'active' vào phần tử được nhấp
        item.classList.add('active');
    });
});
