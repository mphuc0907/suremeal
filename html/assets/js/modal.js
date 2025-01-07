document.addEventListener('DOMContentLoaded', function () {
    const openModalButtons = document.querySelectorAll('.open-modal');
    const modals = document.querySelectorAll('.modal-wrapper');
    const closeModalButtons = document.querySelectorAll('.close-modal');

    // Hàm mở modal
    function openModal(modal) {
        modal.style.display = 'flex';
    }

    // Hàm đóng modal
    function closeModal(modal) {
        modal.style.display = 'none';
    }

    // Thêm sự kiện click cho các nút mở modal
    openModalButtons.forEach(button => {
        button.addEventListener('click', function () {
            const modalId = this.getAttribute('data-modal');
            const modal = document.getElementById(modalId);
            openModal(modal);
        });
    });

    // Thêm sự kiện click cho các nút đóng modal và khoảng đen
    modals.forEach(modal => {
        // Đóng modal khi click vào khoảng đen (modal-wrapper)
        modal.addEventListener('click', function (event) {
            if (event.target === modal) {
                closeModal(modal);
            }
        });
    });

    // Thêm sự kiện click cho các nút có class close-modal
    closeModalButtons.forEach(button => {
        button.addEventListener('click', function () {
            const modal = button.closest('.modal-wrapper');
            closeModal(modal);
        });
    });
});
