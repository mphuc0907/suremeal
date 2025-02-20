<?php
global $wpdb;
$current_uri = str_replace('/', '', $_SERVER['REQUEST_URI']);

// Kiểm tra xem uri hiện có có phải là link affiliate hay không
//$check_campaign = $wpdb->get_row("SELECT * FROM wp_affiliate WHERE shortlink = '{$current_uri}'" . not_deleted);
$check_campaign = $wpdb->get_row($wpdb->prepare("SELECT * FROM wp_affiliate WHERE shortlink = %s", $current_uri));

if(!empty($check_campaign)) {


//    if(!empty($campaign)) {
    $current_request = $check_campaign->domain;

    wp_redirect($current_request . '/product?type=affiliate&distribution_code=' . $check_campaign->distribution_code);
//    }
}
?>

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
