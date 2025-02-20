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
    $dealer_email = $authenticated_dealer->email;
    $dealer_first_name = $authenticated_dealer->first_name;
    $dealer_last_name = $authenticated_dealer->last_name;
    $dealer_avatar = $authenticated_dealer->avatar;
}

$arr_lang = languageArr();
$link_directory = get_template_directory_uri();
$url = get_template_directory_uri();
$lang = ICL_LANGUAGE_CODE;
if ($lang == 'en') {
    $arr = wp_get_nav_menu_items('Menu main');
} elseif ($lang == 'vn') {
    $arr = wp_get_nav_menu_items('Menu Vietnam PC');
} elseif ($lang == 'es') {
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
                $hiden_menu_pott = get_field('hiden_menu', $ar_sub);
                $icon = get_field('icon_menu', $ar_sub);
                $menusub[$i_sub]['ID'] = $ar_sub->ID;
                $menusub[$i_sub]['icon'] = $icon;
                $menusub[$i_sub]['title'] = $ar_sub->title;
                $menusub[$i_sub]['url'] = $ar_sub->url;
                $menusub[$i_sub]['menu_item_parent'] = $ar_sub->menu_item_parent;
                $menusub[$i_sub]['active'] = '';
                $menusub[$i_sub]['hiden_menu'] = $hiden_menu_pott;
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
                        $hiden_menu = get_field('hiden_menu', $ar_sub2);
                        $menusub_2[$i_sub2]['ID'] = $ar_sub2->ID;
                        $menusub_2[$i_sub2]['icon'] = $icon;
                        $menusub_2[$i_sub2]['title'] = $ar_sub2->title;
                        $menusub_2[$i_sub2]['url'] = $ar_sub2->url;
                        $menusub_2[$i_sub2]['hiden_menu'] = $hiden_menu;
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
                                $hiden_menu2 = get_field('hiden_menu', $ar_sub3);
                                //                                $menusub_2[$i_sub2]['check_menu_3'] = 'true';
                                $menusub_3[$i_sub3]['ID'] = $ar_sub3->ID;
                                $menusub_3[$i_sub3]['hiden_menu'] = $hiden_menu2;
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

$id_resller = isset($_GET['distribution_code']) ? $_GET['distribution_code'] : '';

if ($id_resller) {
    setcookie("distribution_code", $id_resller, time() + (86400 * 3), "/"); // Cookie lưu 3 ngày

    // Kiểm tra xem distribution_code có tồn tại không
    $check_campaign = $wpdb->get_row($wpdb->prepare("SELECT * FROM wp_affiliate WHERE distribution_code = %s", $id_resller));

    if ($check_campaign) {
        // Update total_click tăng thêm 1
        $wpdb->query(
            $wpdb->prepare(
                "UPDATE wp_affiliate SET total_click = total_click + 1 WHERE distribution_code = %s",
                $id_resller
            )
        );
    }
}


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
$platform_url = str_replace(['http://', 'https://'], ['http://platform.', 'https://platform.'], home_url());

$page_title = get_the_title();
?>
<?php if (!$authenticated_dealer): ?>
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
        <?php wp_head() ?>
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

        <!-- <link rel="stylesheet" type="text/css" href="<?= $url ?>/dist/build/tailwind.css"> -->
        <!-- <link rel="stylesheet" type="text/css" href="<?= $url ?>/dist/build/style.css"> -->
        <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.js" defer></script>
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
        <header class="relative z-[5]">
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
                            <form action="<?php echo home_url() ?>" class="relative w-full lg:max-w-[200px] 2xl:max-w-[325px]">
                                <input type="text" name="s" class="home-search no-bg"
                                    placeholder="<?php pll_e('Search everything') ?>">
                                <button class="button bg-trans absolute right-6 top-[50%] translate-y-[-50%]" type="submit">
                                    <figure class="w-4 h-4"><img src="<?= $url ?>/assets/image/icon/mag-glass.svg" alt=""></figure>
                                </button>
                            </form>
                            <div class="flex items-center lg:gap-3 2xl:gap-5">
                                <?php if ($page_title != 'Sign-in dealer' && $page_title != 'Sign-up dealer') :?>
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
                                            if ($lang['slug'] != 'es') {
                                                // Ngôn ngữ khác
                                                echo '<option value="' . esc_url($lang['url']) . '">' . esc_html($lang['name']) . '</option>';
                                            }
                                        }
                                    }
                                    ?>
                                </select>
                                <?php endif;?>

                                <a href="<?= $platform_url ?>" class="head-hover flex items-center gap-[14px]">
                                    <figure class="w-6 h-6"><img src="<?= $url ?>/assets/image/icon/user-round.svg"
                                            alt="icon">
                                    </figure>
                                    <div class="flex-col">
                                        <p class="text-body-sm-regular text-neutral-500"><?php pll_e('Login') ?></p>
                                        <p class="text-hover text-body-sm-semibold text-neutral-900"><?php pll_e('For Sale Partners') ?></p>
                                    </div>
                                </a>
                                <a href="#" id="modal-dealer" class="head-hover flex items-center gap-[14px]">
                                    <figure class="w-6 h-6"><img src="<?= $url ?>/assets/image/icon/user-round.svg"
                                            alt="icon">
                                    </figure>
                                    <div class="flex-col">
                                        <p class="text-body-sm-regular text-neutral-500"><?php pll_e('Login') ?></p>
                                        <p class="text-hover text-body-sm-semibold text-neutral-900"><?php pll_e('For Dealer') ?></p>
                                    </div>
                                </a>
                                <?php if ($authenticated_user) : ?>
                                    <a href="<?= home_url() ?>/person-info" class="head-hover flex items-center gap-[14px]">
                                        <figure class="w-6 h-6"><img style="border-radius: 50%;" src="<?= $url . '/assets/image/dashboard/avatar-80.svg' ?>"
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
                                            <a href="<? home_url() ?>/cart" class="w-[14px] h-[14px]  rounded-full bg-primary absolute top-[-4px] right-[-4px] flex items-center justify-center text-body-sm-semibold !text-[8px] text-white cart-count"
                                                id="">
                                                0</a>
                                        <?php elseif (ICL_LANGUAGE_CODE == 'vn') : ?>
                                            <a href="<? home_url() ?>/vn/cart">
                                                <img src="<?= $url ?>/assets/image/icon/cart-shopping.svg" alt="icon">
                                            </a>
                                            <a href="<? home_url() ?>/vn/cart" class="w-[14px] h-[14px]  rounded-full bg-primary absolute top-[-4px] right-[-4px] flex items-center justify-center text-body-sm-semibold !text-[8px] text-white cart-count"
                                                id="">
                                                0</a>
                                        <?php elseif (ICL_LANGUAGE_CODE == 'es') : ?>
                                            <a href="<? home_url() ?>/es/cart">
                                                <img src="https://suremeal.qixtech.com/wp-content/themes/abcd/assets/image/icon/cart-shopping.svg" alt="icon">
                                            </a>
                                            <a href="<? home_url() ?>/es/cart" class="w-[14px] h-[14px]  rounded-full bg-primary absolute top-[-4px] right-[-4px] flex items-center justify-center text-body-sm-semibold !text-[8px] text-white cart-count"
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
                                        class="button bg-primary p-10-24 text-body-md-semibold"><?php pll_e('Login') ?></a>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <!-- second row -->
                    <div class="bg-secondary shadow-1 relative">
                        <div class="container flex items-center justify-center">

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
                                                                    //                            
                                                                    ?>
                                <ul id="menu-menu-main" class="flex items-center gap-2 2xl:gap-4 ">
                                    <?php foreach ($menu_main as $key => $item) :

                                    ?>
                                        <li class=" <?php if ($item['check_menu_3'] == 'true') : ?>sub-wrap <?php endif; ?> <?php if ($item['active'] == 'active') :  ?>bg-white  text-secondary<?php else: ?> text-white<?php endif; ?> px-3 2xl:px-6 py-2 2xl:py-3 hover:bg-white hover:text-secondary text-body-md-semibold">
                                            <a href="<?= $item['url'] ?>"><?= $item['title'] ?></a>
                                            <?php
                                            if (!empty($item['menu_sub'])) :
                                            ?>
                                                <?php if ($item['check_menu_3'] == 'true') :

                                                ?>
                                                    <div class="sub-menu ">

                                                        <div class="container grid grid-cols-4 gap-6">
                                                            <?php
                                                            foreach ($item['menu_sub'] as $key2 => $item2):
                                                            if ($item2['hiden_menu'] != 1) :
                                                            ?>
                                                                <!-- 1 col sub menu -->
                                                                <div class="w-full flex flex-col gap-6">
                                                                    <!-- thẻ head của sub menu         -->
                                                                    <div class="sub-menu-title flex items-center gap-2">
                                                                        <figure class="w-6 h-6"><img src="<?= $item2['icon'] ?>" alt="icon"></figure>
                                                                        <p class="text-body-md-semibold"><?= $item2['title'] ?></p>
                                                                    </div>
                                                                    <hr class="divider">
                                                                    <!-- sub menu list ra ở đây -->

                                                                    <div class="w-full flex flex-col gap-4">
                                                                        <!-- 1 item menu -->
                                                                        <?php foreach ($item2['menusub_2'] as $key3 => $item3) :
                                                                           if ($item3['hiden_menu'] != 1) :
                                                                            ?>
                                                                            <a href="<?= $item3['url'] ?>" class="sub-menu-hover">
                                                                                <p class="text-body-md-medium"><?= $item3['title'] ?></p>
                                                                            </a>
                                                                        <?php
                                                                         endif;
                                                                         endforeach; ?>
                                                                    </div>

                                                                </div>
                                                            <?php
                                                             endif;
                                                             endforeach; ?>

                                                        </div>

                                                    </div>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </li>
                                    <?php endforeach; ?>
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
                        <div class="flex items-center gap-2 lg:gap-5">
                            <div class="flex items-center gap-[14px]">
                                <figure class="relative w-6 h-6">
                                    <a href="<? home_url() ?>/cart">
                                        <img src="<?= $url ?>/assets/image/icon/cart-shopping.svg" alt="icon">
                                    </a>
                                    <a href="<? home_url() ?>/cart" class="w-[14px] h-[14px]  rounded-full bg-primary absolute top-[-4px] right-[-4px] flex items-center justify-center text-body-sm-semibold !text-[8px] text-white cart-count"
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
                            class="breadcrumb-header hidden fixed w-[90vw] max-w-[400px] h-[100vh] top-0 right-0 z-[5] shadow-1 transform transition-transform duration-300 ease-in-out translate-x-full">
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
                                <style>
                                    .open-sub-mobile,
                                    .sub-sub-button {
                                        transform: rotate(180deg);
                                        transition: all .5s ease-in-out;
                                    }

                                    .open-sub-mobile.active,
                                    .sub-sub-button.active {
                                        transform: rotate(0);
                                        transition: all .5s ease-in-out;
                                    }
                                </style>
                                <nav>
                                    <ul id="menu-menu-main" class="flex flex-col items-center gap-2 2xl:gap-4 ">
                                        <?php foreach ($menu_main as $key => $item) :

                                        ?>
                                            <li class=" <?php if ($item['check_menu_3'] == 'true') : ?>sub-wrap  <?php endif; ?> <?php if ($item['active'] == 'active') :  ?>text-secondary <?php else: ?> text-gray-9<?php endif; ?> w-full px-3 2xl:px-6 py-2 2xl:py-3 hover:text-secondary text-body-md-semibold">
                                                <div class="flex items-center justify-between">
                                                    <a href="<?= $item['url'] ?>"><?= $item['title'] ?></a>
                                                    <div class="<?= $item['check_menu_3'] == 'true' ? 'block' : 'hidden' ?> open-sub-mobile">
                                                        <figure class="w-6 h-6">
                                                            <img src="<?= $url ?>/assets/image/icon/chev-up-24.svg" alt="">
                                                        </figure>
                                                    </div>
                                                </div>
                                                <?php
                                                if (!empty($item['menu_sub'])) :
                                                ?>
                                                    <?php if ($item['check_menu_3'] == 'true') :

                                                    ?>
                                                        <div class="sub-menu mobile">

                                                            <div class="grid grid-cols-1 gap-6">
                                                                <?php
                                                                foreach ($item['menu_sub'] as $key2 => $item2):

                                                                ?>
                                                                    <!-- 1 col sub menu -->
                                                                    <div class="sub-sub-wrap w-full flex flex-col gap-4 py-2 px-1">
                                                                        <!-- thẻ head của sub menu         -->
                                                                        <div class="sub-sub-title flex items-center gap-2">
                                                                            <figure class="w-6 h-6"><img src="<?= $item2['icon'] ?>" alt="icon"></figure>
                                                                            <p class="text-body-md-semibold"><?= $item2['title'] ?></p>
                                                                            <figure class="sub-sub-button ml-[auto] w-5 h-5">
                                                                                <img src="<?= $url ?>/assets/image/icon/chev-up-24.svg" alt="">
                                                                            </figure>
                                                                        </div>
                                                                        <hr class="divider">
                                                                        <!-- sub menu list ra ở đây -->

                                                                        <div class="sub-sub-content w-full flex flex-col gap-4">
                                                                            <!-- 1 item menu -->
                                                                            <?php foreach ($item2['menusub_2'] as $key3 => $item3) :
                                                                            if ($item3['hiden_menu'] != 1) :
                                                                                ?>
                                                                                <a href="<?= $item3['url'] ?>" class="sub-menu-hover">
                                                                                    <p class="text-body-md-medium"><?= $item3['title'] ?></p>
                                                                                </a>
                                                                            <?php
                                                                             endif;
                                                                             endforeach; ?>
                                                                        </div>

                                                                    </div>
                                                                <?php endforeach; ?>

                                                            </div>

                                                        </div>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </nav>

                                <script>
                                    document.querySelectorAll('.open-sub-mobile').forEach(item => {
                                        item.addEventListener('click', function(event) {
                                            event.stopPropagation(); // Ngừng sự kiện bọt nổi
                                            const menu = document.querySelector('.sub-menu.mobile');
                                            menu.classList.toggle('show');
                                            menu.classList.toggle('pt-2');
                                            this.classList.toggle('active');
                                        });
                                    });
                                    // Lấy tất cả các thẻ .sub-sub-title
                                    document.querySelectorAll('.sub-sub-title').forEach(item => {
                                        item.addEventListener('click', function(event) {
                                            // Ngừng sự kiện bọt nổi để tránh ảnh hưởng đến thẻ cha
                                            event.stopPropagation();

                                            // Tìm thẻ .sub-sub-wrap cha của thẻ được click
                                            const subWrap = this.closest('.sub-sub-wrap');
                                            // Toggle class 'show' cho thẻ .sub-sub-wrap
                                            subWrap.classList.toggle('show');
                                            subWrap.classList.toggle('border-neutral-50');
                                            // thêm sub sub button
                                            const button = this.querySelector('.sub-sub-button');
                                            if (button) {
                                                button.classList.toggle('active');
                                            }
                                        });
                                    });
                                </script>

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
                                            class="button bg-primary p-10-24 w-full text-body-md-semibold"><?php pll_e('Login') ?></a>
                                    <?php } ?>

                                    <div class="w-full grid grid-cols-2 gap-2">
                                        <a href="<?= $platform_url ?>" class="head-hover flex items-center gap-1">
                                            <figure class="w-6 h-6"><img src="<?= $url ?>/assets/image/icon/user-round.svg"
                                                    alt="icon">
                                            </figure>
                                            <div class="flex-col">
                                                <p class="text-body-sm-regular text-neutral-500"><?php pll_e('Login') ?></p>
                                                <p class="text-hover text-body-sm-semibold text-neutral-900"><?php pll_e('For Sale Partners') ?>
                                                </p>
                                            </div>
                                        </a>
                                        <a href="<?= home_url() ?>/sign-in-dealer" id="modal-dealer-1" class="head-hover flex items-center gap-1">
                                            <figure class="w-6 h-6"><img src="<?= $url ?>/assets/image/icon/user-round.svg"
                                                    alt="icon">
                                            </figure>
                                            <div class="flex-col">
                                                <p class="text-body-sm-regular text-neutral-500"><?php pll_e('Login') ?></p>
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
            <div id="modal-overlay" class="hidden fixed inset-0 z-40" style="background: rgba(51, 51, 51, 0.60);"></div>
            <div id="popup-modal" tabindex="-1" class="hidden flex overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                <div class="relative w-full max-w-3xl max-h-full">
                    <div class="relative bg-white rounded-lg shadow dark:bg-gray-700 p-10">
                        <p class="text-xl mt-0 mb-6 text-center" style="color: #333; font-size: 32px; font-weight: 800; line-height: 140%;">Welcome to SureMeal's Dealers Platform</p>
                        <p class="text-center mb-8" style="color: #333; font-size: 16px; font-weight: 400; line-height: 150%;">Enjoy exclusive pricing available only for dealers! Join us today. To become one of our dealers, simply sign up now and start benefiting from these fantastic deals.</p>
                        <div class="flex items-center justify-between">
                            <a href="#" id="exit-dealer" class="text-[#FF0000] border-[#FF0000] rounded-lg w-full sm:w-auto px-28 py-3 text-center no-underline" style="background: #FFF; font-weight: 600; border: 1px solid;">Maybe later</a>
                            <a href="<?= home_url() ?>/sign-in-dealer" class="text-white border-none rounded-lg w-full sm:w-auto px-28 py-3 text-center no-underline" style="background: #ED1B24; font-weight: 600;">Sign up now</a>
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
            const modalDealer = document.querySelector('#modal-dealer');
            const modalDealer1 = document.querySelector('#modal-dealer-1');
            const overlay = document.getElementById('modal-overlay');
            const modal = document.getElementById('popup-modal');
            const exitDealer = document.getElementById('exit-dealer');
            modalDealer.addEventListener('click', (e) => {
                e.preventDefault();
                overlay.classList.remove('hidden');
                overlay.classList.add('block');

                // Show modal
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                exitDealer.addEventListener('click', (e) => {
                    overlay.classList.remove('block');
                    overlay.classList.add('hidden');

                    // Hide modal
                    modal.classList.remove('flex');
                    modal.classList.add('hidden');
                });
            });
        </script>
    <?php else: ?>
        <!DOCTYPE html>
        <html lang="">

        <head>
            <meta charset="utf-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <?php wp_head() ?>
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
            <!-- jquery -->
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
            <!-- fancy box -->
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.css"
                integrity="sha512-nNlU0WK2QfKsuEmdcTwkeh+lhGs6uyOxuUs+n+0oXSYDok5qy0EI0lt01ZynHq6+p/tbgpZ7P+yUb+r71wqdXg=="
                crossorigin="anonymous" referrerpolicy="no-referrer" />
            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
            <link rel="stylesheet" type="text/css" href="<?= $url ?>/dist/build/tailwind.css">
            <link rel="stylesheet" type="text/css" href="<?= $url ?>/dist/build/style.css">
            <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.js" defer></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js" defer></script>
            <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
            <style>
                @import url(https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/5.3.45/css/materialdesignicons.min.css);
                @import url(https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.css);
            </style>
        </head>

        <body x-data="backToTop()" x-init="init">
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
            <header class="relative z-[5]">
                <div class="header">
                    <!-- header pc -->
                    <div class="hidden lg:block fixed top-0 w-full h-[137px] z-[5]">
                        <div class="bg-white border-b border-solid border-neutral-200 px-4">
                            <div class="flex flex-wrap items-center">
                                <!-- logo -->
                                <div class="flex items-center gap-3 px-6 py-3 border-r border-solid border-neutral-200">
                                    <a href="#">
                                        <figure class="w-[96px]">
                                            <img src="<?= $url ?>/assets/image/head-logo.svg" alt="logo">
                                        </figure>
                                    </a>
                                    <hr class="w-[1px] min-h-[50px] h-full border-none bg-[#030203]">
                                    <a href="<?= home_url() ?>">
                                        <p class="text-heading-h7 uppercase text-secondary">Dealers <br>
                                            Platform</p>
                                    </a>
                                </div>
                                <div class="flex-1 flex items-center justify-between px-8 py-3 gap-5">
                                    <p class="text-heading-h5 text-gray-8">Hi, &nbsp;<a href="#" class="text-secondary"><?= $dealer_first_name ?> <?= $dealer_last_name ?></a></p>
                                    <div class="flex items-center gap-3">
                                        <div class="flex items-center gap-[14px]">
                                            <figure class="relative w-6 h-6">
                                                <?php if (ICL_LANGUAGE_CODE == 'en') : ?>
                                                    <a href="<? home_url() ?>/cart">
                                                        <img src="<?= $url ?>/assets/image/icon/cart-shopping.svg" alt="icon">
                                                    </a>
                                                    <a href="<? home_url() ?>/cart" class="w-[14px] h-[14px]  rounded-full bg-primary absolute top-[-4px] right-[-4px] flex items-center justify-center text-body-sm-semibold !text-[8px] text-white cart-count"
                                                        id="">
                                                        0</a>
                                                <?php elseif (ICL_LANGUAGE_CODE == 'vn') : ?>
                                                    <a href="<? home_url() ?>/vn/cart">
                                                        <img src="<?= $url ?>/assets/image/icon/cart-shopping.svg" alt="icon">
                                                    </a>
                                                    <a href="<? home_url() ?>/vn/cart" class="w-[14px] h-[14px]  rounded-full bg-primary absolute top-[-4px] right-[-4px] flex items-center justify-center text-body-sm-semibold !text-[8px] text-white cart-count"
                                                        id="">
                                                        0</a>
                                                <?php elseif (ICL_LANGUAGE_CODE == 'es') : ?>
                                                    <a href="<? home_url() ?>/es/cart">
                                                        <img src="https://suremeal.qixtech.com/wp-content/themes/abcd/assets/image/icon/cart-shopping.svg" alt="icon">
                                                    </a>
                                                    <a href="<? home_url() ?>/es/cart" class="w-[14px] h-[14px]  rounded-full bg-primary absolute top-[-4px] right-[-4px] flex items-center justify-center text-body-sm-semibold !text-[8px] text-white cart-count"
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
                                        <?php if ($authenticated_dealer) : ?>
                                            <div class="relative min-w-[220px]">
                                                <div class="open-info-button flex items-center justify-between gap-2 cursor-pointer">
                                                    <div class="flex items-center gap-3">
                                                        <figure class="w-10 h-10 rounded-full">
                                                            <img class="" src="<?= $url . '/assets/image/dashboard/avatar-80.svg' ?>" alt="">
                                                        </figure>
                                                        <p class="text-body-lg-regular text-[#292D32]"><?= $dealer_first_name ?> <?= $dealer_last_name ?></p>
                                                    </div>
                                                    <figure class="w-6 h-6 open-info-chev"><img src="<?= $url ?>/assets/image/icon/chev-down-16.svg" alt=""></figure>
                                                </div>
                                                <div class="open-info-modal ">
                                                    <div class="open-info-row">
                                                        <a href="<?= home_url() ?>/dealer-personal-informmation" class="flex items-center gap-2">
                                                            <figure class="w-6 h-6">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M8.25 9C8.25 6.92893 9.92893 5.25 12 5.25C14.0711 5.25 15.75 6.92893 15.75 9C15.75 11.0711 14.0711 12.75 12 12.75C9.92893 12.75 8.25 11.0711 8.25 9ZM12 6.75C10.7574 6.75 9.75 7.75736 9.75 9C9.75 10.2426 10.7574 11.25 12 11.25C13.2426 11.25 14.25 10.2426 14.25 9C14.25 7.75736 13.2426 6.75 12 6.75Z" fill="#292D32" />
                                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M1.25 12C1.25 6.06294 6.06294 1.25 12 1.25C17.9371 1.25 22.75 6.06294 22.75 12C22.75 17.9371 17.9371 22.75 12 22.75C6.06294 22.75 1.25 17.9371 1.25 12ZM12 2.75C6.89137 2.75 2.75 6.89137 2.75 12C2.75 14.5456 3.77827 16.851 5.4421 18.5235C5.6225 17.5504 5.97694 16.6329 6.68837 15.8951C7.75252 14.7915 9.45416 14.25 12 14.25C14.5457 14.25 16.2474 14.7915 17.3115 15.8951C18.023 16.6329 18.3774 17.5505 18.5578 18.5236C20.2217 16.8511 21.25 14.5456 21.25 12C21.25 6.89137 17.1086 2.75 12 2.75ZM17.1937 19.6554C17.0918 18.4435 16.8286 17.5553 16.2318 16.9363C15.5823 16.2628 14.3789 15.75 12 15.75C9.62099 15.75 8.41761 16.2628 7.76815 16.9363C7.17127 17.5553 6.90811 18.4434 6.80622 19.6553C8.28684 20.6618 10.0747 21.25 12 21.25C13.9252 21.25 15.7131 20.6618 17.1937 19.6554Z" fill="#292D32" />
                                                                </svg>
                                                            </figure>
                                                            <p class="text-body-md-regular">Personal Infomation</p>
                                                        </a>
                                                    </div>
                                                    <div class="open-info-row">
                                                        <form class="logout-button" id="logoutForm" action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" method="POST">
                                                            <input type="hidden" name="action" value="logout">
                                                            <button type="submit" class="flex items-center gap-2">
                                                                <figure class="w-6 h-6">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                                        <path d="M14.9453 1.25C13.5778 1.24998 12.4754 1.24996 11.6085 1.36652C10.7084 1.48754 9.95048 1.74643 9.34857 2.34835C8.82363 2.87328 8.55839 3.51836 8.41916 4.27635C8.28387 5.01291 8.25799 5.9143 8.25196 6.99583C8.24966 7.41003 8.58357 7.74768 8.99778 7.74999C9.41199 7.7523 9.74964 7.41838 9.75194 7.00418C9.75803 5.91068 9.78643 5.1356 9.89448 4.54735C9.99859 3.98054 10.1658 3.65246 10.4092 3.40901C10.686 3.13225 11.0746 2.9518 11.8083 2.85315C12.5637 2.75159 13.5648 2.75 15.0002 2.75H16.0002C17.4356 2.75 18.4367 2.75159 19.1921 2.85315C19.9259 2.9518 20.3144 3.13225 20.5912 3.40901C20.868 3.68577 21.0484 4.07435 21.1471 4.80812C21.2486 5.56347 21.2502 6.56459 21.2502 8V16C21.2502 17.4354 21.2486 18.4365 21.1471 19.1919C21.0484 19.9257 20.868 20.3142 20.5912 20.591C20.3144 20.8678 19.9259 21.0482 19.1921 21.1469C18.4367 21.2484 17.4356 21.25 16.0002 21.25H15.0002C13.5648 21.25 12.5637 21.2484 11.8083 21.1469C11.0746 21.0482 10.686 20.8678 10.4092 20.591C10.1658 20.3475 9.99859 20.0195 9.89448 19.4527C9.78643 18.8644 9.75803 18.0893 9.75194 16.9958C9.74964 16.5816 9.41199 16.2477 8.99778 16.25C8.58357 16.2523 8.24966 16.59 8.25196 17.0042C8.25799 18.0857 8.28387 18.9871 8.41916 19.7236C8.55839 20.4816 8.82363 21.1267 9.34857 21.6517C9.95048 22.2536 10.7084 22.5125 11.6085 22.6335C12.4754 22.75 13.5778 22.75 14.9453 22.75H16.0551C17.4227 22.75 18.525 22.75 19.392 22.6335C20.2921 22.5125 21.0499 22.2536 21.6519 21.6517C22.2538 21.0497 22.5127 20.2919 22.6337 19.3918C22.7503 18.5248 22.7502 17.4225 22.7502 16.0549V7.94513C22.7502 6.57754 22.7503 5.47522 22.6337 4.60825C22.5127 3.70814 22.2538 2.95027 21.6519 2.34835C21.0499 1.74643 20.2921 1.48754 19.392 1.36652C18.525 1.24996 17.4227 1.24998 16.0551 1.25H14.9453Z" fill="#292D32" />
                                                                        <path d="M15 11.25C15.4142 11.25 15.75 11.5858 15.75 12C15.75 12.4142 15.4142 12.75 15 12.75H4.02744L5.98809 14.4306C6.30259 14.7001 6.33901 15.1736 6.06944 15.4881C5.79988 15.8026 5.3264 15.839 5.01191 15.5694L1.51191 12.5694C1.34567 12.427 1.25 12.2189 1.25 12C1.25 11.7811 1.34567 11.573 1.51191 11.4306L5.01191 8.43056C5.3264 8.16099 5.79988 8.19741 6.06944 8.51191C6.33901 8.8264 6.30259 9.29988 5.98809 9.56944L4.02744 11.25H15Z" fill="#292D32" />
                                                                    </svg>
                                                                </figure>
                                                                <p class="text-body-md-regular">Log out</p>
                                                            </button>
                                                        </form>

                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-secondary shadow-1 relative">
                            <div class="container flex items-center justify-center">
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
                                                                        //                            
                                                                        ?>
                                    <ul id="menu-menu-main" class="flex items-center gap-2 2xl:gap-4 ">
                                        <?php foreach ($menu_main as $key => $item) :

                                        ?>
                                            <li class=" <?php if ($item['check_menu_3'] == 'true') : ?>sub-wrap <?php endif; ?> <?php if ($item['active'] == 'active') :  ?>bg-white  text-secondary<?php else: ?> text-white<?php endif; ?> px-3 2xl:px-6 py-2 2xl:py-3 hover:bg-white hover:text-secondary text-body-md-semibold">
                                                <a href="<?= $item['url'] ?>"><?= $item['title'] ?></a>
                                                <?php
                                                if (!empty($item['menu_sub'])) :
                                                ?>
                                                    <?php if ($item['check_menu_3'] == 'true') :

                                                    ?>
                                                        <div class="sub-menu ">

                                                            <div class="container grid grid-cols-4 gap-6">
                                                                <?php
                                                                foreach ($item['menu_sub'] as $key2 => $item2):
                                                                if ($item2['hiden_menu'] != 1) :
                                                                ?>
                                                                    <!-- 1 col sub menu -->
                                                                    <div class="w-full flex flex-col gap-6">
                                                                        <!-- thẻ head của sub menu         -->
                                                                        <div class="sub-menu-title flex items-center gap-2">
                                                                            <figure class="w-6 h-6"><img src="<?= $item2['icon'] ?>" alt="icon"></figure>
                                                                            <p class="text-body-md-semibold"><?= $item2['title'] ?></p>
                                                                        </div>
                                                                        <hr class="divider">
                                                                        <!-- sub menu list ra ở đây -->

                                                                        <div class="w-full flex flex-col gap-4">
                                                                            <!-- 1 item menu -->
                                                                            <?php foreach ($item2['menusub_2'] as $key3 => $item3) :  if ($item3['hiden_menu'] != 1) :
                                                                                ?>
                                                                                <a href="<?= $item3['url'] ?>" class="sub-menu-hover">
                                                                                    <p class="text-body-md-medium"><?= $item3['title'] ?></p>
                                                                                </a>
                                                                            <?php
                                                                            endif; endforeach; ?>
                                                                        </div>

                                                                    </div>
                                                                <?php
                                                                 endif;
                                                                 endforeach; ?>

                                                            </div>

                                                        </div>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </nav>
                            </div>
                            <!-- sub menu start -->

                        </div>
                    </div>
                    <!-- header-mobile -->
                    <div class="block lg:hidden fixed top-0 w-[100vw] bg-white z-[5]">
                        <div class="flex mobile-header justify-between container py-5">
                            <!-- logo -->
                            <div class="flex items-center gap-2 px-2 py-1 lg:gap-3 lg:px-6 lg:py-3 border-r border-solid border-neutral-200">
                                <a href="#">
                                    <figure class="w-[96px]">
                                        <img src="<?= $url ?>/assets/image/head-logo.svg" alt="logo">
                                    </figure>
                                </a>
                                <hr class="w-[1px] min-h-[50px] h-full border-none bg-[#030203]">
                                <a href="#">
                                    <p class="text-heading-h7 uppercase text-secondary">Dealers <br>
                                        Platform</p>
                                </a>
                            </div>
                            <div class="flex items-center gap-2 lg:gap-5">
                                <div class="flex items-center gap-[14px]">
                                    <figure class="relative w-6 h-6">
                                        <a href="<? home_url() ?>/cart">
                                            <img src="<?= $url ?>/assets/image/icon/cart-shopping.svg" alt="icon">
                                        </a>
                                        <a href="<? home_url() ?>/cart" class="w-[14px] h-[14px]  rounded-full bg-primary absolute top-[-4px] right-[-4px] flex items-center justify-center text-body-sm-semibold !text-[8px] text-white cart-count"
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
                                class="breadcrumb-header hidden fixed w-[90vw] max-w-[400px] h-[100vh] top-0 right-0 z-[5] shadow-1 transform transition-transform duration-300 ease-in-out translate-x-full">
                                <div class="flex flex-col gap-6 bg-white w-full max-h-[100vh] overflow-y-auto h-full px-6 pt-6">
                                    <div class="flex items-center justify-between">
                                        <!-- logo -->
                                        <div class="flex items-center gap-2 px-2 py-1 lg:gap-3 lg:px-6 lg:py-3 ">
                                            <a href="#">
                                                <figure class="w-[96px]">
                                                    <img src="<?= $url ?>/assets/image/head-logo.svg" alt="logo">
                                                </figure>
                                            </a>
                                            <hr class="w-[1px] min-h-[50px] h-full border-none bg-[#030203]">
                                            <a href="#">
                                                <p class="text-heading-h7 uppercase text-secondary">Dealers <br>
                                                    Platform</p>
                                            </a>
                                        </div>
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
                                        <div class="flex items-center gap-4">
                                            <?php if ($authenticated_dealer) : ?>
                                                <a href="<?= home_url() ?>/dealer-personal-informmation">
                                                    <figure class="w-10 h-10 rounded-full">
                                                        <img class="" src="<?= $dealer_avatar ? $dealer_avatar : $url . '/assets/image/dashboard/avatar-80.svg' ?>" alt="">
                                                    </figure>
                                                </a>
                                                <form class="logout-button" id="logoutForm" action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" method="POST">
                                                    <input type="hidden" name="action" value="logout">
                                                    <button type="submit" class="text-body-lg-regular text-[#292d32]"><?= $dealer_first_name ?> <?= $dealer_last_name ?></button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                        <form action="<?php echo home_url() ?>" class="w-full">
                                            <input type="text" name="s" class="home-search xl:max-w-[200px] 2xl:max-w-[325px]"
                                                placeholder="<?php pll_e('Search everything') ?>">
                                        </form>
                                    </div>
                                    <style>
                                        .open-sub-mobile,
                                        .sub-sub-button {
                                            transform: rotate(180deg);
                                            transition: all .5s ease-in-out;
                                        }

                                        .open-sub-mobile.active,
                                        .sub-sub-button.active {
                                            transform: rotate(0);
                                            transition: all .5s ease-in-out;
                                        }
                                    </style>
                                    <nav>
                                        <ul id="menu-menu-main" class="flex flex-col items-center gap-2 2xl:gap-4 ">
                                            <?php foreach ($menu_main as $key => $item) :

                                            ?>
                                                <li class=" <?php if ($item['check_menu_3'] == 'true') : ?>sub-wrap  <?php endif; ?> <?php if ($item['active'] == 'active') :  ?>text-secondary <?php else: ?> text-gray-9<?php endif; ?> w-full px-3 2xl:px-6 py-2 2xl:py-3 hover:text-secondary text-body-md-semibold">
                                                    <div class="flex items-center justify-between">
                                                        <a href="<?= $item['url'] ?>"><?= $item['title'] ?></a>
                                                        <div class="<?= $item['check_menu_3'] == 'true' ? 'block' : 'hidden' ?> open-sub-mobile">
                                                            <figure class="w-6 h-6">
                                                                <img src="<?= $url ?>/assets/image/icon/chev-up-24.svg" alt="">
                                                            </figure>
                                                        </div>
                                                    </div>
                                                    <?php
                                                    if (!empty($item['menu_sub'])) :
                                                    ?>
                                                        <?php if ($item['check_menu_3'] == 'true') :

                                                        ?>
                                                            <div class="sub-menu mobile">

                                                                <div class="grid grid-cols-1 gap-6">
                                                                    <?php
                                                                    foreach ($item['menu_sub'] as $key2 => $item2):

                                                                    ?>
                                                                        <!-- 1 col sub menu -->
                                                                        <div class="sub-sub-wrap w-full flex flex-col gap-4 py-2 px-1">
                                                                            <!-- thẻ head của sub menu         -->
                                                                            <div class="sub-sub-title flex items-center gap-2">
                                                                                <figure class="w-6 h-6"><img src="<?= $item2['icon'] ?>" alt="icon"></figure>
                                                                                <p class="text-body-md-semibold"><?= $item2['title'] ?></p>
                                                                                <figure class="sub-sub-button ml-[auto] w-5 h-5">
                                                                                    <img src="<?= $url ?>/assets/image/icon/chev-up-24.svg" alt="">
                                                                                </figure>
                                                                            </div>
                                                                            <hr class="divider">
                                                                            <!-- sub menu list ra ở đây -->

                                                                            <div class="sub-sub-content w-full flex flex-col gap-4">
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
                                            <?php endforeach; ?>
                                        </ul>
                                    </nav>

                                    <script>
                                        document.querySelectorAll('.open-sub-mobile').forEach(item => {
                                            item.addEventListener('click', function(event) {
                                                event.stopPropagation(); // Ngừng sự kiện bọt nổi
                                                const menu = document.querySelector('.sub-menu.mobile');
                                                menu.classList.toggle('show');
                                                menu.classList.toggle('pt-2');
                                                this.classList.toggle('active');
                                            });
                                        });
                                        // Lấy tất cả các thẻ .sub-sub-title
                                        document.querySelectorAll('.sub-sub-title').forEach(item => {
                                            item.addEventListener('click', function(event) {
                                                // Ngừng sự kiện bọt nổi để tránh ảnh hưởng đến thẻ cha
                                                event.stopPropagation();

                                                // Tìm thẻ .sub-sub-wrap cha của thẻ được click
                                                const subWrap = this.closest('.sub-sub-wrap');
                                                // Toggle class 'show' cho thẻ .sub-sub-wrap
                                                subWrap.classList.toggle('show');
                                                subWrap.classList.toggle('border-neutral-50');
                                                // thêm sub sub button
                                                const button = this.querySelector('.sub-sub-button');
                                                if (button) {
                                                    button.classList.toggle('active');
                                                }
                                            });
                                        });
                                    </script>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="h-[116px] xl:h-[137px]"></div>
                </div>

            </header>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    const logoutForm = document.getElementById("logoutForm");

                    logoutForm.addEventListener("click", function(event) {
                        event.preventDefault();

                        Swal.fire({
                            title: 'Are you sure?',
                            text: "You will be logged out and your cart will be cleared!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes, log me out!',
                            cancelButtonText: 'Cancel'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Xoa storage
                                localStorage.removeItem('cart');

                                logoutForm.submit();
                            }
                        });
                    });
                });
            </script>

            <script defer>
                document.addEventListener("DOMContentLoaded", function() {
                    const openInfoButton = document.querySelector(".open-info-button");
                    const openInfoModal = document.querySelector(".open-info-modal");
                    const openInfoChev = document.querySelector(".open-info-chev");

                    // Toggle modal and chev active state
                    openInfoButton.addEventListener("click", function(event) {
                        openInfoModal.classList.toggle("active");
                        openInfoChev.classList.toggle("active");
                        event.stopPropagation(); // Ngăn chặn sự kiện click lan ra ngoài
                    });

                    // Ẩn modal và chev khi click bên ngoài
                    document.addEventListener("click", function() {
                        openInfoModal.classList.remove("active");
                        openInfoChev.classList.remove("active");
                    });

                    // Ngăn click vào modal không đóng modal
                    openInfoModal.addEventListener("click", function(event) {
                        event.stopPropagation();
                    });
                });
            </script>
        <?php endif ?>