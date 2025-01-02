<?php
get_header();

$author_id = get_query_var('author');
$author_name = get_the_author_meta('display_name', $author_id);
$author_description = get_the_author_meta('description', $author_id);
$author_avatar = get_avatar_url($author_id);

?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">

        <article class="author-info">
            <header class="author-header">
                <h1 class="author-title"><?php echo esc_html($author_name); ?></h1>
                <div class="author-avatar">
                    <img src="<?php echo esc_url($author_avatar); ?>" alt="<?php echo esc_attr($author_name); ?>" />
                </div>
            </header>
            <div class="author-description">
                <p><?php echo wp_kses_post($author_description); ?></p>
            </div>
        </article>

        <section class="author-posts">
            <h2><?php echo esc_html__('Bài viết của', 'your-theme-text-domain') . ' ' . esc_html($author_name); ?></h2>

            <?php
            $author_posts = new WP_Query(array(
                'author' => $author_id,
                'post_type' => 'post',
                'posts_per_page' => -1 // Hiển thị tất cả các bài viết của tác giả
            ));

            if ($author_posts->have_posts()) :
                while ($author_posts->have_posts()) : $author_posts->the_post();
            ?>

                    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                        <header class="entry-header">
                            <div class="post-thumbnail">
                                <?php
                                if (has_post_thumbnail()) {
                                    the_post_thumbnail('thumbnail'); // Hiển thị hình ảnh đại diện
                                }
                                ?>
                            </div>
                            <h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                        </header>
                    </article>

            <?php
                endwhile;
                wp_reset_postdata(); // Đặt lại dữ liệu bài viết sau khi vòng lặp kết thúc
            else :
                echo esc_html__('Không có bài viết nào của tác giả này.', 'your-theme-text-domain');
            endif;
            ?>

        </section>

    </main>
</div>

<?php get_footer(); ?>
