<?php /* Template Name: About-Us */ ?>
<?php
$id = get_the_ID();
$group_story = get_field('group_story', $id);
$group_mission = get_field('group_mission', $id);
$group_discover = get_field('group_discover', $id);
$group_commit = get_field('group_commit', $id);
$group_health = get_field('group_health', $id);

$url = get_template_directory_uri();
get_header();
?>
<main>
    <section class="relative z-1">
        <div class="h-fit relative z-1">
            <figure class="figure-19-7"><img src="<?= get_the_post_thumbnail_url($id, 'full') ?>" alt="logo"></figure>
        </div>
        <div data-aos="fade-up" data-aos-duration="1500" class="absolute z-2 top-1/2 left-1/2 text-center" style="transform: translate(-50%, -50%);">
            <nav class="breadcrumb text-body-md-medium text-neutral-50" aria-label="breadcrumb">
                <!-- Sử dụng schema.org để định nghĩa breadcrumb list -->
                <ol class="flex flex-wrap gap-3 items-center justify-center" itemscope
                    itemtype="https://schema.org/BreadcrumbList">
                    <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                        <a href="<?= home_url() ?>" class="hover:text-primary" itemprop="item">
                            <span itemprop="name"><?php pll_e('Home') ?></span>
                        </a>
                        <meta itemprop="position" content="1" />
                    </li>
                    <span>/</span>
                    <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" aria-current="page">
                        <span itemprop="name"><?php pll_e('About Us') ?></span>
                        <meta itemprop="position" content="4" />
                    </li>
                </ol>
            </nav>
            <h1 class="text-heading-h1 text-neutral-50">
                <?php pll_e('About Us') ?>
            </h1>
        </div>
    </section>
    <section class="container pt-10 lg:pt-[100px] pb-20 lg:pb-[150px] flex flex-col items-center text-center">
        <h2 data-aos="fade-down" data-aos-duration="1500" class="text-heading-h2 font-bold text-secondary"><?= $group_story['title'] ?></h2>
        <p data-aos="fade-down" data-aos-duration="1500" class="text-body-md-regular text-gray-9 mt-2 max-w-[626px]">
            <?= $group_story['desc'] ?>
        </p>
        <div class="mt-10 flex flex-col gap-10 2xl:flex-row 2xl:gap-0 w-full relative">
            <div data-aos="fade-down-right" data-aos-duration="1500" class="2xl:min-w-[745px]">
                <figure><img class="min-w-full" src="<?= $group_story['image'] ?>" alt="about"></figure>
            </div>
            <div data-aos="fade-up-left" data-aos-duration="1500"
                class="2xl:absolute right-0 2xl:bottom-[-42px] 3xl:top-[65px] p-6 2xl:px-[52px] 2xl:py-10 2xl:max-w-[50%] 3xl:max-w-[673px] bg-[#D5F0FE] text-body-lg-regular text-gray-8 text-justify rounded-[20px]">
                <?= $group_story['content'] ?>
            </div>
        </div>
    </section>
    <section class="py-20 lg:py-[203px] bg-[55%] bg-cover bg-no-repeat bg-[url('<?= $group_mission['cover_img'] ?>')]">
        <div data-aos="zoom-in-up" data-aos-duration="1500" class="container">
            <h2 class="text-heading-h2 text-neutral-50 font-bold"><?= $group_mission['title'] ?></h2>
            <p class="text-heading-h7 font-medium text-neutral-100 max-w-[528px] mt-4 text-justify">
                <?= $group_mission['content'] ?>
            </p>
        </div>
    </section>
    <section class="py-20">
        <div class="container text-center">
            <h2 data-aos="fade-down" data-aos-duration="1500" class="text-heading-h2 text-secondary font-bold"><?= $group_discover['title'] ?></h2>
            <p data-aos="fade-down" data-aos-duration="1500" class="text-body-md-regular text-gray-9 mt-2 max-w-[556px] mx-auto">
                <?= $group_discover['desc'] ?>
            </p>
            <?php if ($group_discover['list_product_and_service']): ?>
                <div data-aos="fade-up" data-aos-duration="1500" class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-14">
                    <?php foreach ($group_discover['list_product_and_service'] as $item): ?>
                        <div
                            class="img-hover flex flex-col items-center justify-between rounded-[24px] bg-white p-6 border border-solid border-[#D1D5DB]">
                            <div class="w-full">
                                <div class="image overflow-hidden max-h-[230px]">
                                    <figure class="figure-40-23"><img src="<?= $item['image'] ?>" alt="icon commitment" /></figure>

                                </div>
                                <p class="text-heading-h6 text-gray-8 mt-6"><?= $item['name'] ?></p>
                                <p class="mt-2 text-body-md-regular text-gray-7 text-center"><?= $item['desc'] ?></p>
                            </div>
                            <a href="<?= $item['url'] ?>"
                                class="mt-6 px-8 py-3 button bg-primary text-body-md-semibold text-white">
                                <?= $item['btn_text'] ?>
                            </a>
                        </div>
                    <?php endforeach ?>
                </div>
            <?php else: ?>
                <p class="text-center"><?php pll_e('Data is being updated') ?></p>
            <?php endif ?>
        </div>
    </section>
    <section class="py-20 bg-cover bg-no-repeart bg-[url('<?= $group_commit['cover_img'] ?>')]">
        <div class="container text-center">
            <h2 data-aos="fade-down" data-aos-duration="1500" class="text-heading-h2 text-secondary font-bold"><?= $group_commit['title'] ?></h2>
            <p data-aos="fade-down" data-aos-duration="1500" class="text-body-md-regular text-gray-9 mt-2 max-w-[626px] mx-auto"><?= $group_commit['desc'] ?></p>
            <?php if ($group_commit['list_commit']): ?>
                <div data-aos="zoom-in-up" data-aos-duration="1500" class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-14">
                    <?php foreach ($group_commit['list_commit'] as $item): ?>
                        <div class="flex flex-col items-center rounded-[24px] bg-white p-6">
                            <figure><img src="<?= $item['image'] ?>" alt="icon commitment" /></figure>
                            <p class="text-heading-h6 text-gray-8 mt-6"><?= $item['title'] ?></p>
                            <p class="mt-2 text-body-md-regular text-justify text-gray-7">
                                <?= $item['content'] ?>
                            </p>
                        </div>
                    <?php endforeach ?>
                </div>
            <?php else: ?>
                <p class="text-center"><?php pll_e('Data is being updated') ?></p>
            <?php endif ?>
        </div>
    </section>
    <section class="bg-[url('<?= $group_health['cover_img'] ?>')] py-[100px] text-center bg-[0_-50px]">
        <div data-aos="zoom-in-up" data-aos-duration="1500" class="container">
            <h2 class="text-heading-h1 font-bold text-neutral-50"><?= $group_health['title'] ?></h2>
            <p class="text-heading-h7 font-medium text-neutral-200 mt-6 max-w-[698px] mx-auto">
                <?= $group_health['desc'] ?>
            </p>
        </div>
    </section>
    <?php
    get_template_part('template-parts/support-info');
    ?>
</main>
<?php get_footer() ?>
<!-- swiper script -->
<script defer>
    const swiper = new Swiper('#banner-swiper', {
        // Optional parameters
        slidesPerView: 1,
        slidesPerGroup: 1,
        spaceBetween: 0,
        loop: true,
        autoplay: true,
        breakpoints: {
            '576': {
                slidesPerView: 3,
                slidesPerGroup: 3,
                spaceBetween: 24,
            },
        },
        // If we need pagination
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        // If we need navigation
        navigation: {
            nextEl: '.swiper-btn-next',
            prevEl: '.swiper-btn-prev',
        },
    });
</script>