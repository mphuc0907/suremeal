<?php
$logo_header = get_field('logo_header', 'option');

$authenticated_user = validate_user_token();
if ($authenticated_user) {
    $user_email = $authenticated_user->email;
    $first_name = $authenticated_user->first_name;
    $last_name = $authenticated_user->last_name;
    $avatar = $authenticated_user->avatar;
}

$arr_lang = languageArr();
$link_directory = get_template_directory_uri();
$url = get_template_directory_uri();
$lang = ICL_LANGUAGE_CODE;
if ($lang == 'en') {
    $arr = wp_get_nav_menu_items('Menu main');
}elseif ($lang == 'vie') {
    $arr = wp_get_nav_menu_items('Menu Vietnam PC');
}elseif ($lang == 'es') {
    $arr = wp_get_nav_menu_items('Menu Spanish (España)');
}


$url = get_template_directory_uri();
$sub_url = $_SERVER['REQUEST_URI'];

$link = home_url() . $sub_url;

$menu = [];
foreach ($arr as $key => $ar) {
    if ($ar->menu_item_parent == 0) {
        $menu[$key]['ID'] = $ar->ID;
        $menu[$key]['title'] = $ar->title;
        $menu[$key]['url'] = $ar->url;
        $menu[$key]['menu_item_parent'] = $ar->menu_item_parent;
        $menu[$key]['active'] = '';
        $menu[$key]['classes'] = $ar->classes;
        $menu[$key]['check_menu_3'] = 'false';
        //        if(get_field('short_des_page',$ar->object_id)!= '') {
        //            $menu[$key]['des_page'] = get_field('short_des_page', $ar->object_id);
        //        }
        $menusub = [];

        $i_sub = 0;
        foreach ($arr as $key_sub => $ar_sub) {
            if ($ar->ID == $ar_sub->menu_item_parent) {
                $icon = get_field('icon_menu', $ar_sub);
                $menusub[$i_sub]['ID'] = $ar_sub->ID;
                $menusub[$i_sub]['icon'] = $icon;
                $menusub[$i_sub]['title'] = $ar_sub->title;
                $menusub[$i_sub]['url'] = $ar_sub->url;
                $menusub[$i_sub]['menu_item_parent'] = $ar_sub->menu_item_parent;
                $menusub[$i_sub]['active'] = '';
                $menusub[$i_sub]['description'] = $ar_sub->description;
                //                $menu[$key]['check_menu_3'] = 'true';
                //                if(get_field('short_des_page',$ar_sub->object_id)!= '') {
                //                    $menusub[$key]['des_page'] = get_field('short_des_page', $ar_sub->object_id);
                //                }
                $menusub_2 = [];

                $i_sub2 = 0;
                foreach ($arr as $key_sub2 => $ar_sub2) {
                    if ($ar_sub->ID == $ar_sub2->menu_item_parent) {
                        $icon = get_field('icon_menu', $ar_sub2);
                        $menusub_2[$i_sub2]['ID'] = $ar_sub2->ID;
                        $menusub_2[$i_sub2]['icon'] = $icon;
                        $menusub_2[$i_sub2]['title'] = $ar_sub2->title;
                        $menusub_2[$i_sub2]['url'] = $ar_sub2->url;
                        $menusub_2[$i_sub2]['menu_item_parent'] = $ar_sub2->menu_item_parent;
                        $menusub_2[$i_sub2]['active'] = '';
                        $menusub_2[$i_sub2]['description'] = $ar_sub2->description;
                        $menu[$key]['check_menu_3'] = 'true';
                        //                        if(get_field('short_des_page', $ar_sub2->object_id)!= '') {
                        //                            $menusub_2[$i_sub2]['des_page'] = get_field('short_des_page', $ar_sub2->object_id);
                        //                        }
                        $menusub_3 = [];

                        $i_sub3 = 0;
                        foreach ($arr as $key_sub3 => $ar_sub3) {
                            if ($ar_sub2->ID == $ar_sub3->menu_item_parent) {
                                //                                $menusub_2[$i_sub2]['check_menu_3'] = 'true';
                                $menusub_3[$i_sub3]['ID'] = $ar_sub3->ID;
                                $menusub_3[$i_sub3]['title'] = $ar_sub3->title;
                                $menusub_3[$i_sub3]['url'] = $ar_sub3->url;
                                $menusub_3[$i_sub3]['menu_item_parent'] = $ar_sub3->menu_item_parent;
                                $menusub_3[$i_sub3]['active'] = '';
                                $menusub_3[$i_sub3]['description'] = $ar_sub3->description;
                                //                                if(get_field('short_des_page',$ar_sub3->object_id)!= '') {
                                //                                    $menusub_3[$i_sub3]['des_page'] = get_field('short_des_page', $ar_sub3->object_id);
                                //                                }
                            }
                        }

                        $menusub_2[$i_sub2]['menusub_3'] = $menusub_3;
                        $i_sub2++;
                    }
                }
                $menusub[$i_sub]['menusub_2'] = $menusub_2;

                $i_sub++;
            }
        }
        $menu[$key]['menu_sub'] = $menusub;
    }
}
$dem = 0;
$menu_1 = [];
foreach ($menu as $mn) {
    $menu_1[$dem] = $mn;
    $dem++;
}

$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$ke = strpos($actual_link, '?');
if (!empty($ke)) {
    $actual_link = substr($actual_link, 0, $ke);
}
$actual_link = rtrim($actual_link, '/');

$menu_main = [];

$parsedUrl = parse_url($link);
//url gốc
$baseUrl = $parsedUrl['scheme'] . '://' . $parsedUrl['host'] . (isset($parsedUrl['path']) ? $parsedUrl['path'] : '');



foreach ($menu_1 as $mn) {
    $check_active = 0;
    if ($mn['url'] == $baseUrl) {
        $check_active = 1;
    }

    if (!empty($mn['menu_sub'])) {
        foreach ($mn['menu_sub'] as $mn1) {
            if ($mn1['url'] == $baseUrl) {
                $check_active = 1;
            }

            if (!empty($mn1['menusub_2'])) {
                foreach ($mn1['menusub_2'] as $mn2) {
                    if ($mn2['url'] == $baseUrl) {
                        $check_active = 1;
                    }
                }
            }
        }
    }

    if ($check_active == 1) {
        //        echo "dat";
        $mn['active'] = 'active';
    }

    $menu_main[] = $mn;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="appleid-signin-client-id" content="[CLIENT_ID]">
    <meta name="appleid-signin-scope" content="[SCOPES]">
    <meta name="appleid-signin-redirect-uri" content="[REDIRECT_URI]">
    <meta name="appleid-signin-state" content="[STATE]">
    <meta name="appleid-signin-nonce" content="[NONCE]">
    <meta name="appleid-signin-use-popup" content="true">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head()?>
    <!-- taildwind css import -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="<?= $url ?>/assets/js/tailwind.config.js"></script>
    <!-- custom css -->
    <link rel="stylesheet" href="<?= $url ?>/assets/style/main.css" />
    <link rel="stylesheet" href="<?= $url ?>/assets/style/sang.css" />

    <!-- aos css -->
    <link rel="stylesheet" href="<?= $url ?>/assets/js/aos.css" />
    <!-- plus jar font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap"
        rel="stylesheet">

    <!-- swiper -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <!-- mz -->
    <link rel="stylesheet" href="<?= $url ?>/assets/magiczoom/magiczoom.css">
    <script src="<?= $url ?>/assets/magiczoom/magiczoom.js"></script>
    <!-- CSS -->
    <link rel="stylesheet" href="https://unpkg.com/flickity@2/dist/flickity.min.css">
    <!-- JavaScript -->
    <script src="https://unpkg.com/flickity@2/dist/flickity.pkgd.min.js"></script>
    <!-- jquery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <!-- fancy box -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.css"
        integrity="sha512-nNlU0WK2QfKsuEmdcTwkeh+lhGs6uyOxuUs+n+0oXSYDok5qy0EI0lt01ZynHq6+p/tbgpZ7P+yUb+r71wqdXg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <style>
        .head-hover:hover .text-hover {
            color: #0E74BC;
        }

        .active-menu-item {
            background: #FFF;
            color: #0E74BC;
        }

        .current-menu-item {
            background: #FFF;
            color: #0E74BC;
        }

        .active-menu-item-mobile {
            background: #0E74BC;
            color: #FFF;
        }

        .current-menu-item-mobile {
            background: #0E74BC;
            color: #FFF;
        }

        .menu-mobile li {
            background: #FFF;
            color: #1F2237;
            border-radius: 8px;
        }

        .menu-mobile li:hover {
            color: #0E74BC;
        }

        @media (max-width: 992px) {
            .active-menu-item {
                color: #0E74BC;
            }
        }
    </style>
    