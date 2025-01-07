<?php /* Template Name: Faq */ ?>
<?php
$id = get_the_ID();
$title = get_field('title', $id);
$desc = get_field('desc', $id);
$general = get_field('general', $id);
$health = get_field('health', $id);

$url = get_template_directory_uri();
get_header();
?>
<style>
    .accorditions-list {
        flex: 1;
    }

    .accorditions-list:not(.active) {
        display: none;
    }
</style>
<main class="bg-[#EEF0F6]">
    <section class="py-6">
        <div class="container">
            <nav class="breadcrumb text-body-md-medium text-gray-8" aria-label="breadcrumb">
                <!-- Sử dụng schema.org để định nghĩa breadcrumb list -->
                <ol class="flex flex-wrap gap-3 items-center" itemscope itemtype="https://schema.org/BreadcrumbList">
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
                    <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" aria-current="page">
                        <span itemprop="name"><?php pll_e('FAQs') ?></span>
                        <meta itemprop="position" content="4" />
                    </li>
                </ol>
            </nav>
        </div>
    </section>

    <section class="pt-6 lg:pt-10 pb-6 lg:pb-20">
        <div class="container">
            <div data-aos="fade-down" data-aos-duration="1500" class="flex justify-center">
                <div class="max-w-[884px] flex flex-col items-center gap-2">
                    <h1 class="text-heading-h2 text-[#191C1F] text-center"><?= $title ?></h1>
                    <p class="text-body-lg-regular text-gray-9 text-center">
                        <?= $desc ?>
                    </p>
                </div>
            </div>
            <div class="mt-20">
                <div class="flex flex-col lg:flex-row w-full gap-6 justify-between">
                    <div class="flex flex-wrap gap-3">
                        <div class="text-body-md-medium tab-item home active" data-tab="general"><?php pll_e('General') ?></div>
                        <div class="text-body-md-medium tab-item home" data-tab="health"><?php pll_e('Health') ?></div>
                    </div>
                    <form id="faq-search-form" class="flex items-center xl:w-[200px] 2xl:w-[386px]">
                        <input type="text" name="search_term"
                            class="home-search radius-8 xl:max-w-[200px] 2xl:max-w-[386px]"
                            placeholder="<?php pll_e('Search by keywords') ?>" />
                        <button type="submit" class="ml-2 hidden"><?php pll_e('Search') ?></button>
                    </form>
                </div>
            </div>
            <div class="flex flex-col lg:flex-row gap-6 mt-6 lg:mt-10">
                <div data-aos="fade-right" data-aos-duration="1500" class="accorditions-list flex flex-col gap-3 active" id="general">
                    <?php if ($general): ?>
                        <?php foreach ($general as $item): ?>
                            <div class="accorditions-list-item bg-white p-6 rounded-xl border border-solid border-neutral-200">
                                <button
                                    class="accordion w-full flex gap-5 justify-between items-center border border-neutral-200">
                                    <span class="text-start text-body-xl-medium"><?= $item['question'] ?></span>
                                    <img src="<?= $url ?>/assets/image/icon/chev-down-16.svg" alt="" />
                                </button>
                                <div class="panel transition-max-height duration-200 ease-out overflow-hidden max-h-[0]">
                                    <p class="text-body-md-regular text-gray-7">
                                        <?= $item['answer'] ?>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach ?>
                    <?php else: ?>
                        <p class="text-center"><?php pll_e('Data is being updated') ?></p>
                    <?php endif ?>
                </div>
                <div data-aos="fade-right" data-aos-duration="1500" class="accorditions-list flex flex-col gap-3" id="health">
                    <?php if ($health): ?>
                        <?php foreach ($health as $item): ?>
                            <div class="accorditions-list-item bg-white p-6 rounded-xl border border-solid border-neutral-200">
                                <button
                                    class="accordion w-full flex gap-5 justify-between items-center border border-color-[#E5E7EB]">
                                    <span class="text-start text-body-xl-medium"><?= $item['question'] ?></span>
                                    <img src="<?= $url ?>/assets/image/icon/chev-down-16.svg" alt="" />
                                </button>
                                <div class="panel transition-max-height duration-200 ease-out overflow-hidden max-h-[0]">
                                    <p class="text-body-md-regular text-gray-7">
                                        <?= $item['answer'] ?>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach ?>
                    <?php else: ?>
                        <p class="text-center"><?php pll_e('Data is being updated') ?></p>
                    <?php endif ?>
                </div>
                <div data-aos="fade-left" data-aos-duration="1500" class="w-full flex flex-col gap-6 rounded-[20px] bg-white p-8 xl:max-w-[461px]">
                    <div class="flex flex-col gap-2">
                        <h2 class="text-heading-h6 text-neutral-700"><?php pll_e('Any question?') ?></h2>
                        <span class="text-neutral-500"><?php pll_e('Our support agents are ready with the answer.') ?></span>
                    </div>

                    <?php echo do_shortcode('[contact-form-7 id="6864d53" title="Form đặt câu hỏi" html_class="flex flex-col gap-4"]'); ?>
                </div>
            </div>
        </div>
    </section>

    <?php
    get_template_part('template-parts/support-info');
    ?>
</main>
<?php get_footer() ?>
<script defer>
    document.querySelector('textarea[name="your-message"]').setAttribute('rows', '5');

    document.addEventListener('DOMContentLoaded', function() {
        // Hàm thiết lập sự kiện accordion
        function setupAccordionEvents() {
            // Chọn tất cả các nút accordion trong tab hiện tại đang active
            const activeTabList = document.querySelector('.accorditions-list.active');
            if (!activeTabList) return;

            const allAccordions = activeTabList.querySelectorAll('.accordion');

            allAccordions.forEach(accordion => {
                // Xóa sự kiện cũ để tránh gán nhiều lần
                accordion.onclick = null;

                // Thêm sự kiện mới
                accordion.addEventListener('click', function() {
                    this.classList.toggle("active");
                    const panel = this.nextElementSibling;
                    const iconAccordition = this.querySelector("img");

                    if (panel.classList.contains('max-h-[0]')) {
                        panel.classList.remove('max-h-[0]');
                        panel.style.maxHeight = panel.scrollHeight + "px";
                        panel.style.marginTop = "20px";
                        iconAccordition.style.transform = "rotate(180deg)";
                    } else {
                        panel.classList.add('max-h-[0]');
                        panel.style.maxHeight = null;
                        panel.style.marginTop = null;
                        iconAccordition.style.transform = null;
                    }
                });
            });
        }

        // Khởi tạo sự kiện accordion ban đầu
        setupAccordionEvents();

        const tabs = document.querySelectorAll('.tab-item');
        const lists = document.querySelectorAll('.accorditions-list');

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                // Xóa active khỏi tất cả các tab
                tabs.forEach(t => t.classList.remove('active'));
                // Thêm active cho tab được chọn
                tab.classList.add('active');

                const targetTab = tab.getAttribute('data-tab');

                // Ẩn/hiện danh sách accordion
                lists.forEach(list => {
                    if (list.id === targetTab) {
                        list.classList.add('active');

                        // Reset nội dung tìm kiếm khi chuyển tab
                        // Nếu có form tìm kiếm, reset input
                        const searchForm = document.getElementById('faq-search-form');
                        if (searchForm) {
                            searchForm.reset(); // Reset form
                        }

                        // Khôi phục danh sách FAQ ban đầu
                        fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded',
                                },
                                body: new URLSearchParams({
                                    action: 'faq_reset',
                                    nonce: '<?php echo wp_create_nonce('faq_reset_nonce'); ?>',
                                    faq_type: targetTab,
                                    page_id: <?= $id ?>,
                                }),
                            })
                            .then(response => response.json())
                            .then(data => {
                                const listContainer = document.querySelector(
                                    `#${targetTab}`);
                                listContainer.innerHTML = '';

                                if (data.success && data.data.length > 0) {
                                    data.data.forEach(item => {
                                        const faqItem = `
                                    <div class="accorditions-list-item bg-white p-6 rounded-xl border border-solid border-neutral-200">
                                        <button class="accordion w-full flex gap-5 justify-between items-center border border-color-[#E5E7EB]">
                                            <span class="text-start text-body-xl-medium">${item.question}</span>
                                            <img src="<?= $url ?>/assets/image/icon/chev-down-16.svg" alt="" />
                                        </button>
                                        <div class="panel transition-max-height duration-200 ease-out overflow-hidden max-h-[0]">
                                            <p class="text-body-md-regular text-gray-7">${item.answer}</p>
                                        </div>
                                    </div>`;
                                        listContainer.insertAdjacentHTML(
                                            'beforeend', faqItem);
                                    });

                                    // Gắn lại sự kiện accordion sau khi thêm nội dung mới
                                    setupAccordionEvents();
                                }
                            })
                            .catch(error => console.error('Error:', error));
                    } else {
                        list.classList.remove('active');
                    }
                });

                // Thiết lập lại sự kiện accordion sau khi chuyển tab
                setupAccordionEvents();
            });
        });
        // Xử lý tìm kiếm FAQ
        const searchForm = document.getElementById('faq-search-form');

        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const searchInput = this.querySelector('input[name="search_term"]');
            const searchTerm = searchInput.value.trim();

            // Bỏ qua nếu không có từ khóa
            if (searchTerm === '') return;

            const activeTab = document.querySelector('.tab-item.active').getAttribute('data-tab');
            const nonce = '<?php echo wp_create_nonce('faq_search_nonce'); ?>';

            fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        action: 'faq_search',
                        nonce: nonce,
                        search_term: searchTerm,
                        faq_type: activeTab,
                        page_id: <?= $id ?>,
                    }),
                })
                .then(response => response.json())
                .then(data => {
                    const listContainer = document.querySelector(`#${activeTab}`);
                    listContainer.innerHTML = '';

                    if (data.success && data.data.length > 0) {
                        data.data.forEach(item => {
                            const faqItem = `
                        <div class="accorditions-list-item bg-white p-6 rounded-xl border border-solid border-neutral-200">
                            <button class="accordion w-full flex gap-5 justify-between items-center border border-color-[#E5E7EB]">
                                <span class="text-start text-body-xl-medium">${item.question}</span>
                                <img src="<?= $url ?>/assets/image/icon/chev-down-16.svg" alt="" />
                            </button>
                            <div class="panel transition-max-height duration-200 ease-out overflow-hidden max-h-[0]">
                                <p class="text-body-md-regular text-gray-7">${item.answer}</p>
                            </div>
                        </div>`;
                            listContainer.insertAdjacentHTML('beforeend', faqItem);
                        });

                        // Gắn lại sự kiện accordion sau khi thêm nội dung mới
                        setupAccordionEvents();
                    } else {
                        listContainer.innerHTML = '<p class="text-center"><?php pll_e('No results found') ?></p>';
                    }
                })
                .catch(error => console.error('Error:', error));
        });
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Lấy form Contact Form 7
        const form = document.querySelector('.wpcf7 form');

        if (form) {
            // Trường email
            const emailInput = form.querySelector('[name="your_email"]');
            emailInput.addEventListener('blur', function() {
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailPattern.test(emailInput.value)) {
                    showError(emailInput, "<?php pll_e('Please enter a valid email address') ?>");
                } else {
                    clearError(emailInput);
                }
            });

            // Trường số điện thoại
            const phoneInput = form.querySelector('[name="your_phone"]');
            phoneInput.addEventListener('blur', function() {
                const phonePattern = /^[0-9]{10,15}$/; // Chỉ cho phép số từ 10-15 ký tự
                if (!phonePattern.test(phoneInput.value)) {
                    showError(phoneInput, "<?php pll_e('Please enter a valid phone number') ?>");
                } else {
                    clearError(phoneInput);
                }
            });
        }

        // Hiển thị lỗi
        function showError(input, message) {
            clearError(input); // Xóa lỗi cũ nếu có
            const errorElement = document.createElement('div');
            errorElement.className = 'error-message text-[#F73131] text-sm';
            errorElement.innerText = message;
            input.parentNode.appendChild(errorElement);
            input.classList.add('border-[#F73131]'); // Đổi màu viền thành đỏ
        }

        // Xóa lỗi
        function clearError(input) {
            const errorElement = input.parentNode.querySelector('.error-message');
            if (errorElement) {
                errorElement.remove();
            }
            input.classList.remove('border-[#F73131]');
        }
    });
</script>