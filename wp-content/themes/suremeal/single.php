<?php
get_header();
?>

<main id="primary" class="site-main">

    <?php
    while (have_posts()) :
        the_post();

        get_template_part('template-parts/content', get_post_type());
        ?>

        <div class="acf-maps">
            <?php
            $maps = get_field('ban_do_2');

            if ($maps) :
                foreach ($maps as $map) :
                    $map_id = $map->ID;
                    ?>
                    <div class="acf-map" id="map-<?php echo $map_id; ?>">
                        <!-- Hiển thị bản đồ ở đây, sử dụng JavaScript -->
                        <?php echo get_field('interactive_map', $map_id); ?>
                    </div>
                <?php endforeach;
            endif;
            ?>
        </div>

        <?php
        the_post_navigation(
            array(
                'prev_text' => '<span class="nav-subtitle">' . esc_html__('Previous:', 'abcd') . '</span> <span class="nav-title">%title</span>',
                'next_text' => '<span class="nav-subtitle">' . esc_html__('Next:', 'abcd') . '</span> <span class="nav-title">%title</span>',
            )
        );

        if (comments_open() || get_comments_number()) :
            comments_template();
        endif;

    endwhile; // End of the loop.
    ?>

</main><!-- #main -->

<?php
// get_sidebar();
get_footer();
