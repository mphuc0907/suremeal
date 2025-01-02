<?php
include __DIR__ . "/../includes/padding.php";
$url = get_template_directory_uri();
global $wpdb;

$idCustomer = xss(no_sql_injection($_GET['id']));
$queryCustomers = $wpdb->get_results("SELECT * FROM wp_account_dealers WHERE id = '{$idCustomer}'");

$addresses = $queryCustomers[0]->addresses;
$first_name = $queryCustomers[0]->first_name;
$last_name = $queryCustomers[0]->last_name;
$email = $queryCustomers[0]->email;
$phone_number = $queryCustomers[0]->phone_number;
$avatar = $queryCustomers[0]->avatar;
$provider = $queryCustomers[0]->provider;

$args = array(
    'post_type' => 'san_pham',
    'posts_per_page' => 10,
    'orderby' => 'post_date',
);

$query_post = new WP_Query($args);
$posts = $query_post->posts;
$arg_product = array(
    'post_type' => array('product', 'product-app', 'smart_screen'),
    'posts_per_page' => -1,
);
$query_product = new WP_Query($arg_product);
$products = $query_product->posts;

$options = '';
foreach ($products as $product) {
    $options .= '<option data-price="' . ($product->sale_price ? $product->sale_price : $product->price) . '" value="' . esc_attr($product->ID) . '">' . $product->post_title . '</option>';
}

$totalOrders = $wpdb->get_var("SELECT COUNT(*) FROM wp_orders WHERE id_dealer = '{$idCustomer}'");

$myrows = $wpdb->get_results("SELECT * FROM wp_orders WHERE id_dealer = '{$idCustomer}' ORDER BY time_order DESC");

$discountList = $wpdb->get_results("SELECT * FROM wp_discount_dealer WHERE id_dealer = '{$idCustomer}' ORDER BY id DESC");

?>

<style>
    input, select {
        width: 100%;
    }

    .d-none {
        display: none;
    }
    .order-item-product img{
        width: 100%;
    }
    .order-item-product{
        padding: 40px 45px;
        /*background: #ecf1ff;*/
        border-radius: 10px;
        margin-bottom: 20px;
    }
    .order-item-product .order-status{
        margin: 20px auto;
    }
    .order-item-product .order-status .st-main{
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 50px;
    }
    .order-item-product .order-status .st-main .st-left{
        display: flex;
        align-items: center;
        gap: 40px;
        flex-basis: 70%;
    }
    .order-item-product .order-status .st-main .st-left .status{
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .order-item-product .order-status .st-main .st-left .status span{
        display: block;
        font-size: 14px;
        line-height: 20px;
        color: #292b2e;
        margin: 0;
    }
    .order-item-product .order-status .st-main .st-left .status strong{
        display: block;
        font-size: 14px;
        line-height: 20px;
        color: #e91c24;
        font-family: K2D-Bold, sans-serif;
        margin: 0;
    }
    .order-item-product .order-status .st-main .st-right{
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        text-align: right;
        gap: 50px;
    }
    .order-item-product .order-status .st-main .st-right span{
        flex: 1;
        display: block;
        font-size: 16px;
        line-height: 24px;
        color: #292b2e;
        margin: 0;
    }
    .order-item-product .order-status .st-main .st-right strong{
        display: block;
        font-size: 20px;
        line-height: 26px;
        color: #e91c24;
        font-family: K2D-Bold, sans-serif;
    }
    .order-item-product .order-detail{
        border-bottom: 1px solid #d9d9d9;
        padding-bottom: 25px;
    }
    .order-item-product .order-detail .list-product{

    }
    .order-item-product .order-detail .list-product .morth-item{
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 50px;
        margin-bottom: 20px;
    }
    .order-item-product .order-detail .list-product .morth-item .morth-img{
        display: flex;
        align-items: center;
        gap: 50px;
        flex-basis: 70%;
    }
    .order-item-product .order-detail .list-product .morth-item .morth-img figure{
        position: relative;
        width: 125px;
        height: 125px;
        border-radius: 10px;
    }
    .order-item-product .order-detail .list-product .morth-item .morth-img figure img {
        height: 100%;
        object-fit: contain;
    }
    .order-item-product .order-detail .list-product .morth-item .morth-img .info{

    }
    .order-item-product .order-detail .list-product .morth-item .morth-img .info h4{
        font-size: 20px;
        line-height: 26px;
        font-family: var(--f-bold);
        color: #292b2e;
        margin-bottom: 10px;
    }
    .order-item-product .order-detail .list-product .morth-item .morth-img .info .type{
        display: flex;
        align-items: center;
        gap: 15px;
    }
    .order-item-product .order-detail .list-product .morth-item .morth-img .info .type span{
        display: block;
        font-size: 14px;
        line-height: 24px;
        color: rgba(22, 7, 8, 0.5019607843);
    }

    .order-item-product .order-detail .list-product .morth-item .morth-price strong{
        font-size: 20px;
        line-height: 26px;
        font-family: K2D-ExtraBold, sans-serif;
        color: #292b2e;
        margin: 0;
    }
    .order-item-product .order-detail .list-product .morth-item .morth-price{
        flex: 1;
        text-align: right;
    }
    .order-item-product .order-detail .list-info{
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 20px;
        margin-top: 30px;
        border-top: 1px solid #d9d9d9;
    }
    .order-item-product .order-detail .list-info .info__left ul li{
        display: flex;
        align-items: center;
        flex-basis: 50%;
        gap: 15px;
        margin-bottom: 10px;
    }
    .order-item-product .order-detail .list-info .info__left ul{
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        margin: 0;
        padding: 0;
        list-style: none;
    }
    .order-item-product .order-detail .list-info .info__left{
        flex-basis: 70%;
    }
    .order-item-product .order-detail .list-info .info__right ul{
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        margin: 0;
        padding: 0;
        list-style: none;
    }
    .order-item-product .order-detail .list-info .info__right ul li{
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 10px;
    }
    .order-item-product .order-detail .list-info .info__right{
        flex: 1;
        padding-left: 120px;
    }
    .order-item-product strong{
        display: block;
        font-size: 16px;
        line-height: 26px;

        color: #292b2e;
        margin: 0;
    }
    .order-item-product span{
        display: block;
        font-size: 16px;
        line-height: 26px;
        color: #292b2e;
        margin: 0;
    }
    .st-left button{
        padding: 9px 30px;
        border: none;
        font-size: 14px;
        line-height: 20px;
        color: #e91c24;
        border: 1px solid #e91c24;
        background: rgba(0, 0, 0, 0);
        border-radius: 10px;
    }
    
    .avatar {
        border-radius: 50%;
        width: 3rem;
        height: 3rem;
    }

    .form-group {
        border: 1px solid rgb(157, 157, 157);
        border-radius: 20px;
        padding: 30px 40px;
        margin-bottom: 16px;
    }

    .form-input {
        display: flex;
        justify-content: space-between;
        width: 70%;
        margin: auto;
    }

    .item-1 {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    #add-form-group {
        display: block;
        margin: auto;
        margin-top: 10px;
        border: none;
        cursor: pointer;
    }

    .remove-form-group {
        display: block;
        margin: auto;
        margin-top: 16px;
        border: none;
        cursor: pointer;
    }

    #form-add-discount {
        position: relative;
        padding-bottom: 3rem;
    }

    #submit-form {
        position: absolute;
        bottom: 0;
        left: 48%;
        right: 48%;
        padding: 6px;
        border-radius: 10px;
        cursor: pointer;
    }
    #submit-form:hover {
        background: #000;
        color: white;
    }
</style>
<style>
    .metabox-holder {
        width: 100%;
    }
    .flr{
        display: flex;
        float: right;
    }
    .d-none{
        display: none;
    }
    .divgif {
        position: fixed;
        width: 100%;
        height: 100%;
        z-index: 1100;
        display: none;
        background: #dedede;
        opacity: 0.5;
        top: 0;
        left: 0;
    }
    .iconloadgif {
        top: 0;
        right: 0;
        left: 0;
        bottom: 0;
        position: absolute;
        margin: auto;
        width: 150px;
        height: 150px;
    }
</style>
<div class="divgif">
    <img class="iconloadgif" src="<?php echo get_template_directory_uri(); ?>/ajax/images/loading2.gif" alt="">
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<div class="wrap">
    <h1>
        Info Customer
    </h1>
    <div id="poststuff">
        <div class="metabox-holder columns-2" id="post-body">
                        <!---left-->
                        <div id="post-body-content" class="pos1">
                <div class="postbox">
                    <div class="inside">
                        <div class="order-table">
                            <div class="order-item-product">
                                <div class="order-detail">

                                    <div class="list-product">
                                            <div class="morth-item">
                                                <div class="morth-img">
                                                    <figure>
                                                        <img class="avatar" src="<?= $avatar ? $avatar : $url . '/assets/image/dashboard/avatar-80.svg' ?>" alt="">
                                                    </figure>
                                                    <div class="info">
                                                        <h4><?= $first_name ?> <?= $last_name ?></h4>

                                                        <div class="type">
                                                            <span>Type: <?= $provider ? $provider : 'nomal' ?></span>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                    </div>

                                    <div class="list-info">
                                        <div class="info__left">
                                            <ul>

                                                <li>
                                                    <span>First name:</span>
                                                    <strong><?= $first_name ?></strong>
                                                </li>
                                                <li>
                                                    <span>Last name:</span>
                                                    <strong><?= $last_name ?></strong>
                                                </li>

                                                <li>
                                                    <span>Email:</span>
                                                    <strong><?= $email ?></strong>
                                                </li>
                                                <li>
                                                    <span>Phone number:</span>
                                                    <strong><?= $phone_number ?></strong>
                                                </li>
                                                <li>
                                                    <span>Total Order:</span>
                                                    <strong><?= $totalOrders ?></strong>
                                                </li>
                                                    <?php
                                                    $stored_addresses = json_decode($addresses, true) ?: [];

                                                    foreach ($stored_addresses as $index => $address) :
                                                    ?>
                                                        <li>
                                                            <span>Address(<?= $index + 1 ?>):</span>
                                                            <strong><?= htmlspecialchars($address) ?></strong>
                                                        </li>
                                                    <?php endforeach ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="post-body-content" class="pos1">
                <h1>
                    List Order
                </h1>
            <table class="wp-list-table widefat fixed striped posts">
                <thead>
                    <tr class="headline">
                        <th style="width:30px;text-align:center;">No.</th>
                        <th>Order Code</th>
                        <th>Discount Code</th>
                        <th>Order Date</th>
                        <th>Payment Method</th>
                        <th>Total Amount</th>
                    </tr>
                </thead>

                <?php
                $i = 0;
                foreach ($myrows as $order) {
                    $delivery_information = json_decode($order->delivery_information);
                    $i++;
                    $rowlink = 'admin.php?page=orders_manager&sub=edit&id=' . $order->id;
                    $rowlinkvoucher = 'admin.php?page=discount_price&sub=voucher_info&code=' . $order->code_voucher;
                    ?>
                    <tr>
                        <td><?= $i ?></td>
                        <td><a href="<?php echo $rowlink; ?>" target="_blank"><?= $order->order_code ?></a></td>
                        <td><a href="<?php echo $rowlinkvoucher ?>"><?= $order->code_voucher ?></a></td>
                        <td><?= date('H:i d/m/Y', $order->time_order) ?></td>
                        <td>    Stripe
                        <td><?= formatBalance($order->price_payment) ?> </td>
                    
                        <td><a href="<?php echo $rowlink; ?>" target="_blank">View detail</a></td>
                    </tr>
                <?php } ?>
            </table>
            </div>
            <div id="post-body-content" class="pos1">
                <h1>
                    List Discount Product
                </h1>
                <table class="wp-list-table widefat fixed striped posts">
                    <thead>
                    <tr class="headline">
                        <th style="width:30px;text-align:center;">No.</th>
                        <th>Product</th>
                        <th>Type discount</th>
                        <th>Discount</th>
                    </tr>
                    </thead>

                    <?php
                    $i = 0;
                    foreach ($discountList as $item) {
                        $i++;
                        $post = get_post($item->product); 
                        $postTitle = $post->post_title;
                        ?>
                        <tr>
                            <td><?= $i ?></td>
                            <td><?= $postTitle ?></td>
                            <td>
                                <?php if($item->discount_type == '0'): ?>
                                    Amount
                                <?php elseif($item->discount_type == '1'): ?>
                                    Percentage
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($item->discount_type == '0'): ?>
                                    <?= $item->discount_amount ?>
                                <?php elseif($item->discount_type == '1'): ?>
                                    <?= intval($item->discount_amount) ?> %
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
            <div id="post-body-content" class="pos1">
                <h1>
                    Add Discount Product
                </h1>
                <form action="" id="form-add-discount">
                    <button id="submit-form">Submit</button>
                    <?php foreach ($discountList as $item): 
                        $post = get_post($item->product);
                    ?>
                        <div class="form-group">
                            <div class="form-input top-up">
                                <div class="item-1">
                                    <label for="">Choose product:</label>
                                    <select name="product[]" id="">
                                        <option value="">Choose product</option>
                                        <?php
                                        foreach ($products as $product) :
                                            $selected = ($product->ID == $item->product) ? 'selected' : '';
                                        ?>
                                            <option data-price="<?= $product->sale_price ? $product->sale_price : $product->price ?>" value="<?= esc_attr($product->ID) ?>" <?= $selected ?>><?= $product->post_title ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="item-1">
                                    <label for="">Price:</label>
                                    <input type="text" class="price-input" value="" disabled>
                                </div>
                                <div class="item-1">
                                    <label for="">Discount type:</label>
                                    <select name="discount_type[]" id="">
                                        <option value="0" <?php echo ($item->discount_type == '0') ? 'selected' : ''; ?>>Amount</option>
                                        <option value="1" <?php echo ($item->discount_type == '1') ? 'selected' : ''; ?>>Percent</option>
                                    </select>
                                </div>
                                <div class="item-1">
                                    <label for="">Discount:</label>
                                    <input type="text" name="discount_amount[]" value="<?php echo $item->discount_amount; ?>">
                                </div>
                            </div>
                            <button class="remove-form-group">
                                <svg fill="#000000" height="20px" width="20px" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" 
                                    viewBox="0 0 310.285 310.285" xml:space="preserve">
                                <path d="M155.143,0.001C69.597,0.001,0,69.597,0,155.143c0,85.545,69.597,155.142,155.143,155.142s155.143-69.597,155.143-155.142
                                    C310.285,69.597,240.689,0.001,155.143,0.001z M244.143,171.498c0,4.411-3.589,8-8,8h-163c-4.411,0-8-3.589-8-8v-32
                                    c0-4.411,3.589-8,8-8h163c4.411,0,8,3.589,8,8V171.498z"/>
                                </svg>
                            </button>
                        </div>
                    <?php endforeach; ?>
                </form>
                <button id="add-form-group">
                    <svg width="24px" height="24px" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><title>70 Basic icons by Xicons.co</title><path d="M24,3A21,21,0,1,0,45,24,21,21,0,0,0,24,3Z" fill="#000000"></path><path d="M32,26H16a2,2,0,0,1,0-4H32A2,2,0,0,1,32,26Z" fill="#ffffff"></path><path d="M24,34a2,2,0,0,1-2-2V16a2,2,0,0,1,4,0V32A2,2,0,0,1,24,34Z" fill="#ffffff"></path></g></svg>
                </button>
            </div>
            
        </div>
    </div>
</div>

<?php
add_admin_js('jquery.min.js');
add_admin_js('jquery.validate.min.js');
?>
<script>
// Hàm cập nhật giá cho một form input cụ thể
function updatePrice(selectElement) {
    // Tìm div cha .form-input chứa select này
    const formInputDiv = selectElement.closest('.form-input');
    
    // Tìm input price trong cùng div cha
    const priceInput = formInputDiv.querySelector('.price-input');
    
    // Lấy option được chọn
    const selectedOption = selectElement.options[selectElement.selectedIndex];
    
    // Nếu có sản phẩm được chọn thì lấy giá, không thì để trống
    if(selectedOption.value) {
        const price = selectedOption.getAttribute('data-price');
        // Format
        priceInput.value = new Intl.NumberFormat('en-US', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(price);
    } else {
        priceInput.value = '';
    }
}

// Thêm event listener cho tất cả các select có sẵn
document.querySelectorAll('.form-input select[name="product[]"]').forEach(select => {
    // Thêm event change
    select.addEventListener('change', function() {
        updatePrice(this);
    });
    
    // Chạy lần đầu để hiển thị giá cho các sản phẩm đã được chọn
    if(select.value) {
        updatePrice(select);
    }
});

// Hàm để disable các sản phẩm đã được chọn
function updateProductSelections() {
    // Lấy tất cả các select product
    const productSelects = document.querySelectorAll('select[name="product[]"]');
    
    // Lấy danh sách các sản phẩm đã được chọn
    const selectedProducts = Array.from(productSelects).map(select => select.value).filter(value => value !== '');
    
    // Duyệt qua từng select
    productSelects.forEach(select => {
        // Lưu lại giá trị đang được chọn của select hiện tại
        const currentValue = select.value;
        
        // Duyệt qua từng option trong select
        Array.from(select.options).forEach(option => {
            // Nếu option có giá trị (không phải option mặc định)
            if (option.value) {
                // Disable option nếu nó đã được chọn ở select khác
                option.disabled = selectedProducts.includes(option.value) && option.value !== currentValue;
            }
        });
    });
}

// Hàm validate discount amount
function validateDiscount(formGroup) {
    const priceInput = formGroup.querySelector('.price-input');
    const discountTypeSelect = formGroup.querySelector('select[name="discount_type[]"]');
    const discountAmountInput = formGroup.querySelector('input[name="discount_amount[]"]');
    
    const price = parseFloat(priceInput.value.replace(/,/g, '')); // Remove commas from price
    const discountType = discountTypeSelect.value;
    const discountAmount = parseFloat(discountAmountInput.value);
    
    if (isNaN(discountAmount)) {
        return {
            isValid: false,
            message: 'Please enter a valid number for discount'
        };
    }

    if (discountType === '0') { // Amount
        if (discountAmount <= 0) {
            return {
                isValid: false,
                message: 'Discount amount must be greater than 0'
            };
        }
        if (discountAmount >= price) {
            return {
                isValid: false,
                message: 'Discount amount cannot be greater than or equal to price'
            };
        }
    } else { // Percent
        if (discountAmount <= 0 || discountAmount > 100) {
            return {
                isValid: false,
                message: 'Discount percentage must be between 0 and 100'
            };
        }
    }

    return {
        isValid: true
    };
}

// Thêm event listener cho discount type change
function addDiscountTypeListener(formGroup) {
    const discountTypeSelect = formGroup.querySelector('select[name="discount_type[]"]');
    const discountAmountInput = formGroup.querySelector('input[name="discount_amount[]"]');
    
    discountTypeSelect.addEventListener('change', function() {
        if (discountAmountInput.value) {
            const validation = validateDiscount(formGroup);
            if (!validation.isValid) {
                discountAmountInput.value = '';
                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid Discount',
                    text: validation.message
                });
            }
        }
    });

    // Validate khi nhập discount amount
    discountAmountInput.addEventListener('input', function() {
        const validation = validateDiscount(formGroup);
        if (!validation.isValid) {
            Swal.fire({
                icon: 'warning',
                title: 'Invalid Discount',
                text: validation.message
            });
            this.value = '';
        }
    });
}

// Thêm validation cho các form group có sẵn
document.querySelectorAll('.form-group').forEach(formGroup => {
    addDiscountTypeListener(formGroup);
});

// Cập nhật hàm thêm form group mới
document.getElementById('add-form-group').addEventListener('click', function(e) {
    e.preventDefault();
    const form = document.querySelector('#form-add-discount');
    const newFormGroup = document.createElement('div');
    newFormGroup.className = 'form-group';

    // HTML content remains the same
    newFormGroup.innerHTML = `
        <div class="form-input top-up">
            <div class="item-1">
                <label for="">Choose product:</label>
                <select name="product[]" id="">
                    <option value="">Choose product</option>
                    <?= $options ?>
                </select>
            </div>
            <div class="item-1">
                <label for="">Price:</label>
                <input type="text" class="price-input" value="" disabled>
            </div>
            <div class="item-1">
                <label for="">Discount type:</label>
                <select name="discount_type[]" id="">
                    <option value="0">Amount</option>
                    <option value="1">Percent</option>
                </select>
            </div>
            <div class="item-1">
                <label for="">Discount:</label>
                <input type="text" name="discount_amount[]" value="">
            </div>
        </div>
        <button class="remove-form-group">
            <svg fill="#000000" height="20px" width="20px" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" 
                viewBox="0 0 310.285 310.285" xml:space="preserve">
            <path d="M155.143,0.001C69.597,0.001,0,69.597,0,155.143c0,85.545,69.597,155.142,155.143,155.142s155.143-69.597,155.143-155.142
                C310.285,69.597,240.689,0.001,155.143,0.001z M244.143,171.498c0,4.411-3.589,8-8,8h-163c-4.411,0-8-3.589-8-8v-32
                c0-4.411,3.589-8,8-8h163c4.411,0,8,3.589,8,8V171.498z"/>
            </svg>
        </button>
    `;

    form.appendChild(newFormGroup);

    // Add event listeners
    const removeButton = newFormGroup.querySelector('.remove-form-group');
    removeButton.addEventListener('click', function (e) {
        e.preventDefault();
        newFormGroup.remove();
        updateProductSelections();
    });
    
    const newSelect = newFormGroup.querySelector('select[name="product[]"]');
    newSelect.addEventListener('change', function() {
        updatePrice(this);
        updateProductSelections();
    });
    
    // Add discount validation listeners
    addDiscountTypeListener(newFormGroup);
    
    updateProductSelections();
});

// Cập nhật hàm submit form để sử dụng validation
document.getElementById('submit-form').addEventListener('click', async function(e) {
    e.preventDefault();
    
    const formGroups = document.querySelectorAll('.form-group');
    let isValid = true;
    let formData = [];
    
    for(let i = 0; i < formGroups.length; i++) {
        const formGroup = formGroups[i];
        const product = formGroup.querySelector('select[name="product[]"]').value;
        const discountType = formGroup.querySelector('select[name="discount_type[]"]').value;
        const discountAmount = formGroup.querySelector('input[name="discount_amount[]"]').value;
        
        if(!product || !discountAmount) {
            isValid = false;
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Please fill in all fields'
            });
            break;
        }
        
        const validation = validateDiscount(formGroup);
        if (!validation.isValid) {
            isValid = false;
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: validation.message
            });
            break;
        }
        
        formData.push({
            product: product,
            discount_type: discountType,
            discount_amount: discountAmount,
            id_dealer: '<?php echo $idCustomer; ?>'
        });
    }
    
    if(!isValid) return;
    
    try {
        const response = await fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                action: 'save_dealer_discount',
                data: JSON.stringify(formData)
            })
        });
        
        const data = await response.json();
        
        if(data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'Discounts saved successfully'
            }).then(() => {
                window.location.reload();
            });
        } else {
            throw new Error(data.message || 'Error saving discounts');
        }
    } catch(error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.message || 'Error saving discounts'
        });
    }
});

// Thêm event listener cho các select product ban đầu
document.querySelectorAll('select[name="product[]"]').forEach(select => {
    select.addEventListener('change', updateProductSelections);
});

// Thêm event listener cho các nút remove
document.querySelectorAll('.remove-form-group').forEach(button => {
    button.addEventListener('click', function(e) {
        e.preventDefault();
        this.closest('.form-group').remove();
        updateProductSelections();
    });
});

// Chạy lần đầu để set up initial state
updateProductSelections();
</script>