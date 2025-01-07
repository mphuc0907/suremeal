document.addEventListener('DOMContentLoaded', function () {
    const stars = document.querySelectorAll('.star-rating img');

    stars.forEach(star => {
        star.addEventListener('click', function () {
            const index = parseInt(this.getAttribute('data-index'));

            // Đặt lại tất cả các ngôi sao thành không sáng
            stars.forEach(s => s.src = '../html/assets/image/icon/star-gray.svg');

            // Đặt các ngôi sao sáng từ trái sang phải đến ngôi sao được nhấp
            for (let i = 0; i < index; i++) {
                stars[i].src = '../html/assets/image/icon/star-yellow.svg';
            }
        });
    });
});
