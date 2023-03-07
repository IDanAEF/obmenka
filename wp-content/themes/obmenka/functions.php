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

    function delete_order(){
        $deleted = wp_delete_post($_GET['post_id']);

        die();
    }
    
    add_action('wp_ajax_delete_order', 'delete_order');
    add_action('wp_ajax_nopriv_delete_order', 'delete_order');

    function check_status(){
        $res = get_field('finish', $_GET['post_id']) ? 'succes' : '';

        if (!$res && get_field('deny', $_GET['post_id'])) $res = 'deny';
        
        echo json_encode($res);

        die();
    }
    
    add_action('wp_ajax_check_status', 'check_status');
    add_action('wp_ajax_nopriv_check_status', 'check_status');

    function create_order(){
        function randomString($length = 12) {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
            return $randomString;
        }

        $id = randomString();

        $post_data = [
            'post_title'    => $id.' - '.$_GET['contacts'],
            'post_name'     => $id,
            'post_status'   => 'publish',
            'post_type'     => 'post',
            'post_author'   => 1,
            'ping_status'   => 'open',
            'post_category' => [3],
            'meta_input'    => [
                'id-code' => $id,
                'contacts' => $_GET['contacts'],
                'sender_card' => $_GET['send-card'], 
                'sender_bank' => $_GET['send-bank'],
                'sender_sum' => $_GET['send-sum'],
                'sender_carrency' => $_GET['send-curr'],
                'getter_card' => $_GET['get-card'], 
                'getter_bank' => $_GET['get-bank'],
                'getter_sum' => $_GET['get-sum'],
                'getter_carrency' => $_GET['get-curr'],
            ],
        ];
        
        $post_id = wp_insert_post($post_data);

        echo $post_id;

        die();
    }
    
    
    add_action('wp_ajax_create_order', 'create_order');
    add_action('wp_ajax_nopriv_create_order', 'create_order');

    function form_steps(){
        $step = (isset($_COOKIE['step']) ? $_COOKIE['step'] : 1);
        $currs = [
            'send-curr' => (isset($_COOKIE['send-curr']) ? $_COOKIE['send-curr'] : get_field('code-default_send', 27)),
            'get-curr' => (isset($_COOKIE['get-curr']) ? $_COOKIE['get-curr'] : get_field('code-default_get', 27))
        ];
        $getBank = [];
        $sendBank = [];
        $allCurr = [];
        $allBanks = [];
        $allReserve = [];
        while(have_rows('banks', 27)) {
            the_row();
            foreach(get_sub_field('currences', true) as $curr) {
                if (isset($_COOKIE['get-bank']) && $_COOKIE['get-bank'] == get_sub_field('name')) {
                    $getBank = [
                        'name' => get_sub_field('name'),
                        'icon' => get_sub_field('icon')
                    ];
                }
                if (isset($_COOKIE['send-bank']) && $_COOKIE['send-bank'] == get_sub_field('name')) {
                    $sendBank = [
                        'name' => get_sub_field('name'),
                        'icon' => get_sub_field('icon')
                    ];
                }
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
            $allCurr[get_sub_field('code')] = [
                'rubs' => get_sub_field('rubs'),
                'min' => get_sub_field('min-sum')
            ];
        }
        // if ($currs['send-curr'] == $currs['get-curr']) {
        //     $currs[$_COOKIE['revert-curr']] = $_COOKIE['old-curr'];
        // }
        if (!isset($_COOKIE['get-bank'])) {
            $getBank = $allBanks[$currs['get-curr']][0];
        }
        if (!isset($_COOKIE['send-bank'])) {
            $sendBank = $allBanks[$currs['send-curr']][0];
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
                    <div class="list text_fw500 text_upper list_target target-currs" data-revert="get-curr">
                        <input class="list_input" type="text" name="send-curr" value="<?=$currs['send-curr']?>" required hidden>
                        <span class="list_text"><?=$currs['send-curr']?></span>
                        <img src="<?=bloginfo('template_url')?>/assets/images/arrow_down.svg" alt="">
                        <div class="list_items">
                            <?php
                                foreach($allCurr as $code => $values) {
                                    ?>
                                    <div class="list_items-val" data-value="<?=$code?>"<?php if ($code == $currs['send-curr']) echo ' style="display: none;"' ?>>
                                        <?=$code?>
                                    </div>
                                    <?php
                                }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="field list list_target text_fz16 target-banks">
                    <input class="list_input" type="text" name="send-bank" value="<?=$sendBank['name']?>" required hidden>
                    <div class="list_info">
                        <img class="list_img" src="<?=$sendBank['icon']?>" alt="">
                        <span class="list_text text_fw500"><?=$sendBank['name']?></span>
                    </div>
                    <img src="<?=bloginfo('template_url')?>/assets/images/arrow_down.svg" alt="">
                    <div class="list_items">
                        <?php
                            foreach($allBanks[$currs['send-curr']] as $key => $bank) {
                                ?>
                                <div class="list_items-val long" data-value="<?=$bank['name']?>"<?=($bank['icon'] ? 'data-img="'.$bank['icon'].'"' : '')?>>
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
                <input type="text" name="send-sum" data-min="<?=$allCurr[$currs['send-curr']]['min']?>" data-rubs="<?=$allCurr[$currs['send-curr']]['rubs']?>" class="field only-num text_fz16 text_fw500 only-number" placeholder="Введите сумму" required>
                <div class="sum-invalid text_fz14"></div>
            </div>
            <div class="main__form-change-col shadow-block">
                <div class="title text_fz16">
                    <div class="text text_fw700">Получаете</div>
                    <div class="list text_fw500 text_upper list_target target-currs" data-revert="send-curr">
                        <input class="list_input" type="text" name="get-curr" value="<?=$currs['get-curr']?>" required hidden>
                        <span class="list_text"><?=$currs['get-curr']?></span>
                        <img src="<?=bloginfo('template_url')?>/assets/images/arrow_down.svg" alt="">
                        <div class="list_items">
                            <?php
                                foreach($allCurr as $code => $values) {
                                    ?>
                                    <div class="list_items-val" data-value="<?=$code?>"<?php if ($code == $currs['get-curr']) echo ' style="display: none;"' ?>>
                                        <?=$code?>
                                    </div>
                                    <?php
                                }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="field list list_target text_fz16 target-banks">
                    <input class="list_input" type="text" name="get-bank" value="<?=$getBank['name']?>" required hidden>
                    <div class="list_info">
                        <img class="list_img" src="<?=$getBank['icon']?>" alt="">
                        <span class="list_text text_fw500"><?=$getBank['name']?></span>
                    </div>
                    <img src="<?=bloginfo('template_url')?>/assets/images/arrow_down.svg" alt="">
                    <div class="list_items">
                        <?php
                            foreach($allBanks[$currs['get-curr']] as $key => $bank) {
                                ?>
                                <div class="list_items-val long" data-value="<?=$bank['name']?>"<?=($bank['icon'] ? 'data-img="'.$bank['icon'].'"' : '')?>>
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
                <input type="test" name="get-sum" pattern="[0-9]" data-rubs="<?=$allCurr[$currs['get-curr']]['rubs']?>" class="field only-num text_fz16 text_fw500 only-number" placeholder="Введите сумму" required>
            </div>
            <div class="main__form-change-row shadow-block">
                <div class="title text_fz16">
                    <div class="text text_fw700">Детали обмена</div>
                    <div class="change-banks text_fw500">
                        <?=$sendBank['name']?> на <?=$getBank['name']?>
                    </div>
                </div>
                <div class="courses text_fz16">
                    <div class="courses-item">
                        <img src="<?=bloginfo('template_url')?>/assets/images/course.svg" alt="">
                        Курс: <b class="text_upper">1 <?=$currs['send-curr']?> = <?=round($allCurr[$currs['send-curr']]['rubs']/$allCurr[$currs['get-curr']]['rubs'], 2)?> <?=$currs['get-curr']?></b>
                    </div>
                    <div class="courses-item">
                        <img src="<?=bloginfo('template_url')?>/assets/images/reserve.svg" alt="">
                        Резерв: <b class="text_upper"><?=$allReserve[$currs['get-curr']]?></b>
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
                        <div class="list_items-val long" data-mask="+_ (___) ___-__-__" data-img="<?=bloginfo('template_url')?>/assets/images/whatsapp.svg" data-value="Whats App" data-type="tel">
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
                            <img src="<?=$sendBank['icon']?>" alt="" class="send-card-img">
                            <input type="text" name="send-card" data-mask="____ ____ ____ ____" class="card-validate field" placeholder="Карта <?=$sendBank['name']?>" required>
                        </div>
                        <div class="cards-invalid text_fz14"></div>
                    </div>
                    <div class="cards-item">
                        <div class="cards-title text_fz14 text_fw600">Реквизиты получателя:</div>
                        <div class="cards-field text_fz16">
                            <img src="<?=$getBank['icon']?>" alt="" class="get-card-img">
                            <input type="text" name="get-card" data-mask="____ ____ ____ ____" class="card-validate field" placeholder="Карта <?=$getBank['name']?>" required>
                        </div>
                        <div class="cards-invalid text_fz14"></div>
                    </div>
                </div>
            </div>
            <div class="main__form-change-button change button button_shadow text_white text_fz18 text_fw700 text_center">
                Обменять
            </div>
            <?php elseif ($step == 2 && isset($_COOKIE['status']) && $_COOKIE['status'] == 'send-money') : ?>
            <?php
                $post_id = $_COOKIE['order-post-id'];    
            ?>
            <div class="main__form-change-order" data-date-out="<?=get_post_timestamp($post_id) + 1080?>">
                <div class="main__form-change-head">
                    <h2 class="text_fz18 text_fw700">Ваша заявка создана</h2>
                    <span class="text_fz16"><b>ID:</b> <?php the_field('id-code', $post_id) ?></span>
                </div>
                <div class="main__form-change-row">
                    <div class="title text_fz16 text_fw600">Вы обмениваете <?php the_field('sender_bank', $post_id) ?> на <?php the_field('getter_bank', $post_id) ?>:</div>
                    <div class="field text_fz16 text_fw500 text_upper">
                        <div class="list_info">
                            <span class="list_text text_fw500"><?php the_field('sender_sum', $post_id) ?> <?php the_field('sender_carrency', $post_id) ?></span>
                        </div>
                    </div>
                    <div class="field text_fz16 text_fw500 text_upper">
                        <div class="list_info">
                            <span class="list_text text_fw500"><?php the_field('getter_sum', $post_id) ?> <?php the_field('getter_carrency', $post_id) ?></span>
                        </div>
                    </div>
                    <div class="main__form-change-ways text_fz14">
                        <div class="main__form-change-way">
                            <div class="number text_white text_fz12">1</div>Перейдите в приложение <?php the_field('sender_bank', $post_id) ?>
                        </div>
                        <div class="main__form-change-way">
                            <div class="number text_white text_fz12">2</div>Выполните перевод
                        </div>
                    </div>
                    <div class="cards-item">
                        <div class="cards-title text_fz14 text_fw500">Реквизиты:</div>
                        <div class="field text_fz16 text_fw500 text_upper">
                            <div class="list_info">
                                <span class="list_text text_fw500"><?=preg_replace('/\d{4}/', "$0 ",get_field('requisites_default', 27)) ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="cards-item">
                        <div class="cards-title text_fz14 text_fw500">Сумма:</div>
                        <div class="field text_fz16 text_fw500 text_upper">
                            <div class="list_info">
                                <span class="list_text text_fw500"><?php the_field('sender_sum', $post_id) ?> <?php the_field('sender_carrency', $post_id) ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="info-row text_fz14 text_fz500">
                        <span>Курс:</span>
                        <span class="text_upper">1 <?php the_field('sender_carrency', $post_id) ?> = <?=round($allCurr[get_field('sender_carrency', $post_id)]['rubs']/$allCurr[get_field('getter_carrency', $post_id)]['rubs'], 2)?> <?php the_field('getter_carrency', $post_id) ?></span>
                    </div>
                    <div class="info-row text_fz14 text_fz500">
                        <span class="window">Окно оплаты</span>
                        <span class="time text_fw500"><span id="timer-minutes">18</span>:<span id="timer-seconds">00</span></span>
                    </div>
                    <div class="modal__buttons">
                        <div class="button delete-order button_red text_fz16 text_fw500 text_center">
                            Отмена
                        </div>
                        <div class="button pay-done text_white text_fz16 text_fw500 text_center">
                            Я оплатил
                        </div>
                    </div>
                </div>
            </div>
            <?php elseif ($step == 2 && isset($_COOKIE['status']) && $_COOKIE['status'] == 'get-money') : ?>
            <?php
                $post_id = $_COOKIE['order-post-id'];    
            ?>
            <div class="main__form-change-order check-status-interval">
                <div class="main__form-change-head">
                    <h2 class="text_fz18 text_fw700">Ваша заявка в обработке</h2>
                    <span class="text_fz16"><b>ID:</b> <?php the_field('id-code', $post_id) ?></span>
                </div>
                <div class="main__form-change-row">
                    <div class="main__form-change-order-loader text_fz14">
                        <div class="loading-anim">
                            <img src="/wp-content/themes/obmenka/assets/images/load1.png" alt="">
                            <img src="/wp-content/themes/obmenka/assets/images/load2.png" alt="">
                            <img src="/wp-content/themes/obmenka/assets/images/load3.png" alt="">
                            <img src="/wp-content/themes/obmenka/assets/images/load4.png" alt="">
                        </div>
                        <span>Обрабатываем заявку...</span>
                    </div>
                    <div class="order-info text_fz16 text_center">
                        Ожидайте перевода <b class="text_upper"><?php the_field('getter_sum', $post_id) ?> <?php the_field('getter_carrency', $post_id) ?></b> на <b><?php the_field('getter_bank', $post_id) ?> <?=preg_replace('/\d{4}/', "$0 ",get_field('getter_card', $post_id)) ?></b>
                    </div>
                </div>
            </div>
            <?php elseif ($step == 3) : ?>
            <?php
                $post_id = $_COOKIE['order-post-id'];    
            ?>
            <div class="main__form-change-order result">
                <div class="main__form-change-head">
                    <h2 class="text_fz18 text_fw700"><?=get_field('deny', $post_id) ? 'Ваша заявка отклонена' : 'Ваша заявка выполнена'?></h2>
                    <span class="text_fz16"><b>ID:</b> <?php the_field('id-code', $post_id) ?></span>
                </div>
                <div class="main__form-change-row">
                    <div class="main__form-change-order-icon text_fz20 text_fw600">
                        <div class="main__products-item-number icon text_white">
                            <?php if (get_field('deny', $post_id)) : ?>
                            <div class="deny"><span></span><span></span></div>
                            <?php else : ?>
                            <img src="/wp-content/themes/obmenka/assets/images/double_check.svg" alt="">
                            <?php endif; ?>
                        </div>
                        <span><?=get_field('deny', $post_id) ? 'Заявка отклонена!' : 'Заявка выполнена!'?></span>
                    </div>
                    <div class="order-info text_fz16 text_center">
                        <?php if (get_field('deny', $post_id)) : ?>
                        <?=get_field('reason', $post_id)?>
                        <?php else : ?>
                        Перевод <b class="text_upper"><?php the_field('getter_sum', $post_id) ?> <?php the_field('getter_carrency', $post_id) ?></b> должен поступить на <b><?php the_field('getter_bank', $post_id) ?> <?=preg_replace('/\d{4}/', "$0 ",get_field('getter_card', $post_id)) ?></b>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php    
        die();
    }
    
    
    add_action('wp_ajax_form_steps', 'form_steps');
    add_action('wp_ajax_nopriv_form_steps', 'form_steps');
?>