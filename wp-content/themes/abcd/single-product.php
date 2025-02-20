<?php
$post_id = get_the_ID();
$url = get_template_directory_uri();
get_header();
$title = get_the_title();
$img_des = get_field('img_des', $post_id);
$price = get_field('price', $post_id);
$sale_price = get_field('sale_price', $post_id);
$list_promotion = get_field('list_promotion', $post_id);
$promo = $list_promotion[0]['promotion'];
function get_affiliate_discount($product_id)
{
    if (!isset($_COOKIE['distribution_code'])) {
        return null;
    }

    global $wpdb;
    $distribution_code = sanitize_text_field($_COOKIE['distribution_code']);

    $affiliate = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM wp_affiliate WHERE distribution_code = %s AND status = 1",
        $distribution_code
    ));

    if (!$affiliate) {
        return null;
    }

    // Decode the product information JSON
    $product_info = json_decode($affiliate->product_infomation, true);
    if (!is_array($product_info)) {
        return null;
    }

    // Find if this product is in the affiliate's product list
    $product_entry = array_filter($product_info, function ($item) use ($product_id) {
        return $item['id'] == $product_id;
    });

    if (empty($product_entry)) {
        return null;
    }

    return [
        'discount_percentage' => $affiliate->discount,
        'product_price' => reset($product_entry)['price']
    ];
}
// Add dealer discount check functionality
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

// Check for dealer login
$authenticated_dealer = validate_dealer_token();
$dealer_id = null;
if ($authenticated_dealer) {
    $dealer_id = $authenticated_dealer->ID;
}

// Get dealer discount if available
$dealer_discount = $dealer_id ? get_dealer_discount($dealer_id, $post_id) : null;

// Calculate final price based on dealer discount
$final_price = $price;
$show_original_price = false;
$discount_percentage = 0;

if ($authenticated_dealer) {
    // If dealer is logged in, use dealer pricing logic
    $dealer_discount = get_dealer_discount($dealer_id, $post_id);
    if ($dealer_discount) {
        $dealer_price = calculate_dealer_price($price, $dealer_discount);
        if ($sale_price) {
            $final_price = min($dealer_price, $sale_price);
        } else {
            $final_price = $dealer_price;
        }
        $show_original_price = $final_price < $price;
    } else {
        $final_price = $sale_price ? $sale_price : $price;
        $show_original_price = $sale_price && $sale_price < $price;
    }
} else {
    // Check for affiliate discount if no dealer is logged in
    $affiliate_info = get_affiliate_discount($post_id);
    if ($affiliate_info) {
        // Calculate affiliate discounted price
        $affiliate_base_price = floatval($affiliate_info['product_price']);
        $discount_percentage = floatval($affiliate_info['discount_percentage']);
        $affiliate_price = $affiliate_base_price * (1 - ($discount_percentage / 100));

        // Compare with sale price if exists
        if ($sale_price) {
            $final_price = min($affiliate_price, $sale_price);
        } else {
            $final_price = $affiliate_price;
        }
        $show_original_price = $final_price < $affiliate_base_price;
        $price = $affiliate_base_price; // Show affiliate base price as original price
    } else {
        // No affiliate discount, use regular sale price logic
        $final_price = $sale_price ? $sale_price : $price;
        $show_original_price = $sale_price && $sale_price < $price;
    }
}

// Calculate discount percentage
if ($final_price < $price) {
    $discount_percentage = (($price - $final_price) / $price) * 100;
}

if (!empty($sale_price)) {
    $percentDecrease = (($price - $sale_price) / $price) * 100;
}
$products_of_the_same_type = get_field('products_of_the_same_type', $post_id);
$authenticated_user = validate_user_token();
$id_user = $authenticated_user->ID;
$qty = get_field('quantity', $post_id);
//print_r($products_of_the_same_type);die();
$packJson = json_encode($products_of_the_same_type, true);
$pond = get_field('pound', $post_id);

$img = checkImage($post_id);
if ($packJson == false) {
    $arg = array(
        'ID' => $post_id,
        'post_title' => $title,
        'post_image' => $img,
        'price' => $price,
        'pack' => $qty
    );
    $packJson = json_encode($arg, true);
} else {
    foreach ($products_of_the_same_type as $key => $p) {
        $qtys = get_field('quantity', $p->ID);
        $p->pack = $qtys;
    }
    $packJson = json_encode($products_of_the_same_type, true);
}

$list_promotion = get_field('list_promotion', $post_id);
$product_info = get_field('product_info', $post_id);
$instock = get_field('instock', $post_id);
if (empty($instock)) {
    $instock = 0;
}
$info = $product_info['product_info'];
$benefit = $product_info['benefit'];
$use = $product_info['use'];
$term_list = get_the_terms($post_id, 'category_product');
if (pll_current_language() == 'en') {
    $args = new WP_Query(array(
        'post_type'      => 'product',
        'posts_per_page' => -1,
        'paged' => $paged,
        'post_status' => 'publish',
        'order' => 'DESC',
        'orderby' => 'date',
        'tax_query' => array(                     //(array) - Lấy bài viết dựa theo taxonomy
            'relation' => 'AND',                      //(string) - Mối quan hệ giữa các tham số bên trong, AND hoặc OR
            array(
                'taxonomy' => 'category_product',                //(string) - Tên của taxonomy
                'field' => 'slug',                    //(string) - Loại field cần xác định term của taxonomy, sử dụng 'id' hoặc 'slug'
                'terms' => $term_list,    //(int/string/array) - Slug của các terms bên trong taxonomy cần lấy bài
                'include_children' => true,           //(bool) - Lấy category con, true hoặc false
                'operator' => 'IN'                   //(string) - Toán tử áp dụng cho mảng tham số này. Sử dụng 'IN' hoặc 'NOT IN'
            )
        ),
        'post__not_in' => array($post_id)
    ));
} elseif (pll_current_language() == 'vn') {
    $args = new WP_Query(array(
        'post_type'      => 'product',
        'posts_per_page' => -1,
        'paged' => $paged,
        'post_status' => 'publish',
        'order' => 'DESC',
        'orderby' => 'date',
        'meta_query'     => array(
            array(
                'key'     => 'view_product',
                'value'   => true,
                'compare' => '='
            ),
        ),
        'post__not_in' => array($post_id)
    ));
}
$args_post = $args->posts;
$total_post = count($args_post);
$servings = get_field('servings', $post_id);
//Lấy data bảng rivew
global $wpdb;
$table_name = $wpdb->prefix . 'review';

$reviews = $wpdb->get_results($wpdb->prepare(
    "SELECT * FROM $table_name WHERE id_product = %d AND status = 1 ORDER BY created_at DESC",
    $post_id
));
$id = '1212';
$Like_view = $wpdb->get_results($wpdb->prepare(
    "SELECT * FROM liked_review WHERE id_review =" . $id
));

// Hiển thị danh sách đánh giá
if ($reviews) {
    $totalRating = 0;
    $totalReviews = count($reviews);

    // Duyệt qua tất cả các review và tính tổng rating
    foreach ($reviews as $review) {
        $totalRating += $review->rating;
    }

    // Tính trung bình rating
    $averageRating = $totalRating / $totalReviews;

    // Hiển thị kết quả
}
// Tình số lượng sao

$ratings = $wpdb->get_results($wpdb->prepare(
    "SELECT rating, COUNT(*) as total 
     FROM $table_name 
     WHERE id_product = %d AND status = 1 
     GROUP BY rating",
    $post_id
));


// Khởi tạo các biến cho từng mức rating
$rating_1 = 0;
$rating_2 = 0;
$rating_3 = 0;
$rating_4 = 0;
$rating_5 = 0;
$total_ratings = 0;

// Duyệt qua kết quả và tính tổng
foreach ($ratings as $rating) {
    $total_ratings += $rating->total; // Tính tổng số rating
    switch ($rating->rating) {
        case 1:
            $rating_1 = $rating->total;
            break;
        case 2:
            $rating_2 = $rating->total;
            break;
        case 3:
            $rating_3 = $rating->total;
            break;
        case 4:
            $rating_4 = $rating->total;
            break;
        case 5:
            $rating_5 = $rating->total;
            break;
    }
}

// Tính phần trăm
$percent_1 = ($total_ratings > 0) ? ($rating_1 / $total_ratings) * 100 : 0;
$percent_2 = ($total_ratings > 0) ? ($rating_2 / $total_ratings) * 100 : 0;
$percent_3 = ($total_ratings > 0) ? ($rating_3 / $total_ratings) * 100 : 0;
$percent_4 = ($total_ratings > 0) ? ($rating_4 / $total_ratings) * 100 : 0;
$percent_5 = ($total_ratings > 0) ? ($rating_5 / $total_ratings) * 100 : 0;
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
                    <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                        <a href="/product" class="text-secondary hover:text-primary" itemprop="item">
                            <span itemprop="name"><?php pll_e('Products') ?></span>
                        </a>
                        <meta itemprop="position" content="1" />
                    </li>
                    <span>/</span>
                    <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" aria-current="page">
                        <span itemprop="name"><?= $title ?></span>
                        <meta itemprop="position" content="4" />
                    </li>

                </ol>
            </nav>
            <!-- <h1 class="text-heading-h2 tex-gray-9">Products</h1> -->
        </div>
    </section>

    <style>
        .swiper .swiper-slide {
            border-radius: 20px;
        }

        .flickity-prev-next-button svg {
            display: none;
        }

        .flickity-prev-next-button.next {
            z-index: 2;
            position: absolute;
            top: 50%;
            right: 0;
            transform: translate(50%, -50%);
            width: 36px;
            height: 36px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            border-radius: 8px;
            background: #FFF;
            box-shadow: 0px 4px 20px 0px rgba(219, 219, 219, 0.49);
        }

        .flickity-prev-next-button.next:after {
            content: "";
            width: 20px;
            height: 20px;
            background: url(http://suremeal.qixtech.com/wp-content/themes/abcd/assets/image/icon/arrow-right-20-second.svg) no-repeat;
        }

        .flickity-prev-next-button.previous {
            z-index: 2;
            position: absolute;
            top: 50%;
            left: 0;
            transform: translate(-50%, -50%);
            width: 36px;
            height: 36px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            border-radius: 8px;
            background: #FFF;
            box-shadow: 0px 4px 20px 0px rgba(219, 219, 219, 0.49);
        }

        .flickity-prev-next-button.previous:after {
            content: "";
            width: 20px;
            height: 20px;
            background: url(http://suremeal.qixtech.com/wp-content/themes/abcd/assets/image/icon/arrow-left-20-second.svg) no-repeat;
        }

        .price-feature {
            display: flex;
            padding: 16px 24px;
            flex-direction: column;
            gap: 12px;
            border-radius: 12px;
            border: 1px solid #FDE68A;
            background: linear-gradient(180deg, #FFFBEC 0%, rgba(255, 255, 255, 0.00) 100%);
        }

        .feature-promo {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .feature-promo li {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .feature-promo li:before {
            content: "";
            background: url(http://suremeal.qixtech.com/wp-content/themes/abcd/assets/image/icon/check-round.svg) no-repeat;
            min-width: 24px;
            height: 24px;
        }

        .mini-item {
            border-radius: 20px;
            border: 1px solid #D1D5DB;
        }

        .is-selected .mini-item {
            border: 2px solid #0E74BC;
        }

        table td {
            border: 1px solid #000;
            padding: 2px 4px;
        }

        #product-info ul, #product-info ol, #benefit ul, #benefit ol, #use ul, #use ol {
            list-style: initial;
            list-style-position: inside;
        }
    </style>

    <section>
        <div class="container">
            <div class="p-6 lg:p-10 rounded-xl bg-white flex flex-wrap gap-8 lg:gap-12">
                <div data-aos="fade-right" data-aos-duration="1500" class="relative w-full lg:w-1/2 xl:max-w-[553px] flex flex-col gap-4">
                    <div class="carousel carousel-main" data-flickity='{ "autoPlay": true , "wrapAround": true, "pageDots": false }'>
                        <?php foreach ($img_des as $key => $im) : ?>
                            <div class="carousel-cell">
                                <div class="rounded-3xl">
                                    <a href="<?= $im ?>" class="MagicZoom">
                                        <figure class="figure-53-43">
                                            <img src="<?= $im ?>" alt="product">
                                        </figure>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="hidden md:block carousel carousel-nav  mt-4"
                        data-flickity='{ "asNavFor": ".carousel-main", "contain": true, "pageDots": false, "prevNextButtons": false }'>
                        <?php
                        $count_img = count($img_des);
                        foreach ($img_des as $key => $im) : ?>
                            <div class="carousel-cell w-[173px] mx-2 p-1">
                                <div class="mini-item overflow-hidden">
                                    <figure class="figure-17-14">
                                        <img src="<?= $im ?>" alt="product">
                                    </figure>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div data-aos="fade-left" data-aos-duration="1500" class="flex-1 flex flex-col gap-6">
                    <div class="w-full flex flex-col gap-8">
                        <div class="flex flex-col md:flex-row gap-2">
                            <div class="flex-1">
                                <h2 class="text-heading-h3-5 font-medium text-black"><?= $title ?>
                                </h2>
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center gap-1 md:gap-3">
                                        <?php if (!empty($servings)) : ?> <p class="text-body-md-regular text-neutral-500"><?= $servings  ?> <?php pll_e('servings') ?> </p>
                                            <div class="w-1 h-1 rounded-full bg-neutral-400"></div><?php endif; ?>
                                        <div class="flex items-center gap-1">
                                            <!--                                            --><? //= renderStarRatingByProductId($post_id) 
                                                                                                ?>

                                            <figure><img src="<?= $url ?>/assets/image/icon/star-second.svg" alt="star icon"></figure>
                                            <figure><img src="<?= $url ?>/assets/image/icon/star-second.svg" alt="star icon"></figure>
                                            <figure><img src="<?= $url ?>/assets/image/icon/star-second.svg" alt="star icon"></figure>
                                            <figure><img src="<?= $url ?>/assets/image/icon/star-second.svg" alt="star icon"></figure>
                                            <figure><img src="<?= $url ?>/assets/image/icon/star-second.svg" alt="star icon"></figure>
                                        </div>
                                        <div class="w-1 h-1 rounded-full bg-neutral-400"></div>
                                        <p class="text-body-sm-regular text-neutral-500"><?= countReview($post_id) ?> <?php pll_e('Reviews') ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-row md:flex-col gap-3">
                                <a href="https://twitter.com/intent/tweet?text=<?= $title ?>&url=<?php echo urlencode(get_permalink($post_id)); ?>"
                                    target="_blank">
                                    <figure class="w-8 h-8"><img src="<?= $url ?>/assets/image/icon/x-round.svg"
                                            alt="icon">
                                    </figure>
                                </a>
                                <a target="_blank"
                                    href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink($post_id)); ?>">
                                    <figure class="w-8 h-8"><img src="<?= $url ?>/assets/image/icon/fb-round.svg"
                                            alt="icon">
                                    </figure>
                                </a>
                                <a target="_blank"
                                    href="https://pinterest.com/pin/create/button/?url=<?php echo urlencode(get_permalink($post_id)); ?>&media=<?= checkImage($post_id) ?>&description=<?= $title ?>">
                                    <figure class="w-8 h-8"><img src="<?= $url ?>/assets/image/icon/pri-round.svg"
                                            alt="icon">
                                    </figure>
                                </a>
                            </div>
                        </div>
                        <div class="flex flex-col gap-6">
                            <div class="flex items-center gap-4 lg:gap-8">
                                <p class="min-w-[70px] text-body-md-regular"><?php pll_e('Quantity') ?></p>
                                <div class="counter flex items-center justify-center w-[110px] h-10 border border-solid border-[#eee] rounded-lg text-body-sm-medium text-[#121138]">
                                    <button class="decrement px-3 py-1 btn-minus">−</button>
                                    <input type="text" value="1" readonly id="qty-pro" class="quantity w-8 text-center focus:outline-none" />
                                    <button class="increment px-3 py-1 btn-plus">+</button>
                                </div>
                            </div>
                            <?php if (!empty($products_of_the_same_type)) { ?>
                                <div class="flex items-center gap-4 lg:gap-8">
                                    <p class="min-w-[70px] text-body-md-regular"><?php pll_e('Combo') ?></p>
                                    <!-- old version -->
                                    <!-- <div class="flex flex-wrap gap-2">

                                        <?php

                                        foreach ($products_of_the_same_type as $key => $post) :
                                            $pack = get_field('quantity', $post->ID);
                                            $instocks = get_field('instock', $post->ID);
                                            if (empty($instock)) {
                                                $instocks = 0;
                                            }
                                        ?>
                                            <?php if ($post->ID == $post_id) : ?>
                                            <div class="quantity-choose text-body-md-regular <?php if ($instocks <= 0) : ?>disable<?php else: ?>active <?php endif; ?> " <?php if ($instocks <= 0) : ?> data-toggle="tooltip" title="Sold out" <?php endif; ?>>
                                                <?= $pack ?> <?php pll_e('pack') ?>
                                            </div>
                                        <?php else : ?>
                                            <div class="tooltip-wrapper">
                                                <?php if ($instocks <= 0) : ?>
                                                    <div class="tooltip-top">
                                                        <p class="text-body-md-medium text-gray-9"><?php pll_e('Sold out') ?></p>
                                                    </div>
                                                <?php endif; ?>
                                                <a href="<?= get_permalink($post->ID) ?>" class="quantity-choose text-body-md-regular " <?php if ($instocks <= 0) : ?> data-toggle="tooltip" title="Sold out" <?php endif; ?>>
                                                    <?= $pack ?> <?php pll_e('pack') ?>
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                        <?php
                                        endforeach;

                                        ?>
                                    </div> -->
                                    <!-- new version -->
                                    <div class="relative combo-wrapper w-full lg:max-w-[240px]">
                                        <div class="input-combo-product cursor-pointer">
                                            <!-- <p class="input-value text-body-md-regular text-gray-9 truncate-1row">Buy <?= $pack ?>-pack ($5 savings)</p> -->
                                            <p class="input-value text-body-md-regular text-gray-9 truncate-1row"><?= $list_promotion[0]['promotion'] ?></p>
                                        </div>
                                        <div class="combo-wrap shadow-1 transition-all duration-500 ease-in-out hidden absolute top-[100%] flex-col p-3 gap-3 mt-3 w-full bg-white rounded-lg">
                                            <div class="combo-container flex flex-col gap-3 max-h-[350px] custom-scrollbar overflow-y-auto">
                                                <?php
                                                foreach ($products_of_the_same_type as $key => $post) :
                                                    $pack = get_field('quantity', $post->ID);
                                                    $instocks = get_field('instock', $post->ID);
                                                    $promotion = get_field('list_promotion', $post->ID);
                                                    // if (empty($instock)) {
                                                    //     $instocks = 0;
                                                    // }
                                                ?>
                                                    <div class="combo-select <?= $instocks <= 0 ? 'disabled' : '' ?>">
                                                        <a href="<?= get_permalink($post->ID) ?>" style="<?= $instocks <= 0 ? 'cursor: not-allowed;' : '' ?>">
                                                            <p class="text-body-md-regular"><?= $promotion[0]['promotion'] ?></p>
                                                        </a>
                                                    </div>
                                                    <hr class="divider bg-[#C0C0C2]">
                                                <?php endforeach;?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="price-feature">
                            <div class="flex flex-col gap-1">
                                <p class="text-body-lg-medium text-gray-7"><?php pll_e('Buy now at price') ?></p>
                                <?php if ($show_original_price) : ?>
                                    <h2 class="text-heading-h3-5 text-gray-8" data-price="<?= $final_price ?>">
                                        <?= formatBalance($final_price) ?>
                                    </h2>
                                    <div class="flex items-center gap-1">
                                        <p class="line-through text-body-md-regular text-gray-7">
                                            <?= formatBalance($price) ?>
                                        </p>
                                        <span class="text-body-md-medium text-primary">
                                            <?= $discount_percentage ?>%
                                        </span>
                                    </div>
                                <?php else : ?>
                                    <h2 class="text-heading-h3-5 text-gray-8" data-price="<?= $price ?>">
                                        <?= formatBalance($price) ?>
                                    </h2>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($list_promotion)) : ?>
                                <hr class="divider">
                                <div class="py-3 px-5">
                                    <p class="text-body-lg-semibold text-gray-7"><?php pll_e('Featured Promotion') ?></p>
                                    <ul class="feature-promo text-body-md-regular text-gray-7 mt-3">
                                        <?php foreach ($list_promotion as $key => $pro) : ?>
                                            <li><?= $pro['promotion'] ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row gap-3">
                        <button
                            class="button border-primary w-full md:w-1/2 text-body-md-semibold text-primary add-to-cart"
                            data-id="<?= $post_id ?>"
                            data-link="<?php echo get_permalink($post_id) ?>"
                            data-img="<?php echo checkImage($post_id) ?>"
                            data-price="<?= $final_price ?>"
                            data-title="<?= $title ?>"
                            data-instock="<?= $instock ?>"
                            data-promo="<?= $promo ?>"
                            data-weight="<?= get_field('pound', $post_id) ?>"
                            data-pack="<?= $qty ?>"
                            data-quantity="<?= $qty ?>">

                            <svg xmlns="http://www.w3.org/2000/svg" width="25" height="24" viewBox="0 0 25 24"
                                fill="none">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M3.79047 2.29242C3.3997 2.15503 2.97155 2.36044 2.83416 2.7512C2.69678 3.14197 2.90218 3.57012 3.29295 3.7075L3.55416 3.79934C4.22177 4.03406 4.66052 4.18961 4.98336 4.34802C5.28659 4.4968 5.42026 4.61723 5.50832 4.74609C5.59858 4.87818 5.66828 5.0596 5.70761 5.42295C5.74877 5.80316 5.74979 6.29837 5.74979 7.03832L5.74979 9.64C5.74979 12.5816 5.81302 13.5523 6.67943 14.4662C7.54583 15.38 8.94028 15.38 11.7292 15.38H17.0321C18.5931 15.38 19.3736 15.38 19.9253 14.9304C20.477 14.4808 20.6346 13.7164 20.9497 12.1875L21.4495 9.76275C21.7966 8.02369 21.9702 7.15417 21.5262 6.57708C21.0823 6 19.5655 6 17.8805 6H7.24233C7.23564 5.72967 7.22295 5.48373 7.1989 5.26153C7.14517 4.76515 7.02875 4.31243 6.74677 3.89979C6.46259 3.48393 6.08474 3.21759 5.64411 3.00139C5.23203 2.79919 4.70839 2.61511 4.09187 2.39838L3.79047 2.29242ZM13.75 8.25C14.1642 8.25 14.5 8.58579 14.5 9V10.25H15.75C16.1642 10.25 16.5 10.5858 16.5 11C16.5 11.4142 16.1642 11.75 15.75 11.75H14.5V13C14.5 13.4142 14.1642 13.75 13.75 13.75C13.3358 13.75 13 13.4142 13 13V11.75H11.75C11.3358 11.75 11 11.4142 11 11C11 10.5858 11.3358 10.25 11.75 10.25H13V9C13 8.58579 13.3358 8.25 13.75 8.25Z"
                                    fill="#ED1B24" />
                                <path
                                    d="M8.25 18C9.07843 18 9.75 18.6716 9.75 19.5C9.75 20.3284 9.07843 21 8.25 21C7.42157 21 6.75 20.3284 6.75 19.5C6.75 18.6716 7.42157 18 8.25 18Z"
                                    fill="#ED1B24" />
                                <path
                                    d="M17.25 18.0001C18.0784 18.0001 18.75 18.6716 18.75 19.5001C18.75 20.3285 18.0784 21.0001 17.25 21.0001C16.4216 21.0001 15.75 20.3285 15.75 19.5001C15.75 18.6716 16.4216 18.0001 17.25 18.0001Z"
                                    fill="#ED1B24" />
                            </svg>
                            <?php pll_e('Add To Cart') ?>
                        </button>
                        <button class="button bg-primary w-full md:w-1/2 text-body-md-semibold text-white buy-now"
                            data-id="<?= $post_id ?>"
                            data-link="<?php echo get_permalink($post_id) ?>"
                            data-img="<?php echo checkImage($post_id) ?>"
                            data-price="<?= $final_price ?>"
                            data-title="<?= $title ?>"
                            data-instock="<?= $instock ?>"
                            data-promo="<?= $promo ?>"
                            data-weight="<?= get_field('pound', $post_id) ?>"
                            data-pack="<?= $qty ?>"
                            data-quantity="<?= $qty ?>">
                            <?php pll_e('Buy Now') ?></button>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <style>
        .description-tab {
            display: flex;
            padding: 20px 24px;
            align-items: center;
            gap: 8px;
            border-radius: 8px;
            border-left: 4px solid transparent;
            background: #FFF;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .description-tab {
                padding: 12px 16px;

            }
        }

        .description-tab.active {
            border-color: #0E74BC;
            background: linear-gradient(90deg, rgba(2, 128, 206, 0.25) 0%, rgba(255, 255, 255, 0.00) 150.99%);
        }
    </style>
    <section class="mt-6 lg:mt-10">
        <div class="container">
            <div data-aos="zoom-in-up" data-aos-duration="1500" class="flex flex-col md:flex-row gap-6 py-10 px-6 rounded-xl bg-white">
                <div class="relative w-full md:w-1/3 lg:max-w-[304px]">
                    <div class="sticky top-[130px] w-full flex flex-row md:flex-col">
                        <?php if (!empty($info)) : ?>
                            <div class="description-tab active">
                                <p class="text-body-xl-medium text-gray-8"><?php pll_e('Product info') ?></p>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($benefit)) : ?>
                            <div class="description-tab">
                                <p class="text-body-xl-medium text-gray-8"><?php pll_e('Benefit') ?></p>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($use)) : ?>
                            <div class="description-tab">
                                <p class="text-body-xl-medium text-gray-8"><?php pll_e('Use') ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="description-view flex-1 flex flex-col gap-6">
                    <h2 class="text-heading-h4 text-secondary"><?= $title ?></h2>
                    <hr class="divider">
                    <div class="flex flex-col gap-10">
                        <?php if (!empty($info)) : ?>
                            <div id="product-info" class="flex flex-col gap-4">
                                <h3 class="text-heading-h6 text-gray-8"><?php pll_e('Product info') ?></h3>
                                <?= $info ?>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($benefit)) : ?>
                            <div id="benefit" class="flex flex-col gap-4">
                                <h3 class="text-heading-h6 text-gray-8"><?php pll_e('Benefit') ?></h3>
                                <?= $benefit ?>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($use)) : ?>
                            <div id="use" class="flex flex-col gap-4">
                                <h3 class="text-heading-h6 text-gray-8"><?php pll_e('Use') ?></h3>
                                <?= $use ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="mt-6 lg:mt-10">
        <div class="container">
            <div data-aos="fade-up" data-aos-duration="1500" class="flex flex-wrap gap-4 md:gap-6 py-10 px-4 md:px-6 rounded-xl bg-white">
                <div class="flex flex-col gap-6 lg:gap-8 w-full">
                    <div class="flex gap-2 items-end">
                        <h2 class="text-heading-h4 text-black"><?php pll_e('Reviews') ?></h2>
                        <p class="mb-[5px] text-body-md-regular text-gray-7">(<?= countReview($post_id) ?> <?php pll_e('Reviews') ?>)</p>
                    </div>
                    <hr class="divider">
                    <div class="flex flex-col md:flex-row items-center gap-10 lg:gap-20">
                        <div class="">
                            <p class="text-body-md-medium text-gray-7"><?php pll_e('Based on') ?> <?= $totalReviews ?> <?php pll_e('Reviews') ?></p>
                            <div class="flex items-center gap-1 mt-2">
                                <p class="text-heading-h2 text-gray-9"><?= number_format($averageRating, 1) ?></p>
                                <figure class="w-6 h-6"><img src="<?= $url ?>/assets/image/icon/star-yellow.svg"
                                        alt="icon">
                                </figure>
                            </div>
                            <button class="button bg-primary mt-5 text-body-md-semibold text-white open-modal"
                                data-modal="modalReview">
                                <?php pll_e('Write a review') ?>
                            </button>
                        </div>

                        <div class="flex flex-col gap-3">
                            <div class="star-list flex items-center gap-4">
                                <div class="flex items-center gap-1">
                                    <figure><img src="<?= $url ?>/assets/image/icon/star-yellow.svg" alt="icon">
                                    </figure>
                                    <figure><img src="<?= $url ?>/assets/image/icon/star-yellow.svg" alt="icon">
                                    </figure>
                                    <figure><img src="<?= $url ?>/assets/image/icon/star-yellow.svg" alt="icon">
                                    </figure>
                                    <figure><img src="<?= $url ?>/assets/image/icon/star-yellow.svg" alt="icon">
                                    </figure>
                                    <figure><img src="<?= $url ?>/assets/image/icon/star-yellow.svg" alt="icon">
                                    </figure>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: <?= $percent_5 ?>%;"></div>
                                </div>
                                <p class="text-body-md-regular text-gray-7"><?= $rating_5 ?></p>
                            </div>
                            <div class="star-list flex items-center gap-4">
                                <div class="flex items-center gap-1">
                                    <figure><img src="<?= $url ?>/assets/image/icon/star-yellow.svg" alt="icon">
                                    </figure>
                                    <figure><img src="<?= $url ?>/assets/image/icon/star-yellow.svg" alt="icon">
                                    </figure>
                                    <figure><img src="<?= $url ?>/assets/image/icon/star-yellow.svg" alt="icon">
                                    </figure>
                                    <figure><img src="<?= $url ?>/assets/image/icon/star-yellow.svg" alt="icon">
                                    </figure>
                                    <figure><img src="<?= $url ?>/assets/image/icon/star-gray.svg" alt="icon"></figure>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: <?= $percent_4 ?>%;"></div>
                                </div>
                                <p class="text-body-md-regular text-gray-7"><?= $rating_4 ?></p>
                            </div>
                            <div class="star-list flex items-center gap-4">
                                <div class="flex items-center gap-1">
                                    <figure><img src="<?= $url ?>/assets/image/icon/star-yellow.svg" alt="icon">
                                    </figure>
                                    <figure><img src="<?= $url ?>/assets/image/icon/star-yellow.svg" alt="icon">
                                    </figure>
                                    <figure><img src="<?= $url ?>/assets/image/icon/star-yellow.svg" alt="icon">
                                    </figure>
                                    <figure><img src="<?= $url ?>/assets/image/icon/star-gray.svg" alt="icon"></figure>
                                    <figure><img src="<?= $url ?>/assets/image/icon/star-gray.svg" alt="icon"></figure>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: <?= $percent_3 ?>%;"></div>
                                </div>
                                <p class="text-body-md-regular text-gray-7"><?= $rating_3 ?></p>
                            </div>
                            <div class="star-list flex items-center gap-4">
                                <div class="flex items-center gap-1">
                                    <figure><img src="<?= $url ?>/assets/image/icon/star-yellow.svg" alt="icon">
                                    </figure>
                                    <figure><img src="<?= $url ?>/assets/image/icon/star-yellow.svg" alt="icon">
                                    </figure>
                                    <figure><img src="<?= $url ?>/assets/image/icon/star-gray.svg" alt="icon"></figure>
                                    <figure><img src="<?= $url ?>/assets/image/icon/star-gray.svg" alt="icon"></figure>
                                    <figure><img src="<?= $url ?>/assets/image/icon/star-gray.svg" alt="icon"></figure>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: <?= $percent_2 ?>%;"></div>
                                </div>
                                <p class="text-body-md-regular text-gray-7"><?= $rating_2 ?></p>
                            </div>
                            <div class="star-list flex items-center gap-4">
                                <div class="flex items-center gap-1">
                                    <figure><img src="<?= $url ?>/assets/image/icon/star-yellow.svg" alt="icon">
                                    </figure>
                                    <figure><img src="<?= $url ?>/assets/image/icon/star-gray.svg" alt="icon"></figure>
                                    <figure><img src="<?= $url ?>/assets/image/icon/star-gray.svg" alt="icon"></figure>
                                    <figure><img src="<?= $url ?>/assets/image/icon/star-gray.svg" alt="icon"></figure>
                                    <figure><img src="<?= $url ?>/assets/image/icon/star-gray.svg" alt="icon"></figure>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: <?= $percent_1 ?>%;"></div>
                                </div>
                                <p class="text-body-md-regular text-gray-7"><?= $rating_1 ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <p class="text-body-md-regular"><?php pll_e('Sort by') ?></p>
                        <div class="flex flex-wrap gap-3">
                            <div class="comment-choose active" data-sort="most_recent">
                                <?php pll_e('Most recent') ?>
                            </div>
                            <div class="comment-choose" data-sort="highest_rating">
                                <?php pll_e('Highest rating') ?>
                            </div>
                            <div class="comment-choose" data-sort="lowest_rating">
                                <?php pll_e('Lowest rating') ?>
                            </div>
                            <div class="comment-choose" data-sort="most_helpful">
                                <?php pll_e('Most helpful') ?>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col items-center gap-8">
                        <div
                            class="recent-view w-full custom-scrollbar grid grid-cols-1 lg:grid-cols-2 gap-y-8 gap-x-6">
                            <?php foreach ($reviews as $review) :
                                $user = get_user_id($review->id_user);

                            ?>
                                <div class="p-5 rounded-xl flex flex-col gap-4 border border-solid border-neutral-300">
                                    <p class="text-body-lg-regular text-neutral-700"><?= $review->comment ?></p>
                                    <div
                                        class="flex flex-col md:flex-row items-start lg:items-end gap-6 md:gap-10 justify-between">
                                        <div class="flex gap-4">
                                            <?php if (!$user) : ?>
                                                <figure class="w-[68px] h-[68px] rounded-full"><img
                                                        src="<?= get_field('image_no_image', 'option') ?>" alt="icon"></figure>
                                            <?php else: ?>
                                                <figure class="w-[68px] h-[68px] rounded-full"><img
                                                        src="<?= $user->avatar ?>" alt="icon"></figure>
                                            <?php endif; ?>
                                            <div class="flex flex-col gap-2">
                                                <p class="text-heading-h7 text-secondary"><?= $review->name ?></p>
                                                <div class="flex items-center gap-1">
                                                    <?php
                                                    $max_rating = 5; // Tổng số sao
                                                    for ($i = 1; $i <= $max_rating; $i++) :
                                                        $star_image = ($review->rating >= $i) ? 'star-yellow.svg' : 'star-gray.svg';
                                                    ?>
                                                        <figure><img src="<?= $url ?>/assets/image/icon/<?= $star_image ?>"
                                                                alt="icon"></figure>
                                                    <?php endfor; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-4">
                                            <!-- đã like -->
                                            <?= check_and_render_like_status($review->id, $id_user, $post_id) ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>


                        </div>
                        <?php if (countReview($post_id) > 6) : ?>
                            <button class="recent-button button button-trans text-body-md-semibold text-secondary">
                                <figure class="w-6 h-6"><img
                                        src="<?= $url ?>/assets/image/icon/double-chev-down-20-second.svg" alt="icon">
                                </figure>
                                <span><?php pll_e('View more reviews') ?></span>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <section class="py-10 lg:py-20">
        <div class="container flex flex-col gap-6">
            <div data-aos="fade-down" data-aos-duration="1500" class="flex flex-row gap-6 md:items-center justify-between">
                <div class="flex flex-col gap-2 flex-1">
                    <h2 class="text-heading-h3-5 text-gray-8"><?php pll_e('Related Products') ?>
                    </h2>
                </div>
                <?php if ($total_post > 3) : ?>
                    <div class="min-h-16 mt-5 lg:mt-0 flex items-end justify-end gap-3">
                        <button
                            class="swiper-button-prev product button bg-white w-10 h-10 xl:w-16 xl:h-16 p-0 flex items-center justify-center shadow-button">
                            <figure class="w-6 xl:w-8 h-6 xl:h-8 rotate-180"><img
                                    src="<?= $url ?>/assets/image/icon/arrow-right.svg" alt="icon">
                            </figure>
                        </button>
                        <button
                            class="swiper-button-next product button bg-white w-10 h-10 xl:w-16 xl:h-16 p-0 flex items-center justify-center shadow-button">
                            <figure class="w-6 xl:w-8 h-6 xl:h-8"><img src="<?= $url ?>/assets/image/icon/arrow-right.svg"
                                    alt="icon">
                            </figure>
                        </button>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Slider main container -->
            <div data-aos="fade-up" data-aos-duration="1500" id="" class="swiper recently-product">
                <!-- Additional required wrapper -->
                <div class="swiper-wrapper">
                    <!-- Slides -->
                    <?php foreach ($args_post as $key => $val) :
                        $price = get_field('price', $val->ID);

                        $term_list = get_the_terms($val->ID, 'category_product');
                        $des = get_field('short_description', $val->ID);
                        $instock = get_field('instock', $val->ID);
                        if (empty($instock)) {
                            $instock = 0;
                        }
                        $sale_price = get_field('sale_price', $val->ID);

                        // Calculate final price based on dealer discount
                        $final_price = $price;
                        $show_original_price = false;

                        if ($authenticated_dealer) {
                            // If dealer is logged in, use dealer pricing logic
                            $dealer_discount = get_dealer_discount($dealer_id, $val->ID);
                            if ($dealer_discount) {
                                $dealer_price = calculate_dealer_price($price, $dealer_discount);
                                if ($sale_price) {
                                    $final_price = min($dealer_price, $sale_price);
                                } else {
                                    $final_price = $dealer_price;
                                }
                                $show_original_price = $final_price < $price;
                            } else {
                                $final_price = $sale_price ? $sale_price : $price;
                                $show_original_price = $sale_price && $sale_price < $price;
                            }
                        } else {
                            // Check for affiliate discount if no dealer is logged in
                            $affiliate_info = get_affiliate_discount($val->ID);
                            if ($affiliate_info) {
                                // Calculate affiliate discounted price
                                $affiliate_base_price = floatval($affiliate_info['product_price']);
                                $discount_percentage = floatval($affiliate_info['discount_percentage']);
                                $affiliate_price = $affiliate_base_price * (1 - ($discount_percentage / 100));

                                // Compare with sale price if exists
                                if ($sale_price) {
                                    $final_price = min($affiliate_price, $sale_price);
                                } else {
                                    $final_price = $affiliate_price;
                                }
                                $show_original_price = $final_price < $affiliate_base_price;
                                $price = $affiliate_base_price; // Show affiliate base price as original price
                            } else {
                                // No affiliate discount, use regular sale price logic
                                $final_price = $sale_price ? $sale_price : $price;
                                $show_original_price = $sale_price && $sale_price < $price;
                            }
                        }
                    ?>
                        <div class="swiper-slide w-full">
                            <!-- item -->
                            <div class="bg-white rounded-3xl overflow-hidden">
                                <div class="xl:max-h-[232px] overflow-hidden">
                                    <a href="<?= get_permalink($val->ID) ?>">
                                        <figure class="figure-30-23">
                                            <img src="<?= checkImage($val->ID) ?>" alt="product">
                                        </figure>
                                    </a>
                                    <?php if ($instock <= 0) : ?>
                                        <div class=" absolute top-4 right-4 rounded-[27px] bg-[#C0C0C2] px-3 py-2">
                                            <span class="text-body-sm-bold text-white"><?php pll_e('Sold out') ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="flex flex-col gap-3 px-6 pb-[20px] mt-4">
                                    <div class="flex flex-col gap-1">
                                        <?php foreach ($term_list as $keys => $vals) :
                                            if ($vals->name === 'SureMeal' || $vals->name === 'Supplement Depot') :
                                        ?>
                                                <p class="text-body-sm-bold uppercase text-neutral-500"><?= $vals->name ?></p>
                                        <?php
                                                break; // Thoát khỏi vòng lặp ngay sau khi tìm thấy danh mục
                                            endif;
                                        endforeach; ?>
                                        <h2 class="text-heading-h6 text-gray-9 min-h-2lh truncate-2row"><a
                                                href="<?= get_permalink($val->ID) ?>"><?= $val->post_title ?></a></h2>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div class="flex items-center gap-1">
                                            <?= renderStarRatingByProductId($val->ID) ?>
                                        </div>
                                        <p class="text-body-sm-regular text-neutral-500"><?= countReview($val->ID) ?> <?php pll_e('Reviews') ?></p>
                                    </div>
                                    <p class="min-h-3lh truncate-3row text-body-md-regular text-neutral-500"><a
                                            href="<?= get_permalink($val->ID) ?>">
                                            <?= $des ?></a>
                                    </p>
                                    <div class="flex gap-3 items-center">
                                        <span class="text-body-sm-regular text-neutral-500"><?php pll_e('From') ?></span>
                                        <?php if ($show_original_price): ?>
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
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>
    </section>

    <?php
    get_template_part('template-parts/support-info');
    ?>


    <!-- modal việc làm -->
    <div id="modalReview" class="modal-wrapper">
        <div class="modal-container w-900">
            <div class="modal-head">
                <div class="w-full flex justify-between gap-6">
                    <h2 class="text-body-xl-medium text-lg lg:text-xl 2xl:text-2xl text-black-2">
                        <?php pll_e('Write a review') ?>
                    </h2>
                    <div class="close-modal">
                        <figure class="w-6 h-6"><img src="<?= $url ?>/assets/image/icon/close-24.svg" alt="close">
                        </figure>
                    </div>
                </div>
            </div>
            <div class="modal-content">
                <form action="" id="review-product" class="flex flex-col gap-6">
                    <input type="hidden" name="id_product" value="<?= $post_id ?>">
                    <input type="hidden" name="csrfToken" value="<?php echo wp_create_nonce('review_nonce') ?>">
                    <div class="flex flex-col lg:flex-row gap-5">
                        <label class="input-label">
                            <p class="input-title"><?php pll_e('Name') ?>
                                <!-- <span class="text-primary">*</span> -->
                            </p>
                            <input type="text" class="input-field" name="name_user" placeholder="<?php pll_e('Your name') ?>
                            ">
                        </label>
                        <label class="input-label">
                            <p class="input-title">Email
                                <!-- <span class="text-primary">*</span> -->
                            </p>
                            <input type="text" class="input-field" name="email" placeholder="<?php pll_e('Your email') ?>
                            ">
                        </label>
                    </div>
                    <div class="flex flex-col gap-1">
                        <p><?php pll_e('Rating') ?></p>
                        <div class="star-rating  flex gap-[15px]">
                            <img src="<?= $url ?>/assets/image/icon/star-gray.svg" class="star cursor-pointer"
                                data-index="1" alt="star">
                            <img src="<?= $url ?>/assets/image/icon/star-gray.svg" class="star cursor-pointer"
                                data-index="2" alt="star">
                            <img src="<?= $url ?>/assets/image/icon/star-gray.svg" class="star cursor-pointer"
                                data-index="3" alt="star">
                            <img src="<?= $url ?>/assets/image/icon/star-gray.svg" class="star cursor-pointer"
                                data-index="4" alt="star">
                            <img src="<?= $url ?>/assets/image/icon/star-gray.svg" class="star cursor-pointer"
                                data-index="5" alt="star">
                        </div>
                    </div>
                    <label class="input-label">
                        <p class="input-title"><?php pll_e('Title of review') ?>
                            <!-- <span class="text-primary">*</span> -->
                        </p>
                        <input type="text" class="input-field" name="title_review" placeholder="<?php pll_e('Give your review a title') ?>
                        ">
                    </label>
                    <label class="input-label">
                        <p class="input-title"><?php pll_e('How was your overall experience?') ?>
                            <!-- <span class="text-primary">*</span> -->
                        </p>
                        <textarea type="text" class="input-field" id="content_review" name="content_review" rows="5"
                            placeholder="<?php pll_e('Your overall experience') ?>
                        "></textarea>
                    </label>

                    <div class="mx-auto lg:mx-0 mt-2 flex items-center justify-end">
                        <button type="submit" class="button bg-primary text-body-md-semibold text-white submit_review"
                            style="width: 260px;"><?php pll_e('Submit') ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
<?php get_footer() ?>
<script>
    $(document).ready(function() {
        $('[data-toggle="tooltip"]').tooltip();
    });
    $('#qty-pro').on('change', function() {
        $('.add-to-cart').attr('data-quantity', $(this).val());
        $('.buy-now').attr('data-quantity', $(this).val());
    }).trigger('change');
</script>

<!-- swiper script -->
<script defer>
    // asNavFor can be set a selector string
    asNavFor: '.carousel-main'
    // or an element
    asNavFor: $('.carousel-main')[0]
    asNavFor: document.querySelector('.carousel-main')
    // 1st carousel, main
    $('.carousel-main').flickity({}

    );
    // 2nd carousel, navigation
    $('.carousel-nav').flickity({
        asNavFor: '.carousel-main',
        contain: true,
        pageDots: false,
        wrapAround: true,

    });
</script>
<script>
    $(".add-to-cart").on("click", function() {
        addToCart($(this));
    });
    $(".buy-now").on("click", function() {
        buyNow($(this));
    });
    const recentProduct = new Swiper('.recently-product', {
        // Optional parameters
        slidesPerView: 1,
        spaceBetween: 24,
        // direction: getDirection(),
        // centeredSlides: true,
        // roundLengths: true,
        loop: true,
        autoplay: true,
        breakpoints: {
            768: {
                slidesPerView: 2,
            },
            1024: {
                slidesPerView: 3,
            },
            1280: {
                slidesPerView: 4,
            },
        },

        // If we need pagination
        // pagination: {
        //     el: '.swiper-pagination',
        //     clickable: true,
        // },

        // Navigation arrows
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },

        // And if we need scrollbar
        // scrollbar: {
        //     el: '.swiper-scrollbar',
        // },
    });

    $('.btn-minus').addClass('disabled');

    $('.btn-plus').click(function() {
        var value = parseInt($(this).prev('input').val());
        $('.btn-minus').removeClass('disabled');
        $(this).prev('input').val(value + 1).trigger('change'); // Trigger the change event
    });

    $('.btn-minus').click(function() {
        var value = parseInt($(this).next('input').val());
        if (value > 1) {
            $(this).next('input').val(value - 1).trigger('change'); // Trigger the change event
        }
        if (value == 2) {
            $(this).addClass('disabled');
        }
    });

    $(document).ready(function() {
        var value = $('#soluong').val();
        $(window).scroll(function() {
            if ($(window).scrollTop() > $('.section-1-device-detail').height() + 200) {
                $('.col-right').addClass('fixed');
            } else {
                $('.col-right').removeClass('fixed');
            }
            if ($(window).scrollTop() + $(window).height() > $(document).height() - 1200) {
                $('.col-right').addClass('fixed-bot');
            } else {
                $('.col-right').removeClass('fixed-bot');
            }
        });
        if ($('.list-device .item').length > 3) {
            $('.list-device').css('max-width', '54rem');
        }
    });
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('review-product');
        const requiredFields = {
            name_user: '<?php pll_e('Please enter your name user') ?>',
            email: '<?php pll_e('Please enter your email') ?>',
            title_review: '<?php pll_e('Please enter your title review') ?>',
            content_review: '<?php pll_e('Please enter your content review') ?>'
        };

        form.addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent form submission

            // Reset previous error messages
            const existingErrors = form.querySelectorAll('.error-message');
            existingErrors.forEach(error => error.remove());

            // Validate form
            let isValid = true;

            // Basic validation
            for (let field in requiredFields) {
                const input = form.querySelector(`[name="${field}"]`);
                const value = input.value.trim();

                if (!value) {
                    isValid = false;
                    showError(input, requiredFields[field]);
                }

                // Email validation
                if (field === 'email' && value) {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(value)) {
                        isValid = false;
                        showError(input, '<?php pll_e('Please enter a valid email address') ?>');
                    }
                }
            }

            // If validation passes, proceed with submission
            if (isValid) {
                const site_key = '<?php echo get_field('site_key', 'option'); ?>';

                grecaptcha.ready(function() {
                    grecaptcha.execute(site_key, {
                        action: 'review_capcha'
                    }).then(function(token) {
                        const formData = new FormData(form);
                        const activeCount = document.querySelectorAll('.star-rating img.active').length;

                        // Append additional data
                        formData.append('action', 'submitReview');
                        formData.append('action1', 'review_capcha');
                        formData.append('token1', token);
                        formData.append('activeCount', activeCount);

                        // Show loading
                        Swal.fire({
                            title: 'Processing',
                            html: 'Please wait...',
                            didOpen: () => {
                                Swal.showLoading()
                            }
                        });

                        // Send AJAX request
                        fetch('<?= admin_url('admin-ajax.php'); ?>', {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-CSRF-TOKEN': form.querySelector('[name="csrfToken"]').value,
                                    'X-RECAPTCHA-TOKEN': token
                                }
                            })
                            .then(response => response.json())
                            .then(response => {
                                if (response.status === 1) {
                                    Swal.fire({
                                        icon: 'success',
                                        text: response.mess,
                                    }).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'warning',
                                        text: response.mess,
                                    });
                                }
                            })
                            .catch(error => {
                                Swal.fire({
                                    icon: 'error',
                                    text: 'An error occurred. Please try again.'
                                });
                            });
                    });
                });
            }
        });

        // Helper function to show error messages
        function showError(input, message) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'error-message text-red-500 text-sm mt-1';
            errorDiv.textContent = message;
            input.parentNode.appendChild(errorDiv);
        }

        // Star rating functionality
        const starRating = document.querySelector('.star-rating');
        if (starRating) {
            const stars = starRating.querySelectorAll('.star');

            stars.forEach(star => {
                star.addEventListener('click', function() {
                    const rating = this.getAttribute('data-index');

                    // Reset all stars
                    stars.forEach(s => {
                        s.src = '<?= $url ?>/assets/image/icon/star-gray.svg';
                        s.classList.remove('active');
                    });

                    // Fill stars up to selected rating
                    for (let i = 0; i < rating; i++) {
                        stars[i].src = '<?= $url ?>/assets/image/icon/star-yellow.svg';
                        stars[i].classList.add('active');
                    }
                });
            });
        }
    });
    $(document).ready(function() {
        //     $("#review-product").on('submit', function(e) {
        //         e.preventDefault();
        //     }).validate({
        //         rules: {
        //             name_user: {
        //                 required: true
        //             },
        //             email: {
        //                 required: true,
        //                 email: true
        //             },
        //             title_review: {
        //                 required: true
        //             },
        //             content_review: {
        //                 required: true
        //             }
        //         },
        //         messages: {
        //             name_user: {
        //                 required: "Please enter your name user"
        //             },
        //             email: {
        //                 required: "Please enter your email",
        //                 email: "Please enter a valid email address"
        //             },
        //             title_review: {
        //                 required: "Please enter your title review"
        //             },
        //             content_review: {
        //                 required: "Please enter your content review"
        //             }
        //         },
        //         submitHandler: function(form) {
        //             var site_key = '<?php echo get_field('site_key', 'option'); ?>';
        //             var link = 'http://your-ajax-endpoint-url'; // Thay bằng URL của bạn

        //             grecaptcha.ready(function() {
        //                 grecaptcha.execute(site_key, {
        //                     action: 'review_capcha'
        //                 }).then(function(token) {
        //                     var id_user = '<?= $id_user ?>';
        //                     var email = $('input[name="email"]').val();
        //                     var name_user = $('input[name="name_user"]').val();
        //                     var title_review = $('input[name="title_review"]').val();
        //                     var id_product = $('input[name="id_product"]').val();
        //                     var content_review = $('#content_review').val();
        //                     var activeCount = $('.star-rating img.active').length;
        //                     let csrfToken = $('input[name="csrfToken"]').val();
        //                     var formData = new FormData($('#review-product')[0]);
        //                     formData.append('email', email);
        //                     formData.append('name_user', name_user);
        //                     formData.append('id_user', name_user);
        //                     formData.append('title_review', title_review);
        //                     formData.append('id_user', id_user);
        //                     formData.append('activeCount', activeCount);
        //                     formData.append('id_product', id_product);
        //                     formData.append('content_review', content_review);
        //                     formData.append('action', 'submitReview');
        //                     formData.append('action1', 'review_capcha');
        //                     formData.append('token1', token);
        //                     $.ajax({
        //                         url: "<?= admin_url('admin-ajax.php'); ?>",
        //                         type: 'POST',
        //                         data: formData,
        //                         dataType: 'json',
        //                         processData: false,
        //                         contentType: false,
        //                         beforeSend: function(xhr) {
        //                             xhr.setRequestHeader('X-CSRF-TOKEN',
        //                                 csrfToken);
        //                             xhr.setRequestHeader('X-RECAPTCHA-TOKEN',
        //                                 token);
        //                             Swal.fire({
        //                                 title: 'Processing',
        //                                 html: 'Please wait...',
        //                                 didOpen: () => {
        //                                     Swal.showLoading()
        //                                 }
        //                             });
        //                         },
        //                         success: function(response) {
        //                             if (response.status === 1) {
        //                                 Swal.fire({
        //                                     icon: 'success',
        //                                     text: response.mess,
        //                                 }).then(() => {
        //                                     location
        //                                         .reload(); // Di chuyển location.reload() vào trong hàm callback
        //                                 });
        //                             } else {
        //                                 Swal.fire({
        //                                     icon: 'warning',
        //                                     text: response.mess,
        //                                 });
        //                             }
        //                         },
        //                         error: function(xhr) {
        //                             Swal.fire({
        //                                 icon: 'error',
        //                                 text: 'An error occurred. Please try again.'
        //                             });
        //                         }
        //                     });
        //                 });
        //             });
        //         }
        //     });



    });
    $(".click-like").on("click", function() {
        var $this = $(this);
        var review = $this.data("review");
        var user = $this.data("user");
        var product = $this.data("product");

        $.ajax({
            url: "<?= admin_url('admin-ajax.php'); ?>",
            type: "POST",
            data: {
                action: "LikeReview",
                review: review,
                user: user,
                product: product,
            },
            dataType: "json",
            success: function(response) {
                if (response.status === 1) {
                    // Đã like
                    $this.html(response.html);
                    $this.addClass("liked"); // Thêm class nhận diện đã like
                } else if (response.status === 0) {
                    // Bỏ like
                    $this.html(response.html);
                    $this.removeClass("liked"); // Loại bỏ class nhận diện
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: "error",
                    text: "An error occurred. Please try again.",
                });
            },
        });
    });
    // lọc đánh giá
    $(document).ready(function() {
        // Bắt sự kiện khi người dùng click vào một phần tử có lớp 'comment-choose'
        $('.comment-choose').on('click', function() {
            // Lấy giá trị của thuộc tính 'data-sort' từ phần tử được click
            var sortBy = $(this).data('sort');
            var id_post = '<?= $post_id ?>'; // Lấy ID bài viết (ID sản phẩm)

            // Gửi yêu cầu AJAX đến server
            $.ajax({
                url: "<?= admin_url('admin-ajax.php'); ?>",
                type: 'POST',
                data: {
                    action: 'sortByReview',
                    dataSort: sortBy,
                    idProduct: id_post
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 1) {
                        // Cập nhật lại giao diện với HTML mới trả về từ server
                        $('.recent-view').html(response.html);
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            text: 'No reviews found.',
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        text: 'An error occurred. Please try again.'
                    });
                }
            });
        });
    });
</script>

<!-- script filter -->
<script defer>
    document.querySelectorAll('.collapse-title').forEach((title) => {
        title.addEventListener('click', () => {
            const item = title.nextElementSibling; // Giả sử item ngay sau title
            const button = title.querySelector('.collapse-button');

            // Toggle class collapse cho item
            item.classList.toggle('collapsed');

            // Xoay button 180 độ
            if (item.classList.contains('collapse')) {
                button.style.transform = 'rotate(180deg)';
            } else {
                button.style.transform = 'rotate(0deg)';
            }
        });
    });

    document.querySelectorAll('.collapse-check-list').forEach(collapseCheckList => {
        collapseCheckList.addEventListener('click', () => {
            const collapseItem = collapseCheckList.closest('.collapse-item');
            const checkList = collapseItem.querySelector('.check-list');
            const collapseCheckButton = collapseItem.querySelector('.collapse-check-button');
            const moreText = collapseCheckList.querySelector('.more');
            const lessText = collapseCheckList.querySelector('.less');

            // Toggle the collapse class on check-list
            checkList.classList.toggle('collapsed');

            // Rotate the button 180 degrees
            collapseCheckButton.style.transform = collapseCheckButton.style.transform === 'rotate(180deg)' ?
                'rotate(0deg)' : 'rotate(180deg)';

            // Toggle the more and less text visibility
            if (moreText && lessText) {
                moreText.classList.toggle('hidden');
                lessText.classList.toggle('hidden');
            }
        });
    });
</script>
<!-- description js -->
<script defer>
    // Lấy tất cả các tab
    const tabs = document.querySelectorAll('.description-tab');

    // Lặp qua từng tab và gán sự kiện click
    tabs.forEach((tab, index) => {
        tab.addEventListener('click', () => {
            // Xóa class "active" của tất cả các tab
            tabs.forEach(t => t.classList.remove('active'));
            // Thêm class "active" cho tab được bấm
            tab.classList.add('active');

            // Lấy ID tương ứng với tab
            const targetId = ['product-info', 'benefit', 'use'][index];
            const targetElement = document.getElementById(targetId);

            // Tính toán vị trí đích và bù trừ 128px
            const offsetTop = targetElement.getBoundingClientRect().top + window.scrollY - 128;

            // Cuộn đến vị trí với khoảng cách bù
            window.scrollTo({
                top: offsetTop,
                behavior: 'smooth' // Cuộn mượt
            });
        });
    });
</script>

<!-- comment js -->
<script defer>
    // Chọn tất cả các phần tử có class "comment-choose"
    const commentOptions = document.querySelectorAll('.comment-choose');

    // Thêm sự kiện click cho từng phần tử
    commentOptions.forEach(option => {
        option.addEventListener('click', () => {
            // Xóa class 'active' khỏi tất cả các phần tử
            commentOptions.forEach(o => o.classList.remove('active'));

            // Thêm class 'active' vào phần tử được nhấp
            option.classList.add('active');
        });
    });
</script>

<!-- script review -->
<script defer>
    // Chọn phần tử recent-view và recent-button
    const recentView = document.querySelector('.recent-view');
    const recentButton = document.querySelector('.recent-button');
    const recentButtonText = recentButton.querySelector('span');
    const recentButtonIcon = recentButton.querySelector('figure');

    // Thêm sự kiện click vào recent-button
    recentButton.addEventListener('click', () => {
        // Kiểm tra xem recent-view có đang ở chế độ mở rộng hay không
        if (recentView.classList.contains('expanded')) {
            // Đặt lại max-height và overflow ban đầu
            recentView.classList.remove('expanded');
            recentView.style.maxHeight = '700px';
            recentView.style.overflowY = 'hidden';

            // Cập nhật text và xoay icon trở lại ban đầu
            recentButtonText.textContent = 'View more reviews';
            recentButtonIcon.style.transform = 'rotate(0deg)';
        } else {
            // Mở rộng max-height và cho phép cuộn
            recentView.classList.add('expanded');
            recentView.style.maxHeight = '1200px';
            recentView.style.overflowY = 'auto';

            // Cập nhật text và xoay icon 180 độ
            recentButtonText.textContent = 'View less';
            recentButtonIcon.style.transform = 'rotate(180deg)';
        }
    });
</script>

<!-- header script -->
<script defer src=".<?= $url ?>/assets/js/header.js"></script>

<script defer src="<?= $url ?>/assets/js/quantity-choose.js"></script>

<script defer src="<?= $url ?>/assets/js/modal.js"></script>

<script defer src="<?= $url ?>/assets/js/star-rating.js"></script>
