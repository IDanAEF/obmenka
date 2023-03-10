<?php
    /*
        Template Name: О компании
    */
    get_header();
?>
<main class="about">
    <section class="about__promo">
        <img src="<?=bloginfo('template_url')?>/assets/images/blue_back.png" alt="" class="about__promo-back img_bg">
        <img src="<?php the_field('promo_back') ?>" alt="" class="about__promo-image">
        <div class="about__promo-text text_center">
            <h1 class="about__promo-title text_fz28 text_fw600 text_upper text_ls005 text_colorfull"><?php the_title() ?></h1>
            <h2 class="about__promo-undertitle text_fz44 text_fw600"><?php the_field('promo_title') ?></h2>
            <div class="about__promo-descr text_fz20"><?php the_field('promo_descr') ?></div>
        </div>
    </section>
    <section class="about__results">
        <div class="container">
            <?php
                while(have_rows('results')) {
                    the_row();
                    ?>
                    <div class="about__results-item">
                        <div class="about__results-item-title text_fz32 text_fw700 text_colorfull"><?php the_sub_field('title') ?></div>
                        <div class="about__results-item-descr text_fz20 text_fw600"><?php the_sub_field('descr') ?></div>
                    </div>
                    <?php
                }
            ?>
        </div>
    </section>
    <section class="about__landmarks">
        <div class="container text_center">
            <h2 class="about__landmarks-title text_fz44 text_fw600"><?php the_field('landmarks_title') ?></h2>
            <div class="about__landmarks-descr text_fz20"><?php the_field('landmarks_descr') ?></div>
            <div class="about__landmarks-items">
                <?php
                    while(have_rows('landmarks_blocks')) {
                        the_row();
                        ?>
                        <div class="about__landmarks-item">
                            <img src="<?php the_sub_field('image') ?>" alt="" class="about__landmarks-item-image">
                            <h3 class="about__landmarks-item-title text_fz28 text_fw600"><?php the_sub_field('title') ?></h3>
                            <div class="about__landmarks-item-descr text_fz16"><?php the_sub_field('descr') ?></div>
                        </div>
                        <?php
                    }
                ?>
            </div>
        </div>
    </section>
    <section class="about__contacts">
        <img src="<?=bloginfo('template_url')?>/assets/images/about_cont.png" alt="" class="about__contacts-image">
        <div class="container">
            <h2 class="about__contacts-title text_fz44 text_fw600 text_center"><?php the_field('contacts_title', 9) ?></h2>
            <div class="about__contacts-descr text_fz20 text_center"><?php the_field('contacts_descr', 9) ?></div>
            <div class="about__contacts-blocks">
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
    </section>
    <section class="faq__translation text_white text_fw600">
        <div class="container">
            <?php
                while(have_rows('translation')) {
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