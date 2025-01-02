<?php
$post_id = get_the_ID();
$thumbnail = get_the_post_thumbnail_url(get_the_ID(), 'full');
$current_url = esc_url(get_permalink());
$title = get_the_title();
$facebook_share_url = 'https://www.facebook.com/sharer.php?u=' . urlencode($current_url);
$twitter_share_url = 'https://twitter.com/intent/tweet?url=' . urlencode($current_url) . '&text=' . urlencode($title);

$latest_blog_list = new WP_Query(array(
    'post_type' => 'blog',
    'post_status' => 'publish',
    'orderby' => 'date',
    'order' => 'DESC',
    'posts_per_page' => 6,
    'post__not_in' => array($post_id)
));

$post_id =  get_the_ID();
$author_id = get_post_field('post_author', $post_id);
$display_name = get_the_author_meta('nickname', $author_id);

$categories = wp_get_post_terms($post_id, 'category_blog', array('fields' => 'ids'));
// Query cùng danh mục
$related_posts_query = new WP_Query(array(
    'post_type' => 'blog',
    'post_status' => 'publish',
    'posts_per_page' => 3,
    'tax_query' => array(
        array(
            'taxonomy' => 'category_blog',
            'field' => 'id',
            'terms' => $categories,
        )
    ),
    'post__not_in' => array($post_id)
));

$url = get_template_directory_uri();
get_header();
?>
<main>
    <section class="py-6">
        <div class="container">
            <nav class="breadcrumb text-body-md-medium text-gray-8" aria-label="breadcrumb">
                <!-- Sử dụng schema.org để định nghĩa breadcrumb list -->
                <ol class="flex flex-wrap gap-3 items-center" itemscope itemtype="https://schema.org/BreadcrumbList">

                    <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                        <a href="<?= home_url() ?>" class="text-secondary hover:text-primary" itemprop="item">
                            <span itemprop="name">Home</span>
                        </a>
                        <meta itemprop="position" content="1" />
                    </li>
                    <span>/</span>
                    <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                        <a href="<?= home_url() ?>/blog" class="text-secondary hover:text-primary" itemprop="item">
                            <span itemprop="name">Blog</span>
                        </a>
                        <meta itemprop="position" content="1" />
                    </li>
                    <span>/</span>
                    <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" aria-current="page">
                        <span itemprop="name"><?php the_title(); ?></span>
                        <meta itemprop="position" content="4" />
                    </li>

                </ol>
            </nav>
        </div>
    </section>

    <style>
        .content-container ul {
            padding-left: 2rem;
            list-style: initial !important;
        }

        .content-container figure {
            width: 100% !important;
            display: flex;
            flex-direction: column;
        }
    </style>

    <section class="pt-6 lg:pt-10 pb-6 lg:pb-20">
        <div class="container">
            <div class="w-full flex flex-col lg:flex-row gap-6">
                <div data-aos="fade-right" data-aos-duration="1500" class="flex-1 flex flex-col gap-8 content-container">
                    <div class="flex flex-col gap-3">
                        <div class="flex items-center gap-2">
                            <figure class="w-5 h-5" style="width: auto !important;">
                                <img src="<?= $url ?>/assets/image/icon/calendar.svg" alt="icon">
                            </figure>
                            <p class="text-body-sm-regular text-neutral-500"><?= get_the_date() ?></p>
                        </div>
                        <h2 class="text-heading-h4 text-gray-8"><?php the_title(); ?></h2>
                    </div>
                    <div class="flex flex-wrap gap-6 justify-between">
                        <a href="#" class="text-body-md-medium text-gray-9"><?= get_field('author', $post_id) ? get_field('author', $post_id) : $display_name ?></a>

                        <div class="flex items-center gap-2.5">
                            <div class="social-icon secondary">
                                <figure>
                                    <a href="<?= $twitter_share_url ?>">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="17" viewBox="0 0 18 17" fill="none">
                                            <g clip-path="url(#clip0_4548_6477)">
                                                <path d="M10.6449 7.28838L16.7526 0.188721H15.3052L10.002 6.35325L5.76625 0.188721H0.880859L7.28609 9.51058L0.880859 16.9557H2.32826L7.92866 10.4457L12.4019 16.9557H17.2873L10.6445 7.28838H10.6449ZM8.66249 9.59271L8.01351 8.66446L2.84978 1.2783H5.0729L9.24009 7.23917L9.88907 8.16742L15.3059 15.9156H13.0828L8.66249 9.59307V9.59271Z" fill="#EBEBEB" />
                                            </g>
                                            <defs>
                                                <clipPath id="clip0_4548_6477">
                                                    <rect width="16.7756" height="16.7756" fill="white" transform="translate(0.689453 0.188721)" />
                                                </clipPath>
                                            </defs>
                                        </svg>
                                    </a>
                                </figure>
                            </div>
                            <div class="social-icon secondary">
                                <figure>
                                    <a href="<?= $facebook_share_url ?>">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="17" viewBox="0 0 10 17" fill="none">
                                            <path d="M8.91448 9.54161L9.37042 6.64475H6.56185V4.76179C6.56185 3.96968 6.95395 3.19567 8.20778 3.19567H9.50264V0.728813C8.74858 0.608566 7.98663 0.543513 7.22296 0.53418C4.91136 0.53418 3.40221 1.92377 3.40221 4.43589V6.64475H0.839844V9.54161H3.40221V16.5484H6.56185V9.54161H8.91448Z" fill="#EBEBEB" />
                                        </svg>
                                    </a>
                                </figure>
                            </div>
                            <div class="social-icon secondary">
                                <figure>
                                    <a href="#" id="copy-url">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                            <path d="M13.1069 3.23649C14.3021 2.03659 16.0505 2.01139 17.0184 2.98303C17.9881 3.95651 17.962 5.71587 16.7659 6.9166L14.7461 8.9443C14.5025 9.18885 14.5032 9.58458 14.7478 9.82819C14.9923 10.0718 15.3881 10.071 15.6317 9.82647L17.6515 7.79877C19.2443 6.19984 19.4442 3.64707 17.904 2.10086C16.362 0.552817 13.8148 0.754577 12.2213 2.35433L8.18157 6.40971C6.58882 8.00864 6.38886 10.5614 7.92908 12.1076C8.17268 12.3522 8.56841 12.353 8.81296 12.1094C9.05751 11.8658 9.05828 11.47 8.81467 11.2255C7.84496 10.252 7.87108 8.4926 9.06716 7.29188L13.1069 3.23649Z" fill="#EBEBEB" />
                                            <path d="M12.0707 7.89214C11.8271 7.64759 11.4313 7.64682 11.1868 7.89043C10.9422 8.13403 10.9415 8.52976 11.1851 8.77431C12.1548 9.74779 12.1287 11.5071 10.9326 12.7079L6.8929 16.7633C5.69763 17.9632 3.94923 17.9884 2.98134 17.0168C2.01163 16.0433 2.03774 14.2839 3.23383 13.0831L5.2537 11.0554C5.4973 10.8109 5.49654 10.4152 5.25199 10.1715C5.00744 9.92794 4.61171 9.92871 4.3681 10.1733L2.34824 12.201C0.755486 13.7999 0.555519 16.3527 2.09575 17.8989C3.63781 19.447 6.18494 19.2452 7.7785 17.6455L11.8182 13.59C13.4109 11.9911 13.6109 9.43835 12.0707 7.89214Z" fill="#EBEBEB" />
                                        </svg>
                                    </a>
                                </figure>
                            </div>
                        </div>
                    </div>

                    <?php the_content() ?>
                </div>

                <div data-aos="fade-left" data-aos-duration="1500" class="flex flex-col gap-6 w-full lg:max-w-[300px] 2xl:max-w-[437px]">
                    <div class="flex flex-col gap-5 p-6 rounded-xl bg-white border border-solid border-neutral-200">
                        <h2 class="text-body-lg-medium text-gray-8">Latest blog</h2>
                        <hr class="divider">
                        <?php if ($latest_blog_list->have_posts()): ?>
                            <?php while ($latest_blog_list->have_posts()):
                                $latest_blog_list->the_post();
                                $blog_id = get_the_ID();
                                $blog = get_field('blog', $blog_id);
                                $thumbnail_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
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
                                        <h2 class="text-body-md-medium text-gray-8 min-h-3lh truncate-3row">
                                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                        </h2>
                                        <p class="text-body-sm-regular text-neutral-500"><?= get_the_date() ?></p>
                                    </div>
                                </div>
                            <?php endwhile ?>
                        <?php else: ?>
                            <p class="text-center">Data is being updated</p>
                        <?php endif ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="pb-6 lg:pb-20">
        <div class="container">
            <div class="flex flex-col gap-6">
                <div data-aos="fade-down" data-aos-duration="1500" class="flex gap-6 items-center justify-between">
                    <div class="flex flex-col gap-2 flex-1">
                        <h2 class="text-heading-h3-5 text-gray-8">Related blog
                        </h2>
                    </div>
                    <?php
                    $related_posts_count = $related_posts_query->post_count;

                    if ($related_posts_query->have_posts() && $related_posts_count >= 3):
                    ?>
                        <div class="min-h-16 mt-5 lg:mt-0 flex items-end justify-end gap-3">
                            <button
                                class="button bg-white w-10 h-10 xl:w-16 xl:h-16 p-0 flex items-center justify-center shadow-button">
                                <figure class="w-6 xl:w-8 h-6 xl:h-8 rotate-180">
                                    <img src="<?= $url ?>/assets/image/icon/arrow-right.svg" alt="icon">
                                </figure>
                            </button>
                            <button
                                class="button bg-white w-10 h-10 xl:w-16 xl:h-16 p-0 flex items-center justify-center shadow-button">
                                <figure class="w-6 xl:w-8 h-6 xl:h-8">
                                    <img src="<?= $url ?>/assets/image/icon/arrow-right.svg" alt="icon">
                                </figure>
                            </button>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- vỉew -->
                <?php if ($related_posts_query->have_posts()): ?>
                    <div data-aos="fade-up" data-aos-duration="1500" class="grid grid-cols-1 xl:grid-cols-2 2xl:grid-cols-3 gap-6">
                        <?php while ($related_posts_query->have_posts()):
                            $related_posts_query->the_post();
                            $blog_id = get_the_ID();
                            $blog = get_field('blog', $blog_id);
                            $thumbnail_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
                        ?>
                            <!-- single item -->
                            <div
                                class="bg-white rounded-xl overflow-hidden flex flex-col gap-3 border border-solid border-neutral-300">
                                <div class="oveflow-hidden" style="aspect-ratio: 44/26;">
                                    <figure>
                                        <img class="hover:scale-110 ease-liner duration-500"
                                            src="<?= esc_url($thumbnail_url) ? esc_url($thumbnail_url) : get_field('image_no_image', 'option') ?>"
                                            alt="img">
                                    </figure>
                                </div>
                                <div class="p-5 flex flex-col gap-3">
                                    <div class="flex items-center gap-2">
                                        <figure class="w-5 h-5">
                                            <img src="<?= $url ?>/assets/image/icon/calendar.svg" alt="icon">
                                        </figure>
                                        <p class="text-body-sm-regular text-neutral-500"><?= get_the_date() ?></p>
                                    </div>
                                    <h2 class="text-body-md-medium text-gray-8 min-h-3lh truncate-3row">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h2>
                                    <p class="text-body-sm-regular text-neutral-500 min-h-3lh truncate-3row">
                                        <?php echo wp_strip_all_tags(apply_filters('the_content', get_the_content())); ?>
                                    </p>
                                    <a href="<?php the_permalink(); ?>" class="text-body-md-semibold text-primary">Read more</a>
                                </div>
                            </div>
                    </div>
                <?php endwhile ?>
            <?php else: ?>
                <p class="text-center">Data is being updated</p>
            <?php endif ?>
            </div>
        </div>
    </section>

</main>
<?php get_footer() ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const copyLink = document.getElementById('copy-url');

        copyLink.addEventListener('click', function(e) {
            e.preventDefault();

            const currentURL = window.location.href;

            try {
                // Tạo một phần tử input tạm thời
                const tempInput = document.createElement('input');
                tempInput.value = currentURL;
                document.body.appendChild(tempInput);

                // Chọn và sao chép nội dung
                tempInput.select();
                tempInput.setSelectionRange(0, 99999); // Hỗ trợ cho các thiết bị di động

                document.execCommand('copy');

                document.body.removeChild(tempInput);
            } catch (err) {
                console.error('Lỗi khi copy link: ', err);
            }
        });
    });
    document.addEventListener('DOMContentLoaded', function() {
        if (document.body.classList.contains('single-blog-active')) {
            document.querySelector('.menu-blog-item').classList.add('active');
        }
    });
</script>