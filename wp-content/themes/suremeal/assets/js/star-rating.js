document.addEventListener('DOMContentLoaded', function () {
    const stars = document.querySelectorAll('.star-rating img');

    stars.forEach(star => {
        star.addEventListener('click', function () {
            const index = parseInt(this.getAttribute('data-index'));

            // Đặt lại tất cả các ngôi sao về trạng thái không sáng và xóa class active
            stars.forEach(s => {
                s.src = 'https://suremeal.qixtech.com/wp-content/themes/abcd/assets/image/icon/star-gray.svg';
                s.classList.remove('active');
            });

            // Đặt các ngôi sao sáng từ trái sang phải đến ngôi sao được nhấp và thêm class active
            for (let i = 0; i < index; i++) {
                stars[i].src = 'https://suremeal.qixtech.com/wp-content/themes/abcd/assets/image/icon/star-yellow.svg';
                stars[i].classList.add('active'); // Thêm class active
            }
        });
    });
});

