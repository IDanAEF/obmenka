<?php
    add_action('init', 'register_post_types');

    function register_post_types(){
        register_post_type( 'abroad', [
            'label'  => null,
            'labels' => [
                'name'               => 'Заявки',
                'singular_name'      => 'Заявка',
                'add_new'            => 'Добавить заявку',
                'add_new_item'       => 'Добавление заявки', 
                'edit_item'          => 'Редактирование заявки',
                'new_item'           => 'Новая заявка',
                'view_item'          => 'Смотреть заявку',
                'search_items'       => 'Искать заявки',
                'not_found'          => 'Не найдено', 
                'not_found_in_trash' => 'Не найдено в корзине',
                'parent_item_colon'  => '',
                'menu_name'          => 'Заявки на покупку за рубежом',
            ],
            'description'            => '',
            'public'                 => true,
            'show_in_menu'           => null, 
            'show_in_rest'        => null,
            'rest_base'           => null,
            'menu_position'       => null,
            'menu_icon'           => null,
            'hierarchical'        => false,
            'supports'            => ['title'],
            'taxonomies'          => [],
            'has_archive'         => false,
            'rewrite'             => true,
            'query_var'           => true,
        ] );
    }

    function getAmoTokenOnce() {
        $subdomain = 'kostenichss';
        $link = 'https://' . $subdomain . '.amocrm.ru/oauth2/access_token';
        
        $data = [
            'client_id' => get_field('client_id', 27),
            'client_secret' => get_field('client_secret', 27),
            'grant_type' => 'authorization_code',
            'code' => get_field('code', 27),
            'redirect_uri' => 'https://topobmenka.com',
        ];
        
        
        $curl = curl_init();
        curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-oAuth-client/1.0');
        curl_setopt($curl,CURLOPT_URL, $link);
        curl_setopt($curl,CURLOPT_HTTPHEADER,['Content-Type:application/json']);
        curl_setopt($curl,CURLOPT_HEADER, false);
        curl_setopt($curl,CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl,CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($curl,CURLOPT_SSL_VERIFYHOST, 2);
        $out = curl_exec($curl);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        
        $code = (int)$code;
        
        $errors = [
            400 => 'Bad request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not found',
            500 => 'Internal server error',
            502 => 'Bad gateway',
            503 => 'Service unavailable',
        ];
        
        try
        {
            if ($code < 200 || $code > 204) {
                throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undefined error', $code);
            }
        }
        catch(\Exception $e)
        {
            die('Ошибка: ' . $e->getMessage() . PHP_EOL . 'Код ошибки: ' . $e->getCode());
        }
        
        
        $response = json_decode($out, true);
        
        $arrParamsAmo = [
            "access_token" => $response['access_token'],
            "refresh_token" => $response['refresh_token'],
            "token_type" => $response['token_type'],
            "expires_in" => $response['expires_in'],
            "endTokenTime" => $response['expires_in'] + time(),
        ];
        
        $args = [
            'ID' => 27,
            'meta_input' => [
                'access_token' => $arrParamsAmo['access_token'],
                'refresh_token' => $arrParamsAmo['refresh_token'],
                'endtokentime' => $arrParamsAmo['endTokenTime']
            ],
        ];

        wp_update_post(wp_slash($args));
    }

    function returnNewAmoToken() {
        if(get_field('endtokentime', 27) > time()) return;

        $link = 'https://kostenichss.amocrm.ru/oauth2/access_token';
    
        $data = [
            'client_id' => get_field('client_id', 27),
            'client_secret' => get_field('client_secret', 27),
            'grant_type' => 'refresh_token',
            'refresh_token' => get_field('refresh_token', 27),
            'redirect_uri' => 'https://topobmenka.ru/',
        ];
    
        $curl = curl_init(); 
        curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-oAuth-client/1.0');
        curl_setopt($curl,CURLOPT_URL, $link);
        curl_setopt($curl,CURLOPT_HTTPHEADER,['Content-Type:application/json']);
        curl_setopt($curl,CURLOPT_HEADER, false);
        curl_setopt($curl,CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl,CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($curl,CURLOPT_SSL_VERIFYHOST, 2);
        $out = curl_exec($curl); 
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        
        $code = (int)$code;
        $errors = [
            400 => 'Bad request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not found',
            500 => 'Internal server error',
            502 => 'Bad gateway',
            503 => 'Service unavailable',
        ];
    
        try
        {
            if ($code < 200 || $code > 204) {
                throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undefined error', $code);
            }
        }
        catch(\Exception $e)
        {
            die('Ошибка: ' . $e->getMessage() . PHP_EOL . 'Код ошибки: ' . $e->getCode());
        }
    
        $response = json_decode($out, true);
    
        if($response) {
    
            $response["endTokenTime"] = time() + $response["expires_in"];
    
            $args = [
                'ID' => 27,
                'meta_input' => [
                    'access_token' => $response['access_token'],
                    'refresh_token' => $response['refresh_token'],
                    'endtokentime' => $response['endTokenTime']
                ],
            ];
    
            wp_update_post(wp_slash($args));
        }
    }

    function amoGetTaskSiteElem($taskId) {
        returnNewAmoToken();

        $headers = [
            "Accept: application/json",
            'Authorization: Bearer ' . get_field('access_token', 27)
        ];

        $link = "https://kostenichss.amocrm.ru/api/v4/leads/".$taskId;

        $curl = curl_init();
        curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-oAuth-client/1.0');
        curl_setopt($curl,CURLOPT_URL, $link);
        curl_setopt($curl,CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl,CURLOPT_HEADER, false);
        curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($curl,CURLOPT_SSL_VERIFYHOST, 2);
        $out = curl_exec($curl);
        curl_close($curl);
        $result = json_decode($out,TRUE);

        foreach($result['custom_fields_values'] as $field) {
            if ($field['field_id'] == 207349) {
                return $field['values'][0]['value'];
            }
        }
    }

    function amoUpdateTask($elemId, $pipe) {
        returnNewAmoToken();

        $arrParams = [
            [
                "id" => (int)get_field('amocrm_lead_id', $elemId),
                "status_id" => (int)$pipe,
            ],
        ];

        $link = "https://kostenichss.amocrm.ru/api/v4/leads";
    
        $headers = [
            "Accept: application/json",
            'Authorization: Bearer ' . get_field('access_token', 27)
        ];
    
        $curl = curl_init();
        curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-oAuth-client/1.0');
        curl_setopt($curl,CURLOPT_URL, $link);
        curl_setopt($curl,CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl,CURLOPT_HEADER, false);
        curl_setopt($curl,CURLOPT_CUSTOMREQUEST, 'PATCH');
        curl_setopt($curl,CURLOPT_POSTFIELDS, json_encode($arrParams));
        curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($curl,CURLOPT_SSL_VERIFYHOST, 2);
        $out = curl_exec($curl);
        curl_close($curl);
        $result = json_decode($out,TRUE);
    }

    function amoAddTask($elemId) {
        returnNewAmoToken();

        $sendVal = 0;
        $getVal = 0;
        $price = (int)get_field('sender_sum', $elemId);

        foreach(get_field('courses', 27) as $course) {
            if ($course['send']['code'] == get_field('sender_carrency', $elemId) && $course['get']['code'] == get_field('getter_carrency', $elemId)) {
                $sendVal = $course['send']['price'];
                $getVal = $course['get']['price'];
            }

            if ($course['send']['code'] == 'rub' && $course['get']['code'] == get_field('sender_carrency', $elemId)) {
                $price = (int)(get_field('sender_sum', $elemId) * $course['send']['price']);
            }
        }

        $contact_fields = [
            [
                "field_id" => 48105, //E-mail
                "values" => [
                    [
                        "value" => get_field('send-email', $elemId)
                    ]
                ]
            ],
        ];

        $custom_fields = [
            [
                "field_id" => 204247, //ID-code заявки
                "values" => [
                    [
                        "value" => get_field('id-code', $elemId)
                    ]
                ]
            ],
            [
                "field_id" => 207349, //ID элемента в админке
                "values" => [
                    [
                        "value" => ''.$elemId.''
                    ],
                ]
            ],
            [
                "field_id" => 204229, //Реквизиты отправителя
                "values" => [
                    [
                        "value" => get_field('sender_card', $elemId)
                    ],
                ]
            ],
            [
                "field_id" => 204231, //Банк отправителя
                "values" => [
                    [
                        "value" => get_field('sender_bank', $elemId)
                    ],
                ]
            ],
            [
                "field_id" => 204233, //Сумма отправителя
                "values" => [
                    [
                        "value" => get_field('sender_sum', $elemId)
                    ],
                ]
            ],
            [
                "field_id" => 204235, //Валюта отправителя
                "values" => [
                    [
                        "value" => mb_strtoupper(get_field('sender_carrency', $elemId))
                    ],
                ]
            ],
            [
                "field_id" => 204239, //Реквизиты получателя
                "values" => [
                    [
                        "value" => get_field('getter_card', $elemId)
                    ],
                ]
            ],
            [
                "field_id" => 204243, //Банк получателя
                "values" => [
                    [
                        "value" => get_field('getter_bank', $elemId)
                    ],
                ]
            ],
            [
                "field_id" => 204241, //Сумма получателя
                "values" => [
                    [
                        "value" => get_field('getter_sum', $elemId)
                    ],
                ]
            ],
            [
                "field_id" => 204245, //Валюта получателя
                "values" => [
                    [
                        "value" => mb_strtoupper(get_field('getter_carrency', $elemId))
                    ],
                ]
            ],
            [
                "field_id" => 204301, //Курс обмена
                "values" => [
                    [
                        "value" => $sendVal.' '.mb_strtoupper(get_field('sender_carrency', $elemId)).' = '.$getVal.' '.mb_strtoupper(get_field('getter_carrency', $elemId))
                    ],
                ]
            ],
            [
                "field_id" => 248387, //Источник
                "values" => [
                    [
                        "value" => get_site_url()
                    ],
                ]
            ],
            [
                "field_id" => 248403, //Дата
                "values" => [
                    [
                        "value" => time()
                    ],
                ]
            ],
        ];

        if (get_field('contacts', $elemId)) {
            $contact_fields[] = [
                "field_id" => 209291, //Контакт Telegram/WhatsApp 
                "values" => [
                    [
                        "value" => get_field('contacts', $elemId)
                    ]
                ]
            ];

            $custom_fields[] = [
                "field_id" => 204299, //Контакт Telegram/WhatsApp
                "values" => [
                    [
                        "value" => get_field('contacts', $elemId)
                    ],
                ]
            ];
        }

        $arrParams = [
            [
                "name" => "Заявка ".get_field('id-code', $elemId),
                "price" => $price,
                "_embedded" => [
                    "contacts" => [
                        [
                            "first_name" => get_field('send-email', $elemId),
                            "custom_fields_values" => $contact_fields
                        ]
                    ]
                ],
                "custom_fields_values" => $custom_fields
            ]
        ];

        $link = "https://kostenichss.amocrm.ru/api/v4/leads/complex";
    
        $headers = [
            "Accept: application/json",
            'Authorization: Bearer ' . get_field('access_token', 27)
        ];
    
        $curl = curl_init();
        curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-oAuth-client/1.0');
        curl_setopt($curl,CURLOPT_URL, $link);
        curl_setopt($curl,CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl,CURLOPT_HEADER, false);
        curl_setopt($curl,CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl,CURLOPT_POSTFIELDS, json_encode($arrParams));
        curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($curl,CURLOPT_SSL_VERIFYHOST, 2);
        $out = curl_exec($curl);
        curl_close($curl);
        $result = json_decode($out,TRUE);
    
        return $result[0];
    }

    add_theme_support('menus');
    add_theme_support('post-thumbnails');

    function obmenkaAddScripts() {
        wp_enqueue_style( 'obmenka_main_style', get_template_directory_uri() . '/assets/css/style.min.css' );
        wp_enqueue_style( 'obmenka_custom_style', get_template_directory_uri() . '/custom.css', false, '6' );
        
        wp_enqueue_script( 'obmenka_main_scrit', get_template_directory_uri() . '/assets/js/script.js', array(), '20', true );
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

    function order_pipe_update(){
        $lead = $_POST['leads']['status'][0]['id'];
        $leadPipe = $_POST['leads']['status'][0]['status_id'];
        $elemId = amoGetTaskSiteElem($lead);

        $args = [
            'ID' => $elemId,
        ];

        if ($leadPipe == 142) {
            $args['meta_input'] = [
                'payd' => 1,
                'finish' => 1
            ];
        } else if ($leadPipe == 143) {
            $args['meta_input'] = [
                'finish' => 0,
                'deny' => 1
            ];
        }

        wp_update_post(wp_slash($args));

        die();
    }
    
    add_action('wp_ajax_order_pipe_update', 'order_pipe_update');
    add_action('wp_ajax_nopriv_order_pipe_update', 'order_pipe_update');

    function delete_order(){
        //$deleted = wp_delete_post($_GET['post_id']);
        $post_id = $_GET['post_id'];

        amoUpdateTask($post_id, 57697570);

        wp_set_post_tags($post_id, 'Отменена пользователем/истекло время оплаты');

        $to = get_option('admin_email'); 
        $from = get_option('admin_email');
        $subject = "Obmenka: Пользователь отменил заявку/истекло время для оплаты"; 
        $messText = "
            ID заявки: ".get_field('id-code', $post_id)."
            Ссылка для просмотра на сайте: https://topobmenka.com/wp-admin/post.php?post=".$post_id."&action=edit
        ";
        $message = "Информационное сообщение Obmenka
        ------------------------------------------
        
        ".$messText."
        
        Сообщение сгенерировано автоматически"; 
        
        $mailheaders = "MIME-Version: 1.0\r\n"; 
        $mailheaders .="Content-Type: text/plain; charset=UTF-8\r\n"; 
        $mailheaders .= "From: ".$from."\r\n";
        $mailheaders .= "X-Mailer: PHP/".phpversion()."\r\n";
        
        $rsf = mail($to,$subject,$message,$mailheaders);

        if (get_field('id-code', $post_id)) {
            $to = get_field('send-email', $post_id); 
            $from = 'info@topobmenka.com';
            $subject = "Obmenka: Вы отменили заявку/истекло время ожидания оплаты."; 
            $messText = "
                ID заявки: ".get_field('id-code', $post_id)."
            ";
            $message = "Информационное сообщение Obmenka
            ------------------------------------------
            
            ".$messText."

            Напишите нашей поддержке, если у вас возникли трудности:
            info@topobmenka.com
            https://t.me/TopObmenka
            
            Сообщение сгенерировано автоматически"; 
             
            // $mailheaders = "MIME-Version: 1.0\r\n"; 
            // $mailheaders .="Content-Type: text/plain; charset=UTF-8\r\n"; 
            // $mailheaders .= "From: ".$from."\r\n";
            $mailheaders = "Reply-To: ".get_option('admin_email')."\r\n";
            //$mailheaders .= "X-Mailer: PHP/".phpversion()."\r\n";
            
            $rsf = wp_mail($to,$subject,$message,$mailheaders);
        }

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

    add_action('save_post', 'order_update', 10, 4);

    function order_update($post_id){
        if ( wp_is_post_revision($post_id) || get_post($post_id)->post_status != 'publish' )
		    return;

        clean_post_cache($post_id);

        $finish = get_post_meta($post_id, 'finish', true);
        
        if (get_the_category($post_id)[0]->term_id == 3 && $finish) {
            $to = get_field('send-email', $post_id); 
            $from = 'info@topobmenka.com';
            $subject = "Obmenka: Ваша заявка успешно завершена."; 
            $messText = "
                Ваша заявка - ".get_field('id-code', $post_id)." - успешно завершена

                Перевод на сумму ".get_field('getter_sum', $post_id)." ".mb_strtoupper(get_field('getter_carrency', $post_id))." должен поступить на ".(get_field('getter_bank', $post_id) ?: get_field('getter_carrency', $post_id))." ".(preg_replace('/\d{4}/', "$0 ",get_field('getter_card', $post_id)))."

                Спасибо, что выбрали нас!
            ";
            $message = "Информационное сообщение Obmenka
            ------------------------------------------
            
            ".$messText."

            Поддержка:
            info@topobmenka.com
            https://t.me/TopObmenka
            
            Сообщение сгенерировано автоматически"; 

            wp_set_post_tags($post_id, 'Завершено');
            
            $mailheaders = "Reply-To: ".get_option('admin_email')."\r\n";
            $rsf = wp_mail($to,$subject,$message,$mailheaders);

            amoUpdateTask($post_id, 142);
        }
    }

    function payd_mess(){
        $to = get_option('admin_email'); 
        $from = get_option('admin_email');
        $subject = "Obmenka: Пользователь совершил перевод";
        $message = "Информационное сообщение Obmenka
        ------------------------------------------
        
        Пользователь совершил перевод по заявке:

        https://topobmenka.com/wp-admin/post.php?post=".$_GET['order-id']."&action=edit
        
        
        Сообщение сгенерировано автоматически"; 
        
        $boundary = "--".md5(uniqid(time())); 
        $mailheaders = "MIME-Version: 1.0\n"; 
        $mailheaders .="Content-Type: multipart/mixed; boundary=".$boundary."\n"; 
        $mailheaders .= "From: ".$from."\r\n";
        $multipart = "--".$boundary."\n"; 
        $multipart .= "Content-Type: text/plain; charset=UTF-8\n\n"; 
        $multipart .= $message."\n\n"; 
        
        $rsf = mail($to,$subject,$multipart,$mailheaders);

        $args = [
            'ID' => $_GET['order-id'],
            'meta_input' => [
                'payd' => 1,
            ],
        ];

        wp_set_post_tags($_GET['order-id'], 'Пользователь подтвердил оплату');
        wp_update_post(wp_slash($args));

        amoUpdateTask($post_id, 57528674);

        die();
    }
    
    add_action('wp_ajax_payd_mess', 'payd_mess');
    add_action('wp_ajax_nopriv_payd_mess', 'payd_mess');

    function create_order(){
        date_default_timezone_set('Europe/Moscow');

        function randomString($length = 12) {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
            return $randomString;
        }

        $contType = 'E-mail';
        if ($_GET['contacts'] && $_GET['conttype'] == 'tel') $contType = 'WhatsApp';
        if ($_GET['contacts'] && $_GET['conttype'] == 'text') $contType = 'Telegram';

        $id = randomString();

        $contactText = $_GET['contacts'] ?: $_GET['send-email'];

        $post_data = [
            'post_title'    => $id.' - '.$contactText.' - '.$contType,
            'post_name'     => $id,
            'post_status'   => 'publish',
            'post_type'     => 'post',
            'post_author'   => 1,
            'ping_status'   => 'open',
            'post_category' => [3],
            'meta_input'    => [
                'id-code' => $id,
                'send-email' => $_GET['send-email'],
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

        $allCurr = [];
            
        while(have_rows('currences', 27)) {
            the_row();
            $allCurr[get_sub_field('code')] = [
                'min' => get_sub_field('min-sum'),
                'reserve' => get_sub_field('reserve'),
                'not-card' => get_sub_field('add')['not-card'],
                'placeholder' => get_sub_field('add')['placeholder']
            ];
        }

        $sendVal = 0;
        $getVal = 0;
        foreach(get_field('courses', 27) as $course) {
            if ($course['send']['code'] == $_GET['send-curr'] && $course['get']['code'] == $_GET['get-curr']) {
                $sendVal = $course['send']['price'];
                $getVal = $course['get']['price'];
            }
        }

        $to = get_option('admin_email'); 
        $from = get_option('admin_email');
        $subject = "Obmenka: Новая заявка от ".$_GET['send-email']; 
        $messText = "
            ID заявки: ".$id."
            Ссылка для просмотра на сайте: https://topobmenka.com/wp-admin/post.php?post=".$post_id."&action=edit

            E-mail: ".$_GET['send-email']."
            ".($_GET['contacts'] ? 'Контакты: '.$_GET['contacts'].' - '.$contType : '')."

            Курс обмена: ".$sendVal." ".mb_strtoupper($_GET['send-curr'])." = ".$getVal." ".mb_strtoupper($_GET['get-curr'])."

            Данные отправителя
            Реквизиты: ".$_GET['send-card']."
            Банк: ".$_GET['send-bank']."
            Сумма: ".$_GET['send-sum']."
            Валюта: ".mb_strtoupper($_GET['send-curr'])."

            Данные получателя
            Реквизиты: ".$_GET['get-card']."
            Банк: ".$_GET['get-bank']."
            Сумма: ".$_GET['get-sum']."
            Валюта: ".mb_strtoupper($_GET['get-curr'])."

            Дата формирования: ".date('d-m-Y H:i:s')." UTC+3
        ";
        $message = "Информационное сообщение Obmenka
        ------------------------------------------
        
        Вы получили новую заявку - 
        
        ".$messText."
        
        Сообщение сгенерировано автоматически"; 
        
        $mailheaders = "MIME-Version: 1.0\r\n"; 
        $mailheaders .="Content-Type: text/plain; charset=UTF-8\r\n"; 
        $mailheaders .= "From: ".$from."\r\n";
        $mailheaders .= "X-Mailer: PHP/".phpversion()."\r\n";
        
        $rsf = mail($to,$subject,$message,$mailheaders);

        if ($_GET['send-email']) {
            $to = $_GET['send-email']; 
            $from = 'info@topobmenka.com';
            $subject = "Obmenka: Вами была успешно создана заявка на обмен."; 
            $messText = "
                ID заявки: ".$id."
                
                E-mail: ".$_GET['send-email']."
                ".($_GET['contacts'] ? 'Контакты: '.$_GET['contacts'].' - '.$contType : '')."

                Курс обмена: ".$sendVal." ".mb_strtoupper($_GET['send-curr'])." = ".$getVal." ".mb_strtoupper($_GET['get-curr'])."

                Данные отправителя
                Реквизиты: ".$_GET['send-card']."
                Банк: ".$_GET['send-bank']."
                Сумма: ".$_GET['send-sum']."
                Валюта: ".mb_strtoupper($_GET['send-curr'])."

                Данные получателя
                Реквизиты: ".$_GET['get-card']."
                Банк: ".$_GET['get-bank']."
                Сумма: ".$_GET['get-sum']."
                Валюта: ".mb_strtoupper($_GET['get-curr'])."

                Дата формирования: ".date('d-m-Y H:i:s')." UTC+3
            ";
            $message = "Информационное сообщение Obmenka
            ------------------------------------------
            
            Детали вашей заявки - 
            
            ".$messText."

            Поддержка:
            info@topobmenka.com
            https://t.me/TopObmenka
            
            Сообщение сгенерировано автоматически"; 
             
            // $mailheaders = "MIME-Version: 1.0\r\n"; 
            // $mailheaders .="Content-Type: text/plain; charset=UTF-8\r\n"; 
            // $mailheaders .= "From: ".$from."\r\n";
            $mailheaders = "Reply-To: ".get_option('admin_email')."\r\n";
            //$mailheaders .= "X-Mailer: PHP/".phpversion()."\r\n";
            
            $rsf = wp_mail($to,$subject,$message,$mailheaders);
        }

        $addTaskRes = amoAddTask($post_id);

        $args = [
            'ID' => $post_id,
            'meta_input' => [
                'amocrm_lead_id' => $addTaskRes['id'],
                'amocrm_contact_id' => $addTaskRes['contact_id']
            ],
        ];

        wp_update_post(wp_slash($args));

        echo $post_id;
        die();
    }
    
    
    add_action('wp_ajax_create_order', 'create_order');
    add_action('wp_ajax_nopriv_create_order', 'create_order');

    function create_buy_order(){
        date_default_timezone_set('Europe/Moscow');

        $post_data = [
            'post_title'    => $_POST['feedproduct'],
            'post_name'     => $_POST['feedproduct'],
            'post_status'   => 'publish',
            'post_type'     => 'abroad',
            'post_author'   => 1,
            'ping_status'   => 'open',
            'post_category' => [],
            'meta_input'    => [
                'link' => $_POST['feedlink'],
                'name' => $_POST['feedproduct'],
                'price' => $_POST['feedprice'],
                'curr' => $_POST['feedcurr'],
                'details' => $_POST['feedmess'],
                'contact' => $_POST['feedcontact'],
                'conttype' => $_POST['feedconttype']
            ],
        ];
        
        $post_id = wp_insert_post($post_data);

        $to = get_option('admin_email'); 
        $from = get_option('admin_email');
        $subject = "Obmenka: Новая заявка на покупку за рубежом"; 
        $messText = "
            Ссылка для просмотра на сайте: https://topobmenka.com/wp-admin/post.php?post=".$post_id."&action=edit

            Ссылка на товар или счет для оплаты: ".$_POST['feedlink']."
            Название товара: ".$_POST['feedproduct']."
            Цена товара: ".$_POST['feedprice']."
            Детали заказа:
            ".$_POST['feedmess']."

            Способ связи: ".$_POST['feedcontact']."

            Дата формирования: ".date('d-m-Y H:i:s')." UTC+3
        ";
        $message = "Информационное сообщение Obmenka
        ------------------------------------------
        
        Вы получили новую заявку - 
        
        ".$messText."
        
        Сообщение сгенерировано автоматически"; 
        
        $mailheaders = "MIME-Version: 1.0\r\n"; 
        $mailheaders .="Content-Type: text/plain; charset=UTF-8\r\n"; 
        $mailheaders .= "From: ".$from."\r\n";
        $mailheaders .= "X-Mailer: PHP/".phpversion()."\r\n";
        
        $rsf = mail($to,$subject,$message,$mailheaders);

        // $addTaskRes = amoAddTask($post_id);

        // $args = [
        //     'ID' => $post_id,
        //     'meta_input' => [
        //         'amocrm_lead_id' => $addTaskRes['id'],
        //         'amocrm_contact_id' => $addTaskRes['contact_id']
        //     ],
        // ];

        // wp_update_post(wp_slash($args));

        die();
    }
    
    add_action('wp_ajax_create_buy_order', 'create_buy_order');
    add_action('wp_ajax_nopriv_create_buy_order', 'create_buy_order');

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
        while(have_rows('currences', 27)) {
            the_row();
            $allCurr[get_sub_field('code')] = [
                'min' => get_sub_field('min-sum'),
                'reserve' => get_sub_field('reserve'),
                'not-card' => get_sub_field('add')['not-card'],
                'placeholder' => get_sub_field('add')['placeholder']
            ];
        }
        $courses = get_field('courses', 27);

        if (!isset($_COOKIE['get-bank'])) {
            $getBank = $allBanks[$currs['get-curr']][0];
        }
        if (!isset($_COOKIE['send-bank'])) {
            $sendBank = $allBanks[$currs['send-curr']][0];
        }
        $sendPrice = 0;
        $getPrice = 0;
        foreach($courses as $course) {
            if ($course['send']['code'] == $currs['send-curr'] && $course['get']['code'] == $currs['get-curr']) {
                $sendPrice = $course['send']['price'];
                $getPrice = $course['get']['price'];
            }
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
                <?php if ($allBanks[$currs['send-curr']]) : ?>
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
                <?php endif; ?>
                <input type="text" name="send-sum" data-min="<?=$allCurr[$currs['send-curr']]['min']?>" <?=$sendPrice == 1 ? 'data-multi="'.$getPrice.'"' : 'data-decr="'.$sendPrice.'"'?> class="field only-num not-click text_fz16 text_fw500 only-number" placeholder="Введите сумму" required>
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
                <?php if ($allBanks[$currs['get-curr']]) : ?>
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
                <?php endif; ?>
                <input type="text" name="get-sum" pattern="[0-9]" <?=$sendPrice == 1 ? 'data-decr="'.$getPrice.'"' : 'data-multi="'.$sendPrice.'"'?> class="field only-num not-click text_fz16 text_fw500 only-number" placeholder="Введите сумму" required>
            </div>
            <div class="main__form-change-row shadow-block">
                <div class="title text_fz16">
                    <div class="text text_fw700">Детали обмена</div>
                    <div class="change-banks text_fw500">
                        <?=$sendBank['name'] ?: strtoupper($currs['send-curr'])?> на <?=$getBank['name'] ?: strtoupper($currs['get-curr'])?>
                    </div>
                </div>
                <div class="courses text_fz16">
                    <div class="courses-item">
                        <img src="<?=bloginfo('template_url')?>/assets/images/course.svg" alt="">
                        Курс: <b class="text_upper"><?=$sendPrice?> <?=$currs['send-curr']?> = <?=$getPrice?> <?=$currs['get-curr']?></b>
                    </div>
                    <div class="courses-item">
                        <img src="<?=bloginfo('template_url')?>/assets/images/reserve.svg" alt="">
                        Резерв: <b class="text_upper"><?=$allCurr[$currs['get-curr']]['reserve']?></b>
                    </div>
                </div>
                <input type="email" name="send-email" class="field text_fz16" placeholder="Email для связи" required>
                <div class="contacts list field list_target input-change text_fz16">
                    <div class="cards-field input-field text_fz16">
                        <img src="<?=bloginfo('template_url')?>/assets/images/whatsapp.svg" alt="" class="send-card-img list_img">
                        <input class="list_input" type="tel" data-mask="+_ (___) ___-__-__" name="contacts" placeholder="Whats App">
                    </div>
                    <img src="<?=bloginfo('template_url')?>/assets/images/arrow_down.svg" alt="">
                    <div class="list_items">
                        <!-- <div class="list_items-val long" data-img="<?=bloginfo('template_url')?>/assets/images/mail_color.svg" data-value="Email для связи" data-type="email" style="display: none;">
                            <img src="<?=bloginfo('template_url')?>/assets/images/mail_color.svg" alt="">
                            Email для связи
                        </div> -->
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
                            <?php if ($allBanks[$currs['send-curr']]) : ?>
                            <img src="<?=$sendBank['icon']?>" alt="" class="send-card-img">
                            <?php endif; ?>
                            <input type="text" name="send-card" <?=$allCurr[$currs['send-curr']]['not-card'] ? 'class="field" placeholder="'.$allCurr[$currs['send-curr']]['placeholder'].'"' : 'data-mask="____ ____ ____ ____" class="card-validate field not-click" placeholder="Карта '.$sendBank['name'].'"'?> required>
                        </div>
                        <div class="cards-invalid text_fz14"></div>
                    </div>
                    <div class="cards-item">
                        <div class="cards-title text_fz14 text_fw600">Реквизиты получателя:</div>
                        <div class="cards-field text_fz16">
                            <?php if ($allBanks[$currs['get-curr']]) : ?>
                            <img src="<?=$getBank['icon']?>" alt="" class="get-card-img">
                            <?php endif; ?>
                            <input type="text" name="get-card" <?=$allCurr[$currs['get-curr']]['not-card'] ? 'class="field" placeholder="'.$allCurr[$currs['get-curr']]['placeholder'].'"' : 'data-mask="____ ____ ____ ____" class="card-validate field not-click" placeholder="Карта '.$getBank['name'].'"'?> required>
                        </div>
                        <div class="cards-invalid text_fz14"></div>
                    </div>
                </div>
            </div>
            <div class="main__form-change-button change button button_shadow text_white text_fz18 text_fw700 text_center">
                Обменять
            </div>
            <?php elseif ($step == 2 && isset($_COOKIE['status']) && $_COOKIE['status'] == 'check-info') : ?>
            <?php
                $post_id = $_COOKIE['order-post-id'];

                $timeChanger = get_post_timestamp($post_id) + 90 - time();
            ?>
            <div class="main__form-change-order check-status-interval">
                <div class="main__form-change-head">
                    <h2 class="text_fz18 text_fw700">Ваша заявка проверяется, ожидайте</h2>
                </div>
                <div class="main__form-change-row">
                    <div class="timer-container" data-time-count="<?=($timeChanger <= 0 ? 1 : $timeChanger)?>">
                        <svg class="progress-ring" viewBox="0 0 520 520">
                            <circle
                                stroke="#B9B9B9"
                                stroke-width="4"
                                fill="transparent"
                                r="255"
                                cx="260"
                                cy="260" />
                            <circle
                                class="progress-ring-circle"
                                stroke="#5675D3"
                                stroke-width="16"
                                fill="transparent"
                                stroke-linecap="round"
                                r="255"
                                cx="260"
                                cy="260" />
                        </svg>
                        <div class="time-vars">
                            <span class="all-seconds text_fz18 text_fw500">90 с</span>
                            <span class="changer-time" id="timer-circle-seconds"><?=($timeChanger <= 0 ? '01' : ($timeChanger < 10 ? '0'.$timeChanger : $timeChanger))?></span>
                        </div>
                    </div>
                </div>
            </div>
            <?php elseif ($step == 2 && isset($_COOKIE['status']) && $_COOKIE['status'] == 'send-money') : ?>
            <?php
                $post_id = $_COOKIE['order-post-id'];    
            ?>
            <div class="main__form-change-order" data-e-addr="<?=get_option('admin_email')?>" data-date-out="<?=get_post_timestamp($post_id) + 1170?>">
                <div class="main__form-change-head">
                    <h2 class="text_fz18 text_fw700">Ваша заявка создана</h2>
                    <span class="text_fz16"><b>ID:</b> <?php the_field('id-code', $post_id) ?></span>
                </div>
                <div class="main__form-change-row">
                    <div class="title text_fz16 text_fw600">Вы обмениваете <?=get_field('sender_bank', $post_id) ?: strtoupper(get_field('sender_carrency', $post_id)) ?> на <?=get_field('getter_bank', $post_id) ?: strtoupper(get_field('getter_carrency', $post_id)) ?>:</div>
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
                            <div class="number text_white text_fz12">1</div>Перейдите в приложение <?=get_field('sender_bank', $post_id) ?: '' ?>
                        </div>
                        <div class="main__form-change-way">
                            <div class="number text_white text_fz12">2</div>Выполните перевод
                        </div>
                    </div>
                    <div class="cards-item">
                        <div class="cards-title text_fz14 text_fw500">Реквизиты:</div>
                        <div class="field text_fz16 text_fw500 text_upper">
                            <div class="list_info">
                                <?php
                                    $requis = get_field('requisites_default', 27);
                                    foreach(get_field('requisites_by-code', 27) as $oneCard) {
                                        if ($oneCard['code'] == get_field('sender_carrency', $post_id)) {
                                            $requis = $oneCard['card'];
                                            break;
                                        }
                                    }
                                ?>
                                <span class="list_text text_fw500"><?=preg_replace('/\d{4}/', "$0 ",$requis) ?></span>
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
                        <?php
                            $sendVal = 0;
                            $getVal = 0;
                            foreach($courses as $course) {
                                if ($course['send']['code'] == get_field('sender_carrency', $post_id) && $course['get']['code'] == get_field('getter_carrency', $post_id)) {
                                    $sendVal = $course['send']['price'];
                                    $getVal = $course['get']['price'];
                                }
                            }
                        ?>
                        <span>Курс:</span>
                        <span class="text_upper"><?=$sendVal?> <?php the_field('sender_carrency', $post_id) ?> = <?=$getVal?> <?php the_field('getter_carrency', $post_id) ?></span>
                    </div>
                    <div class="info-row text_fz14 text_fz500">
                        <span class="window">Окно оплаты</span>
                        <span class="time text_fw500"><span id="timer-minutes">18</span>:<span id="timer-seconds">00</span></span>
                    </div>
                    <div class="modal__buttons mt">
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
                        Ожидайте перевода <b class="text_upper"><?php the_field('getter_sum', $post_id) ?> <?php the_field('getter_carrency', $post_id) ?></b> на <b><?=get_field('getter_bank', $post_id) ?: strtoupper(get_field('getter_carrency', $post_id))?> <?=preg_replace('/\d{4}/', "$0 ",get_field('getter_card', $post_id)) ?></b>
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
                        Перевод на сумму <b class="text_upper"><?php the_field('getter_sum', $post_id) ?> <?php the_field('getter_carrency', $post_id) ?></b> должен поступить на <b><?=get_field('getter_bank', $post_id) ?: strtoupper(get_field('getter_carrency', $post_id)) ?> <?=preg_replace('/\d{4}/', "$0 ",get_field('getter_card', $post_id)) ?></b>
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