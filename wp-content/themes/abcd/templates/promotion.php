<?php /* Template Name: Promotion */ ?>
<?php
$id = get_the_ID();
$slider = get_field('slider', $id);
$special_deals = get_field('special_deals', $id);

$url = get_template_directory_uri();

function get_dealer_discount($dealer_id, $product_id)
{
    global $wpdb;
    $discount = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT * FROM wp_discount_dealer 
            WHERE id_dealer = %d AND product = %d",
            $dealer_id,
            $product_id
        )
    );
    return $discount;
}

// Function to calculate final price with dealer discount
function calculate_dealer_price($original_price, $discount)
{
    if (!$discount) return false;

    if ($discount->discount_type == 0) {
        // Fixed amount discount
        return max(0, $original_price - $discount->discount_amount);
    } else {
        // Percentage discount
        $discount_amount = $original_price * ($discount->discount_amount / 100);
        return max(0, $original_price - $discount_amount);
    }
}

$authenticated_dealer = validate_dealer_token();
$dealer_id = null;
if ($authenticated_dealer) {
    $dealer_id = $authenticated_dealer->ID;
}

// Custom field "on_sale_now"
// Custom field "on_sale_now"
$onSaleNow = get_field('on_sale_now', $id);
$onSaleCountdownEnabled = $onSaleNow['open_the_countdown_timer'] ?? 0;

$flashSaleToday = $onSaleNow['flash_sale_today'] ?? [];
$flashSaleTodayStartTime = $flashSaleToday['flash_sale_start_time'] ?? '';
$flashSaleTodayEndTime = $flashSaleToday['flash_sale_end_time'] ?? '';
$flashSaleTodayProducts = $flashSaleToday['product_list'] ?? [];

// Chuyển đổi thời gian thành timestamp
$startTimestamp = strtotime($flashSaleTodayStartTime);
$endTimestamp = strtotime($flashSaleTodayEndTime);

$currentTimestamp = time();

$flashSaleTomorrow = $onSaleNow['flash_sale_tomorrow'] ?? [];
$flashSaleTomorrowStartTime = $flashSaleTomorrow['flash_sale_start_time_copy'] ?? '';
$flashSaleTomorrowEndTime = $flashSaleTomorrow['flash_sale_end_time_copy'] ?? '';

// Custom field "exclusive_promotion"
$exclusivePromotion = get_field('exclusive_promotion', $id);
$exclusiveCountdownEnabled = $exclusivePromotion['open_the_countdown_timer'] ?? 0;

$exclusiveFlashSaleToday = $exclusivePromotion['flash_sale_today'] ?? [];
$exclusiveFlashSaleStartTime = $exclusiveFlashSaleToday['flash_sale_start_time'] ?? '';
$exclusiveFlashSaleEndTime = $exclusiveFlashSaleToday['flash_sale_end_time'] ?? '';
$exclusiveFlashSaleProducts = $exclusiveFlashSaleToday['product_list'] ?? [];

$exclusiveFlashSaleTomorrow = $exclusivePromotion['flash_sale_tomorrow'] ?? [];
$exclusiveFlashSaleTomorrowStartTime = $exclusiveFlashSaleTomorrow['flash_sale_start_time_copy'] ?? '';
$exclusiveFlashSaleTomorrowEndTime = $exclusiveFlashSaleTomorrow['flash_sale_end_time_copy'] ?? '';

$promotion_1 = $special_deals['promotion_1'];
$promotion_2 = $special_deals['promotion_2'];

$args = [
    'post_type' => 'product',
    'posts_per_page' => 4,
    'paged' => max(1, get_query_var('paged')),
    'post_status' => 'publish',
    'meta_query'     => array(
        array(
            'key'     => 'sale_price',
            'value'   => '',
            'compare' => '!='
        ),
    ),
];
$product_query = new WP_Query($args);
$args_post = $product_query->posts;

get_header();
?>
<style>
    .tab-promotion {
        display: none;
    }

    .tab-promotion.active {
        display: flex;
    }
</style>
<main class="bg-[#EEF0F6]">
    <?php if ($slider): ?>
        <section>
            <div data-aos="zoom-in-up" data-aos-duration="1500" id="home-swiper" class="swiper swiper-homepage">
                <!-- Additional required wrapper -->
                <div class="swiper-wrapper">
                    <!-- Slides -->
                    <?php foreach ($slider as $item): ?>
                        <div class="swiper-slide">
                            <div class="gallery-item">
                                <figure>
                                    <img src="<?= $item['image'] ?>" class="banner-image" alt="promos-item">
                            </div>
                        </div>
                    <?php endforeach ?>
                </div>
                <!-- If we need pagination -->
                <div class="swiper-pagination"></div>
            </div>
        </section>
    <?php endif ?>

    <div class="container">
        <?php if (!empty($flashSaleTodayProducts) || !empty($exclusiveFlashSaleProducts)) :?>
        <div class="rounded-3xl bg-white mt-20 mb-8">
            <div class="w-full flex flex-col md:flex-row">
                <span class="promotions-text tab-promotion-item text-heading-h5" data-tabPromotion="onSale"><?php pll_e('On sale now') ?></span>
                <span class="promotions-text tab-promotion-item text-heading-h5" data-tabPromotion="Exclusive"><?php pll_e('Exclusive promotion') ?></span>
            </div>
            <div class="p-8 flex flex-col items-center tab-promotion active" data-tabPromotion="onSale">
                <?php if ($onSaleCountdownEnabled) :?>
                <div class="p-2 rounded-full md:bg-neutral-100 flex flex-col md:flex-row gap-4">
                    <div class="countdown-container py-2 px-3 sm:px-6 rounded-full bg-primary flex items-center gap-2 sm:gap-4">
                        <span class="text-heading-h7 text-white"><?php pll_e('Ending in:') ?></span>
                        <div class="flex gap-1 sm:gap-2 items-center" id="countdown">
                            <span class="countdown-item py-1 px-2 sm:px-3 bg-white text-heading-h7 font-bold rounded-lg text-primary" id="hours">--</span>
                            <span class="text-heading-h7 text-white">:</span>
                            <span class="countdown-item py-1 px-2 sm:px-3 bg-white text-heading-h7 font-bold rounded-lg text-primary" id="minutes">--</span>
                            <span class="text-heading-h7 text-white">:</span>
                            <span class="countdown-item py-1 px-2 sm:px-3 bg-white text-heading-h7 font-bold rounded-lg text-primary" id="seconds">--</span>
                        </div>
                    </div>
                    <div class="countdown-contain bg-neutral-100 md:bg-none py-2 px-6 rounded-full flex items-center gap-4">
                        <span class="min-h-[34px] flex items-center text-heading-h7 text-neutral-500"><?php pll_e('Tomorrow') ?> <?= $flashSaleTomorrowStartTime ?> - <?= $flashSaleTomorrowEndTime ?></span>
                    </div>
                </div>

                <?php endif;?>
                <div data-aos="fade-up" data-aos-duration="1500" class="mt-14">
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        <?php
                        $total_flash = count($flashSaleTodayProducts);
                        foreach ($flashSaleTodayProducts as $key => $product) :
                            $product_post = $product['product_flash'];

                            $price = get_field('price', $product_post->ID);
                            $sale_price = get_field('sale_price', $product_post->ID);
                            $instock = get_field('instock', $product_post->ID);
                            $term_list = get_the_terms($product_post->ID, 'category_product');
                            $des = get_field('short_description', $product_post->ID);
                            $qty = get_field('quantity', $product_post->ID);
                            if (!empty($sale_price)) {
                                $percentDecrease = (($price - $sale_price) / $price) * 100;
                            }
                            if (empty($instock)) {
                                $instock = 0;
                            }
                        ?>
                            <div class="pro-list img-hover rounded-3xl overflow-hidden shadow-2 product-item <?= $key >= 3 ? 'hidden' : '' ?>">
                                <div style="background: <?= $product['background'] ?>;" class="image max-h-[231px] flex items-center justify-center">
                                    <a href="<?= get_permalink($product_post->ID) ?>">
                                        <figure class="figure-25-23 max-w-[250px]">
                                            <img src="<?= $product['product_image'] ? $product['product_image'] : get_field('image_no_image', 'option'); ?>" alt="img">
                                        </figure>
                                    </a>
                                </div>
                                <div class="p-6 flex bg-white min-h-[255px] lg:min-h-[285px] 3xl:min-h-[312px]">
                                    <div class="flex flex-col justify-between flex-1 gap-3">
                                        <div class="flex flex-col gap-1">
                                            <!-- <p class="text-body-sm-regular text-neutral-500">SUREMEAL</p> -->
                                            <h2 class="min-h-2lh truncate-2row text-heading-h6 text-gray-9"><?= $product_post->post_title ?>
                                            </h2>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <div class="flex items-center gap-1">
                                                <?= renderStarRatingByProductId($product_post->ID) ?>
                                            </div>
                                            <p class="text-body-sm-regular text-neutral-500"><?= countReview($product_post->ID) ?> Reviews</p>
                                        </div>
                                        <p class="min-h-3lh truncate-3row text-body-md-regular text-neutral-500">
                                            <?= $des ?>
                                        </p>
                                        <div class="flex justify-between items-end gap-2">
                                            <?php if ($sale_price) :?>
                                                <div>
                                                    <p class="text-heading-h7 text-gray-9"><?= formatBalance($sale_price) ?></p>
                                                    <div class="flex gap-2 items-center">
                                                        <span class="text-[#898384] text-[14px]"><s><?= formatBalance($price) ?></s></span>
                                                        <div class="flex rounded-full items-center"
                                                             style="background: linear-gradient(90deg, rgba(255, 219, 111, 0) 0%, #FFD600 100%);">
                                                            <figure>
                                                                <img src="<?= $url ?>/assets/image/icon/flashsale.svg" alt="">
                                                            </figure>
                                                            <span
                                                                    class="block text-[#E4626F] font-bold text-[14px] pr-3">-<?= number_format($percentDecrease, 2) ?>%</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php else:?>
                                                <div>
                                                    <p class="text-heading-h7 text-gray-9"><?= formatBalance($price) ?></p>
                                                    <!--                                                    <div class="flex gap-2 items-center">-->
                                                    <!--                                                        <span class="text-[#898384] text-[14px]"><s>--><?//= formatBalance($price) ?><!--</s></span>-->
                                                    <!--                                                        <div class="flex rounded-full items-center"-->
                                                    <!--                                                             style="background: linear-gradient(90deg, rgba(255, 219, 111, 0) 0%, #FFD600 100%);">-->
                                                    <!--                                                            <figure>-->
                                                    <!--                                                                <img src="--><?//= $url ?><!--/assets/image/icon/flashsale.svg" alt="">-->
                                                    <!--                                                            </figure>-->
                                                    <!--                                                            <span-->
                                                    <!--                                                                    class="block text-[#E4626F] font-bold text-[14px] pr-3">---><?//= number_format($percentDecrease, 2) ?><!--%</span>-->
                                                    <!--                                                        </div>-->
                                                    <!--                                                    </div>-->
                                                </div>
                                            <?php endif;?>
                                            <button
                                                class="max-w-[134px] whitespace-nowrap button bg-primary text-body-md-semibold text-white buy-now"
                                                data-id="<?= $product_post->ID ?>" data-link="<?php echo get_permalink($product_post->ID) ?>"
                                                data-img="<?php echo checkImage($product_post->ID) ?>"
                                                data-price="<?php echo !empty($sale_price) ? $sale_price : $price; ?>"
                                                data-title="<?= $product_post->post_title ?>" data-instock="<?= $instock ?>"
                                                data-weight="<?= get_field('pound', $product_post->ID) ?>"
                                                data-pack="<?= $qty ?>"><?php pll_e('Buy Now') ?></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php if ($total_flash > 3): ?>
                    <button class="mt-9 w-full gap-4 text-body-md-semibold button-hover text-secondary flex justify-center view-more-btn">
                        <svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g id="Outline / Arrows / Double Alt Arrow Down">
                                <path id="Vector (Stroke)" fill-rule="evenodd" clip-rule="evenodd" d="M4.93057 6.51215C5.20014 6.19765 5.67361 6.16123 5.98811 6.4308L12.5 12.0124L19.0119 6.4308C19.3264 6.16123 19.7999 6.19765 20.0695 6.51215C20.339 6.82664 20.3026 7.30012 19.9881 7.56968L12.9881 13.5697C12.7072 13.8104 12.2928 13.8104 12.0119 13.5697L5.01192 7.56968C4.69743 7.30012 4.661 6.82664 4.93057 6.51215ZM4.93057 10.5121C5.20014 10.1977 5.67361 10.1612 5.98811 10.4308L12.5 16.0124L19.0119 10.4308C19.3264 10.1612 19.7999 10.1977 20.0695 10.5121C20.339 10.8266 20.3026 11.3001 19.9881 11.5697L12.9881 17.5697C12.7072 17.8104 12.2928 17.8104 12.0119 17.5697L5.01192 11.5697C4.69743 11.3001 4.661 10.8266 4.93057 10.5121Z" fill="#0E74BC" />
                            </g>
                        </svg>
                        <span><?php pll_e('View more products') ?></span>
                    </button>
                <?php endif ?>
            </div>
            <div class="p-8 flex flex-col items-center tab-promotion " data-tabPromotion="Exclusive">
                <div data-aos="fade-up" data-aos-duration="1500" class="mt-14">
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        <?php
                        $total_exclusive = count($exclusiveFlashSaleProducts);
                        foreach ($exclusiveFlashSaleProducts as $key => $product) :
                            $product_post = $product['product_flash'];

                            $price = get_field('price', $product_post->ID);
                            $sale_price = get_field('sale_price', $product_post->ID);
                            $instock = get_field('instock', $product_post->ID);
                            $term_list = get_the_terms($product_post->ID, 'category_product');
                            $des = get_field('short_description', $product_post->ID);
                            $qty = get_field('quantity', $product_post->ID);
                            if (!empty($sale_price)) {
                                $percentDecrease = (($price - $sale_price) / $price) * 100;
                            }
                            if (empty($instock)) {
                                $instock = 0;
                            }
                        ?>
                            <div class="pro-list img-hover rounded-3xl overflow-hidden shadow-2 product-item <?= $key >= 3 ? 'hidden' : '' ?>">
                                <div style="background: <?= $product['background'] ?>;" class="image max-h-[231px] flex items-center justify-center">
                                    <a href="<?= get_permalink($product_post->ID) ?>">
                                        <figure class="figure-25-23 max-w-[250px]">
                                            <img src="<?= $product['product_image'] ? $product['product_image'] : get_field('image_no_image', 'option'); ?>" alt="img">
                                        </figure>
                                    </a>
                                    <?php if ($instock <= 0) :?>
                                        <div class=" absolute top-4 right-4 rounded-[27px] bg-[#C0C0C2] px-3 py-2">
                                            <span class="text-body-sm-bold text-white">Sold out</span>
                                        </div>
                                    <?php endif;?>
                                </div>
                                <div class="p-6 flex bg-white min-h-[255px] lg:min-h-[285px] 3xl:min-h-[312px]">
                                    <div class="flex flex-col flex-1 gap-3">
                                        <div class="flex flex-col gap-1">
                                            <!-- <p class="text-body-sm-regular text-neutral-500">SUREMEAL</p> -->
                                            <h2 class="min-h-2lh truncate-2row text-heading-h6 text-gray-9"><?= $product_post->post_title ?>
                                            </h2>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <div class="flex items-center gap-1">
                                                <?= renderStarRatingByProductId($product_post->ID) ?>
                                            </div>
                                            <p class="text-body-sm-regular text-neutral-500"><?= countReview($product_post->ID) ?> Reviews</p>
                                        </div>
                                        <p class="min-h-3lh truncate-3row text-body-md-regular text-neutral-500">
                                            <?= $des ?>
                                        </p>
                                        <div class="flex justify-between items-end gap-2">
                                            <?php if ($sale_price) :?>
                                            <div>
                                                <p class="text-heading-h7 text-gray-9"><?= formatBalance($sale_price) ?></p>
                                                <div class="flex gap-2 items-center">
                                                    <span class="text-[#898384] text-[14px]"><s><?= formatBalance($price) ?></s></span>
                                                    <div class="flex rounded-full items-center"
                                                        style="background: linear-gradient(90deg, rgba(255, 219, 111, 0) 0%, #FFD600 100%);">
                                                        <figure>
                                                            <img src="<?= $url ?>/assets/image/icon/flashsale.svg" alt="">
                                                        </figure>
                                                        <span
                                                            class="block text-[#E4626F] font-bold text-[14px] pr-3">-<?= number_format($percentDecrease, 2) ?>%</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php else:?>
                                                <div>
                                                    <p class="text-heading-h7 text-gray-9"><?= formatBalance($price) ?></p>
<!--                                                    <div class="flex gap-2 items-center">-->
<!--                                                        <span class="text-[#898384] text-[14px]"><s>--><?//= formatBalance($price) ?><!--</s></span>-->
<!--                                                        <div class="flex rounded-full items-center"-->
<!--                                                             style="background: linear-gradient(90deg, rgba(255, 219, 111, 0) 0%, #FFD600 100%);">-->
<!--                                                            <figure>-->
<!--                                                                <img src="--><?//= $url ?><!--/assets/image/icon/flashsale.svg" alt="">-->
<!--                                                            </figure>-->
<!--                                                            <span-->
<!--                                                                    class="block text-[#E4626F] font-bold text-[14px] pr-3">---><?//= number_format($percentDecrease, 2) ?><!--%</span>-->
<!--                                                        </div>-->
<!--                                                    </div>-->
                                                </div>
                                            <?php endif;?>
                                            <button
                                                class="max-w-[134px] whitespace-nowrap button bg-primary text-body-md-semibold text-white buy-now"
                                                data-id="<?= $product_post->ID ?>" data-link="<?php echo get_permalink($product_post->ID) ?>"
                                                data-img="<?php echo checkImage($product_post->ID) ?>"
                                                data-price="<?php echo !empty($sale_price) ? $sale_price : $price; ?>"
                                                data-title="<?= $product->post_title ?>" data-instock="<?= $instock ?>"
                                                data-weight="<?= get_field('pound', $product_post->ID) ?>"
                                                data-pack="<?= $qty ?>"><?php pll_e('Buy Now') ?></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php if ($total_exclusive > 3): ?>
                    <button class="mt-9 w-full gap-4 text-body-md-semibold text-secondary flex justify-center view-more-btn">
                        <img src="<?= $url ?>/assets/image/icon/double-chev-down-20-second.svg" alt="">
                        <span><?php pll_e('View more products') ?></span>
                    </button>
                <?php endif ?>
            </div>
        </div>
        <?php endif;?>

        <div class="my-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <?php if ($promotion_1): ?>
                    <div data-aos="zoom-in-up" data-aos-duration="1500" class="rounded-xl relative">
                        <?php if($promotion_1['image']): ?>
                            <figure><img src="<?= $promotion_1['image'] ?>" alt="img1"></figure>
                        <?php endif; ?>
                        <?php if($promotion_1['url'] != '' && $promotion_1['btn_text'] != ''): ?>
                            <a href="<?= $promotion_1['url'] ?>"
                                class="scale-[.8] md:scale-[1] absolute left-[37.5%] md:left-[40.5%] lg:left-[40%] bottom-[10%] md:bottom-[17%] 
                                lg:bottom-[10%] xl:bottom-[13%] 2xl:bottom-[15%] button p-12-20 bg-white text-body-md-semibold 
                                text-secondary">
                                <?= $promotion_1['btn_text'] ?>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif ?>
                <?php if ($promotion_2): ?>
                    <div data-aos="zoom-in-up" data-aos-duration="1500" class="rounded-xl relative">
                        <?php if($promotion_2['image']): ?>
                            <figure><img src="<?= $promotion_2['image'] ?>" alt="img1"></figure>
                        <?php endif; ?>
                        <?php if($promotion_2['url'] != '' && $promotion_2['btn_text'] != ''): ?>
                            <a href="<?= $promotion_2['url'] ?>"
                                class="scale-[.8] md:scale-[1] absolute left-[39%] md:left-[42.5%] lg:left-[42%] bottom-[10%] md:bottom-[17%] lg:bottom-[10%] xl:bottom-[13%] 2xl:bottom-[15%] button p-12-20 bg-white text-body-md-semibold text-secondary">
                                <?= $promotion_2['btn_text'] ?>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif ?>
            </div>
        </div>

        <div class="mt-8 mb-20">
            <span class="text-gray-8 text-neutral-700 font-semibold text-heading-h3-5"><?php pll_e('Promotional products') ?></span>
            <div data-aos="fade-up" data-aos-duration="1500" class="my-8 gap-8 flex flex-col items-center">
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4  gap-6">
                    <?php foreach ($args_post as $key => $value) :
                        $price = get_field('price', $value->ID);
                        $sale_price = get_field('sale_price', $value->ID);
                        $term_list = get_the_terms($value->ID, 'category_product');
                        $des = get_field('short_description', $value->ID);
                        $instock = get_field('instock', $value->ID);
                        if (empty($instock)) {
                            $instock = 0;
                        }

                        
                        // Get dealer discount if dealer is logged in
                        $dealer_discount = $dealer_id ? get_dealer_discount($dealer_id, $value->ID) : null;

                        // Calculate final price based on dealer discount
                        $final_price = $price;
                        if ($dealer_discount) {
                            // Calculate dealer discount price from original price
                            $dealer_price = calculate_dealer_price($price, $dealer_discount);
                            // If there's a sale price, compare it with dealer price
                            if ($sale_price) {
                                $final_price = min($dealer_price, $sale_price);
                            } else {
                                $final_price = $dealer_price;
                            }
                        } else {
                            // If no dealer discount, use sale price if available
                            $final_price = $sale_price ? $sale_price : $price;
                        }
                    ?>
                        <div class="img-hover rounded-3xl overflow-hidden shadow-2">
                            <div class="image max-h-[232px] overflow-hidden deport-product-white flex items-center justify-center">
                                <a href="<?= get_permalink($value->ID) ?>">
                                    <figure class="figure-30-23">
                                        <img src="<?= checkImage($value->ID) ?>" alt="product">
                                    </figure>
                                </a>
                                <?php if ($instock <= 0) :?>
                                    <div class=" absolute top-4 right-4 rounded-[27px] bg-[#C0C0C2] px-3 py-2">
                                        <span class="text-body-sm-bold text-white">Sold out</span>
                                    </div>
                                <?php endif;?>
                            </div>
                            <div class="px-6 pt-4 pb-5 flex bg-white min-h-[275px] 3xl:min-h-[300px]">
                                <div class="flex flex-col flex-1 gap-3">
                                    <div class="flex flex-col gap-1">
                                        <?php foreach ($term_list as $keys => $val) :
                                            if ($val->name === 'SureMeal' || $val->name === 'Supplement Depot') :
                                                ?>
                                                <p class="text-body-sm-regular text-neutral-500"><?= $val->name ?></p>
                                                <?php
                                                break; // Thoát khỏi vòng lặp ngay sau khi tìm thấy danh mục
                                            endif;
                                        endforeach; ?>
                                        <a href="<?= get_permalink($value->ID) ?>">
                                            <h2 class="text-heading-h6 text-gray-9 min-h-2lh truncate-2row">
                                                <?= $value->post_title ?></h2>
                                        </a>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div class="flex items-center gap-1">
                                            <?= renderStarRatingByProductId($value->ID) ?>
                                        </div>
                                        <p class="text-body-sm-regular text-neutral-500"><?= countReview($value->ID) ?> <?php pll_e('Reviews') ?></p>
                                    </div>
                                    <p class="min-h-3lh truncate-3row text-body-md-regular text-neutral-500">
                                        <?= $des ?>
                                    </p>
                                    <div class="flex gap-3 items-center">
                                        <span class="text-body-sm-regular text-neutral-500">From</span>
                                        <?php if($price): ?>
                                            <?php if ($final_price < $price): ?>
                                                <div class="flex items-center gap-2">
                                                    <p class="text-body-md-medium text-neutral-500 line-through">
                                                        <?= formatBalance($price) ?>
                                                    </p>
                                                    <p class="text-heading-h7 text-gray-9">
                                                        <?= formatBalance($final_price) ?>
                                                    </p>
                                                </div>
                                            <?php else: ?>
                                                <div class="flex items-center gap-2">
                                                    <p class="text-heading-h7 text-gray-9">
                                                        <?= formatBalance($price) ?>
                                                    </p>
                                                </div>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            0
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="pagination">
                    <?php
                    $total_posts = $product_query->found_posts;
                    $max_pages = $product_query->max_num_pages;

                    // Hiển thị liên kết phân trang
                    echo paginate_links(array(
                        'base'      => str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999))),
                        'format'    => '?paged=%#%',
                        'current'   => max(1, get_query_var('paged')), // Trang hiện tại
                        'total'     => $max_pages,
                        'prev_text' => __(' <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"
                                fill="none">
                                <path
                                    d="M10.3537 12.6463C10.4001 12.6927 10.437 12.7479 10.4621 12.8086C10.4872 12.8693 10.5002 12.9343 10.5002 13C10.5002 13.0657 10.4872 13.1308 10.4621 13.1915C10.437 13.2521 10.4001 13.3073 10.3537 13.3538C10.3072 13.4002 10.252 13.4371 10.1914 13.4622C10.1307 13.4873 10.0656 13.5003 9.99991 13.5003C9.93421 13.5003 9.86915 13.4873 9.80846 13.4622C9.74776 13.4371 9.69261 13.4002 9.64615 13.3538L4.64615 8.35375C4.59967 8.30732 4.56279 8.25217 4.53763 8.19147C4.51246 8.13077 4.49951 8.06571 4.49951 8C4.49951 7.9343 4.51246 7.86923 4.53763 7.80853C4.56279 7.74783 4.59967 7.69269 4.64615 7.64625L9.64615 2.64625C9.73997 2.55243 9.86722 2.49973 9.99991 2.49973C10.1326 2.49973 10.2598 2.55243 10.3537 2.64625C10.4475 2.74007 10.5002 2.86732 10.5002 3C10.5002 3.13269 10.4475 3.25993 10.3537 3.35375L5.70678 8L10.3537 12.6463Z"
                                    fill="#0F0F0F" />
                            </svg>'),
                        'next_text' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none"><path d="M11.3538 8.35375L6.35378 13.3538C6.30733 13.4002 6.25218 13.4371 6.19148 13.4622C6.13079 13.4873 6.06573 13.5003 6.00003 13.5003C5.93434 13.5003 5.86928 13.4873 5.80859 13.4622C5.74789 13.4371 5.69274 13.4002 5.64628 13.3538C5.59983 13.3073 5.56298 13.2521 5.53784 13.1915C5.5127 13.1308 5.49976 13.0657 5.49976 13C5.49976 12.9343 5.5127 12.8693 5.53784 12.8086C5.56298 12.7479 5.59983 12.6927 5.64628 12.6463L10.2932 8L5.64628 3.35375C5.55246 3.25993 5.49976 3.13269 5.49976 3C5.49976 2.86732 5.55246 2.74007 5.64628 2.64625C5.7401 2.55243 5.86735 2.49973 6.00003 2.49973C6.13272 2.49973 6.25996 2.55243 6.35378 2.64625L11.3538 7.64625C11.4003 7.69269 11.4372 7.74783 11.4623 7.80853C11.4875 7.86923 11.5004 7.9343 11.5004 8C11.5004 8.06571 11.4875 8.13077 11.4623 8.19147C11.4372 8.25217 11.4003 8.30732 11.3538 8.35375Z" fill="#0F0F0F"/></svg>',
                    ));
                    ?>
                    <?php wp_reset_query(); ?>
                </div>
            </div>
        </div>
    </div>

    <?php
    get_template_part('template-parts/support-info');
    ?>
</main>
<?php get_footer() ?>
<script>
    const endTime = <?= $endTimestamp * 1000; ?>; // Chuyển đổi timestamp sang milliseconds
    const currentTime = <?= $currentTimestamp * 1000; ?>;

    // Bắt đầu countdown
    const countdownElement = document.getElementById('countdown');
    const hoursElement = document.getElementById('hours');
    const minutesElement = document.getElementById('minutes');
    const secondsElement = document.getElementById('seconds');

    function updateCountdown() {
        const now = new Date().getTime();
        const timeRemaining = endTime - now;

        if (timeRemaining <= 0) {
            hoursElement.textContent = '00';
            minutesElement.textContent = '00';
            secondsElement.textContent = '00';
            clearInterval(countdownInterval);
            return;
        }

        const hours = Math.floor((timeRemaining % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((timeRemaining % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((timeRemaining % (1000 * 60)) / 1000);

        hoursElement.textContent = String(hours).padStart(2, '0');
        minutesElement.textContent = String(minutes).padStart(2, '0');
        secondsElement.textContent = String(seconds).padStart(2, '0');
    }

    // Cập nhật mỗi giây
    const countdownInterval = setInterval(updateCountdown, 1000);

    // Cập nhật lần đầu ngay lập tức
    updateCountdown();
</script>
<script>
    $(".buy-now").on("click", function() {
        buyNow($(this));
    });
    document.addEventListener('DOMContentLoaded', function() {
        // Lấy tất cả các phần tử tab-promotion-item
        const tabPromotionItems = document.querySelectorAll('.tab-promotion-item');

        // Lấy tất cả các phần tử tab nội dung
        const tabContents = document.querySelectorAll('.tab-content');

        // Lấy tất cả các phần tử tab-promotion
        const tabPromotions = document.querySelectorAll('.tab-promotion');

        tabPromotionItems.forEach(item => {
            item.addEventListener('click', function() {
                // Lấy giá trị data-tabPromotion của tab được click
                const targetTab = this.getAttribute('data-tabPromotion');

                // Loại bỏ class "active" khỏi tất cả các tab-promotion-item, tab-content, và tab-promotion
                tabPromotionItems.forEach(tab => tab.classList.remove('active'));
                tabContents.forEach(content => content.classList.remove('active'));
                tabPromotions.forEach(promotion => promotion.classList.remove('active'));

                // Thêm class "active" cho tab được click
                this.classList.add('active');

                // Tìm tab-content có cùng data-tabPromotion và kích hoạt nó
                const targetContent = document.querySelector(`.tab-content[data-tabPromotion="${targetTab}"]`);
                if (targetContent) {
                    targetContent.classList.add('active');
                }

                // Tìm tab-promotion có cùng data-tabPromotion và kích hoạt nó
                const targetPromotion = document.querySelector(`.tab-promotion[data-tabPromotion="${targetTab}"]`);
                if (targetPromotion) {
                    targetPromotion.classList.add('active');
                }
            });
        });
    });
    const promotionItems = document.querySelectorAll('.promotions-text');
    promotionItems[0].style.borderBottomColor = '#0E74BC'
    promotionItems[0].classList.add("active")
    promotionItems.forEach(item => {
        item.addEventListener('click', () => {
            promotionItems.forEach(i => {
                i.classListNaNpxove('active');
                i.style.borderBottomColor = '#E5E7EB'
            });
            item.classList.add('active');
            item.style.borderBottomColor = '#0E74BC';
        });
    });
    const swiper = new Swiper('#home-swiper', {
        // Optional parameters
        slidesPerView: 1,
        spaceBetween: 0,
        // direction: getDirection(),
        // centeredSlides: true,
        // roundLengths: true,
        loop: true,
        autoplay: true,

        // If we need pagination
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },

        // Navigation arrows
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },

        // And if we need scrollbar
        scrollbar: {
            el: '.swiper-scrollbar',

        },
    });


</script>

<style>
    .promotions-text {
        cursor: pointer;
        width: 100%;
        text-align: center;
        padding: 20px 0px;
        color: #6B7280;
        font-weight: 400;
        border-bottom: 2px;
        border-style: solid;
        border-color: #E5E7EB;
    }

    .promotions-text.active {
        font-weight: 600;
        color: #0E74BC;
        border-color: #0E74BC;

    }

    .deport-product-red {
        background: linear-gradient(180deg, #AA2424 0%, #FF6363 100%), linear-gradient(180deg, #FF7070 0%, #EFABAB 100%);
    }

    .deport-product-yellow {
        background: linear-gradient(180deg, #CE8A1C 0%, #F3B773 100%);
    }

    .deport-product-purple {
        background: linear-gradient(180deg, #B137B1 0%, #E862D8 100%), linear-gradient(180deg, #494CA2 0%, #6D79C5 100%);
    }

    .deport-product-white {
        background-color: #fff;
    }

    .view-more-btn svg {
        transition: transform 0.3s ease;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const viewMoreButtons = document.querySelectorAll('.view-more-btn');

        viewMoreButtons.forEach(button => {
            button.addEventListener('click', function() {
                const tabPromotion = this.closest('.tab-promotion');
                const products = tabPromotion.querySelectorAll('.product-item');
                const isShowingAll = this.classList.contains('view-less');

                if (isShowingAll) {
                    // Trở về chế độ hiển thị 3 sản phẩm
                    products.forEach((product, index) => {
                        if (index >= 3) {
                            product.classList.add('hidden');
                        }
                    });

                    this.classListNaNpxove('view-less');
                    this.querySelector('span').textContent = '<?php pll_e('View more products') ?>';
                    this.querySelector('svg').style.transform = 'rotate(0deg)';
                } else {
                    // Hiển thị tất cả sản phẩm
                    products.forEach(product => {
                        product.classListNaNpxove('hidden');
                    });

                    this.classList.add('view-less');
                    this.querySelector('span').textContent = '<?php pll_e('View less products') ?>';
                    this.querySelector('svg').style.transform = 'rotate(180deg)';
                }
            });
        });
    });
</script>