<?php /* Template Name: Search result */ ?>
<?php
get_header();


$meta_query = [];
$tax_query = [];

// Prepare filter parameters
$filter_params = [
    'filter_name' => sanitize_text_field($_GET['filter_name'] ?? ''),
    'min_price' => isset($_GET['min_price']) ? intval($_GET['min_price']) : null,
    'max_price' => isset($_GET['max_price']) ? intval($_GET['max_price']) : null,

    // Modify how "All" is handled
    'target_user_all' => isset($_GET['target_user_all']),
    'target_user' => $filter_params['target_user_all'] ? array_column($TargetUser, 'slug') : ($_GET['target_user'] ?? []),

    'brand_all' => isset($_GET['brand_all']),
    'brand' => $filter_params['brand_all'] ? array_column($termsCategory, 'slug') : ($_GET['brand'] ?? []),

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
    // 'meta_query'     => array(
    //     array(
    //         'key'     => 'view_product',
    //         'value'   => true,
    //         'compare' => '='
    //     ),
    // ),
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

// Query products
$product_query = new WP_Query($args);
$args_post = $product_query->posts;

// Get price range for all products
$args_pro = new WP_Query([
    'post_type' => 'product',
    'posts_per_page' => -1,
    'post_status' => 'publish',
    // 'meta_query'     => array(
    //     array(
    //         'key'     => 'view_product',
    //         'value'   => true,
    //         'compare' => '='
    //     ),
    // )
]);
$args_all = $args_pro->posts;

$max_price = null;
$min_price = null;
foreach ($args_all as $value) {
    $price = get_field('price', $value->ID);
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
    'number' => 4
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

$args_pro = new WP_Query(array(
    'post_type'      => 'product',
    'posts_per_page' => -1,
    'paged' => $paged,
    'post_status' => 'publish',
    'order' => 'DESC',
    'orderby' => 'date',
));
$args_all = $args_pro->posts;

// Khởi tạo giá trị lớn nhất và nhỏ nhất
$max_price = null;
$min_price = null;
foreach ($args_all as $key => $value) {
    $price = get_field('price', $value->ID);
    $value->price = $price;
    // Cập nhật giá trị lớn nhất và nhỏ nhất
    if ($max_price === null || $price > $max_price) {
        $max_price = $price;
    }
    if ($min_price === null || $price < $min_price) {
        $min_price = $price;
    }
}

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

<!-- blog -->
<?php
$posts_per_page = 4;
// Lấy số trang hiện tại
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

if (get_query_var('paged')) {
    $paged = get_query_var('paged');
} elseif (get_query_var('page')) {
    $paged = get_query_var('page');
} else {
    $paged = 1;
}

$latest_blog_list = new WP_Query(array(
    'post_type' => 'blog',
    'post_status' => 'publish',
    'orderby' => 'date',
    'order' => 'DESC',
    'posts_per_page' => 6
));

$blog_list = new WP_Query(array(
    'post_type' => 'blog',
    'post_status' => 'publish',
    'orderby' => 'date',
    'order' => 'DESC',
    'posts_per_page' => $posts_per_page,
    'paged' => $paged,
));

$cate_blog_list = get_terms(array(
    'taxonomy'   => 'category_blog',
    'hide_empty' => false
));
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
                        <span itemprop="name"><?php pll_e('Search result') ?></span>
                        <meta itemprop="position" content="4" />
                    </li>

                </ol>
            </nav>
            <h1 class="text-heading-h2 tex-gray-9"><?php pll_e('Search result') ?></h1>
        </div>
    </section>


    <section class="pb-10 lg:pb-20">
        <div class="container">
            <div class="flex flex-wrap items-center justify-between gap-6">
                <h2 class="text-heading-h3-5 text-gray-8"><?php pll_e('Product list') ?></h2>
            </div>
            <!-- vỉew -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-4 gap-y-8 gap-x-6 py-8">
                <!-- item -->
                <?php foreach ($args_post as $key => $value) :
                    $price = get_field('price', $value->ID);

                    $term_list = get_the_terms($value->ID, 'category_product');
                    $des = get_field('short_description', $value->ID);
                ?>
                    <div class="img-hover bg-white rounded-3xl overflow-hidden">
                        <div class="image max-h-[232px] overflow-hidden">
                            <figure class="figure-30-23">
                                <a href="<?= get_permalink($value->ID) ?>">
                                    <img src="<?= checkImage($value->ID) ?>" alt="product">
                                </a>
                            </figure>
                        </div>
                        <div class="flex flex-col gap-3 px-6 pb-[20px] mt-4">
                            <div class="flex flex-col gap-1">
                                <?php foreach ($term_list as $keys => $val) :
                                    if ($keys == 0) :
                                ?>
                                        <p class="text-body-sm-regular text-neutral-500"><?= $val->name ?></p>
                                <?php
                                    endif;
                                endforeach; ?>
                                <a href="<?= get_permalink($value->ID) ?>">
                                    <h2 class="3xl:min-h-[72px] text-heading-h6 text-gray-9 truncate-2row">
                                        <?= $value->post_title ?></h2>
                                </a>

                            </div>
                            <div class="flex items-center gap-3">
                                <div class="flex items-center gap-1">
                                    <?= renderStarRatingByProductId($value->ID) ?>
                                </div>
                                <p class="text-body-sm-regular text-neutral-500"><?= countReview($value->ID) ?> <?php pll_e('Reviews') ?></p>
                            </div>
                            <p class="truncate-3row text-body-md-regular text-neutral-500">
                                <?= $des ?>
                            </p>
                            <div class="">
                                <span class="text-body-sm-regular text-neutral-500"><?php pll_e('From') ?></span>
                                <p class="text-heading-h7 text-gray-9"><?= formatBalance($price) ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- .pagination -->
            <div class="pagination justify-center">
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
    </section>
    <section class="pb-10 lg:pb-20">
        <div class="container">
            <div class="flex flex-wrap items-center justify-between gap-6">
                <h2 class="text-heading-h3-5 text-gray-8"><?php pll_e('Blog list') ?></h2>
            </div>
            <!-- vỉew -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 py-8">
                <?php if ($blog_list->have_posts()) : ?>
                    <?php while ($blog_list->have_posts()) : $blog_list->the_post();
                        $blog_id = get_the_ID();
                        $blog = get_field('blog', $blog_id);
                        $thumbnail_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
                    ?>
                        <!-- single item -->
                        <div class="img-hover bg-white rounded-xl overflow-hidden flex flex-col gap-3">
                            <div class="image overflow-hidden" style="aspect-ratio: 44/26">
                                <figure class="figure-44-26 max-h-[260px]">
                                    <a href="<?php the_permalink(); ?>">
                                        <img src="<?= esc_url($thumbnail_url) ? esc_url($thumbnail_url) : get_field('image_no_image', 'option') ?>"
                                            alt="img">
                                    </a>
                                </figure>
                            </div>
                            <div class="p-5 flex flex-col gap-3">
                                <div class="flex items-center gap-2">
                                    <figure class="w-5 h-5"><img src="<?= $url ?>/assets/image/icon/calendar.svg"
                                            alt="icon">
                                    </figure>
                                    <p class="text-body-sm-regular text-neutral-500"><?= get_the_date() ?></p>
                                </div>
                                <h2 class="text-body-md-medium text-gray-8 truncate-3row">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h2>
                                <p class="text-body-sm-regular text-neutral-500 truncate-3row">
                                    <?php echo wp_strip_all_tags(apply_filters('the_content', get_the_content())); ?>
                                </p>
                                <a href="<?php the_permalink(); ?>" class="text-body-md-semibold text-primary"><?php pll_e('Read more') ?></a>
                            </div>
                        </div>
                    <?php endwhile ?>
                <?php else: ?>
                    <p class="text-center"><?php pll_e('Data is being updated') ?></p>
                <?php endif ?>
            </div>
            <!-- .pagination -->
            <div class="pagination">
                <?php
                $big = 999999999;
                $pagination = paginate_links(array(
                    'base'      => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                    'format'    => '?paged=%#%',
                    'current'   => max(1, get_query_var('paged')),
                    'total'     => $blog_list->max_num_pages,
                    'type'      => 'array',
                    'prev_text' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M10.3537 12.6463C10.4001 12.6927 10.437 12.7479 10.4621 12.8086C10.4872 12.8693 10.5002 12.9343 10.5002 13C10.5002 13.0657 10.4872 13.1308 10.4621 13.1915C10.437 13.2521 10.4001 13.3073 10.3537 13.3538C10.3072 13.4002 10.252 13.4371 10.1914 13.4622C10.1307 13.4873 10.0656 13.5003 9.99991 13.5003C9.93421 13.5003 9.86915 13.4873 9.80846 13.4622C9.74776 13.4371 9.69261 13.4002 9.64615 13.3538L4.64615 8.35375C4.59967 8.30732 4.56279 8.25217 4.53763 8.19147C4.51246 8.13077 4.49951 8.06571 4.49951 8C4.49951 7.9343 4.51246 7.86923 4.53763 7.80853C4.56279 7.74783 4.59967 7.69269 4.64615 7.64625L9.64615 2.64625C9.73997 2.55243 9.86722 2.49973 9.99991 2.49973C10.1326 2.49973 10.2598 2.55243 10.3537 2.64625C10.4475 2.74007 10.5002 2.86732 10.5002 3C10.5002 3.13269 10.4475 3.25993 10.3537 3.35375L5.70678 8L10.3537 12.6463Z" fill="#0F0F0F" /></svg>',
                    'next_text' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M11.3538 8.35375L6.35378 13.3538C6.30733 13.4002 6.25218 13.4371 6.19148 13.4622C6.13079 13.4873 6.06573 13.5003 6.00003 13.5003C5.93434 13.5003 5.86928 13.4873 5.80859 13.4622C5.74789 13.4371 5.69274 13.4002 5.64628 13.3538C5.59983 13.3073 5.56298 13.2521 5.53784 13.1915C5.5127 13.1308 5.49976 13.0657 5.49976 13C5.49976 12.9343 5.5127 12.8693 5.53784 12.8086C5.56298 12.7479 5.59983 12.6927 5.64628 12.6463L10.2932 8L5.64628 3.35375C5.55246 3.25993 5.49976 3.13269 5.49976 3C5.49976 2.86732 5.55246 2.74007 5.64628 2.64625C5.7401 2.55243 5.86735 2.49973 6.00003 2.49973C6.13272 2.49973 6.25996 2.55243 6.35378 2.64625L11.3538 7.64625C11.4003 7.69269 11.4372 7.74783 11.4623 7.80853C11.4875 7.86923 11.5004 7.9343 11.5004 8C11.5004 8.06571 11.4875 8.13077 11.4623 8.19147C11.4372 8.25217 11.4003 8.30732 11.3538 8.35375Z" fill="#0F0F0F" /></svg>',
                ));

                if (!empty($pagination)) {
                    echo '<div class="pagination">';
                    foreach ($pagination as $key => $page_link) {
                        if (strpos($page_link, 'current') !== false) {
                            echo str_replace('page-numbers', 'page-item active', $page_link);
                        } else {
                            echo str_replace('page-numbers', 'page-item', $page_link);
                        }
                    }
                    echo '</div>';
                }
                ?>
            </div>

            <!-- .pagination -->
            <div class="pagination justify-center">
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
    </section>

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