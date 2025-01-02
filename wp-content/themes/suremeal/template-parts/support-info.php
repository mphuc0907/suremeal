<?php
$support_info = get_field('support_info', 'option');
$i = 1;
?>
<?php if ($support_info): ?>
    <section>
        <div class="grid grid-cols-1 lg:grid-cols-3">
            <?php foreach ($support_info as $item): ?>
                <div data-aos="fade-zoom-out" data-aos-duration="1500" class="bg-deliver-<?= $i ?>" style="background: url(<?= $item['cover_img'] ?>) no-repeat;">
                    <div class="relative z-[2] flex flex-col items-center max-w-1/2 max-w-[316px] min-h-[170px] 2xl:min-h-[211px]">
                        <figure class="w-10 h-10 2xl:w-16 2xl:h-16"><img src="<?= $item['icon'] ?>" alt="icon"></figure>
                        <h2 class="mt-5 text-heading-h7 text-neutral-50 text-center"><?= $item['name'] ?></h2>
                        <p class="mt-2 text-body-lg-regular text-neutral-50 text-center">
                            <a href="<?= $item['url'] ?>" class="hover:text-primary"><?= $item['info'] ?></a>
                        </p>
                    </div>
                </div>
            <?php
                $i++;
            endforeach ?>
        </div>
    </section>
<?php endif ?>