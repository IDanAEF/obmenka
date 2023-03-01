<?php
    get_header();
?>
<main class="main">
    <section class="main__promo">
        <img src="<?php the_field('promo_back') ?>" alt="" class="main__promo-back img_bg">
        <img src="<?php the_field('promo_back-mob') ?>" alt="" class="main__promo-back mob img_bg">
        <div class="main__promo-text">
            <h1 class="main__promo-title text_fz120 text_upper text_ls005 text_fw700"><?php the_field('promo_title') ?></h1>
            <h2 class="main__promo-undertitle text_fz44 text_fw600"><?php the_field('promo_undertitle') ?></h2>
            <div class="main__promo-descr text_fz18"><?php the_field('promo_descr') ?></div>
            <a href="#change-form" class="main__promo-arrow">
                <img src="<?=bloginfo('template_url')?>/assets/images/arrow.svg" alt="">
            </a>
        </div>
    </section>
    <section id="change-form" class="main__form">
        <img src="<?=bloginfo('template_url')?>/assets/images/back_light.png" alt="" class="main__form-back img_bg">
        <div class="container change-form-cont" data-url="<?=admin_url( "admin-ajax.php" )?>">
            
        </div>
    </section>
    <section class="main__ways">
        <img src="<?=bloginfo('template_url')?>/assets/images/blue_back.png" alt="" class="main__ways-back img_bg">
        <div class="container">
            <h2 class="main__ways-title text_fz44 text_fw600"><?php the_field('ways_title') ?></h2>
            <div class="main__ways-blocks">
                <?php
                    while(have_rows('ways_blocks')) {
                        the_row();
                        $color = get_sub_field('color');
                        ?>
                        <article class="main__ways-item text_<?=$color?>">
                            <img src="<?php the_sub_field('back') ?>" alt="" class="main__ways-item-back img_bg">
                            <div class="main__ways-item-text">
                                <h3 class="main__ways-item-title text_fz38 text_fw700"><?php the_sub_field('name') ?></h3>
                                <div class="main__ways-item-descr text_fz20 text_fw500"><?php the_sub_field('descr') ?></div>
                            </div>
                            <a href="<?php the_sub_field('link') ?>" class="main__ways-item-link button button_arrow text_fz14 text_upper">
                                Подробнее
                                <span>
                                    <img src="<?=bloginfo('template_url')?>/assets/images/arrow.svg" alt="">
                                </span>
                            </a>
                        </article>
                        <?php
                    }
                ?>
            </div>
        </div>
    </section>
    <section class="main__reviews">
        <div class="container">
            <h2 class="main__reviews-title text_fz44 text_fw600 text_center"><?php the_field('reviews_title') ?></h2>
            <div class="main__reviews-slider slider-default">
                <div class="main__reviews-slider-line slider-line">
                    <?php
                        echo '<div class="main__reviews-slider-slide slider-item">';
                        for($r = 0; $r < 16; $r++) {
                            ?>
                            <div class="main__reviews-slider-item">
                                <div class="main__reviews-slider-item-top">
                                    <div class="main__reviews-slider-item-image">
                                        <img src="<?=bloginfo('template_url')?>/assets/images/rev.png" alt="" class="img_bg">
                                    </div>
                                    <div class="main__reviews-slider-item-info">
                                        <div class="main__reviews-slider-item-name text_fz18 text_fw500">Смирнова Елена</div>
                                        <div class="main__reviews-slider-item-date text_fz12">08.02.2023, 10:10 </div>
                                    </div>
                                </div>
                                <div class="main__reviews-slider-item-body text_fz16">
                                    Регулярно пользуюсь для покупки валюты. Отличный сервис.
                                    <a href="" class="main__reviews-slider-item-link text_fz14 text_fw500 text_underline">Подробнее</a>
                                </div>
                            </div>
                            <?php
                            if ($r + 1 == 16 || (($r + 1) % 4 == 0 && $r != 0)) echo '</div>';
                            if ($r + 1 != 16 && (($r + 1) % 4 == 0 && $r != 0)) echo '<div class="main__reviews-slider-slide slider-item">';
                        }
                    ?>
                </div>
                <div class="main__reviews-slider-points slider-points"></div>
                <a href="/reviews/" class="main__reviews-slider-button button text_white text_fz16 text_fw600">
                    Больше отзывов 
                </a>
            </div>
        </div>
    </section>
    <section class="main__gold">
        <img src="<?=bloginfo('template_url')?>/assets/images/blue_back.png" alt="" class="main__gold-back img_bg">
        <div class="container">
            <h2 class="main__gold-title text_fz44 text_fw600"><?php the_field('gold_title') ?></h2>
            <div class="main__gold-blocks">
                <?php
                    while(have_rows('gold_blocks')) {
                        the_row();
                        ?>
                        <div class="main__gold-item">
                            <img src="<?=bloginfo('template_url')?>/assets/images/line.svg" alt="" class="line">
                            <img src="<?php the_sub_field('icon') ?>" alt="" class="main__gold-item-image">
                            <h3 class="main__gold-item-title text_fz28 text_fw600"><?php the_sub_field('title') ?></h3>
                            <div class="main__gold-item-descr text_fz16"><?php the_sub_field('descr') ?></div>
                        </div>
                        <?php
                    }
                ?>
            </div>
        </div>
    </section>
    <section class="main__products">
        <div class="container">
            <img src="<?=bloginfo('template_url')?>/assets/images/logo.svg" alt="" class="main__products-logo">
            <h2 class="main__products-title text_fz44 text_fw600"><?php the_field('products_title') ?></h2>
            <div class="main__products-blocks">
                <?php
                    while(have_rows('products_lists')) {
                        the_row();
                        ?>
                        <div class="main__products-item">
                            <div class="main__products-item-title text_fz28 text_fw600">
                                <div class="icon">
                                    <img src="<?php the_sub_field('icon') ?>" alt="">
                                </div>
                                <?php the_sub_field('name') ?>
                            </div>
                            <ul class="main__products-item-list text_fz16 text_fw500">
                                <?php 
                                    $i = 1;
                                    while(have_rows('points')) {
                                        the_row();
                                        ?>
                                        <div class="main__products-item-point">
                                            <div class="main__products-item-number text_white"><?=$i++?></div>
                                            <?php the_sub_field('text') ?>
                                        </div>
                                        <?php
                                    }
                                ?>
                            </ul>
                        </div>
                        <?php
                    }
                ?>
            </div>
        </div>
    </section>
    <section class="main__call">
        <img src="<?=bloginfo('template_url')?>/assets/images/blue_back.png" alt="" class="main__gold-back img_bg">
        <div class="container">
            <h2 class="main__call-title text_fz44 text_fw600 text_center"><?php the_field('contacts_title') ?></h2>
            <div class="main__call-block">
                <div class="main__call-type">
                    <div class="main__call-type-title text_fz20 text_fw600">
                        <div class="icon">
                            <img src="<?=bloginfo('template_url')?>/assets/images/user.svg" alt="">
                        </div>
                        Контакты для клиентов
                    </div>
                    <div class="main__call-type-text text_fz16 text_fw500">
                        <a href="mailto:<?php the_field('clients_email', 27) ?>" class="main__call-type-item mail">
                            <img src="<?=bloginfo('template_url')?>/assets/images/mail.svg" alt="">
                            <?php the_field('clients_email', 27) ?>
                        </a>
                        <a href="<?=(strpos(get_field('clients_telegram', 27), 'http') !== false ? get_field('clients_telegram') : 'https://t.me/'.get_field('clients_telegram'))?>" class="main__call-type-item">
                            <img src="<?=bloginfo('template_url')?>/assets/images/telegram_color.svg" alt="">
                            Напишите нам в Telegram
                        </a>
                    </div>
                </div>
                <img src="<?=bloginfo('template_url')?>/assets/images/call.png" alt="" class="main__call-image">
                <div class="main__call-type">
                    <div class="main__call-type-title text_fz20 text_fw600">
                        <div class="icon">
                            <img src="<?=bloginfo('template_url')?>/assets/images/case.svg" alt="">
                        </div>
                        Контакты для партнеров
                    </div>
                    <div class="main__call-type-text text_fz16 text_fw500">
                        <a href="mailto:<?php the_field('partners_email', 27) ?>" class="main__call-type-item mail">
                            <img src="<?=bloginfo('template_url')?>/assets/images/mail.svg" alt="">
                            <?php the_field('partners_email', 27) ?>
                        </a>
                        <a href="<?=(strpos(get_field('partners_telegram', 27), 'http') !== false ? get_field('partners_telegram') : 'https://t.me/'.get_field('partners_telegram'))?>" class="main__call-type-item">
                            <img src="<?=bloginfo('template_url')?>/assets/images/telegram_color.svg" alt="">
                            Напишите нам в Telegram
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<div class="modal">
    <div class="modal__item" id="how-work">
        <div class="modal__title text_fz16 text_fw600">Как работает OBMENKA</div>
        <div class="modal__descr text_fz16">Мы используем принцип P2P. Это полностью безопасно! Вам не нужно указывать CVV-код и другие персональные данные вашей карты. </div>
        <div class="modal__check text_fz14">
            <input type="checkbox" id="privacy" required hidden checked>
            <label for="privacy" class="active">
                <div class="checkbox">
                    <img src="<?=bloginfo('template_url')?>/assets/images/check.svg" alt="">
                </div>
                Я согласен <a href="">с условиями и правилами сервиса</a>
            </label>
        </div>
        <div class="modal__buttons">
            <div class="button back button_red text_fz16 text_fw500 text_center">
                Отмена
            </div>
            <div class="button continue text_white text_fz16 text_fw500 text_center">
                Продолжить
            </div>
        </div>
    </div>
    <div class="modal__item" id="pay-done">
        <div class="modal__title text_fz16 text_fw600">Подтверждение оплаты</div>
        <div class="modal__descr text_fz16">
        После подтверждения оплаты, мы проверим перевод
        в течении 15 минут и переведем сумму в другой валюте
        на указанные реквизиты.
        <br>
        Пожалуйста, нажмите <b>«Я оплатил»</b> только если вы
        действительно сделали перевод.
        </div>
        <div class="modal__buttons mt">
            <div class="button back button_red text_fz16 text_fw500 text_center">
                Отмена
            </div>
            <div class="button continue-pay text_white text_fz16 text_fw500 text_center">
                Продолжить
            </div>
        </div>
    </div>
</div>
<?php
    get_footer();
?>