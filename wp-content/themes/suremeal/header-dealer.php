<?php
$url = get_template_directory_uri();
?>

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