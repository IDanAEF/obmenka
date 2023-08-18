<?php
    /*
        Template Name: Оплата за рубежом
    */
    get_header();
?>
<main class="abroad-pay">
    <section class="abroad-pay__promo faq__promo">
        <img src="<?=bloginfo('template_url')?>/assets/images/blue_back.png" alt="" class="faq__promo-back img_bg">
        <img src="<?php the_field('promo_image') ?>" alt="" class="abroad-pay__promo-image faq__promo-image">
        <div class="abroad-pay__promo-text faq__promo-text text_center">
            <h2 class="faq__promo-uppertitle text_colorfull text_fz28 text_fw600 text_ls005 text_upper"><?php the_field('promo_title') ?></h2>
            <h1 class="faq__promo-title text_fz44 text_fw600"><?php the_field('promo_undertitle') ?></h1>
            <h3 class="faq__promo-undertitle text_fz20"><?php the_field('promo_descr') ?></h3>
            <div class="abroad-pay__promo-codes">
                <?php
                    function getRubs($code) {
                        $arr = get_field('courses', 27);

                        foreach($arr as $item) {
                            if ($item['send']['code'] == 'rub' && $item['get']['code'] == $code) {
                                return $item['send']['price'];
                            }
                        }
                    }
                ?>
                <div class="abroad-pay__promo-codes-row text_upper text_fw700 text_fz24">
                    <span><?=getRubs(get_field('promo_codes1'))?> RUB = 1 <?php the_field('promo_codes1') ?></span>
                    <span><img src="<?=bloginfo('template_url')?>/assets/images/dollar.svg" alt=""><?=getRubs(get_field('promo_codes2'))?> RUB = 1 <?php the_field('promo_codes2') ?></span>
                </div>
                <div class="abroad-pay__promo-codes-date text_fz20 text_fw600">
                    курс для оплаты <br>обновлено <?=get_the_modified_date('j F, H:i', 27);?>
                </div>
            </div>
            <a href="#abroad-form" class="abroad-pay__promo-button button text_white text_fw600 text_fz16">
                Оформить покупку
            </a>
        </div>
    </section>
    <section class="abroad-pay__form">
        <div class="container">
            <div class="abroad-pay__form-row">
                <?php
                    while(have_rows('form-descr_info')) {
                        the_row();
                        ?>
                        <div class="abroad-pay__form-info abroad-pay__form-col">
                            <div class="abroad-pay__form-info-title text_fz28 text_fw700 title"><?php the_sub_field('name') ?></div>
                            <div class="abroad-pay__form-info-descr text_fz20 text_fw600 text"><?php the_sub_field('descr') ?></div>
                        </div>
                        <?php
                    }
                ?>
            </div>
            <div class="abroad-pay__form-row" id="abroad-form">
                <div class="abroad-pay__form-feed abroad-pay__form-col">
                    <h2 class="abroad-pay__form-for-title title text_fz28 text_fw700">Оформить заявку</h2>
                    <form action="<?=admin_url( "admin-ajax.php" )?>" class="main__form">
                        <div class="label">
                            <span class="text_fz16 text_fw600">Ссылка на товар или счет для оплаты:</span>
                            <div class="input text_fz16 text_fw500">
                                <img src="<?=bloginfo('template_url')?>/assets/images/link.svg" alt="">
                                <input type="text" name="feedlink" placeholder="http://...">
                            </div>
                            <span class="error text_fz16 text_fw600"></span>
                        </div>
                        <div class="label">
                            <span class="text_fz16 text_fw600">Название товара:</span>
                            <div class="input text_fz16 text_fw500">
                                <img src="<?=bloginfo('template_url')?>/assets/images/bag.svg" alt="">
                                <input type="text" name="feedproduct" placeholder="Название товара в магазине">
                            </div>
                            <span class="error text_fz16 text_fw600"></span>
                        </div>
                        <div class="label">
                            <span class="text_fz16 text_fw600">Цена товара:</span>
                            <div class="input text_fz16 text_fw500 in-row">
                                <img src="<?=bloginfo('template_url')?>/assets/images/wavy_check.svg" alt="">
                                <input type="text" name="feedprice" placeholder="Цена товара">
                                <div class="field list list_target text_fz16 text_upper target-banks">
                                    <input class="list_input" type="text" name="feedcurr" value="usd" hidden>
                                    <div class="list_info">
                                        <span class="list_text text_fw500">usd</span>
                                    </div>
                                    <img src="<?=bloginfo('template_url')?>/assets/images/arrow_down.svg" alt="">
                                    <div class="list_items">
                                        <div class="list_items-val long" data-value="usd">
                                            usd
                                        </div>
                                        <div class="list_items-val long" data-value="eur">
                                            eur
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <span class="error text_fz16 text_fw600"></span>
                        </div>
                        <div class="label">
                            <span class="text_fz16 text_fw600">Детали заказа (ваш комментарий):</span>
                            <div class="input text_fz16 text_fw500">
                                <img src="<?=bloginfo('template_url')?>/assets/images/chat.svg" alt="">
                                <input type="text" name="feedmess" placeholder="Количество, размер, цвет и любые другие детали заказа">
                            </div>
                            <span class="error text_fz16 text_fw600"></span>
                        </div>
                        <div class="label">
                            <span class="text_fz16 text_fw600">Email:</span>
                            <div class="input text_fz16 text_fw500">
                                <img src="<?=bloginfo('template_url')?>/assets/images/chat.svg" alt="">
                                <input type="email" name="feedmail" placeholder="Ваш E-mail">
                            </div>
                            <span class="error text_fz16 text_fw600"></span>
                        </div>
                        <div class="label">
                            <span class="text_fz16 text_fw600">Способ связи (телефон или @username):</span>
                            <div class="input text_fz16 text_fw500">
                                <img src="<?=bloginfo('template_url')?>/assets/images/phone.svg" alt="">
                                <!-- <input type="text" name="feedcontact" placeholder="Номер телефона или @username"> -->
                                <div class="list field list_target input-change text_fz16">
                                    <div class="input-field text_fz16">
                                        <input class="list_input" type="tel" data-mask="+_ (___) ___-__-__" name="feedcontact" placeholder="Телефон" reguired>
                                    </div>
                                    <img src="<?=bloginfo('template_url')?>/assets/images/arrow_down.svg" alt="">
                                    <div class="list_items">
                                        <div class="list_items-val long" data-mask="+_ (___) ___-__-__" data-value="Телефон" data-type="tel">
                                            Телефон
                                        </div>
                                        <div class="list_items-val long" data-mask="+_ (___) ___-__-__" data-value="Whats App" data-type="tel">
                                            Whats App
                                        </div>
                                        <div class="list_items-val long" data-value="Telegram" data-type="text">
                                            Telegram
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <span class="error text_fz16 text_fw600"></span>
                        </div>
                        <input type="text" name="g-recaptcha-response" class="g-recaptcha-response" hidden>
                        <button class="button text_white text_fw600 text_fz16" style="margin-top: 0">
                            Оформить покупку
                        </button>
                        <span class="policy text_fz16 text_fw500">
                            Создавая заказ, вы принимаете условия <a href="/terms/">пользовательского соглашения</a> и даете согласие на <a href="/terms/">обработку персональных данных.</a>
                        </span>
                    </form>
                </div>
                <div class="abroad-pay__form-important">
                    <div class="abroad-pay__form-col blue">
                        <img src="<?=bloginfo('template_url')?>/assets/images/block_pay.png" alt="" class="img_bg">
                        <h2 class="abroad-pay__form-important-title title text_white text_fz28 text_fw600">
                            <img src="<?=bloginfo('template_url')?>/assets/images/warning.svg" alt="">
                            Важно
                        </h2>
                        <div class="abroad-pay__form-important-descr text_white text_fz16">
                            <?php the_field('form-descr_important') ?>
                        </div>
                        <a href="<?=(strpos(get_field('contacts_telegram', 27), 'http') !== false ? get_field('contacts_telegram', 27) : 'https://t.me/'.get_field('contacts_telegram', 27))?>" class="abroad-pay__form-important-button button text_white text_fw600 text_fz16">
                            Написать
                        </a>
                    </div>
                </div>
            </div>
            <div class="abroad-pay__form-row">
                <div class="abroad-pay__form-descrinfo abroad-pay__form-col">
                    <img src="<?=bloginfo('template_url')?>/assets/images/p1.png" alt="" class="single-image">
                    <h2 class="abroad-pay__form-descrinfo-title title text_fz28 text_fw700">Подробности об услуге</h2>
                    <div class="abroad-pay__form-descrinfo-descr text text_fz20 text_fw600">
                        <?php the_field('form-descr_descr') ?>
                    </div>
                    <a href="<?=(strpos(get_field('contacts_telegram', 27), 'http') !== false ? get_field('contacts_telegram', 27) : 'https://t.me/'.get_field('contacts_telegram', 27))?>" class="abroad-pay__form-descrinfo-button button text_white text_fw600 text_fz16">
                        Написать
                    </a>
                </div>
                <div class="abroad-pay__form-for abroad-pay__form-col">
                    <img src="<?=bloginfo('template_url')?>/assets/images/p2.png" alt="" class="single-image">
                    <h2 class="abroad-pay__form-for-title title text_fz28 text_fw700">Для кого эта услуга</h2>
                    <div class="abroad-pay__form-for-items">
                        <?php
                            while(have_rows('form-descr_for')) {
                                the_row();
                                ?>
                                <div class="abroad-pay__form-for-item text text_fz20 text_fw600">
                                    <img src="<?php the_sub_field('icon') ?>" alt="" class="icon">
                                    <?php the_sub_field('name') ?>
                                </div>
                                <?php
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="abroad-pay__reviews main__reviews">
        <img src="<?=bloginfo('template_url')?>/assets/images/pay_img.png" alt="" class="about__contacts-image">
        <?php
            $result = json_decode(file_get_contents("https://api.vk.com/method/board.getComments?access_token=34c3df9934c3df9934c3df99d237d0cac6334c334c3df9950d14100463f582d4acaecb2&group_id=219360112&topic_id=50177684&v=5.131"), true);
        ?>
        <div class="container">
            <h2 class="reviews__list-title text_center text_fz44 text_fw600">Нам доверяют</h2>
            <div class="reviews__list-all text_fz20">
                <span>Всего отзывов:</span>
                <div class="reviews__list-count text_fz20 text_fw500">
                    <?=count($result['response']['items'])?>
                    <img src="<?=bloginfo('template_url')?>/assets/images/chat_check.svg" alt="">
                </div>
            </div>
            <div class="main__reviews-slider">
                <div class="main__reviews-slider-line">
                    <?php    
                        foreach ($result['response']['items'] as $key => $value) {
                            $user = json_decode(file_get_contents("https://api.vk.com/method/users.get?access_token=34c3df9934c3df9934c3df99d237d0cac6334c334c3df9950d14100463f582d4acaecb2&user_ids=".$value['from_id']."&fields=photo_100,first_name,last_name&v=5.131"), true)['response'][0];
                            ?>
                            <div class="main__reviews-slider-item">
                                <div class="main__reviews-slider-item-top">
                                    <div class="main__reviews-slider-item-image">
                                        <?php
                                            $userPhoto = $user['photo_100'] && strpos($user['photo_100'], 'camera_100.png') === false ? $user['photo_100'] : "/wp-content/themes/obmenka/assets/images/user_circle.png";
                                        ?>
                                        <img src="<?=$userPhoto?>" alt="" class="img_bg">
                                    </div>
                                    <div class="main__reviews-slider-item-info">
                                        <div class="main__reviews-slider-item-name text_fz18 text_fw500"><?=$user['last_name']?> <?=$user['first_name']?></div>
                                        <div class="main__reviews-slider-item-date text_fz12"><?=date('d.m.Y, H:i', $value['date'])?></div>
                                    </div>
                                </div>
                                <div class="main__reviews-slider-item-body text_fz16">
                                    <?=$value['text']?>
                                </div>
                            </div>
                            <?php
                        }
                    ?>
                </div>
                <a href="/reviews/" class="main__reviews-slider-button button text_white text_fz16 text_fw600">
                    Больше отзывов 
                </a>
            </div>
        </div>
    </section>
    <section class="about__results">
        <h2 class="about__results-title text_fz44 text_fw600 text_center">Наши показатели</h2>
        <div class="container">
            <?php
                while(have_rows('results')) {
                    the_row();
                    ?>
                    <div class="about__results-item">
                        <div class="about__results-item-title text_fz32 text_fw700"><?php the_sub_field('title') ?></div>
                        <div class="about__results-item-descr text_fz20 text_fw600"><?php the_sub_field('descr') ?></div>
                    </div>
                    <?php
                }
            ?>
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
                        <a href="<?=(strpos(get_field('clients_telegram', 27), 'http') !== false ? get_field('clients_telegram', 27) : 'https://t.me/'.get_field('clients_telegram', 27))?>" class="faq__contacts-block-link text_fz16 text_fw600">
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
                        <a href="<?=(strpos(get_field('partners_telegram', 27), 'http') !== false ? get_field('partners_telegram', 27) : 'https://t.me/'.get_field('partners_telegram', 27))?>" class="faq__contacts-block-link text_fz16 text_fw600">
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