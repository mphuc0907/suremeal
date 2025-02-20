<?php
$url = get_template_directory_uri();
?>
</div>
</div>
</div>
</body>
<script src="<?= $url ?>/dist/js/affiliate.js"></script>
<script src="<?= $url ?>/dist/js/doashboard.js"></script>
<script>
    function buyNowPoint(thiss) {
        // Lấy thông tin sản phẩm từ các thuộc tính
        var idPoint = thiss.attr("data-idPoint"); // ID sản phẩm
        var value = thiss.attr("data-value"); // ID sản phẩm
        var point = thiss.attr("data-point"); // ID sản phẩm
        var purchases = thiss.attr("data-purchases"); // ID sản phẩm
        var form;
        var formData = new FormData(form);
        formData.append('idPoint', idPoint);
        formData.append('value', value);
        formData.append('point', point);
        formData.append('purchases', purchases);
        formData.append('action', 'submitOrderPoint');
        $.ajax({
            url: "<?= admin_url('admin-ajax.php'); ?>",
            type: 'POST',
            processData: false,
            contentType: false,
            data: formData,
            dataType: 'json',
            beforeSend: function () {
                Swal.fire({
                    title: 'Processing',
                    html: '<?php pll_e('Please wait...') ?>',
                    didOpen: () => {
                        Swal.showLoading()
                    }
                });
            },
            success: function (response) {
                if (response.status === 1) {
                    Swal.fire({
                        icon: 'success',
                        text: response.message,
                    }).then(() => {
                        window.location.reload(); // Reload lại trang sau khi đóng thông báo
                    });

                } else {
                    Swal.fire({
                        icon: 'warning',
                        text: response.message,
                    });
                }
            },
            error: function (xhr) {
                Swal.fire({
                    icon: 'error',
                    text: '<?php pll_e('An error occurred. Please try again.') ?>'
                });
            }
        });
    }
</script>
</html>