<?php /* Template Name: Order-Info */ ?>
<?php
get_header();
// Kiểm tra đăng nhập
$authenticated_user = validate_user_token();
if (!isset($_COOKIE['user_token']) || !$authenticated_user) {
    wp_redirect(home_url());
    exit;
}
$searchOrder = $_GET['searchOrder'];

global $wpdb;
// Lấy thông tin người dùng
$user_email = $authenticated_user->email;
$first_name = $authenticated_user->first_name;
$last_name = $authenticated_user->last_name;
$avatar = $authenticated_user->avatar;
$addresses = json_decode($authenticated_user->addresses, true) ?: [];
$provider = $authenticated_user->provider;
$id = $authenticated_user->ID;
// Lấy thông tin người dùng từ session
$addresses = json_decode($_SESSION['addresses'], true);

$url = get_template_directory_uri();

if (!empty($searchOrder)) {
    $myrows = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM wp_orders WHERE id_user = %d AND order_code LIKE %s ORDER BY id DESC",
            intval($id),
            '%' . $wpdb->esc_like($searchOrder) . '%'
        )
    );


}else {
    $myrows = $wpdb->get_results("SELECT * FROM wp_orders WHERE id_user =" . $id . " ORDER BY id DESC");

}

$countmyrows = count($myrows);

// Đếm số lượng với điều kiện status_transport = 1
$order_placed = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM wp_orders WHERE id_user = %d AND status = %d",
        $id,
        1
    )
);
$countOrder = count($order_placed);

// Đếm số lượng với điều kiện status_transport = 2
$packaging = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM wp_orders WHERE id_user = %d AND status = %d",
        $id,
        3
    )
);
$countpackaging = count($packaging);

// Đếm số lượng với điều kiện status_transport = 3
$on_the_road = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM wp_orders WHERE id_user = %d AND status = %d",
        $id,
        2
    )
);
$countroad = count($on_the_road);

// Đếm số lượng với điều kiện status_transport = 4
$delivered = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM wp_orders WHERE id_user = %d AND status = %d",
        $id,
        4
    )
);
$countdelivered = count($delivered);
// Hiển thị kết quả

$url = get_template_directory_uri();
get_header();

?>
<main class="bg-[#EEF0F6]">
    <section class="py-6">
        <div class="container">
            <nav class="breadcrumb text-body-md-medium text-gray-8" aria-label="breadcrumb">
                <!-- Sử dụng schema.org để định nghĩa breadcrumb list -->
                <ol class="flex flex-wrap gap-3 items-center" itemscope itemtype="https://schema.org/BreadcrumbList">
                    <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                        <a href="/" class="text-secondary hover:text-primary" itemprop="item">
                            <span itemprop="name"><?php pll_e('Home') ?></span>
                        </a>
                        <meta itemprop="position" content="1" />
                    </li>
                    <span>/</span>
                    <!-- <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                                <a href="#" class="text-secondary hover:text-primary" itemprop="item">
                                    <span itemprop="name">Blog</span>
                                </a>
                                <meta itemprop="position" content="1" />
                            </li>
                            <span>/</span> -->
                    <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" aria-current="page">
                        <span itemprop="name"><?php pll_e('Personal') ?></span>
                        <meta itemprop="position" content="4" />
                    </li>
                </ol>
            </nav>
        </div>
    </section>

    <section class="pb-20">
        <div class="container">
            <div class="flex flex-col lg:flex-row gap-6">
                <div class="w-full lg:max-w-[322px] flex flex-col gap-5">
                    <!-- profile card -->
                    <div  class="h-[200px] bg-secondary rounded-xl flex flex-col items-center justify-center gap-4">
<!--                        <figure class="w-20 h-20 rounded-full overflow-hidden">-->
<!--                            <img src="--><?//= $avatar ? $avatar : $url . '/assets/image/dashboard/avatar-80.svg' ?><!--" alt="avatar">-->
<!--                        </figure>-->
                        <div class="flex flex-col items-center justify-center">
                            <h2 class="text-body-md-semibold text-white"><?= $first_name ?> <?= $last_name ?></h2>
                            <p class="text-body-sm-regular text-white"><?= $user_email ?>
                            </p>
                        </div>
                    </div>

                    <!-- nav-active -->
                    <div  class="dashboard-container">
                        <a href="<?= home_url() ?>/person-info" class="dashboard-item">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M8.25 9C8.25 6.92893 9.92893 5.25 12 5.25C14.0711 5.25 15.75 6.92893 15.75 9C15.75 11.0711 14.0711 12.75 12 12.75C9.92893 12.75 8.25 11.0711 8.25 9ZM12 6.75C10.7574 6.75 9.75 7.75736 9.75 9C9.75 10.2426 10.7574 11.25 12 11.25C13.2426 11.25 14.25 10.2426 14.25 9C14.25 7.75736 13.2426 6.75 12 6.75Z"
                                    fill="#0E74BC" />
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M1.25 12C1.25 6.06294 6.06294 1.25 12 1.25C17.9371 1.25 22.75 6.06294 22.75 12C22.75 17.9371 17.9371 22.75 12 22.75C6.06294 22.75 1.25 17.9371 1.25 12ZM12 2.75C6.89137 2.75 2.75 6.89137 2.75 12C2.75 14.5456 3.77827 16.851 5.4421 18.5235C5.6225 17.5504 5.97694 16.6329 6.68837 15.8951C7.75252 14.7915 9.45416 14.25 12 14.25C14.5457 14.25 16.2474 14.7915 17.3115 15.8951C18.023 16.6329 18.3774 17.5505 18.5578 18.5236C20.2217 16.8511 21.25 14.5456 21.25 12C21.25 6.89137 17.1086 2.75 12 2.75ZM17.1937 19.6554C17.0918 18.4435 16.8286 17.5553 16.2318 16.9363C15.5823 16.2628 14.3789 15.75 12 15.75C9.62099 15.75 8.41761 16.2628 7.76815 16.9363C7.17127 17.5553 6.90811 18.4434 6.80622 19.6553C8.28684 20.6618 10.0747 21.25 12 21.25C13.9252 21.25 15.7131 20.6618 17.1937 19.6554Z"
                                    fill="#0E74BC" />
                            </svg>
                            <p class="text-body-md-medium"><?php pll_e('Personal information') ?></p>
                        </a>
                        <a href="<?= home_url() ?>/order-info" class="dashboard-item active">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <path
                                    d="M8.75039 13C8.75039 12.5858 8.4146 12.25 8.00039 12.25C7.58618 12.25 7.25039 12.5858 7.25039 13V17C7.25039 17.4142 7.58618 17.75 8.00039 17.75C8.4146 17.75 8.75039 17.4142 8.75039 17V13Z"
                                    fill="#373A51" />
                                <path
                                    d="M16.0004 12.25C16.4146 12.25 16.7504 12.5858 16.7504 13V17C16.7504 17.4142 16.4146 17.75 16.0004 17.75C15.5862 17.75 15.2504 17.4142 15.2504 17V13C15.2504 12.5858 15.5862 12.25 16.0004 12.25Z"
                                    fill="#373A51" />
                                <path
                                    d="M12.7504 13C12.7504 12.5858 12.4146 12.25 12.0004 12.25C11.5862 12.25 11.2504 12.5858 11.2504 13V17C11.2504 17.4142 11.5862 17.75 12.0004 17.75C12.4146 17.75 12.7504 17.4142 12.7504 17V13Z"
                                    fill="#373A51" />
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M17.2742 3.47298C16.7986 3.28712 16.2659 3.25574 15.5823 3.25077C15.3016 2.65912 14.6988 2.25 14.0004 2.25H10.0004C9.30199 2.25 8.69914 2.65912 8.41843 3.25077C7.73489 3.25574 7.20215 3.28712 6.7266 3.47298C6.15841 3.69505 5.6642 4.07255 5.30048 4.5623C4.93355 5.05639 4.76131 5.68968 4.52423 6.56133L3.89636 8.86426C3.50885 9.06269 3.16977 9.32992 2.88691 9.6922C2.26491 10.4888 2.15476 11.4377 2.26541 12.5261C2.37278 13.5822 2.70527 14.9121 3.12149 16.5769L3.14789 16.6825C3.41107 17.7353 3.62475 18.5901 3.87889 19.2572C4.14386 19.9527 4.48026 20.5227 5.03488 20.9558C5.5895 21.3888 6.22414 21.5769 6.96315 21.6653C7.67197 21.75 8.55305 21.75 9.63823 21.75H14.3625C15.4476 21.75 16.3287 21.75 17.0375 21.6653C17.7766 21.5769 18.4112 21.3888 18.9658 20.9558C19.5204 20.5227 19.8568 19.9527 20.1218 19.2572C20.3759 18.5901 20.5896 17.7353 20.8528 16.6825L20.8792 16.577C21.2954 14.9122 21.6279 13.5822 21.7353 12.5261C21.8459 11.4377 21.7358 10.4888 21.1138 9.6922C20.8309 9.32995 20.4919 9.06274 20.1044 8.86431L19.4765 6.56133C19.2395 5.68968 19.0672 5.05639 18.7003 4.5623C18.3366 4.07255 17.8424 3.69505 17.2742 3.47298ZM7.27263 4.87007C7.49243 4.78416 7.75801 4.75888 8.41984 4.75219C8.70115 5.34225 9.30315 5.75 10.0004 5.75H14.0004C14.6976 5.75 15.2996 5.34225 15.5809 4.75219C16.2428 4.75888 16.5084 4.78416 16.7282 4.87007C17.0341 4.98964 17.3002 5.19291 17.4961 5.45663C17.6722 5.69377 17.7755 6.02508 18.0664 7.09194L18.42 8.3887C17.3822 8.24996 16.0424 8.24998 14.3777 8.25H9.62295C7.95837 8.24998 6.61858 8.24996 5.58079 8.38868L5.93437 7.09195C6.22527 6.02508 6.32861 5.69376 6.50472 5.45662C6.70057 5.19291 6.96668 4.98964 7.27263 4.87007ZM10.0004 3.75C9.86232 3.75 9.75039 3.86193 9.75039 4C9.75039 4.13807 9.86232 4.25 10.0004 4.25H14.0004C14.1385 4.25 14.2504 4.13807 14.2504 4C14.2504 3.86193 14.1385 3.75 14.0004 3.75H10.0004ZM4.06921 10.6153C4.34805 10.2582 4.78902 10.0183 5.70019 9.88649C6.63083 9.75187 7.89266 9.75 9.68501 9.75H14.3157C16.108 9.75 17.3699 9.75187 18.3005 9.88649C19.2117 10.0183 19.6526 10.2582 19.9315 10.6153C20.2103 10.9725 20.3361 11.4584 20.243 12.3744C20.1479 13.3099 19.8436 14.5345 19.4089 16.2733C19.1317 17.3824 18.9386 18.1496 18.7201 18.7231C18.5088 19.2778 18.3019 19.5711 18.0427 19.7735C17.7835 19.9758 17.4488 20.1054 16.8594 20.1759C16.2501 20.2488 15.4589 20.25 14.3157 20.25H9.68501C8.54182 20.25 7.75064 20.2488 7.14125 20.1759C6.55188 20.1054 6.21716 19.9758 5.958 19.7735C5.69884 19.5711 5.49193 19.2778 5.28061 18.7231C5.06212 18.1496 4.86902 17.3824 4.59176 16.2733C4.15705 14.5345 3.85282 13.3099 3.75772 12.3744C3.6646 11.4584 3.79038 10.9725 4.06921 10.6153Z"
                                    fill="#373A51" />
                            </svg>
                            <p class="text-body-md-medium"><?php pll_e('Order infomation') ?></p>
                        </a>
                        <form class="dashboard-item" id="logoutForm" action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" method="POST">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <path
                                    d="M18.5303 12.5303C18.8232 12.2374 18.8232 11.7626 18.5303 11.4697L16.5303 9.46967C16.2374 9.17678 15.7626 9.17678 15.4697 9.46967C15.1768 9.76256 15.1768 10.2374 15.4697 10.5303L16.1893 11.25L9 11.25C8.58578 11.25 8.25 11.5858 8.25 12C8.25 12.4142 8.58578 12.75 9 12.75L16.1893 12.75L15.4697 13.4697C15.1768 13.7626 15.1768 14.2374 15.4697 14.5303C15.7626 14.8232 16.2374 14.8232 16.5303 14.5303L18.5303 12.5303Z"
                                    fill="#373A51" />
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M10.0547 1.25H8.94491C7.57732 1.24998 6.475 1.24996 5.60803 1.36652C4.70792 1.48754 3.95005 1.74643 3.34813 2.34835C2.74622 2.95027 2.48732 3.70814 2.36631 4.60825C2.24974 5.47522 2.24976 6.57754 2.24978 7.94513V16.0549C2.24976 17.4225 2.24974 18.5248 2.36631 19.3918C2.48732 20.2919 2.74622 21.0497 3.34813 21.6517C3.95005 22.2536 4.70792 22.5125 5.60803 22.6335C6.475 22.75 7.57732 22.75 8.94491 22.75H10.0547C11.4222 22.75 12.5246 22.75 13.3915 22.6335C14.2916 22.5125 15.0495 22.2536 15.6514 21.6517C16.0504 21.2527 16.2993 20.7844 16.457 20.2498C17.4083 20.2486 18.2009 20.2381 18.8431 20.1518C19.6071 20.0491 20.2694 19.8268 20.7981 19.2981C21.3268 18.7694 21.5491 18.1071 21.6518 17.3431C21.75 16.6123 21.75 15.6865 21.75 14.5537V9.44631C21.75 8.31349 21.75 7.38774 21.6518 6.65689C21.5491 5.89294 21.3268 5.2306 20.7981 4.7019C20.2694 4.17321 19.6071 3.95093 18.8431 3.84822C18.2009 3.76188 17.4083 3.75142 16.457 3.75017C16.2993 3.21562 16.0504 2.74729 15.6514 2.34835C15.0495 1.74643 14.2916 1.48754 13.3915 1.36652C12.5246 1.24996 11.4222 1.24998 10.0547 1.25ZM16.748 17.0042C16.7444 17.6487 16.7338 18.2293 16.6972 18.7491C17.5316 18.7459 18.152 18.7312 18.6432 18.6652C19.2409 18.5848 19.5339 18.441 19.7374 18.2374C19.941 18.0339 20.0848 17.7409 20.1652 17.1432C20.2484 16.5241 20.25 15.6997 20.25 14.5V9.5C20.25 8.30029 20.2484 7.47595 20.1652 6.85676C20.0848 6.25914 19.941 5.9661 19.7374 5.76256C19.5339 5.55902 19.2409 5.41519 18.6432 5.33484C18.152 5.2688 17.5316 5.25415 16.6972 5.25091C16.7338 5.77073 16.7444 6.35129 16.748 6.99583C16.7503 7.41003 16.4164 7.74768 16.0022 7.74999C15.588 7.7523 15.2504 7.41838 15.2481 7.00418C15.242 5.91068 15.2136 5.1356 15.1055 4.54735C15.0014 3.98054 14.8342 3.65246 14.5908 3.40901C14.314 3.13225 13.9254 2.9518 13.1917 2.85315C12.4363 2.75159 11.4352 2.75 9.99978 2.75H8.99978C7.56437 2.75 6.56325 2.75159 5.8079 2.85315C5.07413 2.9518 4.68555 3.13225 4.40879 3.40901C4.13203 3.68577 3.95158 4.07435 3.85293 4.80812C3.75138 5.56347 3.74978 6.56459 3.74978 8V16C3.74978 17.4354 3.75138 18.4365 3.85293 19.1919C3.95158 19.9257 4.13203 20.3142 4.40879 20.591C4.68555 20.8678 5.07413 21.0482 5.8079 21.1469C6.56325 21.2484 7.56437 21.25 8.99978 21.25H9.99978C11.4352 21.25 12.4363 21.2484 13.1917 21.1469C13.9254 21.0482 14.314 20.8678 14.5908 20.591C14.8342 20.3475 15.0014 20.0195 15.1055 19.4527C15.2136 18.8644 15.242 18.0893 15.2481 16.9958C15.2504 16.5816 15.588 16.2477 16.0022 16.25C16.4164 16.2523 16.7503 16.59 16.748 17.0042Z"
                                    fill="#373A51" />
                            </svg>
                            <input type="hidden" name="action" value="logout">
                            <button type="submit" class="text-body-md-medium logout"><?php pll_e('Log out') ?></button>
                        </form>
                    </div>
                </div>
                <div  class="flex-1 w-full lg:max-w-[75%]">
                    <div class="flex flex-col lg:flex-row gap-6 items-center justify-between">
                        <h2 class="text-heading-h5 text-gray-8"><?php pll_e('My orders') ?></h2>
                        <form action="" id="searchOrder">
                        <input type="text" name="searchOrder" value="<?= $searchOrder ?>" class="home-search radius-8 xl:max-w-[200px] 2xl:max-w-[386px]"
                            placeholder="<?php pll_e('Search by order code') ?>">
                        </form>
                    </div>

                    <div class="mt-4 rounded-t-xl bg-white flex pt-2 items-center">
                        <div class="max-w-full overflow-x-auto custom-scrollbar">
                            <div class="grid grid-cols-[repeat(4,minmax(253px,1fr))]">
                                <div class="order-tab-item active" data-tab-id="all">
                                    <p class="text-body-md-regular"><?php pll_e('All') ?> (<?= $countmyrows ?>)</p>
                                </div>
                                <div class="order-tab-item" data-tab-id="processing">
                                    <p class="text-body-md-regular"><?php pll_e('Processing') ?> (<?= $countOrder ?>)</p>
                                </div>
                                <!-- <div class="order-tab-item" data-tab-id="in-progress">
                                    <p class="text-body-md-regular"><?php pll_e('In progress') ?> (<?= $countpackaging ?>)</p>
                                </div> -->
                                <div class="order-tab-item" data-tab-id="completed">
                                    <p class="text-body-md-regular"><?php pll_e('Completed') ?> (<?= $countroad ?>)</p>
                                </div>
                                <div class="order-tab-item" data-tab-id="canceled">
                                    <p class="text-body-md-regular"><?php pll_e('Canceled') ?> (<?= $countdelivered ?>)</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 flex flex-col gap-6 order-tab" data-tab-id="all">
                        <?php foreach ($myrows as $key => $value) :

                            $get_newest_order = $wpdb->get_results("SELECT * FROM wp_orders WHERE order_code = '{$value->order_code}' ORDER BY id DESC");
                            $dataProduct = $get_newest_order[0]->dataproduct;
                            $decodedData = json_decode(str_replace('\\', "", $dataProduct), true);
                            $countProduct = count($decodedData);

                            $idPro = $decodedData[0]['id'];
                            $price = get_field('price', $idPro);
                            $sale_price = get_field('sale_price', $idPro);

                            $status = $value->status;
                            $class = "";
                            $name = "";
                            if ($status == 1) {
                                $class = 'warning';
                                $name = 'Processing';
                            } elseif ($status == 2) {
                                $class = 'success';
                                $name = 'Completed';
                            } elseif ($status == 4) {
                                $class = 'error';
                                $name = 'Canceled';
                            } else {
                                $class = 'warning';
                                $name = 'In progress';
                            }
                        ?>
                            <div class="w-full flex flex-col items-center justify-center bg-white rounded-xl">
                                <div
                                    class="w-full flex flex-wrap items-center justify-between gap-2 px-8 py-6 border-b border-neutral-200 border-solid">
                                    <div class="flex flex-wrap items-center gap-4">
                                        <p class="text-body-md-medium text-gray-8"><?= date('m/d/Y', $value->time_order) ?></p>
                                        <div class="w-1 h-1 rounded-full bg-[#D9D9D9]"></div>
                                        <div class="flex items-center gap-2">
                                            <p class="text-body-md-medium text-gray-8"><?php pll_e('Order code') ?>:</p>
                                            <p class="text-body-md-medium text-neutral-500"><?= $value->order_code ?></p>
                                            <input type="text" value="<?= $value->order_code ?>" id="myInput_all<?= $value->order_code ?>" style="display: none">
                                            <button class="button button-trans p-0"  onclick="myFunction('myInput_all<?= $value->order_code ?>')">
                                                <figure class="w-5 h-5"><img src="<?= $url ?>/assets/image/icon/duplicate.svg"
                                                                             alt="icon">
                                                </figure>
                                            </button>
                                        </div>
                                        <div class="w-1 h-1 rounded-full bg-[#D9D9D9]"></div>
                                        <p class="text-body-md-medium text-neutral-500" ><?php pll_e('Deliver') ?></p>
                                        <div class="w-1 h-1 rounded-full bg-[#D9D9D9]"></div>
                                        <p class="text-body-md-medium text-neutral-500"><?= $countProduct ?> <?php pll_e('products') ?></p>
                                        <div class="w-1 h-1 rounded-full bg-[#D9D9D9]"></div>
                                        <a href="/pdf/?order_code=<?= $value->order_code ?>" target="_blank" class="flex items-center gap-2">
                                            <div class="text-body-md-medium text-neutral-500">Receipt</div>
                                            <button class="button bg-trans">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="20"
                                                     viewBox="0 0 16 20" fill="none">
                                                    <path
                                                            d="M9.75 1.24258V4.96007C9.75 5.46411 9.75 5.71614 9.84537 5.90866C9.92926 6.078 10.0631 6.21568 10.2278 6.30197C10.4149 6.40006 10.66 6.40006 11.15 6.40006H14.7642M11.5 10.9H4.5M11.5 14.5H4.5M6.25 7.3H4.5M9.75 1H5.2C3.72986 1 2.99479 1 2.43327 1.29428C1.93935 1.55314 1.53778 1.96619 1.28611 2.47423C1 3.05179 1 3.80786 1 5.32V14.68C1 16.1921 1 16.9482 1.28611 17.5258C1.53778 18.0338 1.93935 18.4469 2.43327 18.7057C2.99479 19 3.72986 19 5.2 19H10.8C12.2701 19 13.0052 19 13.5667 18.7057C14.0607 18.4469 14.4622 18.0338 14.7139 17.5258C15 16.9482 15 16.1921 15 14.68V6.4L9.75 1Z"
                                                            stroke="#475467" stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                </svg>
                                            </button>
                                        </a>
                                    </div>

                                    <div class="badge <?= $class ?>">
                                        <?= $name ?>
                                    </div>
                                </div>
                                
                                <div class="w-full py-6 px-8 flex flex-col gap-3 border-b border-solid border-neutral-200">
                                    <!-- product -->
                                    <?php foreach($decodedData as $key => $item): 
                                        $idPro = $item['id'];
                                    ?>
                                    <div class="w-full flex flex-wrap lg:flex-nowrap justify-between items-center">
                                        <div class="flex lg:flex-0 w-full md:w-2/3 max-w-[653px] items-center gap-5">
                                            <figure
                                                class="w-[60px] h-[60px] md:w-[100px] md:h-[100px] rounded-xl border border-solid border-neutral-200">
                                                <img src="<?= $item['img'] ?>" alt="item">
                                            </figure>
                                            <div class="flex-1 flex flex-col gap-2">
                                                <h2 class="text-body-md-medium text-gray-8 truncate-2row"><?= $item['title'] ?></h2>
                                                <div class="neutral-200 text-body-sm-regular text-gray-7">
                                                    <?php pll_e('Type') ?>: <?= $item['pack'] ?> <?php pll_e('Pack') ?>
                                                </div>
                                            </div>
                                        </div>
                                        <p class="text-body-md-regular text-gray-8"><?php pll_e('Quantity') ?>: <?= $item['qty'] ?></p>
                                        <div class="flex flex-col items-end justify-end gap-1">
                                            <p class="text-body-md-medium text-gray-8"><?= formatBalance($item['price']) ?> </p>
                                        </div>
                                    </div>
                                    <?php endforeach ?>

                                    <!-- total -->
                                    <div class="flex items-center justify-between">
                                        <a href="/order-detail/?order_code=<?= $value->order_code ?>" class="button button-trans p-0 text-body-md-semibold text-secondary">
                                            <?php pll_e('View details') ?>
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <g id="Outline / Arrows / Alt Arrow Right">
                                                    <path id="Vector (Stroke)" fill-rule="evenodd" clip-rule="evenodd" d="M8.51192 4.43057C8.82641 4.161 9.29989 4.19743 9.56946 4.51192L15.5695 11.5119C15.8102 11.7928 15.8102 12.2072 15.5695 12.4881L9.56946 19.4881C9.29989 19.8026 8.82641 19.839 8.51192 19.5695C8.19743 19.2999 8.161 18.8264 8.43057 18.5119L14.0122 12L8.43057 5.48811C8.161 5.17361 8.19743 4.70014 8.51192 4.43057Z" fill="#0E74BC" />
                                                </g>
                                            </svg>
                                        </a>

                                        <div class="flex items-center gap-2">
                                            <p class="text-body-md-regular text-neutral-500"><?php pll_e('Total') ?>:</p>
                                            <p class="text-heading-h6 font-medium text-primary"><?= formatBalance($value->price_payment) ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="mt-3 flex flex-col gap-6 order-tab" data-tab-id="processing">
                        <?php foreach ($order_placed as $key => $value) :

                            $get_newest_order = $wpdb->get_results("SELECT * FROM wp_orders WHERE order_code = '{$value->order_code}' ORDER BY id DESC");
                            $dataProduct = $get_newest_order[0]->dataproduct;
                            $decodedData = json_decode(str_replace('\\', "", $dataProduct), true);
                            $countProduct = count($decodedData);

                            $idPro = $decodedData[0]['id'];
                            $price = get_field('price', $idPro);
                            $sale_price = get_field('sale_price', $idPro);
                            $status = $value->status;
                            $class = "";
                            $name = "";
                            if ($status == 1) {
                                $class = 'warning';
                                $name = 'Processing';
                            } elseif ($status == 2) {
                                $class = 'success';
                                $name = 'Completed';
                            } elseif ($status == 4) {
                                $class = 'error';
                                $name = 'Canceled';
                            } else {
                                $class = 'warning';
                                $name = 'In progress';
                            }
                        ?>
                            <div class="w-full flex flex-col items-center justify-center bg-white rounded-xl">
                                <div
                                    class="w-full flex flex-wrap items-center justify-between gap-2 px-8 py-6 border-b border-neutral-200 border-solid">
                                    <div class="flex flex-wrap items-center gap-4">
                                        <p class="text-body-md-medium text-gray-8"><?= date('m/d/Y', $value->time_order) ?></p>
                                        <div class="w-1 h-1 rounded-full bg-[#D9D9D9]"></div>
                                        <div class="flex items-center gap-2">
                                            <p class="text-body-md-medium text-gray-8"><?php pll_e('Order code') ?>:</p>
                                            <p class="text-body-md-medium text-neutral-500"><?= $value->order_code ?></p>
                                            <input type="text" value="<?= $value->order_code ?>" id="myInput_processing<?= $value->order_code ?>" style="display: none">
                                            <button class="button button-trans p-0"  onclick="myFunction('myInput_processing<?= $value->order_code ?>')">
                                                <figure class="w-5 h-5"><img src="<?= $url ?>/assets/image/icon/duplicate.svg"
                                                                             alt="icon">
                                                </figure>
                                            </button>
                                        </div>
                                        <div class="w-1 h-1 rounded-full bg-[#D9D9D9]"></div>
                                        <p class="text-body-md-medium text-neutral-500"><?php pll_e('Deliver') ?></p>
                                        <div class="w-1 h-1 rounded-full bg-[#D9D9D9]"></div>
                                        <p class="text-body-md-medium text-neutral-500"><?= $countProduct ?> <?php pll_e('products') ?></p>
                                        <div class="w-1 h-1 rounded-full bg-[#D9D9D9]"></div>
                                        <a href="/pdf/?order_code=<?= $value->order_code ?>" target="_blank" class="flex items-center gap-2">
                                            <div class="text-body-md-medium text-neutral-500">Receipt</div>
                                            <button class="button bg-trans">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="20"
                                                     viewBox="0 0 16 20" fill="none">
                                                    <path
                                                            d="M9.75 1.24258V4.96007C9.75 5.46411 9.75 5.71614 9.84537 5.90866C9.92926 6.078 10.0631 6.21568 10.2278 6.30197C10.4149 6.40006 10.66 6.40006 11.15 6.40006H14.7642M11.5 10.9H4.5M11.5 14.5H4.5M6.25 7.3H4.5M9.75 1H5.2C3.72986 1 2.99479 1 2.43327 1.29428C1.93935 1.55314 1.53778 1.96619 1.28611 2.47423C1 3.05179 1 3.80786 1 5.32V14.68C1 16.1921 1 16.9482 1.28611 17.5258C1.53778 18.0338 1.93935 18.4469 2.43327 18.7057C2.99479 19 3.72986 19 5.2 19H10.8C12.2701 19 13.0052 19 13.5667 18.7057C14.0607 18.4469 14.4622 18.0338 14.7139 17.5258C15 16.9482 15 16.1921 15 14.68V6.4L9.75 1Z"
                                                            stroke="#475467" stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                </svg>
                                            </button>
                                        </a>
                                    </div>

                                    <div class="badge <?= $class ?>">
                                        <?= $name ?>
                                    </div>
                                </div>

                                <div class="w-full py-6 px-8 flex flex-col gap-3 border-b border-solid border-neutral-200">
                                    <!-- product -->
                                    <?php foreach($decodedData as $key => $item): 
                                        $idPro = $item['id'];
                                    ?>
                                    <div class="w-full flex flex-wrap lg:flex-nowrap justify-between items-center">
                                        <div class="flex lg:flex-0 w-full md:w-2/3 max-w-[653px] items-center gap-5">
                                            <figure
                                                class="w-[60px] h-[60px] md:w-[100px] md:h-[100px] rounded-xl border border-solid border-neutral-200">
                                                <img src="<?= $item['img'] ?>" alt="item">
                                            </figure>
                                            <div class="flex-1 flex flex-col gap-2">
                                                <h2 class="text-body-md-medium text-gray-8 truncate-2row"><?= $item['title'] ?></h2>
                                                <div class="neutral-200 text-body-sm-regular text-gray-7">
                                                    <?php pll_e('Type') ?>: <?= $item['pack'] ?> <?php pll_e('Pack') ?>
                                                </div>
                                            </div>
                                        </div>
                                        <p class="text-body-md-regular text-gray-8"><?php pll_e('Quantity') ?>: <?= $item['qty'] ?></p>
                                        <div class="flex flex-col items-end justify-end gap-1">
                                            <p class="text-body-md-medium text-gray-8"><?= formatBalance($item['price']) ?> </p>
                                        </div>
                                    </div>
                                    <?php endforeach ?>

                                    <!-- total -->
                                    <div class="flex items-center justify-between">
                                        <a href="/order-detail/?order_code=<?= $value->order_code ?>" class="button button-trans p-0 text-body-md-semibold text-secondary">
                                            <?php pll_e('View details') ?>
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <g id="Outline / Arrows / Alt Arrow Right">
                                                    <path id="Vector (Stroke)" fill-rule="evenodd" clip-rule="evenodd" d="M8.51192 4.43057C8.82641 4.161 9.29989 4.19743 9.56946 4.51192L15.5695 11.5119C15.8102 11.7928 15.8102 12.2072 15.5695 12.4881L9.56946 19.4881C9.29989 19.8026 8.82641 19.839 8.51192 19.5695C8.19743 19.2999 8.161 18.8264 8.43057 18.5119L14.0122 12L8.43057 5.48811C8.161 5.17361 8.19743 4.70014 8.51192 4.43057Z" fill="#0E74BC" />
                                                </g>
                                            </svg>
                                        </a>

                                        <div class="flex items-center gap-2">
                                            <p class="text-body-md-regular text-neutral-500"><?php pll_e('Total') ?>:</p>
                                            <p class="text-heading-h6 font-medium text-primary"><?= formatBalance($value->price_payment) ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <!-- <div class="mt-3 flex flex-col gap-6 order-tab" data-tab-id="in-progress">
                        <?php foreach ($packaging as $key => $value) :

                            $get_newest_order = $wpdb->get_results("SELECT * FROM wp_orders WHERE order_code = '{$value->order_code}' ORDER BY id DESC");
                            $dataProduct = $get_newest_order[0]->dataproduct;
                            $decodedData = json_decode(str_replace('\\', "", $dataProduct), true);
                            $countProduct = count($decodedData);

                            $idPro = $decodedData[0]['id'];
                            $price = get_field('price', $idPro);
                            $sale_price = get_field('sale_price', $idPro);
                            $status = $value->status;
                            $class = "";
                            $name = "";
                            if ($status == 1) {
                                $class = 'warning';
                                $name = pll__('Processing');
                            } elseif ($status == 2) {
                                $class = 'success';
                                $name = pll__('Completed');
                            } elseif ($status == 4) {
                                $class = 'error';
                                $name = pll__('Canceled');
                            } else {
                                $class = 'warning';
                                $name = pll__('In progress');
                            }
                        ?>
                            <div class="w-full flex flex-col items-center justify-center bg-white rounded-xl">
                                <div
                                    class="w-full flex flex-wrap items-center justify-between gap-2 px-8 py-6 border-b border-neutral-200 border-solid">
                                    <div class="flex flex-wrap items-center gap-4">
                                        <p class="text-body-md-medium text-gray-8"><?= date('m/d/Y', $value->time_order) ?></p>
                                        <div class="w-1 h-1 rounded-full bg-[#D9D9D9]"></div>
                                        <div class="flex items-center gap-2">
                                            <p class="text-body-md-medium text-gray-8"><?php pll_e('Order code') ?>:</p>
                                            <p class="text-body-md-medium text-neutral-500"><?= $value->order_code ?></p>
                                            <input type="text" value="<?= $value->order_code ?>" id="myInput_in_progress<?= $value->order_code ?>" style="display: none">
                                            <button class="button button-trans p-0"  onclick="myFunction('myInput_in_progress<?= $value->order_code ?>')">
                                                <figure class="w-5 h-5"><img src="<?= $url ?>/assets/image/icon/duplicate.svg"
                                                                             alt="icon">
                                                </figure>
                                            </button>
                                        </div>
                                        <div class="w-1 h-1 rounded-full bg-[#D9D9D9]"></div>
                                        <p class="text-body-md-medium text-neutral-500"><?php pll_e('Deliver') ?></p>
                                        <div class="w-1 h-1 rounded-full bg-[#D9D9D9]"></div>
                                        <p class="text-body-md-medium text-neutral-500"><?= $countProduct ?> <?php pll_e('products') ?></p>
                                        <div class="w-1 h-1 rounded-full bg-[#D9D9D9]"></div>
                                        <a href="/pdf/?order_code=<?= $value->order_code ?>" target="_blank" class="flex items-center gap-2">
                                            <div class="text-body-md-medium text-neutral-500">Receipt</div>
                                            <button class="button bg-trans">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="20"
                                                     viewBox="0 0 16 20" fill="none">
                                                    <path
                                                            d="M9.75 1.24258V4.96007C9.75 5.46411 9.75 5.71614 9.84537 5.90866C9.92926 6.078 10.0631 6.21568 10.2278 6.30197C10.4149 6.40006 10.66 6.40006 11.15 6.40006H14.7642M11.5 10.9H4.5M11.5 14.5H4.5M6.25 7.3H4.5M9.75 1H5.2C3.72986 1 2.99479 1 2.43327 1.29428C1.93935 1.55314 1.53778 1.96619 1.28611 2.47423C1 3.05179 1 3.80786 1 5.32V14.68C1 16.1921 1 16.9482 1.28611 17.5258C1.53778 18.0338 1.93935 18.4469 2.43327 18.7057C2.99479 19 3.72986 19 5.2 19H10.8C12.2701 19 13.0052 19 13.5667 18.7057C14.0607 18.4469 14.4622 18.0338 14.7139 17.5258C15 16.9482 15 16.1921 15 14.68V6.4L9.75 1Z"
                                                            stroke="#475467" stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                </svg>
                                            </button>
                                        </a>
                                    </div>

                                    <div class="badge <?= $class ?>">
                                        <?= $name ?>
                                    </div>
                                </div>

                                <div class="w-full py-6 px-8 flex flex-col gap-3 border-b border-solid border-neutral-200">
                                    <?php foreach($decodedData as $key => $item): 
                                        $idPro = $item['id'];
                                    ?>
                                    <div class="w-full flex flex-wrap lg:flex-nowrap justify-between items-center">
                                        <div class="flex lg:flex-0 w-full md:w-2/3 max-w-[653px] items-center gap-5">
                                            <figure
                                                class="w-[60px] h-[60px] md:w-[100px] md:h-[100px] rounded-xl border border-solid border-neutral-200">
                                                <img src="<?= $item['img'] ?>" alt="item">
                                            </figure>
                                            <div class="flex-1 flex flex-col gap-2">
                                                <h2 class="text-body-md-medium text-gray-8 truncate-2row"><?= $item['title'] ?></h2>
                                                <div class="neutral-200 text-body-sm-regular text-gray-7">
                                                    <?php pll_e('Type') ?>: <?= $item['pack'] ?> <?php pll_e('Pack') ?>
                                                </div>
                                            </div>
                                        </div>
                                        <p class="text-body-md-regular text-gray-8"><?php pll_e('Quantity') ?>: <?= $item['qty'] ?></p>
                                        <div class="flex flex-col items-end justify-end gap-1">
                                            <p class="text-body-md-medium text-gray-8"><?= formatBalance($item['price']) ?> </p>
                                        </div>
                                    </div>
                                    <?php endforeach ?>

                                    <div class="flex items-center justify-between">
                                        <a href="/order-detail/?order_code=<?= $value->order_code ?>" class="button button-trans p-0 text-body-md-semibold text-secondary">
                                            <?php pll_e('View details') ?>
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <g id="Outline / Arrows / Alt Arrow Right">
                                                    <path id="Vector (Stroke)" fill-rule="evenodd" clip-rule="evenodd" d="M8.51192 4.43057C8.82641 4.161 9.29989 4.19743 9.56946 4.51192L15.5695 11.5119C15.8102 11.7928 15.8102 12.2072 15.5695 12.4881L9.56946 19.4881C9.29989 19.8026 8.82641 19.839 8.51192 19.5695C8.19743 19.2999 8.161 18.8264 8.43057 18.5119L14.0122 12L8.43057 5.48811C8.161 5.17361 8.19743 4.70014 8.51192 4.43057Z" fill="#0E74BC" />
                                                </g>
                                            </svg>
                                        </a>

                                        <div class="flex items-center gap-2">
                                            <p class="text-body-md-regular text-neutral-500"><?php pll_e('Total') ?>:</p>
                                            <p class="text-heading-h6 font-medium text-primary"><?= formatBalance($value->price_payment) ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div> -->
                    <div class="mt-3 flex flex-col gap-6 order-tab" data-tab-id="completed">
                        <?php foreach ($on_the_road as $key => $value) :

                            $get_newest_order = $wpdb->get_results("SELECT * FROM wp_orders WHERE order_code = '{$value->order_code}' ORDER BY id DESC");
                            $dataProduct = $get_newest_order[0]->dataproduct;
                            $decodedData = json_decode(str_replace('\\', "", $dataProduct), true);
                            $countProduct = count($decodedData);

                            $idPro = $decodedData[0]['id'];
                            $price = get_field('price', $idPro);
                            $sale_price = get_field('sale_price', $idPro);
                            $status = $value->status;
                            $class = "";
                            $name = "";
                            if ($status == 1) {
                                $class = 'warning';
                                $name = 'Processing';
                            } elseif ($status == 2) {
                                $class = 'success';
                                $name = 'Completed';
                            } elseif ($status == 4) {
                                $class = 'error';
                                $name = 'Canceled';
                            } else {
                                $class = 'warning';
                                $name = 'In progress';
                            }
                        ?>
                            <div class="w-full flex flex-col items-center justify-center bg-white rounded-xl">
                                <div
                                    class="w-full flex flex-wrap items-center justify-between gap-2 px-8 py-6 border-b border-neutral-200 border-solid">
                                    <div class="flex flex-wrap items-center gap-4">
                                        <p class="text-body-md-medium text-gray-8"><?= date('m/d/Y', $value->time_order) ?></p>
                                        <div class="w-1 h-1 rounded-full bg-[#D9D9D9]"></div>
                                        <div class="flex items-center gap-2">
                                            <p class="text-body-md-medium text-gray-8"><?php pll_e('Order code') ?>:</p>
                                            <p class="text-body-md-medium text-neutral-500 order_code"><?= $value->order_code ?></p>
                                            <input type="text" value="<?= $value->order_code ?>" id="myInput_completed<?= $value->order_code ?>" style="display: none">
                                            <button class="button button-trans p-0"  onclick="myFunction('myInput_completed<?= $value->order_code ?>')">
                                                <figure class="w-5 h-5"><img src="<?= $url ?>/assets/image/icon/duplicate.svg"
                                                                             alt="icon">
                                                </figure>
                                            </button>
                                        </div>
                                        <div class="w-1 h-1 rounded-full bg-[#D9D9D9]"></div>
                                        <p class="text-body-md-medium text-neutral-500"><?php pll_e('Deliver') ?></p>
                                        <div class="w-1 h-1 rounded-full bg-[#D9D9D9]"></div>
                                        <p class="text-body-md-medium text-neutral-500"><?= $countProduct ?> <?php pll_e('products') ?></p>
                                        <div class="w-1 h-1 rounded-full bg-[#D9D9D9]"></div>
                                        <a href="/pdf/?order_code=<?= $value->order_code ?>" target="_blank" class="flex items-center gap-2">
                                            <div class="text-body-md-medium text-neutral-500">Receipt</div>
                                            <button class="button bg-trans">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="20"
                                                     viewBox="0 0 16 20" fill="none">
                                                    <path
                                                            d="M9.75 1.24258V4.96007C9.75 5.46411 9.75 5.71614 9.84537 5.90866C9.92926 6.078 10.0631 6.21568 10.2278 6.30197C10.4149 6.40006 10.66 6.40006 11.15 6.40006H14.7642M11.5 10.9H4.5M11.5 14.5H4.5M6.25 7.3H4.5M9.75 1H5.2C3.72986 1 2.99479 1 2.43327 1.29428C1.93935 1.55314 1.53778 1.96619 1.28611 2.47423C1 3.05179 1 3.80786 1 5.32V14.68C1 16.1921 1 16.9482 1.28611 17.5258C1.53778 18.0338 1.93935 18.4469 2.43327 18.7057C2.99479 19 3.72986 19 5.2 19H10.8C12.2701 19 13.0052 19 13.5667 18.7057C14.0607 18.4469 14.4622 18.0338 14.7139 17.5258C15 16.9482 15 16.1921 15 14.68V6.4L9.75 1Z"
                                                            stroke="#475467" stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                </svg>
                                            </button>
                                        </a>
                                    </div>

                                    <div class="badge <?= $class ?>">
                                        <?= $name ?>
                                    </div>
                                </div>

                                <div class="w-full py-6 px-8 flex flex-col gap-3 border-b border-solid border-neutral-200">
                                    <!-- product -->
                                    <?php foreach($decodedData as $key => $item): 
                                        $idPro = $item['id'];
                                    ?>
                                    <div class="w-full flex flex-wrap lg:flex-nowrap justify-between items-center">
                                        <div class="flex lg:flex-0 w-full md:w-2/3 max-w-[653px] items-center gap-5">
                                            <figure
                                                class="w-[60px] h-[60px] md:w-[100px] md:h-[100px] rounded-xl border border-solid border-neutral-200">
                                                <img src="<?= $item['img'] ?>" alt="item">
                                            </figure>
                                            <div class="flex-1 flex flex-col gap-2">
                                                <h2 class="text-body-md-medium text-gray-8 truncate-2row"><?= $item['title'] ?></h2>
                                                <div class="neutral-200 text-body-sm-regular text-gray-7">
                                                    <?php pll_e('Type') ?>: <?= $item['pack'] ?> <?php pll_e('Pack') ?>
                                                </div>
                                            </div>
                                        </div>
                                        <p class="text-body-md-regular text-gray-8"><?php pll_e('Quantity') ?>: <?= $item['qty'] ?></p>
                                        <div class="flex flex-col items-end justify-end gap-1">
                                            <p class="text-body-md-medium text-gray-8"><?= formatBalance($item['price']) ?> </p>
                                        </div>
                                    </div>
                                    <?php endforeach ?>

                                    <!-- total -->
                                    <div class="flex items-center justify-between">
                                        <a href="/order-detail/?order_code=<?= $value->order_code ?>" class="button button-trans p-0 text-body-md-semibold text-secondary">
                                            <?php pll_e('View details') ?>
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <g id="Outline / Arrows / Alt Arrow Right">
                                                    <path id="Vector (Stroke)" fill-rule="evenodd" clip-rule="evenodd" d="M8.51192 4.43057C8.82641 4.161 9.29989 4.19743 9.56946 4.51192L15.5695 11.5119C15.8102 11.7928 15.8102 12.2072 15.5695 12.4881L9.56946 19.4881C9.29989 19.8026 8.82641 19.839 8.51192 19.5695C8.19743 19.2999 8.161 18.8264 8.43057 18.5119L14.0122 12L8.43057 5.48811C8.161 5.17361 8.19743 4.70014 8.51192 4.43057Z" fill="#0E74BC" />
                                                </g>
                                            </svg>
                                        </a>

                                        <div class="flex items-center gap-2">
                                            <p class="text-body-md-regular text-neutral-500"><?php pll_e('Total') ?>:</p>
                                            <p class="text-heading-h6 font-medium text-primary"><?= formatBalance($value->price_payment) ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="mt-3 flex flex-col gap-6 order-tab " data-tab-id="canceled">
                        <?php foreach ($delivered as $key => $value) :

                            $get_newest_order = $wpdb->get_results("SELECT * FROM wp_orders WHERE order_code = '{$value->order_code}' ORDER BY id DESC");
                            $dataProduct = $get_newest_order[0]->dataproduct;
                            $decodedData = json_decode(str_replace('\\', "", $dataProduct), true);
                            $countProduct = count($decodedData);

                            $idPro = $decodedData[0]['id'];
                            $price = get_field('price', $idPro);
                            $sale_price = get_field('sale_price', $idPro);
                            $status = $value->status;
                            $class = "";
                            $name = "";
                            if ($status == 1) {
                                $class = 'warning';
                                $name = pll__('Processing');
                            } elseif ($status == 2) {
                                $class = 'success';
                                $name = pll__('Completed');
                            } elseif ($status == 4) {
                                $class = 'error';
                                $name = pll__('Canceled');
                            } else {
                                $class = 'warning';
                                $name = pll__('In progress');
                            }
                        ?>
                            <div class="w-full flex flex-col items-center justify-center bg-white rounded-xl">
                                <div
                                    class="w-full flex flex-wrap items-center justify-between gap-2 px-8 py-6 border-b border-neutral-200 border-solid">
                                    <div class="flex flex-wrap items-center gap-4">
                                        <p class="text-body-md-medium text-gray-8"><?= date('m/d/Y', $value->time_order) ?></p>
                                        <div class="w-1 h-1 rounded-full bg-[#D9D9D9]"></div>
                                        <div class="flex items-center gap-2">
                                            <p class="text-body-md-medium text-gray-8"><?php pll_e('Order code') ?>:</p>
                                            <p class="text-body-md-medium text-neutral-500"><?= $value->order_code ?></p>
                                            <input type="text" value="<?= $value->order_code ?>" id="myInput_canceled<?= $value->order_code ?>" style="display: none">
                                            <button class="button button-trans p-0"  onclick="myFunction('myInput_canceled<?= $value->order_code ?>')">
                                                <figure class="w-5 h-5"><img src="<?= $url ?>/assets/image/icon/duplicate.svg"
                                                                             alt="icon">
                                                </figure>
                                            </button>
                                        </div>
                                        <div class="w-1 h-1 rounded-full bg-[#D9D9D9]"></div>
                                        <p class="text-body-md-medium text-neutral-500"><?php pll_e('Deliver') ?></p>
                                        <div class="w-1 h-1 rounded-full bg-[#D9D9D9]"></div>
                                        <p class="text-body-md-medium text-neutral-500"><?= $countProduct ?> <?php pll_e('products') ?></p>
                                        <div class="w-1 h-1 rounded-full bg-[#D9D9D9]"></div>
                                        <a href="/pdf/?order_code=<?= $value->order_code ?>" target="_blank" class="flex items-center gap-2">
                                            <div class="text-body-md-medium text-neutral-500">Receipt</div>
                                            <button class="button bg-trans">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="20"
                                                     viewBox="0 0 16 20" fill="none">
                                                    <path
                                                            d="M9.75 1.24258V4.96007C9.75 5.46411 9.75 5.71614 9.84537 5.90866C9.92926 6.078 10.0631 6.21568 10.2278 6.30197C10.4149 6.40006 10.66 6.40006 11.15 6.40006H14.7642M11.5 10.9H4.5M11.5 14.5H4.5M6.25 7.3H4.5M9.75 1H5.2C3.72986 1 2.99479 1 2.43327 1.29428C1.93935 1.55314 1.53778 1.96619 1.28611 2.47423C1 3.05179 1 3.80786 1 5.32V14.68C1 16.1921 1 16.9482 1.28611 17.5258C1.53778 18.0338 1.93935 18.4469 2.43327 18.7057C2.99479 19 3.72986 19 5.2 19H10.8C12.2701 19 13.0052 19 13.5667 18.7057C14.0607 18.4469 14.4622 18.0338 14.7139 17.5258C15 16.9482 15 16.1921 15 14.68V6.4L9.75 1Z"
                                                            stroke="#475467" stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                </svg>
                                            </button>
                                        </a>
                                    </div>

                                    <div class="badge <?= $class ?>">
                                        <?= $name ?>
                                    </div>
                                </div>

                                <div class="w-full py-6 px-8 flex flex-col gap-3 border-b border-solid border-neutral-200">
                                    <!-- product -->
                                    <?php foreach($decodedData as $key => $item): 
                                        $idPro = $item['id'];
                                    ?>
                                    <div class="w-full flex flex-wrap lg:flex-nowrap justify-between items-center">
                                        <div class="flex lg:flex-0 w-full md:w-2/3 max-w-[653px] items-center gap-5">
                                            <figure
                                                class="w-[60px] h-[60px] md:w-[100px] md:h-[100px] rounded-xl border border-solid border-neutral-200">
                                                <img src="<?= $item['img'] ?>" alt="item">
                                            </figure>
                                            <div class="flex-1 flex flex-col gap-2">
                                                <h2 class="text-body-md-medium text-gray-8 truncate-2row"><?= $item['title'] ?></h2>
                                                <div class="neutral-200 text-body-sm-regular text-gray-7">
                                                    <?php pll_e('Type') ?>: <?= $item['pack'] ?> <?php pll_e('Pack') ?>
                                                </div>
                                            </div>
                                        </div>
                                        <p class="text-body-md-regular text-gray-8"><?php pll_e('Quantity') ?>: <?= $item['qty'] ?></p>
                                        <div class="flex flex-col items-end justify-end gap-1">
                                            <p class="text-body-md-medium text-gray-8"><?= formatBalance($item['price']) ?> </p>
                                        </div>
                                    </div>
                                    <?php endforeach ?>

                                    <!-- total -->
                                    <div class="flex items-center justify-between">
                                        <a href="/order-detail/?order_code=<?= $value->order_code ?>" class="button button-trans p-0 text-body-md-semibold text-secondary">
                                            <?php pll_e('View details') ?>
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <g id="Outline / Arrows / Alt Arrow Right">
                                                    <path id="Vector (Stroke)" fill-rule="evenodd" clip-rule="evenodd" d="M8.51192 4.43057C8.82641 4.161 9.29989 4.19743 9.56946 4.51192L15.5695 11.5119C15.8102 11.7928 15.8102 12.2072 15.5695 12.4881L9.56946 19.4881C9.29989 19.8026 8.82641 19.839 8.51192 19.5695C8.19743 19.2999 8.161 18.8264 8.43057 18.5119L14.0122 12L8.43057 5.48811C8.161 5.17361 8.19743 4.70014 8.51192 4.43057Z" fill="#0E74BC" />
                                                </g>
                                            </svg>
                                        </a>

                                        <div class="flex items-center gap-2">
                                            <p class="text-body-md-regular text-neutral-500"><?php pll_e('Total') ?>:</p>
                                            <p class="text-heading-h6 font-medium text-primary"><?= formatBalance($value->price_payment) ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<?php get_footer() ?>
<style>
    .order-tab-item {
        display: flex;
        padding: 8px 16px 12px 16px;
        justify-content: center;
        align-items: center;
        gap: 8px;
        border-bottom: 2px solid;
        border-color: transparent;
        background: #FFF;
        cursor: pointer;
    }

    .order-tab-item.active {
        font-weight: 600;
        color: #0e74bc;
        border-color: #0e74bc;

    }
</style>
<!-- tab active -->
<script !src="">
function myFunction(inputId) {
    // Lấy input element
    var copyText = document.getElementById(inputId);
    
    copyText.style.display = "block";
    
    // Select text
    copyText.select();
    
    try {
        // Thực hiện copy
        document.execCommand('copy');
        
        // Thông báo
        alert("<?php pll_e('Copied the text') ?>: " + copyText.value);
    } catch (err) {
        console.error('Copy error: ', err);
    } finally {
        // Ẩn input lại 
        copyText.style.display = "none";
    }
}
</script>
<!-- <script>
    jQuery(document).ready(function($) {

        // Xử lý khi người dùng nhấn Enter trong ô input
        $('#searchOrder').on('submit', function(e) {
            e.preventDefault();
            if (e.key === 'Enter') {
                e.preventDefault(); // Ngăn gửi form mặc định

                let orderCode = $(this).val().trim();

                if (orderCode) {
                    // Gửi AJAX request
                    $.ajax({
                        url: '<?= admin_url("admin-ajax.php"); ?>', // Biến ajaxurl được thêm bởi WordPress
                        type: 'POST',
                        data: {
                            action: 'search_order_by_code', // Hook action được định nghĩa trong PHP
                            order_code: orderCode,
                        },
                        beforeSend: function() {
                            Swal.fire({
                                title: 'Processing',
                                html: '<?php pll_e('Please wait...') ?>',
                                didOpen: () => {
                                    Swal.showLoading()
                                }
                            });
                        },
                        success: function(response) {
                            if (response.success) {
                                // renderOrderResults(); // Gọi hàm render kết quả
                                const resultsContainer = $('.order-tab'); // Thay '.order-tab' bằng vùng chứa kết quả
                                resultsContainer.empty(); // Xóa nội dung cũ

                                // Lặp qua danh sách đơn hàng và hiển thị
                                $.each(response.orders, function(index, order) {
                                    resultsContainer.append(`
                <div class="w-full flex flex-col items-center justify-center bg-white rounded-xl">
                    <div class="w-full flex flex-wrap items-center justify-between gap-2 px-8 py-6 border-b border-neutral-200 border-solid">
                        <div class="flex flex-wrap items-center gap-4">
                            <p class="text-body-md-medium text-gray-8">${order.date}</p>
                            <div class="w-1 h-1 rounded-full bg-[#D9D9D9]"></div>
                            <div class="flex items-center gap-2">
                                <p class="text-body-md-medium text-gray-8">Order code:</p>
                                <p class="text-body-md-medium text-neutral-500">${order.order_code}</p>
                            </div>
                        </div>
                        <div class="badge ${order.status_class}">
                            ${order.status}
                        </div>
                    </div>
                </div>
            `);
                                });
                            }
                            } else {
                                $('.order-tab').html('<p>' + (response.message || '<?php pll_e('No orders found.') ?>') + '</p>');
                            }
                        },
                        error: function(error) {
                            console.error('Error:', error);
                            $('.order-tab').html('<p>Something went wrong. Please try again later.</p>');
                        },
                    });
                } else {
                    alert('<?php pll_e('Please enter an order code.') ?>');
                }
            }
        });

        // Hàm hiển thị kết quả đơn hàng
        function renderOrderResults(orders) {

    });
</script> -->
<script defer>
    document.addEventListener("DOMContentLoaded", () => {
        const tabItems = document.querySelectorAll(".order-tab-item");

        tabItems.forEach((item) => {
            item.addEventListener("click", () => {
                // Loại bỏ class `active` khỏi tất cả các mục
                tabItems.forEach((tab) => tab.classList.remove("active"));

                // Thêm class `active` vào mục được click
                item.classList.add("active");
            });
        });
    });
    document.addEventListener('DOMContentLoaded', () => {
        const tabItems = document.querySelectorAll('.order-tab-item');
        const tabContents = document.querySelectorAll('.order-tab');

        // Xử lý click vào tab
        tabItems.forEach(tab => {
            tab.addEventListener('click', () => {
                const tabId = tab.getAttribute('data-tab-id'); // Lấy ID của tab được click

                // Xóa class active khỏi tất cả các tab
                tabItems.forEach(item => item.classList.remove('active'));

                // Ẩn tất cả các nội dung tab
                tabContents.forEach(content => content.style.display = 'none');

                // Thêm class active cho tab được click
                tab.classList.add('active');

                // Hiển thị nội dung tab tương ứng với tab được click
                const activeContent = document.querySelector(`.order-tab[data-tab-id="${tabId}"]`);
                if (activeContent) {
                    activeContent.style.display = 'flex';
                }
            });
        });

        // Hiển thị tab đầu tiên mặc định
        if (tabContents.length > 0) {
            tabContents.forEach(content => content.style.display = 'none'); // Ẩn tất cả nội dung
            const firstContent = tabContents[0];
            if (firstContent) {
                firstContent.style.display = 'flex'; // Hiển thị nội dung tab đầu tiên
            }
        }
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const logoutForm = document.getElementById("logoutForm");

        // Thêm sự kiện click cho toàn bộ form
        logoutForm.addEventListener("click", function(event) {
            // Ngăn chặn sự kiện click lan sang các phần tử khác
            event.preventDefault();

            // Hiển thị SweetAlert
            Swal.fire({
                title: 'Are you sure?',
                text: "You will be logged out!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, log me out!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Người dùng xác nhận, gửi form
                    logoutForm.submit();
                }
            });
        });
    });
</script>