<?php get_header(); ?>

<div class="container">
    <section class="error-404">
        <div class="content">
            <h1>404 - Page Not Found</h1>
            <p>The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.</p>
            <p>Please return to the <a href="<?php echo home_url(); ?>">homepage</a> or use the search form below to find what you are looking for:</p>
            <?php get_search_form(); ?>
        </div>
    </section>
</div>

<?php get_footer(); ?>
