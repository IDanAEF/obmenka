<?php
    add_theme_support('menus');
    add_theme_support('post-thumbnails');

    function obmenkaAddScripts() {
        wp_enqueue_style( 'obmenka_main_style', get_template_directory_uri() . '/assets/css/style.min.css' );
        wp_enqueue_style( 'obmenka_custom_style', get_template_directory_uri() . '/custom.css' );
        
        wp_enqueue_script( 'obmenka_main_scrit', get_template_directory_uri() . '/assets/js/script.js', array(), null, true );
        wp_enqueue_script( 'obmenka_custom_scrit', get_template_directory_uri() . '/custom.js', array(), null, true );
    }

    add_action( 'wp_enqueue_scripts', 'obmenkaAddScripts' );

    function filter_nav_menu_link_attributes($atts, $item, $args) {
        if ($args->menu === 'Main') {
            if ($item->current) {
                $atts['class'] = 'active';
            }
        }
    
        return $atts;
    }
    
    add_filter('nav_menu_link_attributes', 'filter_nav_menu_link_attributes', 10, 3);

    add_action( 'template_redirect', function(){
        ob_start( function( $buffer ){
            $buffer = str_replace('type="text/javascript"', '', $buffer );
            $buffer = str_replace('type="text/css"', '', $buffer );
            $buffer = str_replace("type='text/javascript'", '', $buffer );
            $buffer = str_replace("type='text/css'", '', $buffer );
            $buffer = preg_replace("~<meta (.*?)\/>~", "<meta $1>", $buffer);
            $buffer = preg_replace("~<link (.*?)\/>~", "<link $1>", $buffer);
            $buffer = preg_replace("~<input (.*?)\/>~", "<input $1>", $buffer);
            $buffer = preg_replace("~<img (.*?)\/>~", "<img $1>", $buffer);
            $buffer = str_replace("<br />", '<br>', $buffer );
            return $buffer;
        });
    });

    function form_steps(){
        $step = (isset($_COOKIE['step']) ? $_COOKIE['step'] : 1);
        $send = get_field('code-default_send', 27);
        $get = get_field('code-default_get', 27);
        $allCurr = [];
        $allBanks = [];
        $allReserve = [];
        while(have_rows('banks', 27)) {
            the_row();
            foreach(get_sub_field('currences', true) as $curr) {
                $allBanks[$curr['code']][] = [
                    'name' => get_sub_field('name'),
                    'icon' => get_sub_field('icon')
                ];
            }
        }
        while(have_rows('reserve', 27)) {
            the_row();
            $allReserve[get_sub_field('code')] = get_sub_field('value');
        }
        while(have_rows('currences', 27)) {
            the_row();
            $allCurr[get_sub_field('code')] = get_sub_field('rubs');
        }
        ?>
        <h2 class="main__form-title text_fz44 text_fw600 text_center">Oбмен валют</h2>
        <div class="main__form-step text_fz20 text_fw500 text_center">
            <span class="main__form-step-number">
                0<?=$step?>
            </span>
            . 
            <span class="main__form-step-number">
                <?php
                    switch($step) {
                        case 1:
                            echo "Заполнение данных";
                            break;
                        case 2:
                            echo "Обработка заявки";
                            break;
                        case 3:
                            echo "Заявка выполнена";
                            break;
                    } 
                ?>
            </span>
        </div>
        <div class="main__form-steps">
            <div class="main__form-steps-item<?php if ($step >= 1) echo ' active' ?>">
                <img src="<?=bloginfo('template_url')?>/assets/images/step1.png" alt="">
                <img src="<?=bloginfo('template_url')?>/assets/images/step1-color.png" class="color" alt="">
            </div>
            <div class="main__form-steps-item<?php if ($step >= 2) echo ' active' ?>">
                <div class="line"></div>
                <img src="<?=bloginfo('template_url')?>/assets/images/step2.png" alt="">
                <img src="<?=bloginfo('template_url')?>/assets/images/step2-color.png" class="color" alt="">
            </div>
            <div class="main__form-steps-item<?php if ($step >= 3) echo ' active' ?>">
                <div class="line"></div>
                <img src="<?=bloginfo('template_url')?>/assets/images/step3.png" alt="">
                <img src="<?=bloginfo('template_url')?>/assets/images/step3-color.png" class="color" alt="">
            </div>
        </div>
        <div class="main__form-change">
            <?php if ($step == 1) : ?>
            <div class="main__form-change-col shadow-block">
                <div class="title text_fz16">
                    <div class="text text_fw700">Отправляете</div>
                    <div class="list text_fw500 text_upper list_target">
                        <input class="list_input" type="text" name="send-curr" value="<?=$send?>" required hidden>
                        <span class="list_text"><?=$send?></span>
                        <img src="<?=bloginfo('template_url')?>/assets/images/arrow_down.svg" alt="">
                        <div class="list_items">
                            <?php
                                foreach($allCurr as $code => $rubs) {
                                    ?>
                                    <div class="list_items-val" data-value="<?=$code?>"<?php if ($code == $send) echo ' style="display: none;"' ?>>
                                        <?=$code?>
                                    </div>
                                    <?php
                                }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="field list list_target text_fz16">
                    <input class="list_input" type="text" name="send-bank" value="<?=$allBanks[$send][0]['name']?>" required hidden>
                    <div class="list_info">
                        <img class="list_img" src="<?=$allBanks[$send][0]['icon']?>" alt="">
                        <span class="list_text text_fw500"><?=$allBanks[$send][0]['name']?></span>
                    </div>
                    <img src="<?=bloginfo('template_url')?>/assets/images/arrow_down.svg" alt="">
                    <div class="list_items">
                        <?php
                            foreach($allBanks[$send] as $key => $bank) {
                                ?>
                                <div class="list_items-val long" data-value="<?=$bank['name']?>"<?=($bank['icon'] ? 'data-img="'.$bank['icon'].'"' : '')?><?php if ($key == 0) echo ' style="display: none;"' ?>>
                                    <?php if ($bank['icon']) : ?>
                                        <img src="<?=$bank['icon']?>" alt="">
                                    <?php endif; ?>
                                    <?=$bank['name']?>
                                </div>
                                <?php
                            }
                        ?>
                    </div>
                </div>
                <input type="number" name="send-sum" data-rubs="<?=$allCurr[$send]?>" class="field only-num text_fz16 text_fw500" placeholder="Введите сумму" required>
            </div>
            <div class="main__form-change-col shadow-block">
                <div class="title text_fz16">
                    <div class="text text_fw700">Получаете</div>
                    <div class="list text_fw500 text_upper list_target">
                        <input class="list_input" type="text" name="get-curr" value="<?=$get?>" required hidden>
                        <span class="list_text"><?=$get?></span>
                        <img src="<?=bloginfo('template_url')?>/assets/images/arrow_down.svg" alt="">
                        <div class="list_items">
                            <?php
                                foreach($allCurr as $code => $rubs) {
                                    ?>
                                    <div class="list_items-val" data-value="<?=$code?>"<?php if ($code == $get) echo ' style="display: none;"' ?>>
                                        <?=$code?>
                                    </div>
                                    <?php
                                }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="field list list_target text_fz16">
                    <input class="list_input" type="text" name="send-bank" value="<?=$allBanks[$get][0]['name']?>" required hidden>
                    <div class="list_info">
                        <img class="list_img" src="<?=$allBanks[$get][0]['icon']?>" alt="">
                        <span class="list_text text_fw500"><?=$allBanks[$get][0]['name']?></span>
                    </div>
                    <img src="<?=bloginfo('template_url')?>/assets/images/arrow_down.svg" alt="">
                    <div class="list_items">
                        <?php
                            foreach($allBanks[$get] as $key => $bank) {
                                ?>
                                <div class="list_items-val long" data-value="<?=$bank['name']?>"<?=($bank['icon'] ? 'data-img="'.$bank['icon'].'"' : '')?><?php if ($key == 0) echo ' style="display: none;"' ?>>
                                    <?php if ($bank['icon']) : ?>
                                        <img src="<?=$bank['icon']?>" alt="">
                                    <?php endif; ?>
                                    <?=$bank['name']?>
                                </div>
                                <?php
                            }
                        ?>
                    </div>
                </div>
                <input type="number" name="get-sum" data-rubs="<?=$allCurr[$get]?>" class="field only-num text_fz16 text_fw500" placeholder="Введите сумму" required>
            </div>
            <div class="main__form-change-row shadow-block">
                <div class="title text_fz16">
                    <div class="text text_fw700">Детали обмена</div>
                    <div class="change-banks text_fw500">
                        <?=$allBanks[$send][0]['name']?> на <?=$allBanks[$get][0]['name']?>
                    </div>
                </div>
                <div class="courses text_fz16">
                    <div class="courses-item">
                        <img src="<?=bloginfo('template_url')?>/assets/images/course.svg" alt="">
                        Курс: <b class="text_upper">1 <?=$send?> = <?=$allCurr[$send]/$allCurr[$get]?> <?=$get?></b>
                    </div>
                    <div class="courses-item">
                        <img src="<?=bloginfo('template_url')?>/assets/images/reserve.svg" alt="">
                        Резерв: <b class="text_upper"><?=$allReserve[$get]?></b>
                    </div>
                </div>
                <div class="contacts list field list_target input-change text_fz16">
                    <div class="cards-field input-field text_fz16">
                        <img src="<?=bloginfo('template_url')?>/assets/images/mail_color.svg" alt="" class="send-card-img list_img">
                        <input class="list_input" type="email" name="contacts" placeholder="Email для связи" required>
                    </div>
                    <img src="<?=bloginfo('template_url')?>/assets/images/arrow_down.svg" alt="">
                    <div class="list_items">
                        <div class="list_items-val long" data-img="<?=bloginfo('template_url')?>/assets/images/mail_color.svg" data-value="Email для связи" data-type="email" style="display: none;">
                            <img src="<?=bloginfo('template_url')?>/assets/images/mail_color.svg" alt="">
                            Email для связи
                        </div>
                        <div class="list_items-val long" data-img="<?=bloginfo('template_url')?>/assets/images/whatsapp.svg" data-value="Whats App" data-type="tel">
                            <img src="<?=bloginfo('template_url')?>/assets/images/whatsapp.svg" alt="">
                            Whats App
                        </div>
                        <div class="list_items-val long" data-img="<?=bloginfo('template_url')?>/assets/images/telegram_color.svg" data-value="Telegram" data-type="text">
                            <img src="<?=bloginfo('template_url')?>/assets/images/telegram_color.svg" alt="">
                            Telegram
                        </div>
                    </div>
                </div>
                <div class="cards">
                    <div class="cards-item">
                        <div class="cards-title text_fz14 text_fw600">Реквизиты отправителя:</div>
                        <div class="cards-field text_fz16">
                            <img src="<?=$allBanks[$send][0]['icon']?>" alt="" class="send-card-img">
                            <input type="text" name="send-card" data-mask="____ ____ ____ ____" class="card-validate field" placeholder="Карта <?=$allBanks[$send][0]['name']?>" required>
                        </div>
                    </div>
                    <div class="cards-item">
                        <div class="cards-title text_fz14 text_fw600">Реквизиты получателя:</div>
                        <div class="cards-field text_fz16">
                            <img src="<?=$allBanks[$get][0]['icon']?>" alt="" class="get-card-img">
                            <input type="text" name="get-card" data-mask="____ ____ ____ ____" class="card-validate field" placeholder="Карта <?=$allBanks[$get][0]['name']?>" required>
                        </div>
                    </div>
                </div>
            </div>
            <div class="main__form-change-button button button_shadow text_white text_fz18 text_fw700 text_center">
                Обменять
            </div>
            <?php endif; ?>
        </div>
        <?php    
        die();
    }
    
    
    add_action('wp_ajax_form_steps', 'form_steps');
    add_action('wp_ajax_nopriv_form_steps', 'form_steps');
?>