<?php /* Template Name: affiliate customization wecan */ ?>
<?php
get_header('dl_wg');
$url = get_template_directory_uri();

$authenticated_user = validate_user_token();
$id_user = $authenticated_user->id;
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<div class="flex bg-[#EEF0F6]">
    <!-- place nav content -->
    <!-- place này để content đảy ra không bị side bar đè -->
    <div class="hidden lg:block w-[264px]" style="height: calc(100vh - 88px);"></div>

    <style>
        .add-url {
            color: #0E74BC;
        }

        .add-url:hover {
            color: #ED1B24;
        }

        .add-url:hover svg path {
            stroke: #ED1B24;
        }

        .url-select {
            color: #0F0F0F;
            cursor: pointer;
        }

        .url-select:hover {
            color: #0E74BC;
        }

        .input-focus-secondary:focus {
            border-color: #0E74BC;
        }
    </style>
    <!-- main-view -->
    <!-- view chính ở đây -->
    <div class="flex-1 p-8">
        <div class="flex flex-col gap-6 px-10 py-6 bg-white rounded-xl w-full max-w-[1047px]">
            <h2 class="text-heading-h5 text-gray-8">
                Affiliate link customization
            </h2>
            <form class="flex flex-col gap-6">
                <label class="input-label">
                    <p class="input-title"><?php pll_e('Distribution link') ?></p>
                    <div class="relative password-field">
                        <input type="text" class="input-field !pr-10" placeholder="https://suremeal.com/v2/73BbWa03CxKWTWsDbEnPVA?lp=3gang" name="distribution link">
                        <figure class="show-pw w-6 h-6 cursor-pointer absolute top-1/2 right-3" style="transform: translateY(-50%);">
                            <img src="<?= $url ?>/assets/image/dealer/edit-3.svg" alt="icon">
                        </figure>
                    </div>
                </label>
                <label class="input-label">
                    <p class="input-title"><?php pll_e('Shorten link') ?></p>
                    <div class="flex flex-col lg:flex-row">
                        <div class="flex-1 flex flex-col md:flex-row">
                            <!-- input domain -->
                            <div class="relative password-field !w-full !md:w-1/2 !xl:w-2/5 lg:max-w-[310px]">
                                <input type="text" class="url-select-field input-focus-secondary input-field h-[48px] xl:!rounded-r-[0] !pr-10" placeholder="https://suremeal.com/" value="https://suremeal.com/" name="shorten-link">
                                <figure class="show-pw w-6 h-6 cursor-pointer absolute top-1/2 right-3" style="transform: translateY(-50%);">
                                    <img src="<?= $url ?>/assets/image/dealer/edit-3.svg" alt="icon">
                                </figure>

                                <div class="url-wrap transition-all duration-500 ease-in-out hidden absolute top-[100%] flex-col p-3 gap-3 mt-3 w-full bg-white rounded-lg">
                                    <div class="url-container flex flex-col gap-3 max-h-[350px] overflow-y-auto">
                                        <div class="w-full url-select">
                                            <p class="text-body-md-regular text-black-2">abc.com</p>
                                        </div>
                                        <hr class="divider" style="background: rgba(107, 114, 128, 0.20);">
                                        <div class="w-full url-select">
                                            <p class="text-body-md-regular text-black-2">abc1.com</p>
                                        </div>
                                        <hr class="divider" style="background: rgba(107, 114, 128, 0.20);">
                                        <div class="w-full url-select">
                                            <p class="text-body-md-regular text-black-2">abc2.com</p>
                                        </div>
                                        <hr class="divider" style="background: rgba(107, 114, 128, 0.20);">
                                        <div class="w-full url-select">
                                            <p class="text-body-md-regular text-black-2">abc3.com</p>
                                        </div>
                                    </div>
                                    <hr class="divider" style="background: rgba(107, 114, 128, 0.20);">
                                    <div class="w-full">
                                        <div class="add-url open-modal w-full button bg-trans justify-between text-body-md-semibold" data-modal="modalAddLink">
                                            Add a domain
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <path d="M11.9492 4.26416L11.9492 20.2642" stroke="#0E74BC" stroke-width="2" stroke-linecap="round" />
                                                <path d="M19.9492 12.2642L3.94922 12.2642" stroke="#0E74BC" stroke-width="2" stroke-linecap="round" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- input url link -->
                            <input type="text" class="input-focus-secondary input-field h-[48px] xl:!rounded-l-[0] !w-full !md:w-1/2 !xl:w-3/5 lg:max-w-[460px]" placeholder="/url" name="url">
                        </div>
                        <button class="mx-auto mt-6 lg:mt-0  lg:ml-6 button bg-primary text-body-md-semibold text-white h-[48px]">
                            Generate link
                        </button>
                    </div>
                </label>
            </form>
        </div>
    </div>

    <!-- modal add url-->
    <div id="modalAddLink" class="modal-wrapper">
        <div class="modal-container w-640">
            <div class="modal-head">
                <div class="w-full flex justify-center gap-6">
                    <h2 class="text-heading-h5 text-gray-8">
                        Add a domain
                    </h2>
                    <div class="close-modal">
                        <figure class="w-6 h-6"><img src="<?= $url ?>/assets/image/icon/close-24.svg" alt="close">
                        </figure>
                    </div>
                </div>
            </div>
            <div class="modal-content">
                <form action="" id="review-product" class="flex">
                    <label class="input-label">
                        <input type="text" class="input-field h-[48px]" name="domain" placeholder="https://qixtech.com">
                    </label>
                    <button class="ml-2.5 button bg-primary text-body-md-semibold text-white h-[48px] whitespace-nowrap">
                        Submit domain
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Lấy các phần tử cần thao tác
    const urlField = document.querySelector('.url-select-field');
    const urlWrap = document.querySelector('.url-wrap');
    const urlSelects = document.querySelectorAll('.url-select p');

    // Khi focus vào input field, hiển thị url-wrap
    urlField.addEventListener('focus', function() {
        urlWrap.classList.remove('hidden');
        urlWrap.classList.add('flex');
    });

    // Khi click vào từng url-select, lấy value và ẩn url-wrap
    urlSelects.forEach(function(select) {
        select.addEventListener('click', function() {
            urlField.value = select.textContent;
            urlField.blur();
            urlWrap.classList.add('hidden');
            urlWrap.classList.remove('flex');
        });
    });

    // Xử lý click outside để ẩn url-wrap
    document.addEventListener('click', function(e) {
        if (!urlWrap.contains(e.target) && e.target !== urlField) {
            urlWrap.classList.add('hidden');
            urlWrap.classList.remove('flex');
        }
    });
    $("#review-product").on("submit", function(e) {
        e.preventDefault();
        var domain = $('input[name="domain"]').val();

        $.ajax({
            url: "<?= admin_url('admin-ajax.php'); ?>",
            type: "POST",
            data: {
                action: "AddDomain",
                domain: domain,
            },
            dataType: "json",
            beforeSend: function() {
                $('.close-modal').click();
                Swal.fire({
                    title: 'Processing',
                    html: 'Please wait...',
                    didOpen: () => {
                        Swal.showLoading()
                    }
                });
            },
            success: function(response) {
                if (response.status === 1) {
                    Swal.fire({
                        icon: 'success',
                        text: response.mess,
                    }).then(() => {
                        location
                            .reload(); // Di chuyển location.reload() vào trong hàm callback
                    });
                } else if (response.status === 0) {
                    Swal.fire({
                        icon: 'warning',
                        text: response.mess,
                    });
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: "error",
                    text: "An error occurred. Please try again.",
                });
            },
        });
    });
</script>

<script defer src="<?= $url ?>/assets/js/modal.js"></script>