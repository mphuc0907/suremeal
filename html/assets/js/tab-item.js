document.querySelectorAll('.tab-item').forEach(tab => {
    tab.addEventListener('click', () => {
        // Xóa class 'active' khỏi tất cả các tab
        document.querySelectorAll('.tab-item').forEach(item => item.classList.remove('active'));

        // Thêm class 'active' vào tab được bấm
        tab.classList.add('active');
    });
});
