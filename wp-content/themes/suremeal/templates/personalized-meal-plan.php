<?php /* Template Name: Personalized meal plan */ ?>
<?php
get_header('dealer');
$url = get_template_directory_uri();

global $wpdb;

$current_time = time(); // Thời gian hiện tại

// Truy vấn
$query = $wpdb->prepare(
    "SELECT * FROM product_points 
    WHERE status = %d ",
    1
);

$results = $wpdb->get_results($query);
$authenticated_user = validate_user_token();
$id_user = $authenticated_user->ID;
$points = 0;

$curron_aff = $wpdb->get_results($wpdb->prepare("SELECT * FROM points WHERE id_user = %s", $id_user));
$current_time = time(); // Lấy timestamp hiện tại

$nearest_record = null;
//$closest_date_diff = PHP_INT_MAX; // Khởi tạo khoảng cách thời gian lớn nhất


$min_date_end = PHP_INT_MAX; // Khởi tạo giá trị date_end nhỏ nhất

foreach ($curron_aff as $value) {
    if ($value->transaction_type == 1 && $current_time < (int)$value->date_end) {
        $points += $value->remaining_points; // Tính tổng điểm

        // Kiểm tra xem date_end hiện tại có nhỏ hơn min_date_end không
        if ((int)$value->remaining_points > 0) {
            if ((int)$value->date_end < $min_date_end) {
                $min_date_end = (int)$value->date_end;
                $nearest_record = $value; // Ghi nhận bản ghi có date_end nhỏ nhất
            }
        }
    }
}
?>
<div class="col-span-6 text-center md:p-8 py-4 px-2">
    <div class="mx-auto relative w-1192-full overflow-hidden">
        <div class="flex mb-8 items-center"><img class="mr-4" src="<?= $url ?>/dist/img/left-black.svg" onclick="window.location.href='<?= home_url() ?>/point-management/'"><span class="m-0 color-vector sm:text-2xl text-lg font-medium">Current Points Balance</span></div>
        <div class="bg-white py-5 px-0 rounded-10">
            <div class="w-full">
                <div class="pb-8 pl-5 pr-8 text-center bd-line-bottom">
                    <p class="font-semibold leading-8 text-base checkout-color-text mt-0 mb-1">Points available</p>
                    <p class="text-32 blue-sure font-semibold mt-0 mb-1 line-height-150"><?= number_format($points, 0, '.', ',') ?></p>
                    <p class="text-base font-normal my-0"><?= number_format($nearest_record->remaining_points, 0, '.', ',') ?> points will expire on <?= date('m/d/Y', $nearest_record->date_end) ?></p>
                </div>
                <?php foreach ($results as $key => $value):?>
                <div class="sm:pt-8 py-5 sm:pb-4 sm:pl-5 sm:pr-8 px-5 bd-line-bottom overflow-hidden inline-block w-full">
                    <div class="flex items-center w-h-100-100 float-left mr-4">
                        <img class="w-full" src="<?= $value->image ?>" alt="">
                    </div>
                    <div class="w-815 float-left text-left">
                        <h5 class="font-semibold leading-8 text-18 checkout-color-text mt-0 mb-1">
                            <?= $value->name ?>
                        </h5>
                        <p class="text-base font-normal mt-0 mb-1 text-justify"><?= $value->note ?></p>
<!--                        <p class="text-sm font-normal my-0 line-through color-icon-eye">$96.00</p>-->
                    </div>
                    <div class="float-right pt-27 right-none-545">
                        <button type="button" data-point="<?= $value->point ?>" data-value="<?= $value->id_value ?>" data-idPoint="<?= $value->id ?>" data-purchases="<?= $value->purchases ?>" class="text-white buy-now bg-red-sure border-none px-6 py-3 rounded-lg font-semibold text-sm"><?= $value->point ?> points</button>
                    </div>
                </div>
                <?php endforeach;?>
            </div>
        </div>
    </div>
</div>
<?php get_footer('dealer') ?>

<script !src="">
    $(".buy-now").on("click", function() {
        buyNowPoint($(this));
    });
</script>