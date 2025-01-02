jQuery(document).ready(function($) {
    function collectFilterData() {
        // Collect target user
        var targetUsers = [];
        $('input[name="target_user"]:checked:not(.check-all)').each(function() {
            targetUsers.push($(this).val());
        });

        // Collect needs
        var needs = [];
        $('input[name="needs"]:checked:not(.check-all)').each(function() {
            needs.push($(this).val());
        });

        // Collect brands
        var brands = [];
        $('input[name="brand"]:checked:not(.check-all)').each(function() {
            brands.push($(this).val());
        });

        return {
            action: 'filter_products',
            target_user: targetUsers,
            needs: needs,
            brands: brands,
            min_price: $('input[name="min"]').val(),
            max_price: $('input[name="max"]').val(),
            search_keyword: $('input[name="search_name"]').val(),
            sort_by: $('.tab-item.active').data('sort'),
            paged: 1
        };
    }

    function filterProducts(page) {
        var filterData = collectFilterData();
        filterData.paged = page;

        $.ajax({
            url: productFilterAjax.ajax_url,
            type: 'POST',
            data: filterData,
            success: function(response) {
                if (response.success) {
                    $('.grid.grid-cols-1').html(response.data.html);
                    // Update pagination if needed
                }
            },
            error: function(xhr, status, error) {
                console.error('Filter error:', error);
            }
        });
    }

    // Lọc khi checkbox thay đổi
    $(document).on('change', '.custom-checkbox input[type="checkbox"]', function() {
        filterProducts(1);
    });

    // Lọc theo giá
    $(document).on('click', '.apply-filter', function() {
        filterProducts(1);
    });

    // Tìm kiếm từ khóa
    $(document).on('keypress', 'input[name="search_name"]', function(e) {
        if (e.which == 13) {
            filterProducts(1);
            return false;
        }
    });

    // Sắp xếp
    $(document).on('click', '.tab-item', function() {
        $('.tab-item').removeClass('active');
        $(this).addClass('active');
        filterProducts(1);
    });

    // Xử lý phân trang
    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        var page = $(this).text();
        filterProducts(page);
    });
});