<?php /* Template Name: Contact */ ?>
<?php
$id = get_the_ID();
$contact_info = get_field('contact_info', $id);
$list_social_media = get_field('list_social_media', $id);
$address = get_field('address', $id);

get_header();
?>
    <main>
        <section class="py-6">
            <div class="container">
                <nav class="breadcrumb text-body-md-medium text-gray-8" aria-label="breadcrumb">
                    <!-- Sử dụng schema.org để định nghĩa breadcrumb list -->
                    <ol class="flex flex-wrap gap-3 items-center" itemscope
                        itemtype="https://schema.org/BreadcrumbList">

                        <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                            <a href="<?= home_url() ?>" class="text-secondary hover:text-primary" itemprop="item">
                                <span itemprop="name"><?php pll_e('Home') ?></span>
                            </a>
                            <meta itemprop="position" content="1" />
                        </li>
                        <span>/</span>
                        <!-- <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                            <a href="#" class="text-secondary hover:text-primary" itemprop="item">
                                <span itemprop="name">Blog</span>
                            </a>
                            <meta itemprop="position" content="1" />
                        </li>
                        <span>/</span> -->
                        <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"
                            aria-current="page">
                            <span itemprop="name"><?php pll_e('Contact Us') ?></span>
                            <meta itemprop="position" content="4" />
                        </li>

                    </ol>
                </nav>
            </div>
        </section>


        <section class="pt-6 lg:pt-10 pb-6 lg:pb-20 bg-[#EEF0F6]">
            <div class="container">
                <div class="flex justify-center">
                    <div class="max-w-[884px] flex flex-col items-center gap-2">
                        <h1 class="text-heading-h2 text-[#191C1F]"><?php pll_e('Contact Us') ?></h1>
                        <p class="text-body-lg-regular text-gray-9 text-center">
                            <?php echo wp_strip_all_tags(apply_filters('the_content', get_the_content())); ?>
                        </p>
                    </div>
                </div>

                <div class="flex flex-col lg:flex-row gap-6 mt-10">
                    <div class="w-full lg:max-w-[382px] grid grid-cols-1 gap-4">
                        <?php if($contact_info): ?>
                            <?php foreach($contact_info as $item): ?>
                                <div class="gap-5 p-5 rounded-[20px] bg-white flex items-center justify-center">
                                    <div class="w-[60px] h-[60px] flex items-center justify-center rounded-full"
                                        style="background: rgba(14, 116, 188, 0.16);">
                                        <figure class="w-[28px] h-[28px]"><img src="<?= $item['icon'] ?>"
                                                alt="icon">
                                        </figure>
                                    </div>
                                    <div class="flex-1 flex flex-col gap-1">
                                        <p class="text-body-lg-bold text-gray-8"><?= $item['name'] ?></p>
                                        <p class="text-body-sm-regular text-neutral-500"><?= $item['info'] ?></p>
                                    </div>
                                </div>
                            <?php endforeach ?>
                        <?php endif ?>  
                        <?php if($list_social_media): ?>  
                            <div class="gap-5 p-5 rounded-[20px] bg-white flex items-center justify-center">
                                <?php foreach($list_social_media as $item): ?>
                                    <a href="<?= $item['url'] ?>" class="">
                                        <figure class="w-10 h-10"><img src="<?= $item['icon'] ?>" alt="icon">
                                        </figure>
                                    </a>
                                <?php endforeach ?>
                            </div>
                        <?php endif ?>
                    </div>
                    <div class="w-full flex-1">
                        <div class="form-contact w-full flex flex-col gap-12 rounded-[20px] bg-white p-8">
                            <h2 class="text-heading-h4 text-gray-8"><?php pll_e('Send message') ?></h2>
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
                </div>
            </div>
        </section>

        <style>
            .iframe-container {
                position: relative;
                width: 100%;
                padding-top: calc(64 / 136 * 100%);
                /* Giữ tỷ lệ 136:64 */
                max-height: 638px;
            }

            .iframe-container iframe {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
            }
        </style>
        <section class="py-6 lg:py-20 bg-white">
            <div class="container">
                <div class="w-full flex flex-col items-center justify-center gap-6 lg:gap-10">
                    <h2 class="text-heading-h2 text-[#191C1F]">
                        <?php pll_e('Find us on Google Maps') ?>
                    </h2>
                    <div class="iframe-container">
                        <?= $address ?>
                        <!-- <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3725.339276478062!2d105.78312497584038!3d20.9790328894836!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135acd27916caa5%3A0xd1a865aa1cacdd14!2zMTAyIMSQxrDhu51uZyBUcuG6p24gUGjDuiwgUC4gTeG7mSBMYW8sIEjDoCDEkMO0bmcsIEjDoCBO4buZaSwgVmnhu4d0IE5hbQ!5e0!3m2!1svi!2s!4v1731312591640!5m2!1svi!2s"
                            style="border:0;" allowfullscreen="" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"></iframe> -->
                    </div>

                </div>
            </div>
        </section>

    </main>
<?php get_footer() ?>
<script defer>
    document.querySelectorAll('.form-contact .label').forEach(label => {
        label.classList.add('text-[#1f2237]');
    });
    document.querySelectorAll('.form-contact input[type="text"], .form-contact input[type="email"]').forEach(input => {
        input.classList.add('border-[#DEDFE0]', 'text-[#373A51]', 'placeholder-[#6B7280]', 'bg-[#F9FAFB]', 'w-full');
    });
    document.querySelectorAll('.form-contact textarea').forEach(textarea => {
        textarea.removeAttribute('cols');
        textarea.removeAttribute('rows');
        textarea.classList.add('min-h-[99px]', 'border-[#DEDFE0]', 'text-[#373A51]', 'placeholder-[#6B7280]', 'bg-[#F9FAFB]');
    });

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