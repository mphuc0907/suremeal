<?php /* Template Name: Point management */ ?>
<?php
$url = get_template_directory_uri();
get_header('dealer');
$authenticated_user = validate_user_token();
$id_user = $authenticated_user->ID;
$user_email = $authenticated_user->email;
$first_name = $authenticated_user->first_name;
$last_name = $authenticated_user->last_name;
$addresses = json_decode($authenticated_user->addresses, true) ?: [];
$provider = $authenticated_user->provider;
global $wpdb;
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

// Bạn có thể sử dụng $nearest


?>
<div class="col-span-6 text-center md:p-8 p-2 m-h-content">
    <div class="mx-auto relative w-1192-full overflow-hidden">
        <div class="mb-8 md:mt-0 mt-6">
            <h4 class="text-left m-0 color-vector sm:text-2xl text-lg font-medium">Current Points Balance</h4>
        </div>
        <div class="bg-white py-5 px-0 rounded-10">
            <div class="flex items-center grid grid-cols-11 md:mb-8 mb-4 px-5 gap-4">
                <div class="col-span-12 2xl:col-span-1 md:col-span-2 text-left">
                    <span class="text-32 blue-sure font-semibold"><?= number_format($points, 0, '.', ',') ?></span>
                </div>
                <div class="col-span-12 2xl:col-span-8 md:col-span-6 text-left">
                    <p class="font-semibold text-base m-0 mb-1">Points available</p>
                    <?php if($nearest_record->date_end != null): ?>
                        <p class="font-normal text-base m-0"><?= number_format($nearest_record->remaining_points, 0, '.', ',') ?> points will expire on <?= date('m/d/Y', $nearest_record->date_end) ?></p>
                    <?php endif; ?>
                </div>
                <div class="col-span-12 2xl:col-span-2 md:col-span-3 sm:text-right text-left">
                    <button type="button" class="text-white bg-red-sure border-none px-6 py-3 rounded-lg font-semibold w-144" onclick="window.location.href='<?= home_url() ?>/personalized-meal-plan'">Redeem offer</button>
                </div>
            </div>
            <div x-data="{ selectedTab: 'all' }" class="w-full">
                <div @keydown.right.prevent="$focus.wrap().next()" @keydown.left.prevent="$focus.wrap().previous()" class="flex gap-0 overflow-x-auto grid grid-cols-4 bg-white pt-2 bd-line-bottom-2" role="tablist" aria-label="tab options">
                    <button @click="selectedTab = 'all'" :aria-selected="selectedTab === 'all'" :tabindex="selectedTab === 'all' ? '0' : '-1'" :class="selectedTab === 'all' ? 'pt-2 pb-3 bg-white blue-sure balance-border-bottom text-base font-semibold checkout-color-red-text' : 'pt-2 pb-3 bg-white text-base font-normal color-icon-eye'" type="button" role="tab" aria-controls="tabpanelGroups" aria-selected="true" tabindex="0" class="pt-2 pb-3 bg-white text-18">All history</button>
                    <button @click="selectedTab = 'received'" :aria-selected="selectedTab === 'received'" :tabindex="selectedTab === 'received' ? '0' : '-1'" :class="selectedTab === 'received' ? 'pt-2 pb-3 bg-white blue-sure balance-border-bottom text-base font-semibold checkout-color-red-text' : 'pt-2 pb-3 bg-white text-18 font-normal color-icon-eye'" class="h-min px-4 py-2 text-18 pt-2 pb-3 bg-white text-base font-normal color-icon-eye" type="button" role="tab" aria-controls="tabpanelLikes" tabindex="-1">Received</button>
                    <button @click="selectedTab = 'used'" :aria-selected="selectedTab === 'used'" :tabindex="selectedTab === 'used' ? '0' : '-1'" :class="selectedTab === 'used' ? 'pt-2 pb-3 bg-white blue-sure balance-border-bottom text-base font-semibold checkout-color-red-text' : 'pt-2 pb-3 bg-white text-base font-normal color-icon-eye'" class="h-min px-4 py-2 text-18 pt-2 pb-3 bg-white text-base font-normal color-icon-eye" type="button" role="tab" aria-controls="tabpanelComments" tabindex="-1">Used</button>
                </div>
                <div class="text-neutral-600 dark:text-neutral-300">
                    <div x-show="selectedTab === 'all'" id="tabpanelGroups" role="tabpanel" aria-label="all">
                        <div class="">
                            <?php foreach ($curron_aff as $value) :?>
                            <?php if ($value->transaction_type == 1) :?>
                            <div class="sm:pt-6 pt-5 sm:pb-4 pb-5 pl-5 sm:pr-8 pr-5 text-left bd-line-bottom block w-full overflow-hidden">
                                <div class="flex items-center w-h-100-100 float-left mr-4">
                                    <img class="w-full" src="<?= $value->image ?>" alt="">
                                </div>
                                <div class="w-744 float-left">
                                    <h5 class="font-medium leading-8 text-lg checkout-color-text mt-0 mb-1">
                                        <?= $value->title ?>
                                    </h5>
                                    <p class="text-base font-normal mt-0 mb-1"><?= $value->note ?></p>
                                    <p class="text-sm font-normal my-0"><?= date('H:i m/d/Y', $value->date_create) ?></p>
                                </div>
                                <div class="float-right mt-10 right-none-545">
                                    <span class="font-semibold text-xl blue-sure">+ <?= number_format($value->point, 0, '.', ',') ?></span>
                                </div>
                            </div>
                            <?php else:?>
                            <div class="sm:pt-6 pt-5 sm:pb-4 pb-5 pl-5 sm:pr-8 pr-5 text-left bd-line-bottom block w-full overflow-hidden">
                                <div class="flex items-center w-h-100-100 float-left mr-4">
                                    <img class="w-full" src="<?= $value->image ?>" alt="">
                                </div>
                                <div class="w-744 float-left">
                                    <h5 class="font-medium leading-8 text-lg checkout-color-text mt-0 mb-1">
                                        <?= $value->title ?>
                                    </h5>
                                    <p class="text-base font-normal mt-0 mb-1"><?= $value->note ?></p>
                                    <p class="text-sm font-normal my-0"><?= date('H:i m/d/Y', $value->date_create) ?></p>
                                </div>
                                <div class="float-right mt-10 right-none-545">
                                    <span class="font-semibold text-xl">- <?= number_format($value->point, 0, '.', ',') ?></span>
                                </div>
                            </div>
                            <?php endif;?>
                            <?php endforeach;?>
                        </div>
                    </div>
                    <div x-show="selectedTab === 'received'" id="tabpanelLikes" role="tabpanel" aria-label="Processing" style="display: none;">
                        <div class="mb-6 bg-white">
                            <?php foreach ($curron_aff as $value) :?>
                                <?php if ($value->transaction_type == 1) :?>
                                    <div class="sm:pt-6 pt-5 sm:pb-4 pb-5 pl-5 sm:pr-8 pr-5 text-left bd-line-bottom block w-full overflow-hidden">
                                        <div class="flex items-center w-h-100-100 float-left mr-4">
                                            <img class="w-full" src="<?= $value->image ?>" alt="">
                                        </div>
                                        <div class="w-744 float-left">
                                            <h5 class="font-medium leading-8 text-lg checkout-color-text mt-0 mb-1">
                                                <?= $value->title ?>
                                            </h5>
                                            <p class="text-base font-normal mt-0 mb-1"><?= $value->note ?></p>
                                            <p class="text-sm font-normal my-0"><?= date('H:i m/d/Y', $value->date_create) ?></p>
                                        </div>
                                        <div class="float-right mt-10 right-none-545">
                                            <span class="font-semibold text-xl blue-sure">+ <?= number_format($value->point, 0, '.', ',') ?></span>
                                        </div>
                                    </div>

                                <?php endif;?>
                            <?php endforeach;?>
                        </div>
                    </div>
                    <div x-show="selectedTab === 'used'" id="tabpanelComments" role="tabpanel" aria-label="used" style="display: none;">
                        <div class="mb-6 bg-white">
                            <?php foreach ($curron_aff as $value) :?>
                                <?php if ($value->transaction_type == 0) :?>

                                    <div class="sm:pt-6 pt-5 sm:pb-4 pb-5 pl-5 sm:pr-8 pr-5 text-left bd-line-bottom block w-full overflow-hidden">
                                        <div class="flex items-center w-h-100-100 float-left mr-4">
                                            <img class="w-full" src="<?= $value->image ?>" alt="">
                                        </div>
                                        <div class="w-744 float-left">
                                            <h5 class="font-medium leading-8 text-lg checkout-color-text mt-0 mb-1">
                                                <?= $value->title ?>
                                            </h5>
                                            <p class="text-base font-normal mt-0 mb-1"><?= $value->note ?></p>
                                            <p class="text-sm font-normal my-0"><?= date('H:i m/d/Y', $value->date_create) ?></p>
                                        </div>
                                        <div class="float-right mt-10 right-none-545">
                                            <span class="font-semibold text-xl">- <?= number_format($value->point, 0, '.', ',') ?></span>
                                        </div>
                                    </div>
                                <?php endif;?>
                            <?php endforeach;?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
get_footer('dealer');
?>
