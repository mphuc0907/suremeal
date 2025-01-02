<?php
/* Template Name: OTP dealer */
?>
<?php
get_header('dealer');
?>
    <div class="relative min-h-screen overflow-hidden bg-gray-50 py-16 sm:py-12 reset-pass text-center px-4">
        <div class="mx-auto items-center mb-16">
            <a href="index.html"><img src="/img/logo.png" class="w-64" alt="" /></a>
        </div>
        <div class="mx-auto relative p-8 rounded-2xl bg-white w-500-full overflow-hidden border-cm">
            <h1 class="text-center text-32 font-semibold mt-0 mb-2">Account Verification</h1>
            <p class="text-center text-base otp-color mt-0 mb-6">You have successfully registered as a dealer, please check your email inbox to receive the OTP for account verification and log in.</p>
            <form class="" action="#" method="">
                <div class="mb-6 text-left">
                    <div class="flex">
                        <div class="countdown"></div>
                        <div class="timer ml-auto order-2 text-base text-arm" x-data="timer(new Date().setDate(new Date().getDate() + 1))" x-init="init();">
                            <span class="text-arm text-baset" x-text="time().minutes"></span><span class="text-arm">:</span><span class="text-arm text-base" x-text="time().seconds"></span>
                        </div>
                        <label class="block mb-1 text-base font-medium checkout-color-text">Enter OTP here</label>
                    </div>
                    <div class="flex">
                        <input type="number" class="box-border bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white text-center text-base font-medium" placeholder="-" autocomplete="off" maxlength="4" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" value=""/>
                        <input type="number" class="box-border bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue- mx-2 text-center text-base font-medium" placeholder="-" autocomplete="off" maxlength="4" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" value=""/>
                        <input type="number" class="box-border bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 text-center text-base font-medium" placeholder="-" autocomplete="off" maxlength="4" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" value=""/>
                        <input type="number" class="box-border bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 mx-2 text-center text-base font-medium" placeholder="-" autocomplete="off" maxlength="4" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" value=""/>
                        <input type="number" class="box-border bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 mr-2 text-center text-base font-medium" placeholder="-" autocomplete="off" maxlength="4" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" value=""/>
                        <input type="number" class="box-border bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 text-center text-base font-medium" placeholder="-" autocomplete="off" maxlength="4" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" value=""/>
                    </div>
                </div>
                <p class="text-center otp-color-2 mt-0 p-0 mb-6 text-base font-normal">Didn’t receive the OTP? <a class="otp-color-3" href="">Resend</a></p>
                <button type="submit" class="text-white bg-red-sure border-none bg-red-sure font-medium rounded-lg text-sm w-full px-10 py-4 text-center">Submit</button>
            </form>
        </div>
    </div>
<?php get_footer('dealer') ?>
<script !src="">
    // Lấy tất cả các input trong form
    const inputs = document.querySelectorAll('input[type="number"]');

    // Xử lý khi người dùng paste chuỗi vào một trong các ô input
    inputs.forEach((input, index) => {
        input.addEventListener('paste', (event) => {
            event.preventDefault(); // Ngăn hành vi mặc định của trình duyệt

            // Lấy dữ liệu được paste
            const pasteData = (event.clipboardData || window.clipboardData).getData('text');

            // Kiểm tra xem dữ liệu paste có phải là chuỗi số không
            if (/^\d+$/.test(pasteData)) {
                const numbers = pasteData.split(''); // Chia chuỗi số thành mảng

                // Rải từng số vào các ô input
                numbers.forEach((num, i) => {
                    if (index + i < inputs.length) {
                        inputs[index + i].value = num; // Gán giá trị vào ô input
                    }
                });

                // Tự động focus vào ô cuối cùng đã nhập
                const lastInputIndex = Math.min(index + numbers.length, inputs.length) - 1;
                inputs[lastInputIndex].focus();
            }
        });
    });

    // Xử lý nhập từng số vào ô như trước
    inputs.forEach((input, index) => {
        input.addEventListener('input', () => {
            if (input.value.length >= 1) {
                input.value = input.value.slice(0, 1);
                if (index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            }
        });

        input.addEventListener('keydown', (event) => {
            if (event.key === 'Backspace' && input.value === '') {
                if (index > 0) {
                    inputs[index - 1].focus();
                }
            }
        });
    });


</script>
