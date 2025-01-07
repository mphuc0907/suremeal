<?php /* Template Name: Product-List */ ?>
<?php
get_header();

$term = get_queried_object();
$slug = $term->slug;
$name = $term->name;

// Function to get dealer discount for a specific product
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

$meta_query = [
    [
        'key'     => 'view_product',
        'value'   => true,
        'compare' => '='
    ]
];
$tax_query = [];
if (!empty($_GET['brand'])) {
    $brand = $_GET['brand'];
} else {
    $brand = $slug;
}
// Prepare filter parameters
$filter_params = [
    'filter_name' => sanitize_text_field($_GET['filter_name'] ?? ''),
    'min_price' => isset($_GET['min_price']) ? intval($_GET['min_price']) : null,
    'max_price' => isset($_GET['max_price']) ? intval($_GET['max_price']) : null,

    // Modify how "All" is handled
    'target_user_all' => isset($_GET['target_user_all']),
    'target_user' => $filter_params['target_user_all'] ? array_column($TargetUser, 'slug') : ($_GET['target_user'] ?? []),

    'brand_all' => isset($_GET['brand_all']),

    'brand' => $filter_params['brand_all'] ? array_column($termsCategory, 'slug') : ($brand ?? []),

    'needs_all' => isset($_GET['needs_all']),
    'needs' => $filter_params['needs_all'] ? array_column($needs, 'slug') : ($_GET['needs'] ?? []),
    'sort' => $_GET['sort'] ?? 'date'
];

// Build search query
$args = [
    'post_type' => 'product',
    'posts_per_page' => 9,
    'paged' => max(1, get_query_var('paged')),
    'post_status' => 'publish',
    'meta_query'     => array(
        array(
            'key'     => 'view_product',
            'value'   => true,
            'compare' => '='
        ),
    ),
];
$arg_query = new WP_Query($args);


// Search by name
if (!empty($filter_params['filter_name'])) {
    $args['s'] = $filter_params['filter_name'];
}

// Price range filter
if ($filter_params['min_price'] !== null || $filter_params['max_price'] !== null) {
    $min_price = $filter_params['min_price'] ?? 0;
    $max_price = $filter_params['max_price'] ?? PHP_INT_MAX;
    $meta_query[] = [
        'key'     => 'price',
        'value'   => [$min_price, $max_price],
        'compare' => 'BETWEEN',
        'type'    => 'NUMERIC',
    ];
}

// Taxonomy filters
$tax_query = [];
$taxonomy_filters = [
    'target_user' => [
        'terms' => $filter_params['target_user'],
        'all' => $filter_params['target_user_all']
    ],
    'category_product' => [
        'terms' => $filter_params['brand'],
        'all' => $filter_params['brand_all']
    ],
    'needs' => [
        'terms' => $filter_params['needs'],
        'all' => $filter_params['needs_all']
    ]
];

// Set the overall relation to OR
$tax_query['relation'] = 'OR';

foreach ($taxonomy_filters as $taxonomy => $filter_data) {
    // If "All" is checked, we don't add any taxonomy filter
    if ($filter_data['all']) {
        continue;
    }

    // If specific terms are selected
    if (!empty($filter_data['terms'])) {
        $terms = is_array($filter_data['terms']) ? $filter_data['terms'] : [$filter_data['terms']];

        $tax_query[] = [
            'taxonomy' => $taxonomy,
            'field' => 'slug',
            'terms' => array_map('sanitize_text_field', $terms),
            'operator' => 'IN'
        ];
    }
}

// Add tax_query to args if it's not empty
if (!empty($tax_query) && count($tax_query) > 1) {
    $args['tax_query'] = $tax_query;
}

// Sorting logic
$order_by = 'date';
$order = 'DESC';
$meta_key = null;

switch ($filter_params['sort']) {
    case 'low_price':
        $order_by = 'meta_value_num';
        $meta_key = 'price';
        $order = 'ASC';
        break;
    case 'high_price':
        $order_by = 'meta_value_num';
        $meta_key = 'price';
        $order = 'DESC';
        break;
}

// Add meta and tax queries to args
if (!empty($meta_query)) {
    $args['meta_query'] = $meta_query;
}
if (!empty($tax_query)) {
    $args['tax_query'] = $tax_query;
}

// Add sorting parameters
$args['orderby'] = $order_by;
$args['order'] = $order;
if ($meta_key) {
    $args['meta_key'] = $meta_key;
}

// Đảm bảo meta_query có mối quan hệ AND
$meta_query['relation'] = 'AND';

// Áp dụng meta_query cho các truy vấn
$args['meta_query'] = $meta_query;
// Query products
$product_query = new WP_Query($args);
$args_post = $product_query->posts;

// Get price range for all products
$args_pro = new WP_Query([
    'post_type' => 'product',
    'posts_per_page' => -1,
    'post_status' => 'publish',
    'meta_query' => [
        [
            'key'     => 'view_product',
            'value'   => true,
            'compare' => '='
        ]
    ]
]);
$args_all = $args_pro->posts;

// Reset min max price
$max_price = null;
$min_price = null;
foreach ($args_all as $key => $value) {
    $price = get_field('price', $value->ID);

    // Cập nhật giá trị lớn nhất và nhỏ nhất
    if ($max_price === null || $price > $max_price) {
        $max_price = $price;
    }
    if ($min_price === null || $price < $min_price) {
        $min_price = $price;
    }
}

// Current page URL
$current_url = home_url(add_query_arg(null, null));

// Get taxonomies
$termsCategory = get_terms([
    'taxonomy' => 'category_product',
    'orderby' => 'ID',
    'order' => 'DESC',
    'hide_empty' => false,
]);

$slider_page = get_field('slider_page');

$TargetUser = get_terms([
    'taxonomy' => 'target_user',
    'orderby' => 'ID',
    'order' => 'DESC',
    'hide_empty' => false
]);

$needs = get_terms([
    'taxonomy' => 'needs',
    'orderby' => 'ID',
    'order' => 'DESC',
    'hide_empty' => false
]);


//Sản phẩm đã xem
$viewed_products = get_viewed_products();

if (!empty($viewed_products)) {
    $args = array(
        'post_type'      => 'product',
        'post__in'       => $viewed_products,
        'posts_per_page' => -1,
        'orderby'        => 'post__in', // Sắp xếp theo thứ tự trong cookie
    );

    $viewed_query = new WP_Query($args);
    $post_view = $viewed_query->posts;
    $count_view = count($post_view);
}

$url = get_template_directory_uri();
?>
<main class="bg-[#EEF0F6]">
    <section class="pt-6 pb-10">
        <div class="container">
            <nav class="breadcrumb text-body-md-medium text-gray-8" aria-label="breadcrumb">
                <!-- Sử dụng schema.org để định nghĩa breadcrumb list -->
                <ol class="flex flex-wrap gap-3 items-center" itemscope itemtype="https://schema.org/BreadcrumbList">

                    <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                        <a href="<?= home_url()  ?>" class="text-secondary hover:text-primary" itemprop="item">
                            <span itemprop="name"><?php pll_e('Home') ?></span>
                        </a>
                        <meta itemprop="position" content="1" />
                    </li>
                    <span>/</span>
                    <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" aria-current="page">
                        <span itemprop="name"><?php pll_e('Products') ?></span>
                        <meta itemprop="position" content="4" />
                    </li>
                    <span>/</span>
                    <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" aria-current="page">
                        <span itemprop="name"><?php echo $name ?></span>
                        <meta itemprop="position" content="4" />
                    </li>
                </ol>
            </nav>
            <h1 class="text-heading-h2 tex-gray-9"><?php pll_e('Products') ?></h1>
        </div>
    </section>

    <!-- product swiper -->
    <section>
        <div class="container">
            <!-- Slider main container -->
            <div data-aos="fade-zoom-out" data-aos-duration="2000" id="product-swiper" class="swiper swiper-product">
                <!-- Additional required wrapper -->
                <div class="swiper-wrapper">
                    <!-- Slides -->
                    <?php foreach ($slider_page as $value) :
                        ?>
                        <div class="swiper-slide">
                            <div class="rounded-[1.25rem] overflow-hidden">
                                <a href="<?php if (!empty($value['link'])) {
                                    echo $value['link'];
                                } else {
                                    echo "#!";
                                } ?>">
                                    <figure>
                                        <img src="<?= $value['image'] ?>" class="" alt="product-item">
                                    </figure>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <!-- If we need pagination -->
                <div class="swiper-pagination"></div>
            </div>
        </div>
    </section>
    <style>
        .product-hover {
            background: #fff;
            cursor: pointer;
        }

        .product-hover:hover {
            background: linear-gradient(50deg, rgba(255, 255, 255, 0.00) -16.45%, rgba(0, 146, 249, 0.20) 100.19%), #FFF;
        }
    </style>
    <section class="pt-8">
        <div class="container">
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
                <?php foreach ($needs as $key => $value) :
                    $icon = get_field('icon_image', $value);
                    $term_id = $value->term_id; // Thay bằng ID của term
                    $taxonomy = $value->taxonomy; // Thay bằng taxonomy của bạn

                    $term = get_term($term_id, $taxonomy);
                    ?>
                    <div data-aos="zoom-in-up" data-aos-duration="1500" class="product-hover  gap-6 p-6  rounded-xl">
                        <a href="<?= home_url() ?>/product?needs=<?= $value->slug ?>" class="items-center flex  flex-col justify-center">
                            <figure class="w-[4.25rem] h-[4.25rem]">
                                <img src="<?= $icon ?>" alt="icon">
                            </figure>
                            <div class="flex flex-col items-center gap-1" onclick="window.location.href='/?filter_name=&needs%5B%5D='<?= $value->slug ?>">
                                <h2 class="text-heading-h6 text-gray-9 text-center"><?= $value->name ?></h2>
                                <p class="text-body-md-regular text-neutral-500"><?= $term->count ?> <?php pll_e('products') ?></p>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <style>
        .collapse-title {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .collapse-item {
            margin-top: 1rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
            max-height: 125rem;
            transition: all 0.5s ease;
        }

        .collapse-item.collapsed {
            max-height: 0;
            overflow: hidden;
            transition: all 0.5s ease;
        }

        .check-list {
            display: flex;
            flex-direction: column;
            gap: .75rem;
            max-height: 125rem;
            transition: all 0.5s ease;
        }

        .check-list.collapsed {
            max-height: 6.25rem;
            overflow: hidden;
            transition: all 0.5s ease;
        }

        .collapse-check-list {
            display: flex;
            align-items: center;
            gap: 1rem;
            cursor: pointer;
        }

        .collapse-button,
        .collapse-check-button {
            transition: all 0.5s ease;
        }
    </style>
    <section class="pt-10 lg:pt-20 pb-5 lg:pb-10">
        <div id="product-container" class="container flex flex-col lg:flex-row gap-6">
            <form data-aos="fade-right" data-aos-duration="1500" id="product-filter-form" method="GET" action="<?= esc_url($current_url) ?>" class="flex flex-wrap lg:flex-col gap-6 p-6 rounded-xl bg-white w-full lg:max-w-[250px] xl:max-w-[322px] h-fit">
                <div class="flex items-center gap-3">
                    <figure class="w-[2.25rem] h-[2.25rem]"><img src="<?= $url ?>/assets/image/icon/filter.svg" alt="icon">
                    </figure>
                    <p class="text-heading-h6 text-gray-8"><?php pll_e('Filter') ?></p>
                </div>
                <hr class="divider">
                <!-- collapse item 1 -->
                <div class="w-full">
                    <div class="collapse-title">
                        <p class="text-body-xl-medium text-gray-8"><?php pll_e('Target user') ?></p>
                        <div class="collapse-button">
                            <figure class="w-6 h-6"><img src="<?= $url ?>/assets/image/icon/chev-up-24.svg" alt="icon">
                            </figure>
                        </div>
                    </div>
                    <div class="collapse-item">
                        <input type="text" class="filter-search" name="filter_name"
                               placeholder="<?php pll_e('Search by name') ?>"
                               value="<?= esc_attr($filter_params['filter_name']) ?>">
                        <div class="check-list collapsed">
                            <label class="custom-checkbox">
                                <div class="checkbox-container">
                                    <input type="checkbox" class="check-all" name="target_user_all" value=""
                                        <?= $filter_params['target_user_all'] ? 'checked' : '' ?>>
                                    <span class="checkmark"></span>
                                </div>
                                <p class="text-body-md-regular text-gray-8"><?php pll_e('All') ?></p>
                            </label>
                            <?php foreach ($TargetUser as $key => $item) : ?>
                                <label class="custom-checkbox">
                                    <div class="checkbox-container">
                                        <input type="checkbox" name="target_user[]" value="<?= $item->slug ?>"
                                            <?= in_array($item->slug, (array)$filter_params['target_user']) ? 'checked' : '' ?>>
                                        <span class="checkmark"></span>
                                    </div>
                                    <p class="text-body-md-regular text-gray-8"><?= $item->name ?></p>
                                </label>
                            <?php endforeach; ?>
                        </div>
                        <?php if (count($TargetUser) > 2): ?>
                            <div class="collapse-check-list">
                                <div class="collapse-check-button">
                                    <figure class="w-5 h-5"><img src="<?= $url ?>/assets/image/icon/double-chev-down-20.svg"
                                                                 alt="icon"></figure>
                                </div>
                                <p class="more text-body-sm-regular text-gray-9"><?php pll_e('View more') ?></p>
                                <p class="less hidden text-body-sm-regular text-gray-9"><?php pll_e('View less') ?></p>
                            </div>
                        <?php endif ?>

                    </div>
                </div>
                <hr class="divider">
                <!-- collapse item 2 -->
                <div class="w-full">
                    <div class="collapse-title">
                        <p class="text-body-xl-medium text-gray-8"><?php pll_e('Price') ?></p>
                        <div class="collapse-button">
                            <figure class="w-6 h-6"><img src="<?= $url ?>/assets/image/icon/chev-up-24.svg" alt="icon">
                            </figure>
                        </div>
                    </div>
                    <div class="collapse-item">
                        <!-- ranger pick -->
                        <div class="range mt-20">
                            <div class="range-slider">
                                <?php if (isset($_GET['max_price']) && isset($_GET['min_price'])): ?>
                                    <span class="range-selected" style="right: <?= round((($max_price - $_GET['max_price']) / $max_price) * 100, 2) ?>%;left: <?= round((($_GET['min_price'] - $min_price) / $max_price) * 100, 2) ?>%;"></span>
                                <?php else: ?>
                                    <span class="range-selected" style="left: <?= round(($min_price / $max_price) * 100, 2) ?>%;"></span>
                                <?php endif ?>
                                <span class="range-tooltip" id="tooltip-min"><?= isset($filter_params['min_price']) ? esc_attr($filter_params['min_price']) : 0 ?></span>
                                <span class="range-tooltip" id="tooltip-max"><?= isset($filter_params['max_price']) ? esc_attr($filter_params['max_price']) : $max_price ?></span>
                            </div>
                            <div class="range-input">
                                <input type="range" class="min"
                                       min="0"
                                       max="<?= $max_price ?>"
                                       value="<?= isset($filter_params['min_price']) ? esc_attr($filter_params['min_price']) : 0 ?>"
                                       step="1">
                                <input type="range" class="max"
                                       min="0"
                                       max="<?= $max_price ?>"
                                       value="<?= isset($filter_params['max_price']) ? esc_attr($filter_params['max_price']) : $max_price ?>"
                                       step="1">
                            </div>
                            <div class="range-price mt-4 flex items-center gap-3">
                                <div class="relative" style="width: calc(50% - 40px / 2)">
                                    <input type="number" name="min_price"
                                           value="<?= isset($filter_params['min_price']) ? esc_attr($filter_params['min_price']) : $min_price ?>"
                                           placeholder="From">
                                    <p class="text-input">$</p>
                                </div>
                                -
                                <div class="relative" style="width: calc(50% - 40px / 2)">
                                    <input type="number" name="max_price"
                                           value="<?= isset($filter_params['max_price']) ? esc_attr($filter_params['max_price']) : $max_price ?>"
                                           placeholder="to">
                                    <p class="text-input">$</p>
                                </div>
                            </div>
                        </div>

                        <p class="text-body-sm-regular text-red-1" style="display: none"><?php pll_e('Please fill in the appropriate price range') ?></p>
                        <button type="submit" class="w-full button bg-primary text-body-md-semibold text-white apply-filter"><?php pll_e('Apply') ?></button>
                    </div>
                </div>
                <hr class="divider">
                <!-- collapse item 3 -->
                <div class="w-full">
                    <div class="collapse-title">
                        <p class="text-body-xl-medium text-gray-8"><?php pll_e('Brand') ?></p>
                        <div class="collapse-button">
                            <figure class="w-6 h-6"><img src="<?= $url ?>/assets/image/icon/chev-up-24.svg" alt="icon">
                            </figure>
                        </div>
                    </div>
                    <div class="collapse-item">
                        <div class="check-list collapsed">
                            <label class="custom-checkbox">
                                <div class="checkbox-container">
                                    <input type="checkbox" class="check-all" name="brand_all" value=""
                                        <?= $filter_params['brand_all'] ? 'checked' : '' ?>>
                                    <span class="checkmark"></span>
                                </div>
                                <p class="text-body-md-regular text-gray-8"><?php pll_e('All') ?></p>
                            </label>
                            <?php foreach ($termsCategory as $key => $item) : ?>
                                <label class="custom-checkbox">
                                    <div class="checkbox-container">
                                        <input type="checkbox" name="brand[]" value="<?= $item->slug ?>"
                                            <?= in_array($item->slug, (array)$filter_params['brand']) ? 'checked' : '' ?>>
                                        <span class="checkmark"></span>
                                    </div>
                                    <p class="text-body-md-regular text-gray-8"><?= $item->name ?></p>
                                </label>
                            <?php endforeach; ?>
                        </div>
                        <?php if (count($termsCategory) > 2): ?>
                            <div class="collapse-check-list">
                                <div class="collapse-check-button">
                                    <figure class="w-5 h-5"><img src="<?= $url ?>/assets/image/icon/double-chev-down-20.svg"
                                                                 alt="icon"></figure>
                                </div>
                                <p class="more text-body-sm-regular text-gray-9"><?php pll_e('View more') ?></p>
                                <p class="less hidden text-body-sm-regular text-gray-9"><?php pll_e('View less') ?></p>
                            </div>
                        <?php endif ?>

                    </div>
                </div>
                <hr class="divider">
                <!-- collapse item 4 -->
                <div class="w-full">
                    <div class="collapse-title">
                        <p class="text-body-xl-medium text-gray-8"><?php pll_e('Needs') ?></p>
                        <div class="collapse-button">
                            <figure class="w-6 h-6"><img src="<?= $url ?>/assets/image/icon/chev-up-24.svg" alt="icon">
                            </figure>
                        </div>
                    </div>
                    <div class="collapse-item">
                        <div class="check-list collapsed">
                            <label class="custom-checkbox">
                                <div class="checkbox-container">
                                    <input type="checkbox" class="check-all" name="needs_all" value=""
                                        <?= $filter_params['needs_all'] ? 'checked' : '' ?>>
                                    <span class="checkmark"></span>
                                </div>
                                <p class="text-body-md-regular text-gray-8"><?php pll_e('All') ?></p>
                            </label>
                            <?php foreach ($needs as $key => $item) : ?>
                                <label class="custom-checkbox">
                                    <div class="checkbox-container">
                                        <input type="checkbox" name="needs[]" value="<?= $item->slug ?>"
                                            <?= in_array($item->slug, (array)$filter_params['needs']) ? 'checked' : '' ?>>
                                        <span class="checkmark"></span>
                                    </div>
                                    <p class="text-body-md-regular text-gray-8"><?= $item->name ?></p>
                                </label>
                            <?php endforeach; ?>
                        </div>
                        <?php if (count($TargetUser) > 2): ?>
                            <div class="collapse-check-list">
                                <div class="collapse-check-button">
                                    <figure class="w-5 h-5"><img src="<?= $url ?>/assets/image/icon/double-chev-down-20.svg"
                                                                 alt="icon"></figure>
                                </div>
                                <p class="more text-body-sm-regular text-gray-9"><?php pll_e('View more') ?></p>
                                <p class="less hidden text-body-sm-regular text-gray-9"><?php pll_e('View less') ?></p>
                            </div>
                        <?php endif ?>

                    </div>
                </div>
            </form>
            <div data-aos="fade-left" data-aos-duration="1500" class="flex-1">
                <div class="flex flex-wrap items-center justify-between gap-6">
                    <h2 class="text-heading-h3-5 text-gray-8"><?php echo $name ?></h2>
                    <div class="flex gap-4">
                        <p class="pt-2.5 text-body-md-medium text-gray-8 whitespace-nowrap"><?php pll_e('Sort by') ?></p>
                        <div class="flex flex-wrap gap-3">
                            <a href="<?= add_query_arg('sort', 'best_seller', $current_url) ?>" class="text-body-md-medium tab-item <?= $sort_order === 'best_seller' ? 'active' : '' ?>">
                                <?php pll_e('Best seller') ?>
                            </a>
                            <a href="<?= add_query_arg('sort', 'low_price', $current_url) ?>" class="text-body-md-medium tab-item <?= $sort_order === 'low_price' ? 'active' : '' ?>">
                                <?php pll_e('Low price') ?>
                            </a>
                            <a href="<?= add_query_arg('sort', 'high_price', $current_url) ?>" class="text-body-md-medium tab-item <?= $sort_order === 'high_price' ? 'active' : '' ?>">
                                <?php pll_e('High price') ?>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- vỉew -->
                <div class="grid grid-cols-1 md:grid-cols-2 2xl:grid-cols-3 gap-y-8 gap-x-6 py-8">
                    <!-- item -->
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
                        <div class="img-hover bg-white rounded-3xl overflow-hidden">
                            <div class="image max-h-[232px] overflow-hidden">
                                <a href="<?= get_permalink($value->ID) ?>">
                                    <figure class="figure-30-23">
                                        <img src="<?= checkImage($value->ID) ?>" alt="product">
                                    </figure>
                                </a>
                                <?php if ($instock <= 0) : ?>
                                    <div class=" absolute top-4 right-4 rounded-[27px] bg-[#C0C0C2] px-3 py-2">
                                        <span class="text-body-sm-bold text-white">Sold out</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="flex flex-col gap-3 px-6 pb-[20px] mt-4">
                                <div class="flex flex-col gap-1">
                                    <?php foreach ($term_list as $keys => $val) :
                                        if ($val->name === 'SureMeal' || $val->name === 'Supplement Depot') :
                                            ?>
                                            <p class="text-body-sm-bold uppercase text-neutral-500"><?= $val->name ?></p>
                                            <?php
                                            break; // Thoát khỏi vòng lặp ngay sau khi tìm thấy danh mục
                                        endif;
                                    endforeach; ?>
                                    <a href="<?= get_permalink($value->ID) ?>">
                                        <h2 class="3xl:min-h-[72px] text-heading-h6 text-gray-9 min-h-2lh truncate-2row">
                                            <?= $value->post_title ?></h2>
                                    </a>

                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center gap-1">
                                        <!-- <?= renderStarRatingByProductId($value->ID) ?> -->
                                        <figure><img src="<?= $url ?>/assets/image/icon/blue_star.svg" alt="icon"></figure>
                                        <figure><img src="<?= $url ?>/assets/image/icon/blue_star.svg" alt="icon"></figure>
                                        <figure><img src="<?= $url ?>/assets/image/icon/blue_star.svg" alt="icon"></figure>
                                        <figure><img src="<?= $url ?>/assets/image/icon/blue_star.svg" alt="icon"></figure>
                                        <figure><img src="<?= $url ?>/assets/image/icon/blue_star.svg" alt="icon"></figure>
                                    </div>
                                    <p class="text-body-sm-regular text-neutral-500">
                                        <!-- <?= countReview($value->ID) ?> <?php pll_e('Reviews') ?> -->
                                        <?= rand(1, 9) ?> <?php pll_e('Reviews') ?>
                                    </p>
                                </div>
                                <p class="min-h-3lh truncate-3row text-body-md-regular text-neutral-500">
                                    <?= $des ?>
                                </p>
                                <div class="flex gap-3 items-center">
                                    <span class="text-body-sm-regular text-neutral-500"><?php pll_e('From') ?></span>
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
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- .pagination -->
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
    </section>
    <?php if ($count_view > 0) : ?>
        <section class="pt-5 lg:pt-10 pb-10 lg:pb-20">
            <div class="container flex flex-col gap-6">
                <div data-aos="fade-down" data-aos-duration="1500" class="flex flex-row gap-6 md:items-center justify-between">
                    <div class="flex flex-col gap-2 flex-1">
                        <h2 class="text-heading-h3-5 text-gray-8"><?php pll_e('Recently viewed products') ?>
                        </h2>
                    </div>
                    <?php if ($count_view > 3) : ?>
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
                        <?php foreach ($post_view as $key => $val) :
                            $price = get_field('price', $val->ID);

                            $term_list = get_the_terms($val->ID, 'category_product');
                            $des = get_field('short_description', $val->ID);
                            $instock = get_field('instock', $val->ID);
                            if (empty($instock)) {
                                $instock = 0;
                            }
                            $sale_price = get_field('sale_price', $val->ID);
                            ?>
                            <div class="swiper-slide w-full">
                                <!-- item -->
                                <div class="img-hover bg-white rounded-3xl overflow-hidden">
                                    <div class="image max-h-[232px] overflow-hidden">
                                        <a href="<?= get_permalink($val->ID) ?>">
                                            <figure class="figure-30-23">
                                                <img src="<?= checkImage($val->ID) ?>" alt="product">
                                            </figure>
                                        </a>
                                        <?php if ($instock <= 0) : ?>
                                            <div class=" absolute top-4 right-4 rounded-[27px] bg-[#C0C0C2] px-3 py-2">
                                                <span class="text-body-sm-bold text-white">Sold out</span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex flex-col gap-3 px-6 pb-[20px] mt-4 md:min-h-[300px] 2xl:min-h-[310px]">
                                        <div class="flex flex-col gap-1 min-h-[100px]">
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
                                                <!-- <?= renderStarRatingByProductId($val->ID) ?> -->
                                                <figure><img src="<?= $url ?>/assets/image/icon/blue_star.svg" alt="icon"></figure>
                                                <figure><img src="<?= $url ?>/assets/image/icon/blue_star.svg" alt="icon"></figure>
                                                <figure><img src="<?= $url ?>/assets/image/icon/blue_star.svg" alt="icon"></figure>
                                                <figure><img src="<?= $url ?>/assets/image/icon/blue_star.svg" alt="icon"></figure>
                                                <figure><img src="<?= $url ?>/assets/image/icon/blue_star.svg" alt="icon"></figure>
                                            </div>
                                            <p class="text-body-sm-regular text-neutral-500">
                                                <!-- <?= countReview($val->ID) ?> <?php pll_e('Reviews') ?> -->
                                                <?= rand(1, 9) ?> <?php pll_e('Reviews') ?>
                                            </p>
                                        </div>
                                        <p class="min-h-3lh truncate-3row text-body-md-regular text-neutral-500"><a
                                                    href="<?= get_permalink($val->ID) ?>">
                                                <?= $des ?></a>
                                        </p>
                                        <div class="flex gap-3 items-center">
                                            <span class="text-body-sm-regular text-neutral-500"><?php pll_e('From') ?></span>
                                            <?php if ($sale_price) :?>
                                                <div class="flex items-center gap-2">
                                                    <p class=" text-body-md-medium text-neutral-500 line-through"><?= formatBalance($price) ?></p>
                                                    <p class="text-heading-h7 text-gray-9"><?= formatBalance($sale_price) ?></p>
                                                </div>
                                            <?php else:?>
                                                <div class="flex items-center gap-2">
                                                    <p class="text-heading-h7 text-gray-9"><?= formatBalance($price) ?></p>
                                                </div>
                                            <?php endif;?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

            </div>
        </section>
    <?php endif ?>

    <?php
    get_template_part('template-parts/support-info');
    ?>
</main>
<?php get_footer() ?>
<!-- swiper script -->
<script defer>
    //Search product

    $(document).ready(function() {

    })

    const productSwiper = new Swiper('#product-swiper', {
        // Optional parameters
        slidesPerView: 1,
        spaceBetween: 0,
        // direction: getDirection(),
        // centeredSlides: true,
        // roundLengths: true,
        loop: true,
        autoplay: true,
        breakpoints: {
            552: {
                slidesPerView: 1,
            },
            1200: {
                slidesPerView: 1,
            },
            2560: {
                slidesPerView: 1,
            }
        },

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

        // // If we need pagination
        // pagination: {
        //     el: '.swiper-pagination',
        //     clickable: true,
        // },

        // Navigation arrows
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },

        // // And if we need scrollbar
        // scrollbar: {
        //     el: '.swiper-scrollbar',

        // },
    });
</script>
<script>
    $(document).ready(function() {
        // Khi checkbox "All" được chọn
        $('.check-all').on('change', function() {
            const isChecked = $(this).is(':checked');
            const $parentDiv = $(this).closest('.check-list'); // Tìm container cha

            // Bỏ chọn hoặc khóa các checkbox khác
            $parentDiv.find('input[type="checkbox"]').not(this).prop('checked', false);
        });

        // Khi checkbox khác được chọn
        $('.check-list input[type="checkbox"]').not('.check-all').on('change', function() {
            const $parentDiv = $(this).closest('.check-list'); // Tìm container cha

            // Nếu bất kỳ checkbox nào khác "All" được chọn, bỏ chọn "All"
            if ($(this).is(':checked')) {
                $parentDiv.find('.check-all').prop('checked', false);
            }
        });
    });
</script>

<!-- script filter -->
<script defer>
    document.querySelectorAll('.collapse-title').forEach((title) => {
        title.addEventListener('click', () => {
            // Tìm phần tử cha chứa cả .collapse-title và .collapse-item
            const parent = title.closest('.w-full');
            const collapseItem = parent.querySelector('.collapse-item');

            const button = title.querySelector('.collapse-button');

            // Toggle class 'collapsed' cho collapse-item
            collapseItem.classList.toggle('collapsed');

            // Đổi icon của collapse-button
            if (collapseItem.classList.contains('collapsed')) {
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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Price range filter
        const applyFilterBtn = document.querySelector('.apply-filter');
        if (applyFilterBtn) {
            applyFilterBtn.addEventListener('click', function(e) {
                e.preventDefault();
                document.getElementById('product-filter-form').submit();
            });
        }

        // Name search filter
        const nameSearchInput = document.querySelector('input[name="filter_name"]');
        if (nameSearchInput) {
            nameSearchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    document.getElementById('product-filter-form').submit();
                }
            });
        }

        // Checkbox filters
        const checkboxes = document.querySelectorAll('input[type="checkbox"][name="brand[]"], input[type="checkbox"][name="target_user[]"], input[type="checkbox"][name="needs[]"]');
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                document.getElementById('product-filter-form').submit();
            });
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Target all checkbox filters
        const checkboxFilters = document.querySelectorAll('input[type="checkbox"]:not(.min, .max)');
        checkboxFilters.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                // Exclude price inputs from this submission
                const form = this.closest('form');
                const priceInputs = form.querySelectorAll('input[name="min_price"], input[name="max_price"]');
                priceInputs.forEach(input => input.disabled = true);
                form.submit();
            });
        });

        // Re-enable price inputs when price apply button is clicked
        const priceApplyButton = document.querySelector('.apply-filter');
        if (priceApplyButton) {
            priceApplyButton.addEventListener('click', function() {
                const form = this.closest('form');
                const priceInputs = form.querySelectorAll('input[name="min_price"], input[name="max_price"]');
                priceInputs.forEach(input => input.disabled = false);
            });
        }
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkAllBoxes = document.querySelectorAll('.check-all');

        checkAllBoxes.forEach(checkAllBox => {
            checkAllBox.addEventListener('change', function() {
                // Find sibling checkboxes
                const checkboxContainer = this.closest('.check-list');
                const individualCheckboxes = checkboxContainer.querySelectorAll('input[type="checkbox"]:not(.check-all)');

                if (this.checked) {
                    // Uncheck all individual checkboxes
                    individualCheckboxes.forEach(cb => {
                        cb.checked = false;
                    });
                }
            });

            // When an individual checkbox is checked, uncheck "All"
            const checkboxContainer = checkAllBox.closest('.check-list');
            const individualCheckboxes = checkboxContainer.querySelectorAll('input[type="checkbox"]:not(.check-all)');

            individualCheckboxes.forEach(cb => {
                cb.addEventListener('change', function() {
                    if (this.checked) {
                        checkAllBox.checked = false;
                    }
                });
            });
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Check if there are filter parameters or a search query
        const urlParams = new URLSearchParams(window.location.search);
        const hasFilterParams = Array.from(urlParams.keys()).some(
            key => ['filter_name', 'min_price', 'max_price', 'target_user', 'brand', 'needs', 'sort'].includes(key)
        );

        if (hasFilterParams) {
            // Find the product list section
            const productListSection = document.querySelector('#product-container');

            if (productListSection) {
                // Smooth scroll to the product list section
                productListSection.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        }
    });
</script>