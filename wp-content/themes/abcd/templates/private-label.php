<?php /* Template Name: Private-Label */ ?>
<?php
$id = get_the_ID();
$group_manufacturing = get_field('group_manufacturing', $id);
$group_reason = get_field('group_reason', $id);
$group_consultation = get_field('group_consultation', $id);
$group_product = get_field('group_product', $id);
$group_how_it_work = get_field('group_how_it_work', $id);
$group_contact = get_field('group_contact', $id);
$group_commit = get_field('group_commit', $id);

$url = get_template_directory_uri();
get_header();
?>
<main>
    <style>
        .banner-image {
            aspect-ratio: 1920/ 790;
        }

        @media (max-width: 1024px) {
            .banner-image {
                aspect-ratio: unset;
                padding: 80px 0;
            }
        }

        .wpcf7-form-control-wrap {
            display: inline-grid;
            width: 100%;
        }
    </style>
    <section data-aos="zoom-in-up" data-aos-duration="1500"
        class="banner-image figure-19-7 w-full relative z-1 bg-[url('<?= get_the_post_thumbnail_url($id, 'full') ?>')] bg-cover bg-no-repeart">
        <!-- <div class="relative z-1 h-[832px]">
                <figure class="h-full"><img src="<?= get_the_post_thumbnail_url($id, 'full') ?>"
                        alt="logo">
                </figure>
            </div> -->
        <div class="flex flex-col h-full items-center justify-center">
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
                        <span itemprop="name"><?php pll_e('Private Label') ?></span>
                        <meta itemprop="position" content="4" />
                    </li>
                </ol>
            </nav>
            <h1 class="text-heading-h1 text-neutral-50">
                <?php pll_e('Private Label') ?>
            </h1>
            <p class="hidden xl:block text-body-lg text-neutral-200 w-[320px] sm:w-[500px] 2xl:w-[1004px]">
                <?php echo wp_strip_all_tags(apply_filters('the_content', get_the_content())); ?></p>
        </div>
    </section>
    <section class="container bg-white py-10 xl:py-20">
        <div data-aos="fade-down" data-aos-duration="1500" class="flex justify-between">
            <div>
                <h2 class="text-heading-h2 font-bold text-secondary">
                    <?= $group_manufacturing['title'] ?>
                </h2>
                <p class="text-body-md-regular mt-2 max-w-[671px] text-gray-7">
                    <?= $group_manufacturing['desc'] ?>
                </p>
            </div>
            <?php if ($group_manufacturing['list_img']): ?>
                <div class="hidden md:flex items-end gap-3">
                    <button
                        class="button bg-white w-16 h-16 p-0 flex items-center justify-center shadow-button swiper-btn-prev">
                        <figure class="w-8 h-8 rotate-180"><img src="<?= $url ?>/assets/image/icon/arrow-right.svg" alt="icon">
                        </figure>
                    </button>
                    <button
                        class="button bg-white w-16 h-16 p-0 flex items-center justify-center shadow-button swiper-btn-next">
                        <figure class="w-8 h-8"><img src="<?= $url ?>/assets/image/icon/arrow-right.svg" alt="icon">
                        </figure>
                    </button>
                </div>
            <?php endif ?>
        </div>

        <?php if ($group_manufacturing['list_img']): ?>
            <div data-aos="fade-up" data-aos-duration="1500" id="banner-swiper" class="swiper swiper-private-label mt-14">
                <!-- Additional required wrapper -->
                <div class="swiper-wrapper">
                    <!-- Slides -->
                    <?php foreach ($group_manufacturing['list_img'] as $item): ?>
                        <div class="swiper-slide">
                            <div class="gallery-item">
                                <figure>
                                    <img src="<?= $item['image'] ?>" class="" alt="slider" />
                                </figure>
                            </div>
                        </div>
                    <?php endforeach ?>
                </div>
                <!-- If we need pagination -->
                <div class="swiper-pagination gray-1"></div>
            </div>
        <?php endif ?>
    </section>
    <style>
        .why-choose {
            background: #FFF;
        }

        .why-choose:hover {
            background: linear-gradient(50deg, rgba(255, 255, 255, 0.00) -16.45%, rgba(0, 146, 249, 0.20) 100.19%), #FFF;
        }
    </style>
    <section class="container bg-white py-10 xl:py-20">
        <h2 data-aos="fade-down" data-aos-duration="1500" class="text-heading-h2 font-bold text-center text-secondary">
            <?= $group_reason['title'] ?>
        </h2>
        <?php if ($group_reason['list_reason']): ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6 mt-14">
                <?php foreach ($group_reason['list_reason'] as $item): ?>
                    <div data-aos="zoom-in-up" data-aos-duration="1500"
                        class="why-choose border border-solid border-neutral-300 rounded-3xl flex flex-col items-center text-center p-6">
                        <figure class="w-16 h-16">
                            <img src="<?= $item['image'] ?>" alt="icon">
                        </figure>
                        <h4 class="text-heading-h6 mt-6 text-gray-8">
                            <?= $item['title'] ?>
                        </h4>
                        <p class="text-body-md-regular mt-2 text-gray-7">
                            <?= $item['content'] ?>
                        </p>
                    </div>
                <?php endforeach ?>
            </div>
        <?php else: ?>
            <p class="text-center"><?php pll_e('Data is being updated') ?></p>
        <?php endif ?>
    </section>
    <section class="py-20 bg-[url('<?= $group_commit['cover_img'] ?>')] bg-cover">
        <div class="container text-center">
            <h2 data-aos="fade-down" data-aos-duration="1500" class="text-heading-h2 text-secondary font-bold"><?= $group_commit['title'] ?></h2>
            <p data-aos="fade-down" data-aos-duration="1500" class="text-body-md-regular text-gray-7 mt-2 max-w-[626px] mx-auto"><?= $group_commit['desc'] ?></p>
            <?php if ($group_commit['list_commit']): ?>
                <div data-aos="fade-up" data-aos-duration="1500" class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-14">
                    <?php foreach ($group_commit['list_commit'] as $item): ?>
                        <div class="flex flex-col items-center rounded-[24px] bg-white p-6">
                            <figure><img src="<?= $item['image'] ?>" alt="icon commitment" /></figure>
                            <p class="text-heading-h6 text-gray-8 mt-6 mb-2"><?= $item['title'] ?></p>
                            <p class="text-body-md-regular text-gray-7 text-justify"><?= $item['content'] ?></p>
                        </div>
                    <?php endforeach ?>
                </div>
            <?php else: ?>
                <p class="text-center"><?php pll_e('Data is being updated') ?></p>
            <?php endif ?>
        </div>
    </section>
    <section class="bg-[url('<?= $group_consultation['cover_img'] ?>')] bg-cover bg-no-repeat bg-center py-[104px]">
        <div data-aos="zoom-in-up" data-aos-duration="1500" class="container flex flex-col items-center">
            <h3 class="text-heading-h3 font-medium text-neutral-50 text-center max-w-[1004px]">
                <?= $group_consultation['content'] ?>
            </h3>
            <a href="#contact-section"
                class="block mt-10 button bg-primary text-body-md-semibold text-white"><?= $group_consultation['btn_text'] ?></a>
        </div>
    </section>
    <section class="bg-[url('<?= $group_product['cover_img'] ?>')] py-[100px]">
        <div class="container flex flex-col items-center text-neutral-50 text-center">
            <h2 data-aos="fade-down" data-aos-duration="1500" class="text-heading-h2 font-bold"><?= $group_product['title'] ?></h2>
            <p data-aos="fade-down" data-aos-duration="1500" class="text-body-md-regular mt-2 max-w-[626px]">
                <?= $group_product['desc'] ?>
            </p>
            <?php if ($group_product['list_product']): ?>
                <div data-aos="fade-up" data-aos-duration="1500" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-14">
                    <?php foreach ($group_product['list_product'] as $category_data):
                        $category = $category_data['category'][0];
                        $category_name = $category->name;
                        $category_slug = $category->slug;
                    ?>
                        <div class="why-choose rounded-[24px] bg-white p-6 border border-solid border-neutral-300">
                            <a href="<?= home_url() ?>/product?needs=<?= $category_slug ?>" class="flex flex-col items-center">
                                <figure class="w-16 h-16">
                                    <img src="<?= $category_data['image'] ?>" alt="icon" />
                                </figure>
                                <p class="text-heading-h6 text-gray-8 mt-6"><?= $category_name ?></p>
                                <p class="text-body-md-regular text-center text-gray-7"><?= $category_data['content'] ?></p>
                            </a>
                        </div>
                    <?php endforeach ?>
                </div>
            <?php else: ?>
                <p class="text-center"><?php pll_e('Data is being updated') ?></p>
            <?php endif ?>
        </div>
    </section>
    <style>
        .how-it-work {
            cursor: pointer;
            border-left: 3px solid #D1D5DB;
            margin-left: -3px;
        }

        .how-it-work:hover {
            background: linear-gradient(90deg, rgba(14, 116, 188, 0.10) 0%, rgba(255, 255, 255, 0.10) 80.23%);
            border-color: #0E74BC;
        }

        .how-it-work:hover p {
            color: #0E74BC;
        }

        html {
            scroll-behavior: smooth;
        }
    </style>
    <section class="bg-white container py-[100px]">
        <h2 data-aos="fade-down" data-aos-duration="1500" class="text-heading-h2 text-center text-secondary font-bold"><?= $group_how_it_work['title'] ?></h2>
        <div class="flex flex-wrap lg:flex-nowrap mt-14 gap-[74px]">
            <figure data-aos="fade-right" data-aos-duration="1500"><img class="2xl:w-[668px]" src="<?= $group_how_it_work['image'] ?>" alt="illus">
            </figure>
            <?php if ($group_how_it_work['list_step']): ?>
                <div data-aos="fade-left" data-aos-duration="1500" class="border-l-[3px] border-solid border-neutral-300 flex flex-col gap-3">
                    <?php
                    $i = 1;
                    foreach ($group_how_it_work['list_step'] as $item): ?>
                        <div class="how-it-work pl-[37px] flex flex-col gap-1 py-3">
                            <p class="text-body-md-regular text-gray-7"><?php pll_e('Step') ?> <?= $i ?></p>
                            <p class="text-heading-h6 font-semibold text-neutral-500"><?= $item['title'] ?></p>
                            <p class="text-body-md-regular text-neutral-500"><?= $item['content'] ?></p>
                        </div>
                    <?php
                        $i++;
                    endforeach ?>
                </div>
            <?php endif ?>
        </div>
    </section>
    <style>
        .icon-wrap-hover:hover svg path {
            stroke: #ED1B24;
        }

        .icon-wrap-hover:hover .info {
            color: #ED1B24;
        }
    </style>
    <section class="bg-secondary py-[100px]" id="contact-section">
        <div class="container flex flex-wrap xl:flex-nowrap">
            <div data-aos="fade-right" data-aos-duration="1500" class="xl:max-w-[492px]">
                <p class="text-heading-h2 text-neutral-50 font-bold"><?= $group_contact['title'] ?></p>
                <p class="text-body-md-regular text-neutral-50 mt-2"><?= $group_contact['desc'] ?></p>
                <?php if ($group_contact['contact_info']): ?>
                    <div class="mt-14 flex flex-col sm:flex-row xl:flex-col gap-10">
                        <?php foreach ($group_contact['contact_info'] as $item): ?>
                            <div class="icon-wrap-hover flex gap-5 items-center">
                                <figure>
                                    <?= $item['icon'] ?>
                                </figure>
                                <div class="text-white">
                                    <p class="text-body-lg-semibold "><?= $item['name'] ?></p>
                                    <a href="<?= $item['url'] ?>">
                                        <p class="info text-body-md-regular mt-1"><?= $item['info'] ?></p>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach ?>
                    </div>
                <?php endif ?>
            </div>
            <div data-aos="fade-left" data-aos-duration="1500" class="form-contact mt-10 xl:mt-0 2xl:ml-[129px] flex-1">
                <?php if(pll_current_language() == 'en'): ?>
                    <?php echo do_shortcode('[contact-form-7 id="0268a16" title="Form liên hệ" html_class="form-contact flex flex-col gap-4"]'); ?>
                <?php elseif(pll_current_language() == 'vn'): ?>
                    <?php echo do_shortcode('[contact-form-7 id="26ae51d" title="Form liên hệ(VN)" html_class="form-contact flex flex-col gap-4"]'); ?>
                <?php endif ?>
                <div class="notice text-[#FF0000]">
                    <span class="no"></span>
                </div>
            </div>
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
    document.querySelectorAll('.form-contact .label').forEach(label => {
        label.classList.add('text-[#F9FAFB]');
    });
    document.querySelectorAll('.form-contact input[type="text"], .form-contact input[type="email"]').forEach(input => {
        input.classList.add('border-[#DEDFE0]', 'text-[#373A51]', 'placeholder-[#6B7280]', 'bg-[#F9FAFB]');
    });
    document.querySelectorAll('.form-contact textarea').forEach(textarea => {
        textarea.removeAttribute('cols');
        textarea.removeAttribute('rows');
        textarea.classList.add('min-h-[99px]', 'border-[#DEDFE0]', 'text-[#373A51]', 'placeholder-[#6B7280]',
            'bg-[#F9FAFB]');
    });
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.form-contact form.wpcf7-form');
    const notice = document.querySelector('.form-contact .notice .no');
    const submitButton = form.querySelector('button[type="submit"]');
    const currentLang = '<?php echo pll_current_language(); ?>';
    
    const messages = {
        'en': {
            required: 'Please fill in all required fields',
            emailError: 'Please enter a valid email address',
            phoneError: 'Please enter a valid phone number',
            submitSuccess: 'Message sent successfully!',
            loading: 'Sending...',
            button: 'Send message',
            notice: 'An error occurred. Please try again.'
        },
        'vi': {
            required: 'Vui lòng điền đầy đủ thông tin',
            emailError: 'Vui lòng nhập đúng định dạng email',
            phoneError: 'Vui lòng nhập đúng định dạng số điện thoại',
            submitSuccess: 'Gửi tin nhắn thành công!',
            loading: 'Đang gửi...',
            button: 'Gửi tin nhắn',
            notice: 'Có lỗi xảy ra. Vui lòng thử lại.'
        }
    };

    if (form) {
        form.addEventListener('wpcf7beforesubmit', function(e) {
            let isValid = true;
            let errorMessage = '';
            
            // First priority: Check if all required fields are filled
            const requiredInputs = form.querySelectorAll('[aria-required="true"]');
            const emptyFields = Array.from(requiredInputs).filter(input => !input.value.trim());
            
            if (emptyFields.length > 0) {
                isValid = false;
                errorMessage = messages[currentLang].required;
            } 
            // Only check format validations if all required fields are filled
            else {
                // Validate email format
                const emailInput = form.querySelector('.your_email');
                if (emailInput && !validateEmail(emailInput.value)) {
                    isValid = false;
                    errorMessage = messages[currentLang].emailError;
                }
                // Only check phone if email is valid
                else {
                    // Validate phone format
                    const phoneInput = form.querySelector('.your_phone');
                    if (phoneInput && !validatePhone(phoneInput.value)) {
                        isValid = false;
                        errorMessage = messages[currentLang].phoneError;
                    }
                }
            }

            if (!isValid) {
                e.preventDefault();
                notice.innerHTML = errorMessage;
            } else {
                submitButton.innerHTML = messages[currentLang].loading;
            }
        });

        form.addEventListener('wpcf7mailsent', function() {
            notice.innerHTML = messages[currentLang].submitSuccess;
            form.reset();
            submitButton.innerHTML = messages[currentLang].button;
        });

        form.addEventListener('wpcf7mailfailed', function() {
            notice.innerHTML = messages[currentLang].notice;
            submitButton.innerHTML = messages[currentLang].button;
        });

        // Clear notice when user starts typing
        form.querySelectorAll('input, textarea').forEach(input => {
            input.addEventListener('input', function() {
                notice.innerHTML = '';
            });
        });
    }

    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(String(email).toLowerCase());
    }

    function validatePhone(phone) {
        // Accepts formats: +84xxxxxxxxx, 84xxxxxxxxx, 0xxxxxxxxx
        const re = /^(?:\+84|84|0)[1-9]\d{8}$/;
        return re.test(phone.replace(/\s/g, ''));
    }
});
</script>