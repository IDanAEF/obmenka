<?php
    /*
        Template Name: Отзывы
    */
    get_header();
?>
<main class="reviews">
    <section class="abroad-pay__promo faq__promo">
        <img src="<?=bloginfo('template_url')?>/assets/images/blue_back.png" alt="" class="faq__promo-back img_bg">
        <img src="<?php the_field('promo_image') ?>" alt="" class="reviews__promo-image abroad-pay__promo-image faq__promo-image">
        <div class="abroad-pay__promo-text faq__promo-text text_center">
            <h2 class="faq__promo-uppertitle text_colorfull text_fz28 text_fw600 text_ls005 text_upper"><?php the_field('promo_title') ?></h2>
            <h1 class="faq__promo-title text_fz44 text_fw600"><?php the_field('promo_undertitle') ?></h1>
            <h3 class="faq__promo-undertitle text_fz20"><?php the_field('promo_descr') ?></h3>
        </div>
    </section>
    <section class="reviews__list">
        <div class="container">
            <h2 class="reviews__list-title text_center text_fz44 text_fw600">Нам доверяют</h2>
            <div class="reviews__list-all text_fz20">
                <span>Всего отзывов:</span>
                <div class="reviews__list-count text_fz20 text_fw500">
                    <?php the_field('all-reviews') ?>
                    <img src="<?=bloginfo('template_url')?>/assets/images/chat_check.svg" alt="">
                </div>
            </div>
            <div class="reviews__items">
                <div class="main__reviews-slider-item">
                    <div class="main__reviews-slider-item-top">
                        <div class="main__reviews-slider-item-image">
                            <img src="<?=bloginfo('template_url')?>/assets/images/rev.png" alt="" class="img_bg">
                        </div>
                        <div class="main__reviews-slider-item-info">
                            <div class="main__reviews-slider-item-name text_fz20 text_fw500">Смирнова Елена</div>
                            <div class="main__reviews-slider-item-date text_fz14">08.02.2023, 10:10 </div>
                        </div>
                    </div>
                    <div class="main__reviews-slider-item-body text_fz16">
                        Регулярно пользуюсь для покупки валюты. Круто, что есть возможность делать обмен валют онлайн, не нужно никуда ехать и стоять в очереди. Курс валют достаточно выгодный. Обмен осуществляется быстро. Сервис очень удобный.
                    </div>
                    <a href="" class="reviews__item-vk text_fz16 text_fw500 text_colorfull">
                        Перейти к отзыву
                        <img src="<?=bloginfo('template_url')?>/assets/images/vk.svg" alt="">
                    </a>
                </div>
                <div class="main__reviews-slider-item">
                    <div class="main__reviews-slider-item-top">
                        <div class="main__reviews-slider-item-image">
                            <img src="<?=bloginfo('template_url')?>/assets/images/rev.png" alt="" class="img_bg">
                        </div>
                        <div class="main__reviews-slider-item-info">
                            <div class="main__reviews-slider-item-name text_fz20 text_fw500">Смирнова Елена</div>
                            <div class="main__reviews-slider-item-date text_fz14">08.02.2023, 10:10 </div>
                        </div>
                    </div>
                    <div class="main__reviews-slider-item-body text_fz16">
                        Регулярно пользуюсь для покупки валюты. Круто, что есть возможность делать обмен валют онлайн, не нужно никуда ехать и стоять в очереди. Курс валют достаточно выгодный. Обмен осуществляется быстро. Сервис очень удобный.
                    </div>
                    <a href="" class="reviews__item-vk text_fz16 text_fw500 text_colorfull">
                        Перейти к отзыву
                        <img src="<?=bloginfo('template_url')?>/assets/images/vk.svg" alt="">
                    </a>
                </div>
                <div class="main__reviews-slider-item">
                    <div class="main__reviews-slider-item-top">
                        <div class="main__reviews-slider-item-image">
                            <img src="<?=bloginfo('template_url')?>/assets/images/user_circle.png" alt="" class="img_bg">
                        </div>
                        <div class="main__reviews-slider-item-info">
                            <div class="main__reviews-slider-item-name text_fz20 text_fw500">Смирнова Елена</div>
                            <div class="main__reviews-slider-item-date text_fz14">08.02.2023, 10:10 </div>
                        </div>
                    </div>
                    <div class="main__reviews-slider-item-body text_fz16">
                        Регулярно пользуюсь для покупки валюты. Круто, что есть возможность делать обмен валют онлайн, не нужно никуда ехать и стоять в очереди. Курс валют достаточно выгодный. Обмен осуществляется быстро. Сервис очень удобный.
                    </div>
                </div>
                <div class="main__reviews-slider-item">
                    <div class="main__reviews-slider-item-top">
                        <div class="main__reviews-slider-item-image">
                            <img src="<?=bloginfo('template_url')?>/assets/images/rev.png" alt="" class="img_bg">
                        </div>
                        <div class="main__reviews-slider-item-info">
                            <div class="main__reviews-slider-item-name text_fz20 text_fw500">Смирнова Елена</div>
                            <div class="main__reviews-slider-item-date text_fz14">08.02.2023, 10:10 </div>
                        </div>
                    </div>
                    <div class="main__reviews-slider-item-body text_fz16">
                        Регулярно пользуюсь для покупки валюты. Круто, что есть возможность делать обмен валют онлайн, не нужно никуда ехать и стоять в очереди. Курс валют достаточно выгодный. Обмен осуществляется быстро. Сервис очень удобный.
                    </div>
                    <a href="" class="reviews__item-vk text_fz16 text_fw500 text_colorfull">
                        Перейти к отзыву
                        <img src="<?=bloginfo('template_url')?>/assets/images/vk.svg" alt="">
                    </a>
                </div>
                <div class="main__reviews-slider-item">
                    <div class="main__reviews-slider-item-top">
                        <div class="main__reviews-slider-item-image">
                            <img src="<?=bloginfo('template_url')?>/assets/images/user_circle.png" alt="" class="img_bg">
                        </div>
                        <div class="main__reviews-slider-item-info">
                            <div class="main__reviews-slider-item-name text_fz20 text_fw500">Смирнова Елена</div>
                            <div class="main__reviews-slider-item-date text_fz14">08.02.2023, 10:10 </div>
                        </div>
                    </div>
                    <div class="main__reviews-slider-item-body text_fz16">
                        Регулярно пользуюсь для покупки валюты. Круто, что есть возможность делать обмен валют онлайн, не нужно никуда ехать и стоять в очереди. Курс валют достаточно выгодный. Обмен осуществляется быстро. Сервис очень удобный.
                    </div>
                </div>
                <div class="main__reviews-slider-item">
                    <div class="main__reviews-slider-item-top">
                        <div class="main__reviews-slider-item-image">
                            <img src="<?=bloginfo('template_url')?>/assets/images/user_circle.png" alt="" class="img_bg">
                        </div>
                        <div class="main__reviews-slider-item-info">
                            <div class="main__reviews-slider-item-name text_fz20 text_fw500">Смирнова Елена</div>
                            <div class="main__reviews-slider-item-date text_fz14">08.02.2023, 10:10 </div>
                        </div>
                    </div>
                    <div class="main__reviews-slider-item-body text_fz16">
                        Регулярно пользуюсь для покупки валюты. Круто, что есть возможность делать обмен валют онлайн, не нужно никуда ехать и стоять в очереди. Курс валют достаточно выгодный. Обмен осуществляется быстро. Сервис очень удобный.
                    </div>
                </div>
                <div class="main__reviews-slider-item">
                    <div class="main__reviews-slider-item-top">
                        <div class="main__reviews-slider-item-image">
                            <img src="<?=bloginfo('template_url')?>/assets/images/rev.png" alt="" class="img_bg">
                        </div>
                        <div class="main__reviews-slider-item-info">
                            <div class="main__reviews-slider-item-name text_fz20 text_fw500">Смирнова Елена</div>
                            <div class="main__reviews-slider-item-date text_fz14">08.02.2023, 10:10 </div>
                        </div>
                    </div>
                    <div class="main__reviews-slider-item-body text_fz16">
                        Регулярно пользуюсь для покупки валюты. Круто, что есть возможность делать обмен валют онлайн, не нужно никуда ехать и стоять в очереди. Курс валют достаточно выгодный. Обмен осуществляется быстро. Сервис очень удобный.
                    </div>
                    <a href="" class="reviews__item-vk text_fz16 text_fw500 text_colorfull">
                        Перейти к отзыву
                        <img src="<?=bloginfo('template_url')?>/assets/images/vk.svg" alt="">
                    </a>
                </div>
                <div class="main__reviews-slider-item">
                    <div class="main__reviews-slider-item-top">
                        <div class="main__reviews-slider-item-image">
                            <img src="<?=bloginfo('template_url')?>/assets/images/user_circle.png" alt="" class="img_bg">
                        </div>
                        <div class="main__reviews-slider-item-info">
                            <div class="main__reviews-slider-item-name text_fz20 text_fw500">Смирнова Елена</div>
                            <div class="main__reviews-slider-item-date text_fz14">08.02.2023, 10:10 </div>
                        </div>
                    </div>
                    <div class="main__reviews-slider-item-body text_fz16">
                        Регулярно пользуюсь для покупки валюты. Круто, что есть возможность делать обмен валют онлайн, не нужно никуда ехать и стоять в очереди. Курс валют достаточно выгодный. Обмен осуществляется быстро. Сервис очень удобный.
                    </div>
                </div>
            </div>
            <div class="reviews__show text_colorfull text_fz20 text_fw500">
                Показать еще 
                <img src="<?=bloginfo('template_url')?>/assets/images/color_arrow.svg" alt="">
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