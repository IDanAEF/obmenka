<?php
    /*
        Template Name: Правила и условия
    */
    get_header();
?>
<main class="terms">
    <section class="faq__promo terms__promo">
        <img src="<?=bloginfo('template_url')?>/assets/images/blue_back.png" alt="" class="faq__promo-back img_bg">
        <img src="<?php the_field('promo_image') ?>" alt="" class="faq__promo-image">
        <div class="faq__promo-text terms__promo-text text_center">
            <h1 class="faq__promo-title text_fz44 text_fw600"><?php the_field('promo_title') ?></h1>
        </div>
    </section>
    <section class="faq__points">
        <div class="container slide-field on-scroll">
            <div class="slide-content text_fz20 default-text">
                <?php the_field('text') ?>
            </div>
            <div class="slide-sidebar">
                <div class="faq__contacts slide-elem">
                    <h2 class="faq__contacts-title text_fz28 text_fw600">Если остались вопросы</h2>
                    <div class="faq__contacts-descr text_fz20">Будем рады на них ответить  и выслушать предложения по улучшению нашего сервиса©</div>
                    <div class="faq__contacts-block">
                        <div class="faq__contacts-block-title text_fz28 text_fw700">
                            <div class="img green">
                                <img src="<?=bloginfo('template_url')?>/assets/images/user.svg" alt="">
                            </div>
                            Контакты для клиентов
                        </div>
                        <div class="faq__contacts-links">
                            <a href="mailto:<?php the_field('clients_email', 27) ?>" class="faq__contacts-block-link text_fz16 text_fw600">
                                <img src="<?=bloginfo('template_url')?>/assets/images/mail.svg" alt="">
                                <?php the_field('clients_email', 27) ?>
                            </a>
                            <a href="<?=(strpos(get_field('clients_telegram', 27), 'http') !== false ? get_field('clients_telegram') : 'https://t.me/'.get_field('clients_telegram'))?>" class="faq__contacts-block-link text_fz16 text_fw600">
                                <img src="<?=bloginfo('template_url')?>/assets/images/telegram.svg" alt="">
                                Telegram
                            </a>
                        </div>
                    </div>
                    <div class="faq__contacts-block">
                        <div class="faq__contacts-block-title text_fz28 text_fw700">
                            <div class="img">
                                <img src="<?=bloginfo('template_url')?>/assets/images/case.svg" alt="">
                            </div>
                            Для партнеров
                        </div>
                        <div class="faq__contacts-links">
                            <a href="mailto:<?php the_field('partners_email', 27) ?>" class="faq__contacts-block-link text_fz16 text_fw600">
                                <img src="<?=bloginfo('template_url')?>/assets/images/mail.svg" alt="">
                                <?php the_field('partners_email', 27) ?>
                            </a>
                            <a href="<?=(strpos(get_field('partners_telegram', 27), 'http') !== false ? get_field('partners_telegram') : 'https://t.me/'.get_field('partners_telegram'))?>" class="faq__contacts-block-link text_fz16 text_fw600">
                                <img src="<?=bloginfo('template_url')?>/assets/images/telegram.svg" alt="">
                                Telegram
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="faq__translation text_white text_fw600">
        <div class="container">
            <?php
                while(have_rows('translation', 15)) {
                    the_row();
                    ?>
                        <div class="faq__translation-item">
                            <div class="faq__translation-item-type text_fz16 text_fw400">Переводы</div>
                            <h2 class="faq__translation-item-title text_fz28 text_fw700"><?php the_sub_field('title'); ?></h2>
                            <div class="faq__translation-item-descr text_fz20"><?php the_sub_field('descr'); ?></div>
                            <a href="<?php the_sub_field('link'); ?>" class="faq__translation-item-button button text_fz16 text_center">Подробнее</a>
                        </div>
                    <?php
                }
            ?>
        </div>
    </section>
</main>
<?php
    get_footer();
?>