<?php /* Template Name: Blog-List */ ?>
<?php
$posts_per_page = 4;

// Get current page
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

// Get filter parameters from URL
$category_id = isset($_GET['category']) ? intval($_GET['category']) : 0;
$search_query = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';
$sort_type = isset($_GET['sort']) ? sanitize_text_field($_GET['sort']) : 'latest';

// Build query arguments
$args = array(
    'post_type' => 'blog',
    'post_status' => 'publish',
    'posts_per_page' => $posts_per_page,
    'paged' => $paged,
    'orderby' => 'date',
    'order' => 'DESC'
);

// Apply category filter
if ($category_id > 0) {
    $args['tax_query'] = array(
        array(
            'taxonomy' => 'category_blog',
            'field' => 'term_id',
            'terms' => $category_id,
        )
    );
}

// Apply search filter
if (!empty($search_query)) {
    $args['s'] = $search_query;
}

// Apply sorting
if ($sort_type === 'popular') {
    $args['meta_key'] = 'is_popular';
    $args['meta_value'] = true;
    $args['orderby'] = 'meta_value_num';
}

$blog_list = new WP_Query($args);

// Get categories for filter
$cate_blog_list = get_terms(array(
    'taxonomy' => 'category_blog',
    'hide_empty' => false
));

// Get latest blogs for sidebar
$latest_blog_list = new WP_Query(array(
    'post_type' => 'blog',
    'post_status' => 'publish',
    'orderby' => 'date',
    'order' => 'DESC',
    'posts_per_page' => 6
));

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
                        <a href="<?= home_url() ?>" class="text-secondary hover:text-primary" itemprop="item">
                            <span itemprop="name"><?php pll_e('Home') ?></span>
                        </a>
                        <meta itemprop="position" content="1" />
                    </li>
                    <span>/</span>
                    <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" aria-current="page">
                        <span itemprop="name"><?php pll_e('Blog') ?></span>
                        <meta itemprop="position" content="4" />
                    </li>

                </ol>
            </nav>
        </div>
    </section>


    <section class="pb-20">
        <div class="container">
            <div class="w-full flex flex-col lg:flex-row gap-6">
                <div class="flex flex-col gap-6 w-full lg:w-1/3 lg:max-w-[437px]">
                    <div data-aos="fade-down" data-aos-duration="1500" class="flex flex-col gap-5 p-6 rounded-xl bg-white">
                        <h2 class="text-body-lg-medium text-gray-8"><?php pll_e('Category') ?></h2>
                        <hr class="divider">
                        <form id="category-form" method="GET" action="<?php echo get_permalink(); ?>">
                            <?php if (!empty($search_query)): ?>
                                <input type="hidden" name="search" value="<?php echo esc_attr($search_query); ?>">
                            <?php endif; ?>
                            <?php if (!empty($sort_type)): ?>
                                <input type="hidden" name="sort" value="<?php echo esc_attr($sort_type); ?>">
                            <?php endif; ?>
                            <div class="flex flex-col gap-3">
                                <label class="custom-checkbox">
                                    <input type="radio" name="category" class="radio-blue" value=""
                                        <?php echo $category_id === 0 ? 'checked' : ''; ?>
                                        onchange="this.form.submit()">
                                    <p class="text-body-md-regular text-gray-8">All</p>
                                </label>
                                <?php if (!empty($cate_blog_list) && !is_wp_error($cate_blog_list)): ?>
                                    <?php foreach ($cate_blog_list as $item): ?>
                                        <label class="custom-checkbox">
                                            <input type="radio" name="category" class="radio-blue"
                                                value="<?php echo esc_attr($item->term_id); ?>"
                                                <?php echo $category_id === $item->term_id ? 'checked' : ''; ?>
                                                onchange="this.form.submit()">
                                            <p class="text-body-md-regular text-gray-8"><?php echo esc_html($item->name); ?></p>
                                        </label>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>

                    <div data-aos="fade-right" data-aos-duration="1500" class="flex flex-col gap-5 p-6 rounded-xl bg-white">
                        <h2 class="text-body-lg-medium text-gray-8"><?php pll_e('Latest blog') ?></h2>
                        <hr class="divider">
                        <?php if ($latest_blog_list->have_posts()) : ?>
                            <?php $count = 0; ?>
                            <?php while ($latest_blog_list->have_posts()) : $latest_blog_list->the_post();
                                $blog_id = get_the_ID();
                                $blog = get_field('blog', $blog_id);
                                $thumbnail_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
                                $count++;
                            ?>
                                <!-- single post blog -->
                                <div class="img-hover flex gap-5 items-center">
                                    <a href="<?php the_permalink(); ?>" class="image w-1/2 max-w-[72px] figure-7-10 h-full rounded-md overflow-hidden">
                                        <figure class="w-full max-w-[72px] figure-7-10 h-full rounded-md overflow-hidden">
                                            <img src="<?= esc_url($thumbnail_url) ? esc_url($thumbnail_url) : get_field('image_no_image', 'option') ?>"
                                                alt="img">
                                        </figure>
                                    </a>
                                    <div class="flex-1 flex flex-col gap-2">
                                        <h2 class="text-body-md-medium text-gray-8 min-h-3lh truncate-3row"><a
                                                href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                                        <p class="text-body-sm-regular text-neutral-500"><?= get_the_date() ?></p>
                                    </div>
                                </div>
                                <?php if ($count >= 6) break; ?>
                            <?php endwhile ?>
                        <?php else: ?>
                            <p class="text-center"><?php pll_e('Data is being updated') ?></p>
                        <?php endif ?>
                    </div>
                </div>
                <div data-aos="fade-left" data-aos-duration="1500" class="flex-1 flex flex-col gap-6 w-full lg:w-2/3">
                    <div class="flex flex-col lg:flex-row justify-between gap-6 items-center">
                        <form method="GET" action="" class="flex-1">
                            <?php if ($category_id > 0): ?>
                                <input type="hidden" name="category" value="<?php echo esc_attr($category_id); ?>">
                            <?php endif; ?>
                            <?php if (!empty($sort_type)): ?>
                                <input type="hidden" name="sort" value="<?php echo esc_attr($sort_type); ?>">
                            <?php endif; ?>

                            <div class="relative 2xl:max-w-[386px]">
                                <input type="text" name="search" class="w-full home-search no-bg radius-8 2xl:max-w-[386px]"
                                    placeholder="Search..." value="<?php echo esc_attr($search_query); ?>">
                                <button class="button bg-trans absolute right-6 top-[50%] translate-y-[-50%]" type="submit">
                                    <figure class="w-4 h-4"><img src="<?= $url ?>/assets/image/icon/mag-glass.svg" alt=""></figure>
                                </button>
                            </div>
                        </form>
                        <div class="flex gap-4">
                            <p class="pt-2.5 text-body-md-medium text-gray-8 whitespace-nowrap">Sort by</p>
                            <form id="sort-form" method="GET" action="">
                                <?php if ($category_id > 0): ?>
                                    <input type="hidden" name="category" value="<?php echo esc_attr($category_id); ?>">
                                <?php endif; ?>
                                <?php if (!empty($search_query)): ?>
                                    <input type="hidden" name="search" value="<?php echo esc_attr($search_query); ?>">
                                <?php endif; ?>
                                <div class="flex flex-wrap gap-3">
                                    <button type="submit" name="sort" value="latest"
                                        class="text-body-md-medium tab-item <?php echo $sort_type === 'latest' ? 'active' : ''; ?>">
                                        Latest
                                    </button>
                                    <button type="submit" name="sort" value="popular"
                                        class="text-body-md-medium tab-item <?php echo $sort_type === 'popular' ? 'active' : ''; ?>">
                                        Most popular
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6" id="blog-results">
                        <?php if ($blog_list->have_posts()) : ?>
                            <?php while ($blog_list->have_posts()) : $blog_list->the_post();
                                $blog_id = get_the_ID();
                                $thumbnail_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
                            ?>
                                <!-- single item -->
                                <div class="img-hover bg-white rounded-xl overflow-hidden flex flex-col gap-3">
                                    <div class="image overflow-hidden">
                                        <a href="<?php the_permalink(); ?>">
                                            <figure class="figure-44-26 max-h-[260px]">
                                                <img src="<?= esc_url($thumbnail_url) ? esc_url($thumbnail_url) : get_field('image_no_image', 'option') ?>"
                                                    alt="img">
                                            </figure>
                                        </a>
                                    </div>
                                    <div class="p-5 flex flex-col gap-3">
                                        <div class="flex items-center gap-2">
                                            <figure class="w-5 h-5"><img src="<?= $url ?>/assets/image/icon/calendar.svg"
                                                    alt="icon">
                                            </figure>
                                            <p class="text-body-sm-regular text-neutral-500"><?= get_the_date() ?></p>
                                        </div>
                                        <h2 class="text-body-md-medium text-gray-8 min-h-2lh truncate-2row">
                                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                        </h2>
                                        <p class="text-body-sm-regular text-neutral-500 min-h-3lh truncate-3row">
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
                        // Add current query parameters to pagination links
                        $current_url = add_query_arg(array());
                        $base = add_query_arg('paged', '%#%', $current_url);

                        echo paginate_links(array(
                            'base' => str_replace($big, '%#%', esc_url($base)),
                            'format' => '?paged=%#%',
                            'current' => max(1, $paged),
                            'total' => $blog_list->max_num_pages,
                            'prev_text' => '<',
                            'next_text' => '>',
                            'type' => 'list'
                        ));
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<?php get_footer() ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Scroll to results if there are filter parameters
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('category') || urlParams.has('search') || urlParams.has('sort')) {
            const resultsElement = document.getElementById('blog-results');
            if (resultsElement) {
                resultsElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        }
    });
</script>