<?php
    /*
        Template Name: FAQ
    */
    get_header();
?>
<main class="faq">
    <section class="faq__promo">
        <img src="<?=bloginfo('template_url')?>/assets/images/blue_back.png" alt="" class="faq__promo-back img_bg">
        <img src="<?php the_field('promo_back') ?>" alt="" class="faq__promo-image">
        <div class="faq__promo-text text_center">
            <h2 class="faq__promo-uppertitle text_colorfull text_fz28 text_fw600 text_ls005">FAQ</h2>
            <h1 class="faq__promo-title text_fz44 text_fw600"><?php the_field('promo_title') ?></h1>
            <h3 class="faq__promo-undertitle text_fz20"><?php the_field('promo_descr') ?></h3>
        </div>
    </section>
    <section class="faq__points">
        <div class="container slide-field on-scroll">
            <div class="slide-content text_fz16">
                <?php
                    while(have_rows('quests')) {
                        the_row();
                        ?>
                        <div class="faq__points-item list_open_target">
                            <h4 class="faq__points-item-title text_fw600 list_open_btn"><?php the_sub_field('title') ?><img src="<?=bloginfo('template_url')?>/assets/images/arrow_down.svg" alt=""></h4>
                            <div class="faq__points-item-descr list_open_content">
                                <?php the_sub_field('answer') ?>
                            </div>
                        </div>
                        <?php
                    }
                ?>
            </div>
            <div class="slide-sidebar">
                <div class="faq__contacts slide-elem">
                    <h2 class="faq__contacts-title text_fz28 text_fw600"><?php the_field('contacts_title', 9) ?></h2>
                    <div class="faq__contacts-descr text_fz20"><?php the_field('contacts_descr', 9) ?></div>
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