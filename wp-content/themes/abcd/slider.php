<section class="banner-homepage">
    <div class="slider-banner js-slider">
        <?php
        $slider_args = array(
            'post_type' => 'slider', // Điều chỉnh post type tương ứng với slider nếu cần
            'posts_per_page' => -1, // Hiển thị tất cả các slide
        );

        $slider_query = new WP_Query($slider_args);

        if ($slider_query->have_posts()) :
            while ($slider_query->have_posts()) : $slider_query->the_post();
                ?>
                <div class="child">
                    <div class="img">
                        <figure>
                            <?php
                            if (has_post_thumbnail()) {
                                the_post_thumbnail('medium'); // Điều chỉnh kích thước ảnh tùy theo yêu cầu
                            }
                            ?>
                        </figure>
                    </div>
                    <div class="container">
                        <div class="text">
                            <div class="title">
                                <h2><?php the_title(); ?></h2>
                            </div>
                            <div class="desc">
                                <p><?php the_content(); ?></p>
                            </div>
                            <div class="action">
                                <a href="<?php the_permalink(); ?>">
                                    <button>View detail</button>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile;
            wp_reset_postdata();
        endif;
        ?>
    </div>
</section>
