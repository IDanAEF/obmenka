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

    function amoGetTaskStatus($taskId) {
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

        return $result['status_id'];
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
            if (($field['field_id'] == 207349 && $field['values'][0]['value']) || ($field['field_id'] == 886159 && $field['values'][0]['value'])) {
                return $field['values'][0]['value'];
            }
        }
    }

    function amoUpdateTask($elemId, $pipe, $fieldsCheck = []) {
        returnNewAmoToken();

        $arrParams = [];

        $taskElem = [
            "id" => (int)get_field('amocrm_lead_id', $elemId),
            "status_id" => (int)$pipe,
        ];


        $custom_fields = [];

        foreach($fieldsCheck as $id => $check) {
            $custom_fields[] = [
                "field_id" => $id,
                "values" => [
                    [
                        "value" => $check
                    ]
                ]
            ];
        }

        if ($custom_fields) $taskElem["custom_fields_values"] = $custom_fields;

        $arrParams[] = $taskElem;

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

    function amoAddTaskBuy($elemId) {
        returnNewAmoToken();

        $price = (int)get_field('price', $elemId);

        foreach(get_field('courses', 27) as $course) {
            if ($course['send']['code'] == 'rub' && $course['get']['code'] == get_field('curr', $elemId)) {
                $price = (int)(get_field('price', $elemId) * $course['send']['price']);
            }
        }

        $custom_fields = [
            [
                "field_id" => 886159, //ID заявки на сайте
                "values" => [
                    [
                        "value" => ''.$elemId.''
                    ],
                ]
            ],
            [
                "field_id" => 865887, //Ссылка на товар или счет для оплаты
                "values" => [
                    [
                        "value" => get_field('link', $elemId)
                    ],
                ]
            ],
            [
                "field_id" => 865889, //Название товара
                "values" => [
                    [
                        "value" => get_field('name', $elemId)
                    ],
                ]
            ],
            [
                "field_id" => 865893, //Цена товара
                "values" => [
                    [
                        "value" => get_field('price', $elemId)
                    ],
                ]
            ],
            [
                "field_id" => 865999, //Валюта
                "values" => [
                    [
                        "value" => mb_strtoupper(get_field('curr', $elemId))
                    ],
                ]
            ],
            [
                "field_id" => 866001, //Детали заказа
                "values" => [
                    [
                        "value" => get_field('details', $elemId)
                    ],
                ]
            ],
            [
                "field_id" => 866003, //Способ связи
                "values" => [
                    [
                        "value" => get_field('contact', $elemId) . ' ' . get_field('conttype', $elemId)
                    ],
                ]
            ],
            [
                "field_id" => 941401, //Email
                "values" => [
                    [
                        "value" => get_field('email', $elemId)
                    ],
                ]
            ],
            [
                "field_id" => 886641, //Дата создания
                "values" => [
                    [
                        "value" => time()
                    ],
                ]
            ],
        ];

        $contact_fields[] = [
            "field_id" => 209291, //Контакт Telegram/WhatsApp
            "values" => [
                [
                    "value" => get_field('contact', $elemId)
                ]
            ]
        ];

        $arrParams = [
            [
                "name" => get_field('name', $elemId),
                "price" => $price,
                'pipeline_id' => 6835658,
                "_embedded" => [
                    "contacts" => [
                        [
                            "first_name" => get_field('email', $elemId),
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
        wp_enqueue_style( 'obmenka_main_style', get_template_directory_uri() . '/assets/css/style.min.css', false, '7' );
        wp_enqueue_style( 'obmenka_custom_style', get_template_directory_uri() . '/custom.css', false, '7' );

        wp_enqueue_script( 'obmenka_main_scrit', get_template_directory_uri() . '/assets/js/script.js', array(), '28', true );
        wp_enqueue_script( 'obmenka_custom_scrit', get_template_directory_uri() . '/custom.js', array(), '22', true );
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

    function order_pipe__update_func($elemId) {
        $leadPipe = amoGetTaskStatus(get_field('amocrm_lead_id', $elemId));

        $postType = get_post_type($elemId);

        $args = [
            'ID' => $elemId,
        ];

        if ($leadPipe == 142) {
            $fieldUpdate = [204305 => true, 204307 => true];

            $checks = [
                'payd' => 1,
                'finish' => 1
            ];

            if ($postType == 'abroad') {
                $fieldUpdate = [885875 => true];
                $args['post_title'] = get_the_title($elemId) . ' Завершено';

                $checks = [
                    'finish' => 1
                ];
            } else {
                wp_set_post_tags($elemId, 'Завершено');
            }

            $args['meta_input'] = $checks;
            amoUpdateTask($elemId, 142, $fieldUpdate);
        } else if ($leadPipe == 143) {
            $fieldUpdate = [885875 => false, 885879 => true];

            $checks = [
                'finish' => 0,
                'deny' => 1
            ];

            if ($postType == 'abroad') {
                $fieldUpdate = [204305 => false, 204307 => false, 204309 => true];
                $args['post_title'] = get_the_title($elemId) . ' Отклонено';
            } else {
                wp_set_post_tags($elemId, 'Отклонено');
            }

            $args['meta_input'] = $checks;
            amoUpdateTask($elemId, 143, $fieldUpdate);
        }

        wp_update_post(wp_slash($args));
    }

    function order_pipe_update(){
        $lead = $_POST['leads']['status'][0]['id'];
        $leadPipe = $_POST['leads']['status'][0]['status_id'];
        $elemId = amoGetTaskSiteElem($lead);

        $postType = get_post_type($elemId);

        $args = [
            'ID' => $elemId,
        ];

        if ($leadPipe == 142) {
            $checks = [
                'payd' => 1,
                'finish' => 1
            ];

            if ($postType == 'abroad') {
                $args['post_title'] = get_the_title($elemId) . ' Завершено';

                $checks = [
                    'finish' => 1
                ];
            }

            $args['meta_input'] = $checks;
        } else if ($leadPipe == 143) {
            $checks = [
                'finish' => 0,
                'deny' => 1
            ];

            if ($postType == 'abroad') $args['post_title'] = get_the_title($elemId) . ' Отклонено';

            $args['meta_input'] = $checks;
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
        ";
        $message = "Информационное сообщение Obmenka
        ------------------------------------------

        Ссылка для просмотра на сайте: https://topobmenka.com/wp-admin/post.php?post=".$post_id."&action=edit

        ".$messText."

        Сообщение сгенерировано автоматически";

        $mailheaders = "MIME-Version: 1.0\r\n";
        $mailheaders .="Content-Type: text/html; charset=UTF-8\r\n";
        $mailheaders .= "From: ".$from."\r\n";
        $mailheaders .= "X-Mailer: PHP/".phpversion()."\r\n";

        $rsf = mail($to,$subject,$message,$mailheaders);

        if (get_field('send-email', $post_id)) {
            $to = get_field('send-email', $post_id);
            $subject = "Obmenka: Вы отменили заявку/истекло время ожидания оплаты.";
            // $message =
            // "Информационное сообщение Obmenka
            // ------------------------------------------
            //
            // ".$messText."
            //
            // Напишите нашей поддержке, если у вас возникли трудности:
            // info@topobmenka.com
            // https://t.me/TopObmenka
            //
            // Сообщение сгенерировано автоматически";
            $message = "<!DOCTYPE html>
             <html xmlns:v='urn:schemas-microsoft-com:vml' xmlns:o='urn:schemas-microsoft-com:office:office' lang='en'>
               <body>
                 <div style='background-color:rgba(244, 244, 244, 1);padding: 90px;display: flex;justify-content: center;'>
                   <div style='max-width: 582px;width: 100%;display: flex;flex-direction: column;align-items: center;margin:0 auto'>
                   <table cellpadding='0' cellspacing='0' border='0' style='width: 100%;'>
                     <tr>
                       <td style='width: 50%;'>
                         <div style='margin: 20px;'>
                               <a href='https://topobmenka.com/'>
                                  <img alt='logo.png' src='https://app.makemail.ru/content/e0b83b07ecd92e8bf6966d958d01b328.png' style='max-width: 170px;width: 100%;height: 100%;object-fit: contain;' />
                               </a>
                         </div>
                       </td>
                       <td style='width: 50%;'>
                       <div style='margin: 20px;  text-align: right;'>
                          <a style='text-decoration: none;' target='_blank' href='https://t.me/TopObmenka'>
                            <img src='http://topobmenka.com/wp-content/uploads/2023/06/tg.png' alt=''>
                         </a>
                         <a  style='text-decoration: none;'  target='_blank'  href='https://vk.com/obmenka_servis'>
                           <img  src='http://topobmenka.com/wp-content/uploads/2023/06/vk.png' alt=''>
                         </a>
                       </div>
                       </td>
                     </tr>
                   </table>
                       <div style='font-size: 26px;line-height: 36px;color: black;margin-top: 60px;text-align: center;'>Информационное сообщение Obmenka</div>
                       <div style='display: flex;flex-direction: column;text-align: center;margin: 0 auto;margin-top: 20px;color:black;font-size: 18px;'>
                           <p style='margin-top: 8px;margin-bottom: 0px;'>Вы отменили заявку/истекло время ожидания оплаты.</b></p>
                       </div>
                        <div style='display: flex;margin-top: 16px;'>
                             <p style='font-size: 16px;'>Ссылка для просмотра на сайте: https://topobmenka.com/wp-admin/post.php?post=".$post_id."&action=edit</p>
                             <p><b>Сообщение сгенерировано автоматически</b></p>
                       </div>
                   </div>
                 </div>
               </body>
             </html>";

            $mailheaders .= "Reply-To: ".get_option('admin_email')."\r\n";

            $rsf = wp_mail($to,$subject,$message,$mailheaders);
        }

        die();
    }

    add_action('wp_ajax_delete_order', 'delete_order');
    add_action('wp_ajax_nopriv_delete_order', 'delete_order');

    function check_status(){
        order_pipe__update_func($_GET['post_id']);

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
            // $messText = "
            //     Ваша заявка - ".get_field('id-code', $post_id)." - успешно завершена
            //
            //     Перевод на сумму ".get_field('getter_sum', $post_id)." ".mb_strtoupper(get_field('getter_carrency', $post_id))." должен поступить на ".(get_field('getter_bank', $post_id) ?: get_field('getter_carrency', $post_id))." ".(preg_replace('/\d{4}/', "$0 ",get_field('getter_card', $post_id)))."
            //
            //     Спасибо, что выбрали нас!
            // ";
            // $message = "Информационное сообщение Obmenka
            // ------------------------------------------
            //
            // ".$messText."
            //
            // Поддержка:
            // info@topobmenka.com
            // https://t.me/TopObmenka
            //
            // Сообщение сгенерировано автоматически";

            $message = "<!DOCTYPE html>
             <html xmlns:v='urn:schemas-microsoft-com:vml' xmlns:o='urn:schemas-microsoft-com:office:office' lang='en'>
               <body>
                 <div style='background-color:rgba(244, 244, 244, 1);padding: 90px;display: flex;justify-content: center;'>
                   <div style='max-width: 582px;width: 100%;display: flex;flex-direction: column;align-items: center;margin:0 auto'>
                   <table cellpadding='0' cellspacing='0' border='0' style='width: 100%;'>
                     <tr>
                       <td style='width: 50%;'>
                         <div style='margin: 20px;'>
                               <a href='https://topobmenka.com/'>
                                  <img alt='logo.png' src='https://app.makemail.ru/content/e0b83b07ecd92e8bf6966d958d01b328.png' style='max-width: 170px;width: 100%;height: 100%;object-fit: contain;' />
                               </a>
                         </div>
                       </td>
                       <td style='width: 50%;'>
                       <div style='margin: 20px;  text-align: right;'>
                          <a style='text-decoration: none;' target='_blank' href='https://t.me/TopObmenka'>
                            <img src='http://topobmenka.com/wp-content/uploads/2023/06/tg.png' alt=''>
                         </a>
                         <a  style='text-decoration: none;' target='_blank'  href='https://vk.com/obmenka_servis'>
                           <img  src='http://topobmenka.com/wp-content/uploads/2023/06/vk.png' alt=''>
                         </a>
                       </div>
                       </td>
                     </tr>
                   </table>
                       <div style='font-size: 26px;line-height: 36px;color: black;margin-top: 60px;text-align: center;'>Ваша заявка - ".get_field('id-code', $post_id)." - успешно завершена</div>
                       <div style='display: flex;flex-direction: column;text-align: center;margin: 0 auto;margin-top: 20px;color:black;font-size: 18px;'>
                           <p style='margin-top: 8px;margin-bottom: 0px;'>Перевод на сумму ".get_field('getter_sum', $post_id)." ".mb_strtoupper(get_field('getter_carrency', $post_id))." должен поступить на ".(get_field('getter_bank', $post_id) ?: get_field('getter_carrency', $post_id))." ".(preg_replace('/\d{4}/', "$0 ",get_field('getter_card', $post_id)))."</b></p>
                       </div>
                        <div style='display: flex;margin-top: 16px;'>
                             <p style='font-size: 16px;'>Спасибо, что выбрали нас!</p>
                             <p><b>Сообщение сгенерировано автоматически</b></p>
                       </div>
                       <div style='display: flex;margin-top: 16px;'>
                            <p style='font-size: 16px;'>Поддержка:</p>
                            <p>info@topobmenka.com</p>
                            <p>https://t.me/TopObmenka</p>
                      </div>
                   </div>
                 </div>
               </body>
             </html>";

            wp_set_post_tags($post_id, 'Завершено');

            $mailheaders .= "Reply-To: ".get_option('admin_email')."\r\n";
            $rsf = wp_mail($to,$subject,$message,$mailheaders);

            amoUpdateTask($post_id, 142, [204307 => true, 204305 => true]);
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

        amoUpdateTask($post_id, 57528674, [204305 => true]);

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


        $message = "<!DOCTYPE html>
          <html xmlns:v='urn:schemas-microsoft-com:vml' xmlns:o='urn:schemas-microsoft-com:office:office' lang='en'>
            <body>
              <div style='background-color:rgba(244, 244, 244, 1);padding: 90px;display: flex;justify-content: center;'>
                <div style='max-width: 582px;width: 100%;display: flex;flex-direction: column;align-items: center;margin:0 auto'>
                <table cellpadding='0' cellspacing='0' border='0' style='width: 100%;'>
                  <tr>
                    <td style='width: 50%;'>
                      <div style='margin: 20px;'>
                            <a href='https://topobmenka.com/'>
                               <img alt='logo.png' src='https://app.makemail.ru/content/e0b83b07ecd92e8bf6966d958d01b328.png' style='max-width: 170px;width: 100%;height: 100%;object-fit: contain;' />
                            </a>
                      </div>
                    </td>
                    <td style='width: 50%;'>
                    <div style='margin: 20px;  text-align: right;'>
                       <a style='text-decoration: none;' target='_blank' href='https://t.me/TopObmenka'>
                         <img src='http://topobmenka.com/wp-content/uploads/2023/06/tg.png' alt=''>
                      </a>
                      <a  style='text-decoration: none;'  target='_blank'  href='https://vk.com/obmenka_servis'>
                        <img  src='http://topobmenka.com/wp-content/uploads/2023/06/vk.png' alt=''>
                      </a>
                    </div>
                    </td>
                  </tr>
                </table>
                    <h1 style='font-size: 28px;line-height: 36px;color: black;text-align: center;'>Заявка принята<br> в обработку!</h1>
                    <div style='display: flex;flex-direction: column;text-align: center;margin: 0 auto;margin-top: 20px;color:rgba(176, 176, 176, 1);font-size: 18px;'>
                        <p style='margin-top: 8px;margin-bottom: 0px;'>Здравствуйте, <b style='color:black'>".$_GET['send-email']."!</b></p>
                        <p style='margin-top: 8px;margin-bottom: 0px;'>Уважаемый клиент, вы создали заявку: <b style='color:black'>".$id."</b></p>
                        <p style='margin-top: 8px;margin-bottom: 0px;'>Статус вашей заявки: <b style='color:black'>Принята в обработку</b></p>
                        <p style='margin-top: 8px;margin-bottom: 0px;'>Будет обработана в течении: <b style='color:black'>5-15 минут</b></p>
                    </div>
                    <div style='width: 100%;padding: 30px 40px;background-color: white;border-radius: 10px;margin-top: 60px;'>
                      <h3 style='color: black;text-align: center;font-size: 24px;font-weight: 600;'>Информация по заявке:</h3>
                        <div style='margin-top: 24px;display: flex;flex-direction: column;'>

                          <table class='table-full' style='display: flex;margin-top: 16px;border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; background: transparent repeat center center; padding: 0;' width='100%' bgcolor='transparent'>
                            <tbody>
                                <tr style='vertical-align: top; text-align: left; padding: 0;' align='left'>
                                  <td height='%' style='background-color: transparent; width: 50% !important; height: % !important; word-break: break-word; -webkit-hyphens: none; -moz-hyphens: none; hyphens: none; border-collapse: collapse !important; vertical-align: top; text-align: left; color: #717e7f; font-family: Helvetica, Arial, sans-serif; font-weight: normal; font-size: 14px; margin: 0; padding: 0;' width='50%' align='left' bgcolor='transparent' valign='top'>
                                  <p style='color: rgba( 176 , 176 , 176 , 1 ); font-size: 16px; text-align: left; line-height: 1.5; margin: 0 0 10px; padding: 0;' align='left'>Курс обмена:</p>
                                  </td>

                                  <td class='' style='word-break: break-word; -webkit-hyphens: none; -moz-hyphens: none; hyphens: none; border-collapse: collapse !important; vertical-align: top; text-align: left; width: 100%; color: #717e7f; font-family: Helvetica, Arial, sans-serif; font-weight: normal; font-size: 14px; background: transparent repeat center center; margin: 0; padding: 0px 10px;' align='left' bgcolor='transparent' valign='top'>
                                    <p style='color: black; font-weight: bold; font-size: 16px; text-align: left; line-height: 1.5; margin: 0 0 10px; padding: 0;' align='left'>".$sendVal." ".mb_strtoupper($_GET['send-curr'])." = ".$getVal." ".mb_strtoupper($_GET['get-curr'])."</p>
                                  </td>
                                </tr>
                                <tr style='vertical-align: top; text-align: left; padding: 0;' align='left'>
                                  <td height='%' style='background-color: transparent; width: 50% !important; height: % !important; word-break: break-word; -webkit-hyphens: none; -moz-hyphens: none; hyphens: none; border-collapse: collapse !important; vertical-align: top; text-align: left; color: #717e7f; font-family: Helvetica, Arial, sans-serif; font-weight: normal; font-size: 14px; margin: 0; padding: 0;' width='50%' align='left' bgcolor='transparent' valign='top'>
                                  <p style='color: rgba( 176 , 176 , 176 , 1 ); font-size: 16px; text-align: left; line-height: 1.5; margin: 0 0 10px; padding: 0;' align='left'><b>Данные отправителя:</b></p>
                                  </td>
                                </tr>
                                <tr style='vertical-align: top; text-align: left; padding: 0;' align='left'>
                                  <td height='%' style='background-color: transparent; width: 50% !important; height: % !important; word-break: break-word; -webkit-hyphens: none; -moz-hyphens: none; hyphens: none; border-collapse: collapse !important; vertical-align: top; text-align: left; color: #717e7f; font-family: Helvetica, Arial, sans-serif; font-weight: normal; font-size: 14px; margin: 0; padding: 0;' width='50%' align='left' bgcolor='transparent' valign='top'>
                                  <p style='color: rgba( 176 , 176 , 176 , 1 ); font-size: 16px; text-align: left; line-height: 1.5; margin: 0 0 10px; padding: 0;' align='left'>Реквизиты:</p>
                                  </td>

                                  <td class='' style='word-break: break-word; -webkit-hyphens: none; -moz-hyphens: none; hyphens: none; border-collapse: collapse !important; vertical-align: top; text-align: left; width: 100%; color: #717e7f; font-family: Helvetica, Arial, sans-serif; font-weight: normal; font-size: 14px; background: transparent repeat center center; margin: 0; padding: 0px 10px;' align='left' bgcolor='transparent' valign='top'>
                                    <p style='color: black; font-weight: bold; font-size: 16px; text-align: left; line-height: 1.5; margin: 0 0 10px; padding: 0;' align='left'>".$_GET['send-card']."</p>
                                  </td>
                                </tr>
                                <tr style='vertical-align: top; text-align: left; padding: 0;' align='left'>
                                  <td height='%' style='background-color: transparent; width: 50% !important; height: % !important; word-break: break-word; -webkit-hyphens: none; -moz-hyphens: none; hyphens: none; border-collapse: collapse !important; vertical-align: top; text-align: left; color: #717e7f; font-family: Helvetica, Arial, sans-serif; font-weight: normal; font-size: 14px; margin: 0; padding: 0;' width='50%' align='left' bgcolor='transparent' valign='top'>
                                  <p style='color: rgba( 176 , 176 , 176 , 1 ); font-size: 16px; text-align: left; line-height: 1.5; margin: 0 0 10px; padding: 0;' align='left'>Банк:</p>
                                  </td>

                                  <td class='' style='word-break: break-word; -webkit-hyphens: none; -moz-hyphens: none; hyphens: none; border-collapse: collapse !important; vertical-align: top; text-align: left; width: 100%; color: #717e7f; font-family: Helvetica, Arial, sans-serif; font-weight: normal; font-size: 14px; background: transparent repeat center center; margin: 0; padding: 0px 10px;' align='left' bgcolor='transparent' valign='top'>
                                    <p style='color: black; font-weight: bold; font-size: 16px; text-align: left; line-height: 1.5; margin: 0 0 10px; padding: 0;' align='left'>".$_GET['send-bank']."</p>
                                  </td>
                                </tr>
                                <tr style='vertical-align: top; text-align: left; padding: 0;' align='left'>
                                  <td height='%' style='background-color: transparent; width: 50% !important; height: % !important; word-break: break-word; -webkit-hyphens: none; -moz-hyphens: none; hyphens: none; border-collapse: collapse !important; vertical-align: top; text-align: left; color: #717e7f; font-family: Helvetica, Arial, sans-serif; font-weight: normal; font-size: 14px; margin: 0; padding: 0;' width='50%' align='left' bgcolor='transparent' valign='top'>
                                  <p style='color: rgba( 176 , 176 , 176 , 1 ); font-size: 16px; text-align: left; line-height: 1.5; margin: 0 0 10px; padding: 0;' align='left'>Сумма:</p>
                                  </td>

                                  <td class='' style='word-break: break-word; -webkit-hyphens: none; -moz-hyphens: none; hyphens: none; border-collapse: collapse !important; vertical-align: top; text-align: left; width: 100%; color: #717e7f; font-family: Helvetica, Arial, sans-serif; font-weight: normal; font-size: 14px; background: transparent repeat center center; margin: 0; padding: 0px 10px;' align='left' bgcolor='transparent' valign='top'>
                                    <p style='color: black; font-weight: bold; font-size: 16px; text-align: left; line-height: 1.5; margin: 0 0 10px; padding: 0;' align='left'>".$_GET['send-sum']."</p>
                                  </td>
                                </tr>
                                <tr style='vertical-align: top; text-align: left; padding: 0;' align='left'>
                                  <td height='%' style='background-color: transparent; width: 50% !important; height: % !important; word-break: break-word; -webkit-hyphens: none; -moz-hyphens: none; hyphens: none; border-collapse: collapse !important; vertical-align: top; text-align: left; color: #717e7f; font-family: Helvetica, Arial, sans-serif; font-weight: normal; font-size: 14px; margin: 0; padding: 0;' width='50%' align='left' bgcolor='transparent' valign='top'>
                                  <p style='color: rgba( 176 , 176 , 176 , 1 ); font-size: 16px; text-align: left; line-height: 1.5; margin: 0 0 10px; padding: 0;' align='left'>Валюта:</p>
                                  </td>

                                  <td class='' style='word-break: break-word; -webkit-hyphens: none; -moz-hyphens: none; hyphens: none; border-collapse: collapse !important; vertical-align: top; text-align: left; width: 100%; color: #717e7f; font-family: Helvetica, Arial, sans-serif; font-weight: normal; font-size: 14px; background: transparent repeat center center; margin: 0; padding: 0px 10px;' align='left' bgcolor='transparent' valign='top'>
                                    <p style='color: black; font-weight: bold; font-size: 16px; text-align: left; line-height: 1.5; margin: 0 0 10px; padding: 0;' align='left'>".mb_strtoupper($_GET['send-curr'])."</p>
                                  </td>
                                </tr>
                                <tr style='vertical-align: top; text-align: left; padding: 0;' align='left'>
                                  <td height='%' style='background-color: transparent; width: 50% !important; height: % !important; word-break: break-word; -webkit-hyphens: none; -moz-hyphens: none; hyphens: none; border-collapse: collapse !important; vertical-align: top; text-align: left; color: #717e7f; font-family: Helvetica, Arial, sans-serif; font-weight: normal; font-size: 14px; margin: 0; padding: 0;' width='50%' align='left' bgcolor='transparent' valign='top'>
                                  <p style='color: rgba( 176 , 176 , 176 , 1 ); font-size: 16px; text-align: left; line-height: 1.5; margin: 0 0 10px; padding: 0;' align='left'>Дата формирования:</p>
                                  </td>

                                  <td class='' style='word-break: break-word; -webkit-hyphens: none; -moz-hyphens: none; hyphens: none; border-collapse: collapse !important; vertical-align: top; text-align: left; width: 100%; color: #717e7f; font-family: Helvetica, Arial, sans-serif; font-weight: normal; font-size: 14px; background: transparent repeat center center; margin: 0; padding: 0px 10px;' align='left' bgcolor='transparent' valign='top'>
                                    <p style='color: black; font-weight: bold; font-size: 16px; text-align: left; line-height: 1.5; margin: 0 0 10px; padding: 0;' align='left'>".date('d-m-Y H:i:s')." UTC+3</p>
                                  </td>
                                </tr>



                                <tr style='vertical-align: top; text-align: left; padding: 0;' align='left'>
                                  <td height='%' style='background-color: transparent; width: 50% !important; height: % !important; word-break: break-word; -webkit-hyphens: none; -moz-hyphens: none; hyphens: none; border-collapse: collapse !important; vertical-align: top; text-align: left; color: #717e7f; font-family: Helvetica, Arial, sans-serif; font-weight: normal; font-size: 14px; margin: 0; padding: 0;' width='50%' align='left' bgcolor='transparent' valign='top'>
                                  <p style='color: rgba( 176 , 176 , 176 , 1 ); font-size: 16px; text-align: left; line-height: 1.5; margin: 0 0 10px; padding: 0;' align='left'><b> Данные получателя:</b></p>
                                  </td>
                                </tr>
                                <tr style='vertical-align: top; text-align: left; padding: 0;' align='left'>
                                  <td height='%' style='background-color: transparent; width: 50% !important; height: % !important; word-break: break-word; -webkit-hyphens: none; -moz-hyphens: none; hyphens: none; border-collapse: collapse !important; vertical-align: top; text-align: left; color: #717e7f; font-family: Helvetica, Arial, sans-serif; font-weight: normal; font-size: 14px; margin: 0; padding: 0;' width='50%' align='left' bgcolor='transparent' valign='top'>
                                  <p style='color: rgba( 176 , 176 , 176 , 1 ); font-size: 16px; text-align: left; line-height: 1.5; margin: 0 0 10px; padding: 0;' align='left'>Реквизиты:</p>
                                  </td>

                                  <td class='' style='word-break: break-word; -webkit-hyphens: none; -moz-hyphens: none; hyphens: none; border-collapse: collapse !important; vertical-align: top; text-align: left; width: 100%; color: #717e7f; font-family: Helvetica, Arial, sans-serif; font-weight: normal; font-size: 14px; background: transparent repeat center center; margin: 0; padding: 0px 10px;' align='left' bgcolor='transparent' valign='top'>
                                    <p style='color: black; font-weight: bold; font-size: 16px; text-align: left; line-height: 1.5; margin: 0 0 10px; padding: 0;' align='left'>".$_GET['get-card']."</p>
                                  </td>
                                </tr>
                                <tr style='vertical-align: top; text-align: left; padding: 0;' align='left'>
                                  <td height='%' style='background-color: transparent; width: 50% !important; height: % !important; word-break: break-word; -webkit-hyphens: none; -moz-hyphens: none; hyphens: none; border-collapse: collapse !important; vertical-align: top; text-align: left; color: #717e7f; font-family: Helvetica, Arial, sans-serif; font-weight: normal; font-size: 14px; margin: 0; padding: 0;' width='50%' align='left' bgcolor='transparent' valign='top'>
                                  <p style='color: rgba( 176 , 176 , 176 , 1 ); font-size: 16px; text-align: left; line-height: 1.5; margin: 0 0 10px; padding: 0;' align='left'>Банк:</p>
                                  </td>

                                  <td class='' style='word-break: break-word; -webkit-hyphens: none; -moz-hyphens: none; hyphens: none; border-collapse: collapse !important; vertical-align: top; text-align: left; width: 100%; color: #717e7f; font-family: Helvetica, Arial, sans-serif; font-weight: normal; font-size: 14px; background: transparent repeat center center; margin: 0; padding: 0px 10px;' align='left' bgcolor='transparent' valign='top'>
                                    <p style='color: black; font-weight: bold; font-size: 16px; text-align: left; line-height: 1.5; margin: 0 0 10px; padding: 0;' align='left'>".$_GET['get-bank']."</p>
                                  </td>
                                </tr>
                                <tr style='vertical-align: top; text-align: left; padding: 0;' align='left'>
                                  <td height='%' style='background-color: transparent; width: 50% !important; height: % !important; word-break: break-word; -webkit-hyphens: none; -moz-hyphens: none; hyphens: none; border-collapse: collapse !important; vertical-align: top; text-align: left; color: #717e7f; font-family: Helvetica, Arial, sans-serif; font-weight: normal; font-size: 14px; margin: 0; padding: 0;' width='50%' align='left' bgcolor='transparent' valign='top'>
                                  <p style='color: rgba( 176 , 176 , 176 , 1 ); font-size: 16px; text-align: left; line-height: 1.5; margin: 0 0 10px; padding: 0;' align='left'>Сумма:</p>
                                  </td>

                                  <td class='' style='word-break: break-word; -webkit-hyphens: none; -moz-hyphens: none; hyphens: none; border-collapse: collapse !important; vertical-align: top; text-align: left; width: 100%; color: #717e7f; font-family: Helvetica, Arial, sans-serif; font-weight: normal; font-size: 14px; background: transparent repeat center center; margin: 0; padding: 0px 10px;' align='left' bgcolor='transparent' valign='top'>
                                    <p style='color: black; font-weight: bold; font-size: 16px; text-align: left; line-height: 1.5; margin: 0 0 10px; padding: 0;' align='left'>".$_GET['get-sum']."</p>
                                  </td>
                                </tr>
                                <tr style='vertical-align: top; text-align: left; padding: 0;' align='left'>
                                  <td height='%' style='background-color: transparent; width: 50% !important; height: % !important; word-break: break-word; -webkit-hyphens: none; -moz-hyphens: none; hyphens: none; border-collapse: collapse !important; vertical-align: top; text-align: left; color: #717e7f; font-family: Helvetica, Arial, sans-serif; font-weight: normal; font-size: 14px; margin: 0; padding: 0;' width='50%' align='left' bgcolor='transparent' valign='top'>
                                  <p style='color: rgba( 176 , 176 , 176 , 1 ); font-size: 16px; text-align: left; line-height: 1.5; margin: 0 0 10px; padding: 0;' align='left'>Валюта:</p>
                                  </td>

                                  <td class='' style='word-break: break-word; -webkit-hyphens: none; -moz-hyphens: none; hyphens: none; border-collapse: collapse !important; vertical-align: top; text-align: left; width: 100%; color: #717e7f; font-family: Helvetica, Arial, sans-serif; font-weight: normal; font-size: 14px; background: transparent repeat center center; margin: 0; padding: 0px 10px;' align='left' bgcolor='transparent' valign='top'>
                                    <p style='color: black; font-weight: bold; font-size: 16px; text-align: left; line-height: 1.5; margin: 0 0 10px; padding: 0;' align='left'>".mb_strtoupper($_GET['get-curr'])."</p>
                                  </td>
                                </tr>
                                <tr style='vertical-align: top; text-align: left; padding: 0;' align='left'>
                                  <td height='%' style='background-color: transparent; width: 50% !important; height: % !important; word-break: break-word; -webkit-hyphens: none; -moz-hyphens: none; hyphens: none; border-collapse: collapse !important; vertical-align: top; text-align: left; color: #717e7f; font-family: Helvetica, Arial, sans-serif; font-weight: normal; font-size: 14px; margin: 0; padding: 0;' width='50%' align='left' bgcolor='transparent' valign='top'>
                                  <p style='color: rgba( 176 , 176 , 176 , 1 ); font-size: 16px; text-align: left; line-height: 1.5; margin: 0 0 10px; padding: 0;' align='left'>Дата формирования:</p>
                                  </td>

                                  <td class='' style='word-break: break-word; -webkit-hyphens: none; -moz-hyphens: none; hyphens: none; border-collapse: collapse !important; vertical-align: top; text-align: left; width: 100%; color: #717e7f; font-family: Helvetica, Arial, sans-serif; font-weight: normal; font-size: 14px; background: transparent repeat center center; margin: 0; padding: 0px 10px;' align='left' bgcolor='transparent' valign='top'>
                                    <p style='color: black; font-weight: bold; font-size: 16px; text-align: left; line-height: 1.5; margin: 0 0 10px; padding: 0;' align='left'>".date('d-m-Y H:i:s')." UTC+3</p>
                                  </td>
                                </tr>

                            </tbody>
                          </table>

                        </div>
                        <div style='display: flex;margin-top: 16px;'>
                          <p style='font-size: 16px;'>Ссылка для просмотра на сайте: https://topobmenka.com/wp-admin/post.php?post=".$post_id."&action=edit</p>
                          <p><b>Сообщение сгенерировано автоматически</b></p>
                        </div>
                      </div>
                    </div>
                  </div>
            </body>
          </html>";


        // $messText = "
        //     ID заявки: ".$id."
        //
        //     E-mail: ".$_GET['send-email']."
        //     ".($_GET['contacts'] ? 'Контакты: '.$_GET['contacts'].' - '.$contType : '')."
        //
        //     Курс обмена: ".$sendVal." ".mb_strtoupper($_GET['send-curr'])." = ".$getVal." ".mb_strtoupper($_GET['get-curr'])."
        //
        //     Данные отправителя
        //     Реквизиты: ".$_GET['send-card']."
        //     Банк: ".$_GET['send-bank']."
        //     Сумма: ".$_GET['send-sum']."
        //     Валюта: ".mb_strtoupper($_GET['send-curr'])."
        //
        //     Данные получателя
        //     Реквизиты: ".$_GET['get-card']."
        //     Банк: ".$_GET['get-bank']."
        //     Сумма: ".$_GET['get-sum']."
        //     Валюта: ".mb_strtoupper($_GET['get-curr'])."
        //
        //     Дата формирования: ".date('d-m-Y H:i:s')." UTC+3
        // ";
        // $message = "Информационное сообщение Obmenka
        // ------------------------------------------
        //
        // Вы получили новую заявку -
        //
        // Ссылка для просмотра на сайте: https://topobmenka.com/wp-admin/post.php?post=".$post_id."&action=edit
        //
        // ".$messText."
        //
        // Сообщение сгенерировано автоматически";

        $mailheaders = "MIME-Version: 1.0" . "\r\n";
        $mailheaders .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $mailheaders .= "From: ".$from."\r\n";
        $mailheaders .= "X-Mailer: PHP/".phpversion()."\r\n";

        $rsf = mail($to,$subject,$message,$mailheaders);

        if ($_GET['send-email']) {
            $to = $_GET['send-email'];
            $subject = "Obmenka: Вами была успешно создана заявка на обмен.";
            // $message = "Информационное сообщение Obmenka
            // ------------------------------------------
            //
            // Детали вашей заявки -
            //
            // ".$messText."
            //
            // Поддержка:
            // info@topobmenka.com
            // https://t.me/TopObmenka
            //
            // Сообщение сгенерировано автоматически";
            //
            $mailheaders .= "Reply-To: ".get_option('admin_email')."\r\n";

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
                'email' => $_POST['feedmail'],
                'contact' => $_POST['feedcontact'],
                'conttype' => $_POST['feedconttype']
            ],
        ];

        $post_id = wp_insert_post($post_data);

        $to = get_option('admin_email');
        $from = get_option('admin_email');
        $subject = "Obmenka: Новая заявка на покупку за рубежом";

        $message = "<!DOCTYPE html>
              <html xmlns:v='urn:schemas-microsoft-com:vml' xmlns:o='urn:schemas-microsoft-com:office:office' lang='en'>
                <body>
                  <div style='background-color:rgba(244, 244, 244, 1);padding: 90px;display: flex;justify-content: center;'>
                    <div style='max-width: 582px;width: 100%;display: flex;flex-direction: column;align-items: center;margin:0 auto'>
                    <table cellpadding='0' cellspacing='0' border='0' style='width: 100%;'>
                      <tr>
                        <td style='width: 50%;'>
                          <div style='margin: 20px;'>
                                <a href='https://topobmenka.com/'>
                                   <img alt='logo.png' src='https://app.makemail.ru/content/e0b83b07ecd92e8bf6966d958d01b328.png' style='max-width: 170px;width: 100%;height: 100%;object-fit: contain;' />
                                </a>
                          </div>
                        </td>
                        <td style='width: 50%;'>
                        <div style='margin: 20px;  text-align: right;'>
                           <a style='text-decoration: none;' target='_blank' href='https://t.me/TopObmenka'>
                             <img src='http://topobmenka.com/wp-content/uploads/2023/06/tg.png' alt=''>
                          </a>
                          <a  style='text-decoration: none;'  target='_blank'  href='https://vk.com/obmenka_servis'>
                            <img  src='http://topobmenka.com/wp-content/uploads/2023/06/vk.png' alt=''>
                          </a>
                        </div>
                        </td>
                      </tr>
                    </table>
                        <h1 style='font-size: 28px;line-height: 36px;color: black;text-align: center;'>Вы получили новую заявку</h1>
                        <div style='display: flex;flex-direction: column;text-align: center;margin: 0 auto;margin-top: 20px;color:rgba(176, 176, 176, 1);font-size: 18px;'>
                            <p style='margin-top: 8px;margin-bottom: 0px;'> Ссылка для просмотра на сайте: <b style='color:black'>https://topobmenka.com/wp-admin/post.php?post=".$post_id."&action=edit</b></p>
                        </div>
                        <div style='width: 100%;padding: 30px 40px;background-color: white;border-radius: 10px;margin-top: 60px;'>
                          <h3 style='color: black;text-align: center;font-size: 24px;font-weight: 600;'>Информация по заявке:</h3>
                            <div style='margin-top: 24px;display: flex;flex-direction: column;'>

                              <table class='table-full' style='display: flex;margin-top: 16px;border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; background: transparent repeat center center; padding: 0;' width='100%' bgcolor='transparent'>
                                <tbody>
                                    <tr style='vertical-align: top; text-align: left; padding: 0;' align='left'>
                                      <td height='%' style='background-color: transparent; width: 50% !important; height: % !important; word-break: break-word; -webkit-hyphens: none; -moz-hyphens: none; hyphens: none; border-collapse: collapse !important; vertical-align: top; text-align: left; color: #717e7f; font-family: Helvetica, Arial, sans-serif; font-weight: normal; font-size: 14px; margin: 0; padding: 0;' width='50%' align='left' bgcolor='transparent' valign='top'>
                                      <p style='color: rgba( 176 , 176 , 176 , 1 ); font-size: 16px; text-align: left; line-height: 1.5; margin: 0 0 10px; padding: 0;' align='left'>Ссылка на товар или счет для оплаты:</p>
                                      </td>

                                      <td class='' style='word-break: break-word; -webkit-hyphens: none; -moz-hyphens: none; hyphens: none; border-collapse: collapse !important; vertical-align: top; text-align: left; width: 100%; color: #717e7f; font-family: Helvetica, Arial, sans-serif; font-weight: normal; font-size: 14px; background: transparent repeat center center; margin: 0; padding: 0px 10px;' align='left' bgcolor='transparent' valign='top'>
                                        <p style='color: black; font-weight: bold; font-size: 16px; text-align: left; line-height: 1.5; margin: 0 0 10px; padding: 0;' align='left'>".$_POST['feedlink']."</p>
                                      </td>
                                    </tr>
                                    <tr style='vertical-align: top; text-align: left; padding: 0;' align='left'>

                                      <td height='%' style='background-color: transparent; width: 50% !important; height: % !important; word-break: break-word; -webkit-hyphens: none; -moz-hyphens: none; hyphens: none; border-collapse: collapse !important; vertical-align: top; text-align: left; color: #717e7f; font-family: Helvetica, Arial, sans-serif; font-weight: normal; font-size: 14px; margin: 0; padding: 0;' width='50%' align='left' bgcolor='transparent' valign='top'>
                                      <p style='color: rgba( 176 , 176 , 176 , 1 ); font-size: 16px; text-align: left; line-height: 1.5; margin: 0 0 10px; padding: 0;' align='left'>Название товара:</p>
                                      </td>

                                      <td class='' style='word-break: break-word; -webkit-hyphens: none; -moz-hyphens: none; hyphens: none; border-collapse: collapse !important; vertical-align: top; text-align: left; width: 100%; color: #717e7f; font-family: Helvetica, Arial, sans-serif; font-weight: normal; font-size: 14px; background: transparent repeat center center; margin: 0; padding: 0px 10px;' align='left' bgcolor='transparent' valign='top'>
                                        <p style='color: black; font-weight: bold; font-size: 16px; text-align: left; line-height: 1.5; margin: 0 0 10px; padding: 0;' align='left'>".$_POST['feedproduct']."</p>
                                      </td>
                                    </tr>
                                    <tr style='vertical-align: top; text-align: left; padding: 0;' align='left'>
                                      <td height='%' style='background-color: transparent; width: 50% !important; height: % !important; word-break: break-word; -webkit-hyphens: none; -moz-hyphens: none; hyphens: none; border-collapse: collapse !important; vertical-align: top; text-align: left; color: #717e7f; font-family: Helvetica, Arial, sans-serif; font-weight: normal; font-size: 14px; margin: 0; padding: 0;' width='50%' align='left' bgcolor='transparent' valign='top'>
                                      <p style='color: rgba( 176 , 176 , 176 , 1 ); font-size: 16px; text-align: left; line-height: 1.5; margin: 0 0 10px; padding: 0;' align='left'>Цена товара:</p>
                                      </td>

                                      <td class='' style='word-break: break-word; -webkit-hyphens: none; -moz-hyphens: none; hyphens: none; border-collapse: collapse !important; vertical-align: top; text-align: left; width: 100%; color: #717e7f; font-family: Helvetica, Arial, sans-serif; font-weight: normal; font-size: 14px; background: transparent repeat center center; margin: 0; padding: 0px 10px;' align='left' bgcolor='transparent' valign='top'>
                                        <p style='color: black; font-weight: bold; font-size: 16px; text-align: left; line-height: 1.5; margin: 0 0 10px; padding: 0;' align='left'>".$_POST['feedprice']."</p>
                                      </td>
                                    </tr>
                                    <tr style='vertical-align: top; text-align: left; padding: 0;' align='left'>
                                      <td height='%' style='background-color: transparent; width: 50% !important; height: % !important; word-break: break-word; -webkit-hyphens: none; -moz-hyphens: none; hyphens: none; border-collapse: collapse !important; vertical-align: top; text-align: left; color: #717e7f; font-family: Helvetica, Arial, sans-serif; font-weight: normal; font-size: 14px; margin: 0; padding: 0;' width='50%' align='left' bgcolor='transparent' valign='top'>
                                      <p style='color: rgba( 176 , 176 , 176 , 1 ); font-size: 16px; text-align: left; line-height: 1.5; margin: 0 0 10px; padding: 0;' align='left'>Детали заказа:</p>
                                      </td>

                                      <td class='' style='word-break: break-word; -webkit-hyphens: none; -moz-hyphens: none; hyphens: none; border-collapse: collapse !important; vertical-align: top; text-align: left; width: 100%; color: #717e7f; font-family: Helvetica, Arial, sans-serif; font-weight: normal; font-size: 14px; background: transparent repeat center center; margin: 0; padding: 0px 10px;' align='left' bgcolor='transparent' valign='top'>
                                        <p style='color: black; font-weight: bold; font-size: 16px; text-align: left; line-height: 1.5; margin: 0 0 10px; padding: 0;' align='left'>".$_POST['feedmess']."</p>
                                      </td>
                                    </tr>
                                    <tr style='vertical-align: top; text-align: left; padding: 0;' align='left'>
                                      <td height='%' style='background-color: transparent; width: 50% !important; height: % !important; word-break: break-word; -webkit-hyphens: none; -moz-hyphens: none; hyphens: none; border-collapse: collapse !important; vertical-align: top; text-align: left; color: #717e7f; font-family: Helvetica, Arial, sans-serif; font-weight: normal; font-size: 14px; margin: 0; padding: 0;' width='50%' align='left' bgcolor='transparent' valign='top'>
                                      <p style='color: rgba( 176 , 176 , 176 , 1 ); font-size: 16px; text-align: left; line-height: 1.5; margin: 0 0 10px; padding: 0;' align='left'>E-mail:</p>
                                      </td>

                                      <td class='' style='word-break: break-word; -webkit-hyphens: none; -moz-hyphens: none; hyphens: none; border-collapse: collapse !important; vertical-align: top; text-align: left; width: 100%; color: #717e7f; font-family: Helvetica, Arial, sans-serif; font-weight: normal; font-size: 14px; background: transparent repeat center center; margin: 0; padding: 0px 10px;' align='left' bgcolor='transparent' valign='top'>
                                        <p style='color: black; font-weight: bold; font-size: 16px; text-align: left; line-height: 1.5; margin: 0 0 10px; padding: 0;' align='left'>".$_POST['feedmail']."</p>
                                      </td>
                                    </tr>

                                    <tr style='vertical-align: top; text-align: left; padding: 0;' align='left'>
                                      <td height='%' style='background-color: transparent; width: 50% !important; height: % !important; word-break: break-word; -webkit-hyphens: none; -moz-hyphens: none; hyphens: none; border-collapse: collapse !important; vertical-align: top; text-align: left; color: #717e7f; font-family: Helvetica, Arial, sans-serif; font-weight: normal; font-size: 14px; margin: 0; padding: 0;' width='50%' align='left' bgcolor='transparent' valign='top'>
                                      <p style='color: rgba( 176 , 176 , 176 , 1 ); font-size: 16px; text-align: left; line-height: 1.5; margin: 0 0 10px; padding: 0;' align='left'>Способ связи:</p>
                                      </td>

                                      <td class='' style='word-break: break-word; -webkit-hyphens: none; -moz-hyphens: none; hyphens: none; border-collapse: collapse !important; vertical-align: top; text-align: left; width: 100%; color: #717e7f; font-family: Helvetica, Arial, sans-serif; font-weight: normal; font-size: 14px; background: transparent repeat center center; margin: 0; padding: 0px 10px;' align='left' bgcolor='transparent' valign='top'>
                                        <p style='color: black; font-weight: bold; font-size: 16px; text-align: left; line-height: 1.5; margin: 0 0 10px; padding: 0;' align='left'>".$_POST['feedcontact']." ".$_POST['feedconttype']."</p>
                                      </td>
                                    </tr>

                                    <tr style='vertical-align: top; text-align: left; padding: 0;' align='left'>
                                      <td height='%' style='background-color: transparent; width: 50% !important; height: % !important; word-break: break-word; -webkit-hyphens: none; -moz-hyphens: none; hyphens: none; border-collapse: collapse !important; vertical-align: top; text-align: left; color: #717e7f; font-family: Helvetica, Arial, sans-serif; font-weight: normal; font-size: 14px; margin: 0; padding: 0;' width='50%' align='left' bgcolor='transparent' valign='top'>
                                      <p style='color: rgba( 176 , 176 , 176 , 1 ); font-size: 16px; text-align: left; line-height: 1.5; margin: 0 0 10px; padding: 0;' align='left'>Дата формирования:</p>
                                      </td>

                                      <td class='' style='word-break: break-word; -webkit-hyphens: none; -moz-hyphens: none; hyphens: none; border-collapse: collapse !important; vertical-align: top; text-align: left; width: 100%; color: #717e7f; font-family: Helvetica, Arial, sans-serif; font-weight: normal; font-size: 14px; background: transparent repeat center center; margin: 0; padding: 0px 10px;' align='left' bgcolor='transparent' valign='top'>
                                        <p style='color: black; font-weight: bold; font-size: 16px; text-align: left; line-height: 1.5; margin: 0 0 10px; padding: 0;' align='left'>".date('d-m-Y H:i:s')." UTC+3</p>
                                      </td>
                                    </tr>

                                </tbody>
                              </table>

                            </div>
                            <div style='display: flex;margin-top: 16px;'>
                              <p><b>Сообщение сгенерировано автоматически</b></p>
                            </div>
                            <div style='display: flex;margin-top: 16px;'>
                              <span>Поддержка:</span><br>
                              <span>info@topobmenka.com</span><br>
                              <span>https://t.me/TopObmenka</span>
                            </div>
                          </div>
                        </div>
                      </div>
                </body>
              </html>";


        // $messText = "
        //     Ссылка на товар или счет для оплаты: ".$_POST['feedlink']."
        //     Название товара: ".$_POST['feedproduct']."
        //     Цена товара: ".$_POST['feedprice']."
        //     Валюта: ".mb_strtoupper($_POST['feedcurr'])."
        //     Детали заказа:
        //     ".$_POST['feedmess']."
        //
        //     E-mail: ".$_POST['feedmail']."
        //     Способ связи: ".$_POST['feedcontact']." ".$_POST['feedconttype']."
        //
        //     Дата формирования: ".date('d-m-Y H:i:s')." UTC+3
        // ";
        // $message = "Информационное сообщение Obmenka
        // ------------------------------------------
        //
        // Вы получили новую заявку -
        //
        // Ссылка для просмотра на сайте: https://topobmenka.com/wp-admin/post.php?post=".$post_id."&action=edit
        //
        // ".$messText."
        //
        // Сообщение сгенерировано автоматически";

        $mailheaders = "MIME-Version: 1.0\r\n";
        $mailheaders .="Content-Type: text/html; charset=UTF-8\r\n";
        $mailheaders .= "From: ".$from."\r\n";
        $mailheaders .= "X-Mailer: PHP/".phpversion()."\r\n";

        $rsf = mail($to,$subject,$message,$mailheaders);

        if ($_POST['feedmail']) {
            $to = $_POST['feedmail'];
            $subject = "Obmenka: Вы оставили заявку на покупку за рубежом";
            // $message = "Информационное сообщение Obmenka
            // ------------------------------------------
            //
            // Данные заявки -
            //
            // ".$messText."
            //
            // Поддержка:
            // info@topobmenka.com
            // https://t.me/TopObmenka
            //
            // Сообщение сгенерировано автоматически";

            $rsf = wp_mail($to,$subject,$message,$mailheaders);
        }

        $addTaskRes = amoAddTaskBuy($post_id);

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

    if (is_admin() && strpos($_SERVER['REQUEST_URI'], 'edit.php') !== false) {
        $my_posts = get_posts( array(
            'numberposts' => 5,
            'category'    => 0,
            'orderby'     => 'date',
            'order'       => 'DESC',
            'include'     => array(),
            'exclude'     => array(),
            'meta_key'    => '',
            'meta_value'  =>'',
            'post_type'   => 'post',
            'suppress_filters' => true,
        ) );

        global $post;
        $iter = 0;

        foreach( $my_posts as $post ){
            setup_postdata( $post );
            $iter++;

            if (!get_field('finish') && !get_field('deny')) {
                order_pipe__update_func($post->ID);
            }

            if ($iter > 20) break;
        }

        wp_reset_postdata();

        $my_posts = get_posts( array(
            'numberposts' => 5,
            'category'    => 0,
            'orderby'     => 'date',
            'order'       => 'DESC',
            'include'     => array(),
            'exclude'     => array(),
            'meta_key'    => '',
            'meta_value'  =>'',
            'post_type'   => 'abroad',
            'suppress_filters' => true,
        ) );

        global $post;
        $iter = 0;

        foreach( $my_posts as $post ){
            setup_postdata( $post );
            $iter++;

            if (!get_field('finish') && !get_field('deny')) {
                order_pipe__update_func($post->ID);
            }

            if ($iter > 20) break;
        }

        wp_reset_postdata();
    }
?>
