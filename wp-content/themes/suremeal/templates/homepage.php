<?php /* Template Name: Homepage */ ?>
<?php
$id = get_the_ID();
$group_slider = get_field('group_slider', $id);
$group_best_selling = get_field('group_best_selling', $id);
$group_intro = get_field('group_intro', $id);
$group_sup_product = get_field('group_sup_product', $id);
$group_product = get_field('group_product', $id);
$group_post = get_field('group_post', $id);
$group_rating = get_field('group_rating', $id);

$blog_list = new WP_Query(array(
    'post_type' => 'blog',
    'post_status' => 'publish',
    'orderby' => 'date',
    'order' => 'DESC',
    'posts_per_page' => 6,
));

$args_product = new WP_Query(array(
    'post_type' => 'product',
    'post_status' => 'publish',
    'orderby' => 'date',
    'order' => 'DESC',
    'posts_per_page' => 6,
));
$product_list = $args_product->posts;

$best_selling = new WP_Query(array(
    'post_type' => 'product',
    'post_status' => 'publish',
    'posts_per_page' => 4,
    'orderby' => 'meta_value_num',
    'order' => 'DESC',
    'meta_key' => 'purchases', // Tên của custom field
));
$best_selling_products = $best_selling->posts;

// Category all
$termsCateAll = get_terms(array(
    'taxonomy' => 'category_product',
    'orderby' => 'ID', // default: 'orderby' => 'name',
    'order' => 'DESC',
    'hide_empty' => false, // default: true
));

// var_dump($group_product['products_by_category']);
// die;


$url = get_template_directory_uri();
get_header();
?>
<style>
    .selling-product-card-red {
        aspect-ratio: 67/36;
        position: relative;
        padding-left: 44px;
        padding-top: 48px;
        border-radius: 24px;
        /* background: linear-gradient(180deg, #AA2424 0%, #FF6363 100%); */
    }

    .selling-product-card-purple {
        aspect-ratio: 67/36;
        position: relative;
        padding-left: 44px;
        padding-top: 48px;
        border-radius: 24px;
        /* background: linear-gradient(180deg, #AF42AF 0%, #DD5ECE 100%); */
    }

    @media (max-width:768px) {
        .selling-product-card-red {
            padding: 24px;
        }

        .selling-product-card-purple {
            padding: 24px;
        }
    }

    .bg-banner-cta {
        background: url(<?= $group_intro['cover_img'] ?>) no-repeat;
        background-size: cover;
        background-position: 75%;
        position: relative;
    }

    .bg-banner-cta::after {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(270deg, rgba(217, 217, 217, 0.00) 35.47%, rgba(0, 0, 0, 0.72) 91.93%);
        user-select: none;
        z-index: 0;
    }

    .bg-banner-health {
        background: url(<?= $group_post['cover_img'] ?>) no-repeat;
        background-size: cover;
        position: relative;
    }

    .deport-product-red {
        aspect-ratio: 43/31;
        /* background: linear-gradient(180deg, #AA2424 0%, #FF6363 100%), linear-gradient(180deg, #FF7070 0%, #EFABAB 100%); */
    }

    .deport-product-yellow {
        aspect-ratio: 43/31;
        background: linear-gradient(180deg, #CE8A1C 0%, #F3B773 100%);
    }

    .deport-product-purple {
        aspect-ratio: 43/31;
        background: linear-gradient(180deg, #B137B1 0%, #E862D8 100%), linear-gradient(180deg, #494CA2 0%, #6D79C5 100%);
    }
</style>
<main>
    <!-- home swiper -->
    <?php if ($group_slider): ?>
        <section>
            <div class="">
                <!-- Slider main container -->
                <div data-aos="zoom-in-up" data-aos-duration="1500" id="home-swiper" class="swiper swiper-homepage">
                    <!-- Additional required wrapper -->
                    <div class="swiper-wrapper">
                        <!-- Slides -->
                        <?php foreach ($group_slider as $item): ?>
                            <div class="swiper-slide">
                                <div class="gallery-item">
                                    <figure class="figure-19-7">
                                        <img src="<?= $item['image'] ?>" class="" alt="promos-item">
                                </div>
                            </div>
                        <?php endforeach ?>
                    </div>
                    <!-- If we need pagination -->
                    <div class="swiper-pagination"></div>
                </div>
            </div>
        </section>
    <?php endif ?>

    <!-- best selling -->
    <section class="py-8 lg:py-10 xl:py-[100px]">
        <div class="container">
            <div data-aos="fade-down" data-aos-duration="1500" class="flex flex-col gap-2 max-w-[626px] mx-auto">
                <h2 class="text-heading-home font-bold text-secondary text-center"><?= $group_best_selling['title'] ?>
                </h2>
                <p class="text-body-lg-regular text-gray-7 text-center">
                    <?= $group_best_selling['desc'] ?>
                </p>
            </div>
            <?php
            $best_selling_1 = $group_best_selling['best_selling_1'];
            $best_selling_2 = $group_best_selling['best_selling_2'];
            ?>

            <div class="mt-14">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div data-aos="fade-right" data-aos-duration="1500" class="flex flex-col sm:flex-row selling-product-card-red" style="background: <?= $best_selling_1['background'] ?>;">
                        <div class="lg:w-1/2 lg:max-w-[316px] lg:pb-4 2xl:pb-0">
                            <h2 class="text-heading-h4 font-bold text-neutral-50 truncate-3row">
                                <?= $best_selling_1['category'][0]->name ?>
                            </h2>
                            <p class="mt-1 text-body-md-regular text-neutral-100 truncate-4row">
                                <?= $best_selling_1['desc'] ?>
                            </p>
                            <a href="<?= home_url() ?>/product?brand=<?= $best_selling_1['category'][0]->slug ?>"
                                class="mt-2 lg:mt-6 button bg-white p-10-20 text-body-md-bold text-secondary hover:text-secondary"><?php pll_e('Shop now') ?></a>
                        </div>
                        <figure class="w-2/5 sm:w-auto lg:absolute ml-auto bottom-0 right-0 h-full figure-35-36 max-w-[346px] max-h-[358px]">
                            <img src="<?= $best_selling_1['image'] ? $best_selling_1['image'] : get_field('image_no_image', 'option'); ?>"
                                alt="img">
                        </figure>
                    </div>
                    <div data-aos="fade-left" data-aos-duration="1500" class="flex flex-col sm:flex-row selling-product-card-purple" style="background: <?= $best_selling_2['background'] ?>;">
                        <div class="lg:w-1/2 lg:max-w-[316px] lg:pb-4 2xl:pb-0">
                            <h2 class="text-heading-h4 font-bold text-neutral-50 truncate-3row">
                                <?= $best_selling_2['category'][0]->name ?>
                            </h2>
                            <p class="mt-1 text-body-md-regular text-neutral-100 truncate-4row">
                                <?= $best_selling_2['desc'] ?>
                            </p>
                            <a href="<?= home_url() ?>/product?brand=<?= $best_selling_2['category'][0]->slug ?>"
                                class="mt-2 lg:mt-6 button bg-white p-10-20 text-body-md-bold text-secondary hover:text-secondary"><?php pll_e('Shop now') ?></a>
                        </div>
                        <figure class="w-2/5 sm:w-auto lg:absolute ml-auto bottom-0 right-0 h-full figure-35-36 max-w-[346px] max-h-[358px]">
                            <img src="<?= $best_selling_2['image'] ? $best_selling_2['image'] : get_field('image_no_image', 'option'); ?>"
                                alt="img">
                        </figure>
                    </div>
                </div>
                <div data-aos="fade-up" data-aos-duration="1500" class="mt-8">
                    <!-- Slider main container -->
                    <div id="selling-product-swiper" class="swiper">
                        <!-- Additional required wrapper -->
                        <div class="swiper-wrapper">
                            <!-- Slides -->
                            <?php foreach ($best_selling_products as $key => $value) :
                                $price = get_field('price', $value->ID);

                                $term_list = get_the_terms($value->ID, 'category_product');
                                $des = get_field('short_description', $value->ID);
                                $instock = get_field('instock', $value->ID);
                                if (empty($instock)) {
                                    $instock = 0;
                                }
                            ?>
                                <div class="swiper-slide w-full">
                                    <div class="item img-hover rounded-2xl overflow-hidden">
                                        <div class="image xl:max-h-[320px] 3xl:max-h-[342px] bg-[#F7F7F7] overflow-hidden">
                                            <a href="<?= get_permalink($value->ID) ?>">
                                                <figure class="figure-43-34">
                                                    <img src="<?= checkImage($value->ID) ?>" alt="product">
                                                </figure>
                                            </a>
                                            <?php if ($instock <= 0) : ?>
                                                <div class=" absolute top-4 right-4 rounded-[27px] bg-[#C0C0C2] px-3 py-2">
                                                    <span class="text-body-sm-bold text-white">Sold out</span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="flex flex-col gap-3 px-6 mt-4 min-h-[300px]">
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
                                                    <h2 class="text-heading-h6 text-gray-9 truncate-2row min-h-2lh"><?= $value->post_title ?></h2>
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
                                                    <!-- <?= countReview($value->ID) ?> <?php pll_e('Reviews') ?></p> -->
                                                    <?= rand(1, 9) ?> <?php pll_e('Reviews') ?>
                                            </div>
                                            <p
                                                class="truncate-3row min-h-3lh text-body-md-regular text-neutral-500">
                                                <?= $des ?>
                                            </p>
                                            <div class="flex gap-3 items-center">
                                                <span class="text-body-sm-regular text-neutral-500"><?php pll_e('From') ?></span>
                                                <p class="text-heading-h7 text-gray-9"><?= formatBalance($price) ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <!-- If we need pagination -->
                        <div class="swiper-pagination gray-1"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section data-aos="zoom-in-up" data-aos-duration="1500" class="bg-banner-cta flex items-center" style="height: calc(100vh - 128px);">
        <div class="container z-[2]">
            <div data-aos="zoom-in-up" data-aos-duration="1500" class="max-w-[528px]">
                <h2 class="text-heading-h2 text-neutral-50"><?= $group_intro['title'] ?></h2>
                <p class="mt-4 text-body-xl-medium text-neutral-100">
                    <?= $group_intro['desc'] ?>
                </p>
                <a href="<?= $group_intro['url'] ?>"
                    class="mt-8 button bg-white text-body-md-semibold text-gray-10 hover:text-secondary">
                    <?= $group_intro['btn_text'] ?>
                </a>
            </div>
        </div>
    </section>

    <section class="py-10 lg:py-20">
        <div class="container">
            <div data-aos="fade-down" data-aos-duration="1500" class="flex flex-col lg:flex-row gap-6">
                <div class="flex flex-col gap-2 flex-1">
                    <h2 class="text-heading-home font-bold text-secondary"><?= $group_sup_product['title'] ?></h2>
                    <p class="text-body-lg-regular text-gray-7">
                        <?= $group_sup_product['desc'] ?>
                    </p>
                </div>
                <div class="min-h-16 mt-5 lg:mt-0 flex items-end justify-end gap-3">
                    <button
                        class="swiper-button-prev product button bg-white w-16 h-16 p-0 flex items-center justify-center shadow-button">
                        <figure class="w-8 h-8 rotate-180"><img src="<?= $url ?>/assets/image/icon/arrow-right.svg"
                                alt="icon">
                        </figure>
                    </button>
                    <button
                        class="swiper-button-next product button bg-white w-16 h-16 p-0 flex items-center justify-center shadow-button">
                        <figure class="w-8 h-8"><img src="<?= $url ?>/assets/image/icon/arrow-right.svg" alt="icon">
                        </figure>
                    </button>
                </div>
            </div>
            <div data-aos="fade-up" data-aos-duration="1500" class="mt-14">
                <!-- Slider main container -->
                <div id="" class="swiper supplement-product pb-4">
                    <!-- Additional required wrapper -->
                    <div class="swiper-wrapper">
                        <!-- Slides -->
                        <?php
                        if (!empty($group_sup_product['product_list'])) :
                            foreach ($group_sup_product['product_list'] as $product) :
                                $product_post = $product['product_post'];

                                $featured_image_url = get_the_post_thumbnail_url($product_post->ID, 'full');

                                $price = get_post_meta($product_post->ID, 'price', true);

                                $desc = get_post_meta($product_post->ID, 'short_description', true);

                                // Get product categories
                                $categories = get_the_terms($product_post->ID, 'category_product');
                                $category_names = [];
                                if ($categories) :
                                    foreach ($categories as $category) :
                                        $category_names[] = $category->name;
                                    endforeach;
                                endif;
                                $matching_categories = array_intersect(['SureMeal', 'Supplement Depot'], $category_names);

                        ?>
                                <div class="swiper-slide w-full">
                                    <div class="img-hover rounded-3xl overflow-hidden shadow-2">
                                        <div class="deport-product-red w-full pt-[30px] flex flex-col bg-cover items-center lg:justify-between 3xl:justify-center max-h-[317px] overflow-hidden"
                                            style="background: <?= $product['background'] ?>">
                                            <div class="flex justify-between gap-3">
                                                <div class="flex px-2 py-1 rounded-lg bg-white w-fit">
                                                    <p class="text-body-sm-bold text-secondary"><?php pll_e('NEW') ?></p>
                                                </div>
                                                <h2 class="text-heading-h6 text-neutral-50">
                                                    <?= $product['name'] ?></h2>
                                            </div>
                                            <div class="image mt-4 lg:mt-8 pb-5">
                                                <a href="<?= get_permalink($product_post->ID) ?>">
                                                    <figure class="min-h-[160px] max-h-[217px]">
                                                        <img src="<?= $product['image'] ? $product['image'] : get_field('image_no_image', 'option'); ?>"
                                                            class="" alt="">
                                                    </figure>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="px-3 py-6 flex bg-white md:min-h-[300px] lg:min-h-[310px] 3xl:min-h-[320px]">
                                            <a href="<?= get_permalink($product_post->ID) ?>">
                                                <figure class="w-[83px] h-[101px] my-auto">
                                                    <img src="<?php echo esc_url($featured_image_url) ? esc_url($featured_image_url) : get_field('image_no_image', 'option'); ?>"
                                                        alt="">
                                                </figure>
                                            </a>
                                            <div class="flex flex-col flex-1 gap-3 px-6">
                                                <div class="flex flex-col gap-1">
                                                    <p class="text-body-sm-bold uppercase text-neutral-500">
                                                        <?php echo $matching_categories[1] ?>
                                                    </p>
                                                    <a href="<?= get_permalink($product_post->ID) ?>">
                                                        <h2 class="text-heading-h6 text-gray-9 min-h-2lh truncate-2row">
                                                            <?php echo esc_html($product_post->post_title); ?></h2>
                                                    </a>

                                                </div>
                                                <div class="flex items-center gap-3">
                                                    <div class="flex items-center gap-1">
                                                        <!-- <?= renderStarRatingByProductId($product_post->ID) ?> -->
                                                        <figure><img src="<?= $url ?>/assets/image/icon/blue_star.svg" alt="icon"></figure>
                                                        <figure><img src="<?= $url ?>/assets/image/icon/blue_star.svg" alt="icon"></figure>
                                                        <figure><img src="<?= $url ?>/assets/image/icon/blue_star.svg" alt="icon"></figure>
                                                        <figure><img src="<?= $url ?>/assets/image/icon/blue_star.svg" alt="icon"></figure>
                                                        <figure><img src="<?= $url ?>/assets/image/icon/blue_star.svg" alt="icon"></figure>
                                                    </div>
                                                    <p class="text-body-sm-regular text-neutral-500">
                                                        <!-- <?= countReview($product_post->ID) ?> <?php pll_e('Reviews') ?></p> -->
                                                        <?= rand(1, 9) ?> <?php pll_e('Reviews') ?>
                                                </div>
                                                <p
                                                    class="min-h-3lh truncate-3row text-body-md-regular text-neutral-500">
                                                    <?= $desc ?>
                                                </p>
                                                <div class="flex gap-3 items-center">
                                                    <span class="text-body-sm-regular text-neutral-500"><?php pll_e('From') ?></span>
                                                    <p class="text-heading-h7 text-gray-9"><?= formatBalance($price) ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        <?php
                            endforeach;
                        endif;
                        ?>
                    </div>
                </div>

                <div class="flex items-center justify-center mt-8">
                    <a href="<?= $group_sup_product['url'] ?>"
                        class="button bg-primary text-body-md-semibold text-white"><?= $group_sup_product['btn_text'] ?></a>
                </div>
            </div>
        </div>
    </section>

    <style>
        .blur-top {
            z-index: 1;
            width: 546px;
            height: 546px;
            position: absolute;
            left: -99px;
            top: -22px;
            border-radius: 546px;
            opacity: 0.48;
            background: #0E74BC;
            filter: blur(250px);
        }

        .blur-bottom {
            z-index: 1;
            width: 686px;
            height: 686px;
            position: absolute;
            right: -208px;
            bottom: -49px;
            border-radius: 686px;
            opacity: 0.37;
            background: #ED1B24;
            filter: blur(250px);
        }

        .product-item {
            width: fit-content;
            display: flex;
            padding: 12px;
            justify-content: center;
            align-items: center;
            gap: 8px;
            border-radius: 8px;
            background: #F1F9FE;
            color: #37514B;
        }

        .product-item.active {
            color: #0E74BC;
            background: #FFF;
            box-shadow: 0px 4px 20px 0px #C2EBFF;
        }
    </style>
    <section class="py-[100px] relative bg-[#D5F2FF] overflow-hidden">
        <!-- blur color top and bottom -->
        <div class="blur-top"></div>
        <div class="blur-bottom"></div>
        <div class="container z-[2] relative">
            <div class="flex flex-col gap-[28px]">
                <div data-aos="fade-down" data-aos-duration="1500" class="flex flex-col gap-2 flex-1">
                    <h2 class="text-heading-home font-bold text-secondary"><?= $group_product['title'] ?></h2>
                    <p class="text-body-lg-regular text-gray-7">
                        <?= $group_product['desc'] ?>
                    </p>
                </div>
                <style>
                    .category-tabs .category-tab {
                        cursor: pointer;
                        background: #F1F9FE;
                        transition: all 0.3s;
                    }

                    .category-tabs .category-tab.active {
                        background: #FFFFFF;
                        /* Add any additional active state styles */
                    }

                    .category-content .category-section {
                        display: none;
                    }

                    .category-content .category-section.active {
                        display: grid;
                    }
                </style>
                <div data-aos="fade-right" data-aos-duration="1500" class="flex flex-col lg:flex-row justify-between gap-6">
                    <div class="flex flex-wrap gap-3 category-tabs">
                        <?php foreach ($group_product['products_by_category'] as $category_data):
                            $category = $category_data['category'][0];
                            $category_name = $category->name;
                            $category_slug = $category->slug;
                        ?>
                            <div class="product-item text-body-md-medium category-tab <?= $category_slug === reset($group_product['products_by_category'])['category'][0]->slug ? 'active' : '' ?>"
                                data-category="<?= $category_slug ?>">
                                <p class="text-body-md-medium text-inherit"><?= $category_name ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="min-h-16 mt-5 lg:mt-0 flex items-end justify-end gap-3">
                        <button
                            class="swiper-button-prev product button bg-white w-16 h-16 p-0 flex items-center justify-center shadow-button">
                            <figure class="w-8 h-8 rotate-180"><img src="<?= $url ?>/assets/image/icon/arrow-right.svg"
                                    alt="icon">
                            </figure>
                        </button>
                        <button
                            class="swiper-button-next product button bg-white w-16 h-16 p-0 flex items-center justify-center shadow-button">
                            <figure class="w-8 h-8"><img src="<?= $url ?>/assets/image/icon/arrow-right.svg" alt="icon">
                            </figure>
                        </button>
                    </div>
                </div>
            </div>
            <div class="mt-14 category-content">
                <?php foreach ($group_product['products_by_category'] as $category_data):
                    $category = $category_data['category'][0];
                    $category_slug = $category->slug;
                    $category_name = $category->name;
                    $featured_product = $category_data['featured_product'];
                    $featured_product_id =  $featured_product['product']->ID;
                ?>
                    <div class="category-section grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 <?= $category_slug === reset($group_product['products_by_category'])['category'][0]->slug ? 'active' : '' ?>"
                        data-category="<?= $category_slug ?>">
                        <div data-aos="fade-right" data-aos-duration="1500" class="order-[2] xl:order-1 min-h-[500px] 3xl:min-h-[627px] card-bg-yellow rounded-3xl oveflow-hidden relative pt-12 px-11"
                            style="background: <?= $featured_product['background'] ?>;">
                            <h2 class="text-heading-h4 text-neutral-50 truncate-2row">
                                <?= $featured_product['product']->post_title ?></h2>
                            <a href="<?= get_permalink($featured_product_id) ?>"
                                class="relative z-[2] mt-6 button bg-white p-10-20 text-body-md-bold text-secondary hover:text-secondary"><?php pll_e('Shop now') ?></a>
                            <div class="lg:absolute bottom-0 left-auto xl:left-0 lg:w-2/3 xl:w-full">
                                <figure class="mt-6">
                                    <img src="<?= $featured_product['image'] ?>" alt="icon">
                                </figure>

                            </div>
                        </div>

                        <div data-aos="fade-left" data-aos-duration="1500" id="" class="order-[1] xl:order-2 md:col-span-2 swiper suremeal-product">
                            <!-- Additional required wrapper -->
                            <div class="swiper-wrapper">
                                <!-- Slides -->
                                <?php foreach ($category_data['product_list'] as $product):
                                    $product_id = $product->ID;
                                    $product_title = $product->post_title;
                                    $desc = get_post_meta($product_id, 'short_description', true);
                                    $price = get_post_meta($product_id, 'price', true);
                                    $instock = get_field('instock', $product->ID);
                                    if (empty($instock)) {
                                        $instock = 0;
                                    }
                                ?>
                                    <div class="swiper-slide w-full supplement-pro"
                                        data-category="<?= $category_data['category'][0]->slug ?>">
                                        <div class="img-hover bg-white rounded-3xl w-full overflow-hidden"
                                            data-category="<?= $item->slug ?>">
                                            <div class="item">
                                                <div class="image xl:max-h-[320px] 3xl:max-h-[342px] bg-[#F7F7F7] overflow-hidden">
                                                    <a href="<?= get_permalink($product_id) ?>">
                                                        <figure class="figure-43-34">
                                                            <img src="<?= get_the_post_thumbnail_url($product_id, 'full') ? get_the_post_thumbnail_url($product_id, 'full') : get_field('image_no_image', 'option') ?>"
                                                                alt="product">
                                                        </figure>
                                                    </a>
                                                    <?php if ($instock <= 0) : ?>
                                                        <div class=" absolute top-4 right-4 rounded-[27px] bg-[#C0C0C2] px-3 py-2">
                                                            <span class="text-body-sm-bold text-white">Sold out</span>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="flex flex-col gap-3 px-6 mt-4 pb-[20px] min-h-[300px]">
                                                    <div class="flex flex-col gap-1">
                                                        <p class="text-body-sm-bold uppercase text-neutral-500"><?= $category_name ?></p>
                                                        <a href="<?= get_permalink($product_id) ?>">
                                                            <h2 class="min-h-2lh truncate-2row text-heading-h6 text-gray-9">
                                                                <?= $product_title ?></h2>
                                                        </a>
                                                    </div>
                                                    <div class="flex items-center gap-3">
                                                        <div class="flex items-center gap-1">
                                                            <!-- <?= renderStarRatingByProductId($product_id) ?> -->
                                                            <figure><img src="<?= $url ?>/assets/image/icon/blue_star.svg" alt="icon"></figure>
                                                            <figure><img src="<?= $url ?>/assets/image/icon/blue_star.svg" alt="icon"></figure>
                                                            <figure><img src="<?= $url ?>/assets/image/icon/blue_star.svg" alt="icon"></figure>
                                                            <figure><img src="<?= $url ?>/assets/image/icon/blue_star.svg" alt="icon"></figure>
                                                            <figure><img src="<?= $url ?>/assets/image/icon/blue_star.svg" alt="icon"></figure>
                                                        </div>
                                                        <p class="text-body-sm-regular text-neutral-500">
                                                            <!-- <?= countReview($product_id) ?> <?php pll_e('Reviews') ?></p> -->
                                                            <?= rand(1, 9) ?> <?php pll_e('Reviews') ?>
                                                    </div>
                                                    <p class="min-h-3lh truncate-3row text-body-md-regular text-neutral-500">
                                                        <?= $desc ?>
                                                    </p>
                                                    <div class="flex gap-3 items-center">
                                                        <span class="text-body-sm-regular text-neutral-500"><?php pll_e('From') ?></span>
                                                        <p class="text-heading-h7 text-gray-9"><?= formatBalance($price) ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach ?>
                <div class="flex items-center justify-center mt-8">
                    <a href="<?= home_url() ?>/product?needs=<?= $category_data['category'][0]->slug ?>"
                        class="button bg-primary text-body-md-semibold text-white"><?= $group_product['btn_text'] ?></a>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-banner-health py-[100px]">
        <div class="container">
            <div data-aos="fade-down" data-aos-duration="1500" class="flex flex-col items-center gap-6 max-w-[800px] mx-auto">
                <div class="flex flex-col gap-2">
                    <h2 class="text-heading-home font-bold text-neutral-50 text-center"><?= $group_post['title'] ?></h2>
                    <p class="text-body-lg-regular text-neutral-100 text-center">
                        <?= $group_post['desc'] ?>
                    </p>
                </div>
                <a href="<?= $group_post['url'] ?>"
                    class="button bg-primary text-body-md-semibold text-white"><?= $group_post['btn_text'] ?></a>
            </div>
            <?php if ($blog_list->have_posts()) : ?>
                <div data-aos="fade-up" data-aos-duration="1500" class="mt-14">
                    <!-- Slider main container -->
                    <div id="health-swiper" class="swiper">
                        <!-- Additional required wrapper -->
                        <div class="swiper-wrapper">
                            <!-- Slides -->
                            <?php while ($blog_list->have_posts()) : $blog_list->the_post();
                                $blog_id = get_the_ID();
                                $blog = get_field('blog', $blog_id);
                                $thumbnail_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
                            ?>
                                <div class="swiper-slide">
                                    <div class="img-hover health-item bg-white rounded-3xl overflow-hidden">
                                        <div class="image h-full max-h-[231px]">
                                            <a href="<?php the_permalink(); ?>">
                                                <figure class="figure-41-23">
                                                    <img src="<?= esc_url($thumbnail_url) ? esc_url($thumbnail_url) : get_field('image_no_image', 'option') ?>"
                                                        alt="img'">
                                                </figure>
                                            </a>
                                        </div>
                                        <div class="p-5 min-h-[260px]">
                                            <p class="text-body-sm-medium text-secondary"><?= get_the_date() ?></p>
                                            <h2 class="mt-2 text-body-lg-semibold text-gray-9 truncate-3row min-h-3lh">
                                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                            </h2>
                                            <p class="mt-3 text-body-md-regular text-neutral-500 truncate-3row min-h-3lh">
                                                <?php echo wp_strip_all_tags(apply_filters('the_content', get_the_content())); ?>
                                            </p>
                                            <a href="<?php the_permalink(); ?>"
                                                class="mt-3 button bg-trans text-body-sm-semibold text-primary"><?php pll_e('Read more') ?></a>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile ?>

                        </div>
                        <!-- If we need pagination -->
                        <div class="swiper-pagination"></div>
                    </div>
                </div>
            <?php endif ?>
        </div>
    </section>

    <section class="px-6 lg:px-0 py-10 lg:py-20">
        <div data-aos="fade-down" data-aos-duration="1500" class="flex flex-col gap-2 max-w-[800px] mx-auto">
            <h2 class="text-heading-home font-bold text-secondary text-center"><?= $group_rating['title'] ?></h2>
            <p class="text-body-lg-regular text-neutral-500 text-center">
                <?= $group_rating['desc'] ?>
            </p>
        </div>
        <div data-aos="zoom-in-up" data-aos-duration="1500" class="mt-14">
            <!-- Slider main container -->
            <style>
                @media screen and (min-width: 1920px) and (max-width: 4000px) {
                    .max-w-1920 {
                        max-width: 1920px;
                    }
                }
            </style>
            <?php if ($group_rating['rating']): ?>
                <div id="review-swiper" class="swiper max-w-1920">
                    <!-- Additional required wrapper -->
                    <div class="swiper-wrapper mb-[-50px] lg:mb-0">
                        <!-- Slides -->
                        <?php foreach ($group_rating['rating'] as $item): ?>
                            <div class="swiper-slide" style="width: fit-content;">
                                <div class="slide-active relative max-w-[846px] pt-8" style="aspect-ratio: 846 / 416;">
                                    <a href="<?= $item['link_video_youtobe'] ? $item['link_video_youtobe'] : $item['image'] ?>"
                                        data-fancybox="best-review">
                                        <figure class="relative" style="aspect-ratio: 846 / 416;">
                                            <img src="<?= $item['image'] ?>" class="rounded-2xl"
                                                style="aspect-ratio: 846 / 416;" alt="">
                                            <?php if ($item['link_video_youtobe']): ?>
                                                <img src="<?= $url ?>/assets/image/icon/play.svg"
                                                    class="play-button hidden absolute top-[50%] left-[50%] z-[2] min-w-[84px] max-w-[84px] h-[84px]"
                                                    style="transform: translate(-50%, -50%);" alt="play">
                                            <?php endif ?>
                                        </figure>
                                    </a>
                                    <div class="review-context hidden w-[90%] lg:w-[80%] py-5 px-9 rounded-3xl bg-[#E6F5FC] mx-auto"
                                        style="transform: translateY(-50%);">
                                        <h2 class="text-body-lg-semibold text-secondary truncate-3row text-center">
                                            <?= $item['title'] ?>
                                        </h2>
                                        <p class="mt-3 text-body-md-regular text-neutral-500 min-h-4lh truncate-4row text-center">
                                            <?= $item['content'] ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach ?>
                    </div>
                    <!-- If we need pagination -->
                    <div class="swiper-pagination gray-1 mt-0"></div>
                    <!-- If we need navigation -->
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                </div>
            <?php else: ?>
                <p class="text-center"><?php pll_e('Data is being updated') ?></p>
            <?php endif ?>
        </div>
    </section>
    <?php
    get_template_part('template-parts/support-info');
    ?>
</main>
<?php get_footer() ?>
<!-- swiper script -->
<script defer>
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
    const swiper2 = new Swiper('#selling-product-swiper', {
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
                slidesPerView: 2,
            },
            1280: {
                slidesPerView: 3,
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

    const supplementProduct = new Swiper('.supplement-product', {
        // Optional parameters
        slidesPerView: 1,
        spaceBetween: 24,
        // direction: getDirection(),
        // centeredSlides: true,
        // roundLengths: true,
        loop: true,
        // autoplay: true,
        breakpoints: {
            768: {
                slidesPerView: 2,
            },
            1024: {
                slidesPerView: 2,
            },
            1280: {
                slidesPerView: 3,
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

    const suremealProduct = new Swiper('.suremeal-product', {
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
                slidesPerView: 2,
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


    const swiper3 = new Swiper('#health-swiper', {
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
                slidesPerView: 2,
            },
            1280: {
                slidesPerView: 3,
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
    const swiper4 = new Swiper('#review-swiper', {
        slidesPerView: 'auto',
        spaceBetween: 24,
        centeredSlides: true,
        loop: true,
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
        speed: 1000,
        breakpoints: {
            768: {
                slidesPerView: 1.5,
            },
            992: {
                slidesPerView: 2,
            },
            1024: {
                slidesPerView: 2.2,
            },
            1280: {
                slidesPerView: 2.2,
            },
            1440: {
                slidesPerView: 2.2,
            },
        },

        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },

        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },

        scrollbar: {
            el: '.swiper-scrollbar',
        },

        on: {
            slideChangeTransitionEnd: function() {
                const slides = swiper4.slides;

                slides.forEach((slide) => {
                    const slideActive = slide.querySelector('.slide-active');
                    const reviewContext = slide.querySelector('.review-context');
                    const playButton = slide.querySelector('.play-button');

                    // Kiểm tra nếu slide đang là active
                    if (slide.classList.contains('swiper-slide-active')) {
                        reviewContext.classList.remove('hidden');

                        // Nếu có nút playButton, hiển thị nó
                        if (playButton) {
                            playButton.classList.remove('hidden');
                        }

                        // Kiểm tra nếu có slideActive và xử lý
                        if (slideActive) {
                            slideActive.classList.remove('pt-8');
                        }
                    } else {
                        reviewContext.classList.add('hidden');

                        // Ẩn nút playButton nếu tồn tại
                        if (playButton) {
                            playButton.classList.add('hidden');
                        }

                        if (slideActive) {
                            slideActive.classList.add('pt-8');
                        }
                    }
                });
            },
        },
    });
    // Kích hoạt slideChangeTransitionEnd ngay sau khi khởi tạo
    swiper4.on('init', function() {
        swiper4.slideChangeTransitionEnd();
    });

    // Khởi động Swiper với cấu hình ban đầu
    swiper4.init();
</script>

<script>
    $('[data-fancybox]').fancybox({
        protect: true,
        loop: true,
        buttons: [
            "zoom",
            "slideShow",
            "fullScreen",
            "download",
            "thumbs",
            "close"
        ],
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Tab switching functionality
        var tabs = document.querySelectorAll(".category-tabs .category-tab");
        var sections = document.querySelectorAll(".category-content .category-section");

        tabs.forEach(function(tab) {
            tab.addEventListener("click", function() {
                // Remove active class from all tabs and sections
                tabs.forEach(function(tab) {
                    tab.classList.remove("active");
                });
                sections.forEach(function(section) {
                    section.classList.remove("active");
                });

                // Add active class to clicked tab
                tab.classList.add("active");

                // Get the category slug
                var categorySlug = tab.getAttribute("data-category");

                // Show corresponding category section
                var targetSection = document.querySelector(
                    '.category-content .category-section[data-category="' + categorySlug + '"]');
                if (targetSection) {
                    targetSection.classList.add("active");
                }

            });
        });
    });
</script>