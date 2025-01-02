document.querySelectorAll('.password-field').forEach(field => {
    const input = field.querySelector('.input-field');
    const showIcon = field.querySelector('.show-pw');
    const hideIcon = field.querySelector('.hide-pw');

    // Hàm xử lý khi bấm vào icon để hiển thị/ẩn mật khẩu
    const togglePasswordVisibility = () => {
        if (input.type === 'password') {
            input.type = 'text';
            showIcon.classList.add('hidden');
            hideIcon.classList.remove('hidden');
        } else {
            input.type = 'password';
            hideIcon.classList.add('hidden');
            showIcon.classList.remove('hidden');
        }
    };

    // Gắn sự kiện click cho cả hai icon
    showIcon.addEventListener('click', togglePasswordVisibility);
    hideIcon.addEventListener('click', togglePasswordVisibility);
});
