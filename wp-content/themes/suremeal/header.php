<?php
$logo_header = get_field('logo_header', 'option');

$authenticated_user = validate_user_token();
if ($authenticated_user) {
    $user_email = $authenticated_user->email;
    $first_name = $authenticated_user->first_name;
    $last_name = $authenticated_user->last_name;
    $avatar = $authenticated_user->avatar;
}
$authenticated_dealer = validate_dealer_token();
if ($authenticated_dealer) {
    $user_email = $authenticated_dealer->email;
    $first_name = $authenticated_dealer->first_name;
    $last_name = $authenticated_dealer->last_name;
    $avatar = $authenticated_dealer->avatar;
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
<?php if(!$authenticated_dealer): ?>
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
    <header>
        <div class="header">
            <!-- header pc -->
            <div class="hidden lg:block fixed top-0 w-full h-[128px] z-[5]">
                <!-- first row -->
                <div class="bg-white">
                    <div
                        class="container flex items-center justify-between lg:gap-3 xl:gap-5 2xl:gap-[30px] 3xl:gap-[80px] py-2">
                        <a href="/">
                            <figure class="w-[128px]"><img src="<?= $logo_header ?>" alt="logo"></figure>
                        </a>
                        <form action="<?php echo home_url() ?>" class="w-full lg:max-w-[200px] 2xl:max-w-[325px]">
                            <input type="text" name="s" class="home-search"
                                placeholder="<?php pll_e('Search everything') ?>">
                        </form>
                        <div class="flex items-center lg:gap-3 2xl:gap-5">
                            <select name="language" id="language-select" class="input-language px-1">
                                <?php
                                $languages = pll_the_languages(array('raw' => true)); // Lấy danh sách ngôn ngữ
                                foreach ($arr_lang as $arr) {
                                    $name_nn = $arr['name_lang']; // Tên ngôn ngữ từ mảng $arr_lang
                                    if (ICL_LANGUAGE_CODE == $arr['lang']) {
                                        // Ngôn ngữ hiện tại
                                        echo '<option value="' . esc_url($languages[$arr['lang']]['url']) . '" selected>' . esc_html($name_nn) . '</option>';
                                    }
                                }

                                foreach ($languages as $lang) {
                                    if (ICL_LANGUAGE_CODE != $lang['slug']) {
                                        // Ngôn ngữ khác
                                        echo '<option value="' . esc_url($lang['url']) . '">' . esc_html($lang['name']) . '</option>';
                                    }
                                }
                                ?>
                            </select>


                            <a href="#!" class="head-hover flex items-center gap-[14px]">
                                <figure class="w-6 h-6"><img src="<?= $url ?>/assets/image/icon/user-round.svg"
                                        alt="icon">
                                </figure>
                                <div class="flex-col">
                                    <p class="text-body-sm-regular text-neutral-500"><?php pll_e('Sign in') ?></p>
                                    <p class="text-hover text-body-sm-semibold text-neutral-900"><?php pll_e('For Sale Partners') ?></p>
                                </div>
                            </a>
                            <a href="#!" class="head-hover flex items-center gap-[14px]">
                                <figure class="w-6 h-6"><img src="<?= $url ?>/assets/image/icon/user-round.svg"
                                        alt="icon">
                                </figure>
                                <div class="flex-col">
                                    <p class="text-body-sm-regular text-neutral-500"><?php pll_e('Sign in') ?></p>
                                    <p class="text-hover text-body-sm-semibold text-neutral-900"><?php pll_e('For Dealer') ?></p>
                                </div>
                            </a>
                            <?php if ($authenticated_user) : ?>
                                <a href="<?= home_url() ?>/person-info" class="head-hover flex items-center gap-[14px]">
                                    <figure class="w-6 h-6"><img style="border-radius: 50%;" src="<?= $avatar ? $avatar : $url . '/assets/image/dashboard/avatar-80.svg' ?>"
                                            alt="icon">
                                    </figure>
                                    <div class="flex-col">
                                        <p class="text-body-sm-regular text-neutral-500">Customer</p>
                                        <p class="text-hover text-body-sm-semibold text-neutral-900"><?= $first_name ?> <?= $last_name ?></p>
                                    </div>
                                </a>
                            <?php endif ?>
                            <div class="flex items-center gap-[14px]">
                                <figure class="relative w-6 h-6">
                                    <?php if (ICL_LANGUAGE_CODE == 'en') : ?>
                                        <a href="<? home_url() ?>/cart">
                                            <img src="<?= $url ?>/assets/image/icon/cart-shopping.svg" alt="icon">
                                        </a>
                                        <a href="<? home_url() ?>/cart" class="w-[14px] h-[14px]  rounded-full bg-primary absolute top-[-4px] right-[-4px] flex items-center justify-center text-body-sm-semibold text-[8px] text-white cart-count"
                                            id="">
                                            0</a>
                                    <?php elseif (ICL_LANGUAGE_CODE == 'vie') : ?>
                                        <a href="<? home_url() ?>/vie/cart">
                                            <img src="<?= $url ?>/assets/image/icon/cart-shopping.svg" alt="icon">
                                        </a>
                                        <a href="<? home_url() ?>/vie/cart" class="w-[14px] h-[14px]  rounded-full bg-primary absolute top-[-4px] right-[-4px] flex items-center justify-center text-body-sm-semibold text-[8px] text-white cart-count"
                                            id="">
                                            0</a>
                                    <?php elseif (ICL_LANGUAGE_CODE == 'es') : ?>
                                        <a href="<? home_url() ?>/es/cart">
                                            <img src="https://suremeal.qixtech.com/wp-content/themes/abcd/assets/image/icon/cart-shopping.svg" alt="icon">
                                        </a>
                                        <a href="<? home_url() ?>/es/cart" class="w-[14px] h-[14px]  rounded-full bg-primary absolute top-[-4px] right-[-4px] flex items-center justify-center text-body-sm-semibold text-[8px] text-white cart-count"
                                            id="">
                                            0</a>
                                    <?php endif; ?>
                                </figure>
                                <div class="flex-col">
                                    <a href="<? home_url() ?>/cart">
                                        <p class="text-body-sm-regular text-neutral-500 "><?php pll_e('Cart') ?></p>
                                        <p class="text-body-sm-semibold text-neutral-900 cart-total-price" id="">$0.00
                                        </p>
                                    </a>
                                </div>
                            </div>
                            <?php if (!$authenticated_user) { ?>
                                <a href="<?= home_url() ?>/sign-in"
                                    class="button bg-primary p-10-24 text-body-md-semibold"><?php pll_e('Sign in') ?></a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <!-- second row -->
                <div class="bg-secondary shadow-1 relative">
                    <div class="container flex items-center justify-center">
                        <!--
                    hiện tại đang demo nên để hover cả cụm sau fix lại
                        trong nav này sẽ để submenu và sub wrap vào cái item cần có sub, với layout
                        subwrap
                        
                        menu item
                        
                        sub menu
                        
                        /subwrap -->
                        <nav>
<!--                            --><?php
//                            wp_nav_menu(array(
//                                'menu'            => 'Menu primary',
//                                'container'       => 'ul',
//                                'container_class' => '',
//                                'menu_class' => 'flex items-center gap-2 2xl:gap-4 ',
//                                'depth'           => 2,
//                                'fallback_cb'     => 'wp_page_menu',
//                            ));
//                            ?>
                            <ul id="menu-menu-main" class="flex items-center gap-2 2xl:gap-4 ">
                                <?php foreach ($menu_main as $key => $item) :

                                    ?>
                                <li class=" <?php if ($item['check_menu_3'] == 'true') : ?>sub-wrap <?php endif;?> px-3 2xl:px-6 py-2 2xl:py-3 hover:bg-white hover:text-secondary text-body-md-semibold text-white">
                                    <a href="<?= $item['url']?>"><?= $item['title']?></a>
                                    <?php
                                    if (!empty($item['menu_sub'])) :
                                        ?>
                                        <?php if ($item['check_menu_3'] == 'true') :

                                        ?>
                                        <div class="sub-menu ">

                                            <div class="container grid grid-cols-4 gap-6">
                                                <?php
                                                foreach ($item['menu_sub'] as $key2 => $item2):

                                                    ?>
                                                    <!-- 1 col sub menu -->
                                                    <div class="w-full flex flex-col gap-6">
                                                        <!-- thẻ head của sub menu         -->
                                                        <a href="<?= $item2['url'] ?>" class="sub-menu-title flex items-center gap-2">
                                                            <figure class="w-6 h-6"><img src="<?= $item2['icon'] ?>" alt="icon"></figure>
                                                            <p class="text-body-md-semibold"><?= $item2['title'] ?></p>
                                                        </a>
                                                        <hr class="divider">
                                                        <!-- sub menu list ra ở đây -->

                                                        <div class="w-full flex flex-col gap-4">
                                                            <!-- 1 item menu -->
                                                            <?php foreach ($item2['menusub_2'] as $key3 => $item3) : ?>
                                                                <a href="<?= $item3['url'] ?>" class="sub-menu-hover">
                                                                    <p class="text-body-md-medium"><?= $item3['title'] ?></p>
                                                                </a>
                                                            <?php endforeach; ?>
                                                        </div>

                                                    </div>
                                                <?php endforeach; ?>

                                            </div>

                                        </div>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                </li>
                                <?php endforeach;?>
                            </ul>
                        </nav>
                    </div>
                    <!-- sub menu start -->

                </div>
            </div>
            <!-- header-mobile -->
            <div class="block lg:hidden fixed top-0 w-[100vw] bg-white z-[5]">
                <div class="flex mobile-header justify-between container py-5">
                    <a href="/">
                        <div class="logo">
                            <figure>
                                <img src="<?= $logo_header ?>" alt="logo">
                            </figure>
                        </div>
                    </a>
                    <div class="flex items-center gap-5">
                        <div class="flex items-center gap-[14px]">
                            <figure class="relative w-6 h-6">
                                <a href="<? home_url() ?>/cart">
                                    <img src="<?= $url ?>/assets/image/icon/cart-shopping.svg" alt="icon">
                                </a>
                                <a href="<? home_url() ?>/cart" class="w-[14px] h-[14px]  rounded-full bg-primary absolute top-[-4px] right-[-4px] flex items-center justify-center text-body-sm-semibold text-[8px] text-white cart-count"
                                    id="">
                                    0</a>
                            </figure>
                            <div class="flex-col">
                                <a href="<? home_url() ?>/cart">
                                    <p class="text-body-sm-regular text-neutral-500"><?php pll_e('Cart') ?></p>
                                    <p class="text-body-sm-semibold text-neutral-900 cart-total-price" id="">$0.00
                                    </p>
                                </a>
                            </div>
                        </div>
                        <button class="breadcrumb-button">
                            <figure>
                                <img src="<?= $url ?>/assets/image/icon/3bar.svg" alt="">
                            </figure>
                        </button>
                    </div>
                    <div
                        class="breadcrumb-header hidden fixed w-[80vw] max-w-[400px] h-[100vh] top-0 right-0 z-[5] shadow-1 transform transition-transform duration-300 ease-in-out translate-x-full">
                        <div class="flex flex-col gap-6 bg-white w-full max-h-[100vh] overflow-y-auto h-full px-6 pt-6">
                            <div class="flex items-center justify-between">
                                <a href="/">
                                    <div class="logo">
                                        <figure class="w-[80px]">
                                            <img src="<?= $logo_header ?>" alt="logo">
                                        </figure>
                                    </div>
                                </a>
                                <div class="flex items-center gap-2">
                                    <select name="language" id="language-select-1" class="input-language p-8-12 bg-neutral-100">
                                        <?php
                                        $languages = pll_the_languages(array('raw' => true)); // Lấy danh sách ngôn ngữ
                                        foreach ($arr_lang as $arr) {
                                            $name_nn = $arr['name_lang']; // Tên ngôn ngữ từ mảng $arr_lang
                                            if (ICL_LANGUAGE_CODE == $arr['lang']) {
                                                // Ngôn ngữ hiện tại
                                                echo '<option value="' . esc_url($languages[$arr['lang']]['url']) . '" selected>' . esc_html($name_nn) . '</option>';
                                            }
                                        }

                                        foreach ($languages as $lang) {
                                            if (ICL_LANGUAGE_CODE != $lang['slug']) {
                                                // Ngôn ngữ khác
                                                echo '<option value="' . esc_url($lang['url']) . '">' . esc_html($lang['name']) . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                    <button class="breadcrumb-close">
                                        <figure class="w-6 h-6">
                                            <img src="<?= $url ?>/assets/image/icon/close-24.svg" alt="">
                                        </figure>
                                    </button>
                                </div>
                            </div>
                            <div class="flex flex-col items-center gap-6 ">
                                <form action="<?php echo home_url() ?>" class="w-full">
                                    <input type="text" name="s" class="home-search xl:max-w-[200px] 2xl:max-w-[325px]"
                                        placeholder="<?php pll_e('Search everything') ?>">
                                </form>
                            </div>
                            <nav>
                                <?php
                                wp_nav_menu(array(
                                    'menu'            => 'Menu mobile',
                                    'container'       => 'ul',
                                    'container_class' => '',
                                    'menu_class'      => 'flex flex-col gap-2 2xl:gap-4 menu-mobile',
                                    'depth'           => 2,
                                    'fallback_cb'     => 'wp_page_menu',
                                ));
                                ?>
                            </nav>

                            <div class="flex flex-col gap-4">
                                <?php if ($authenticated_user) { ?>
                                    <a href="<?= home_url() ?>/person-info" class="head-hover flex flex-col items-center gap-[14px]">
                                        <figure class="w-6 h-6"><img style="border-radius: 50%;" src="<?= $avatar ? $avatar : $url . '/assets/image/dashboard/avatar-80.svg' ?>"
                                                alt="icon">
                                        </figure>
                                        <div class="flex-col">
                                            <p class="text-hover text-body-sm-semibold text-neutral-900"><?= $first_name ?> <?= $last_name ?></p>
                                        </div>
                                    </a>
                                <?php } else { ?>
                                    <a href="<?= home_url() ?>/sign-in"
                                        class="button bg-primary p-10-24 w-full text-body-md-semibold"><?php pll_e('Sign in') ?></a>
                                <?php } ?>

                                <div class="w-full grid grid-cols-2 gap-2">
                                    <a href="<?= home_url() ?>/sign-in" class="head-hover flex items-center gap-1">
                                        <figure class="w-6 h-6"><img src="<?= $url ?>/assets/image/icon/user-round.svg"
                                                alt="icon">
                                        </figure>
                                        <div class="flex-col">
                                            <p class="text-body-sm-regular text-neutral-500"><?php pll_e('Sign in') ?></p>
                                            <p class="text-hover text-body-sm-semibold text-neutral-900"><?php pll_e('For Sale Partners') ?>
                                            </p>
                                        </div>
                                    </a>
                                    <a href="<?= home_url() ?>/sign-in" class="head-hover flex items-center gap-1">
                                        <figure class="w-6 h-6"><img src="<?= $url ?>/assets/image/icon/user-round.svg"
                                                alt="icon">
                                        </figure>
                                        <div class="flex-col">
                                            <p class="text-body-sm-regular text-neutral-500"><?php pll_e('Sign in') ?></p>
                                            <p class="text-hover text-body-sm-semibold text-neutral-900"><?php pll_e('For Dealer') ?></p>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="h-[104px] xl:h-[120px]"></div>
    </header>
    <script>
        document.getElementById('language-select').addEventListener('change', function() {
            const selectedUrl = this.value; // Lấy URL từ giá trị của option đã chọn
            if (selectedUrl) {
                window.location.href = selectedUrl; // Chuyển hướng đến URL tương ứng
            }
        });
        document.getElementById('language-select-1').addEventListener('change', function() {
            const selectedUrl = this.value; // Lấy URL từ giá trị của option đã chọn
            if (selectedUrl) {
                window.location.href = selectedUrl; // Chuyển hướng đến URL tương ứng
            }
        });
    </script>
<?php else: ?>
    <!DOCTYPE html>
<html lang="">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head()?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?= $url ?>/dist/build/tailwind.css">
    <link rel="stylesheet" type="text/css" href="<?= $url ?>/dist/build/style.css">
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.js" defer></script>
</head>

<body x-data="backToTop()" x-init="init">

    <header>
        <div class="bg-white border-b border-solid border-neutral-200 px-4">
            <div class="flex flex-wrap items-center">
                <!-- logo -->
                <div class="flex items-center gap-3 px-6 py-3 border-r border-solid border-neutral-200">
                    <a href="#">
                        <figure class="w-[96px]">
                            <img src="<?= $url ?>/assets/image/head-logo.svg" alt="logo">
                        </figure>
                    </a>
                    <hr class="w-1 h-full border-none bg-[#030203]">
                    <a href="#">
                        <p class="text-heading-h7 uppercase text-secondary">Dealers <br>
                            Platform</p>
                    </a>
                </div>
                <div class="flex-1 flex items-center justify-between px-8 py-3 gap-5">
                    <p class="text-heading-h5 text-gray-8">Hi, &nbsp;<a href="#" class="text-secondary">Duong Nguyen</a></p>
                    <div class="flex items-center gap-3">
                        <figure class="w-10 h-10 rounded-full">
                            <img class="" src="<?= $url ?>/dist/img/avatar.png" alt="">
                        </figure>
                        <span class="text-body-lg-regular text-[#292d32]">Nguyen Duong</span>
                    </div>
                </div>
            </div>
        </div>
    </header>
<?php endif ?>