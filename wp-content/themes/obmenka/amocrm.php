<?php
    // $subdomain = 'kostenichss';
    // $link = 'https://' . $subdomain . '.amocrm.ru/oauth2/access_token';
    
    // $data = [
    //     'client_id' => '5d940a1d-8edb-48c9-9383-abe34fd25d0e',
    //     'client_secret' => '4xpE9UTOW0WTTtRNwnpb6Md2pqlifknDFCO14hNXgm19z37iD0TlaRg6InSTLdyl',
    //     'grant_type' => 'authorization_code',
    //     'code' => 'def5020018ddfa90925309e8642f7d2db6f95b0f09205fb003c8205ba5c127cd180fbc3627cc5fa889723710ee898a2af8a4ecce7269d6e598a2345debfb49c9517f535867036ba705ea3c80e147c153503f6d6fee8ce998352982d45f4da2e99383f73087dd9a94e61ee9c6c7c4f36652fc6782bd5e0c11c5c83eb2f299430b4f4cb092c54193c9bcf638b1ca723771913c6fc353366b54d664ab9dadc834a0aad8d1116b601e4252fe28668c283cb16a687053b50dcc55ff78d639b6c45d34f16607b12320a178b312a87c16c734d912e463a9188daf36e5ed940e72a3eb120013356bd5bcc3c858cb43984b7b7473dc0b63df7840d963cb77e7e24c7ba135bbb94dcbcce2ef5c50e36e6efa076782ae120d25ec85b5a8f193be6e28d64a48f25a6ba1bf6c1c1f4f6093087607a41b99b64965ed4a1ee518e7d58b94944f2e3277570a626ed58e53662f4ce73bae4065114c5428861da0b8b3d0d65bf16ceb13633302137b379b3a587337470204a585d4dac50e227cbdc6c07156dcc17a15718b981d487b22a6bab7465a65a0a36fe208953969ad3f7c38a260bb6a87a6801e19ff282395b99c892d1f883b5f07d387e56016755b5dd0d8f793cde8545a61c0b0b25b5c017eb1c3d2387df6caa98ca096e1ee661a30f326ae17b616923edd994dcadd2f46',
    //     'redirect_uri' => 'https://topobmenka.com',
    // ];
    
    
    // $curl = curl_init();
    // curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
    // curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-oAuth-client/1.0');
    // curl_setopt($curl,CURLOPT_URL, $link);
    // curl_setopt($curl,CURLOPT_HTTPHEADER,['Content-Type:application/json']);
    // curl_setopt($curl,CURLOPT_HEADER, false);
    // curl_setopt($curl,CURLOPT_CUSTOMREQUEST, 'POST');
    // curl_setopt($curl,CURLOPT_POSTFIELDS, json_encode($data));
    // curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, 1);
    // curl_setopt($curl,CURLOPT_SSL_VERIFYHOST, 2);
    // $out = curl_exec($curl);
    // $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    // curl_close($curl);
    
    // $code = (int)$code;
    
    // $errors = [
    //     400 => 'Bad request',
    //     401 => 'Unauthorized',
    //     403 => 'Forbidden',
    //     404 => 'Not found',
    //     500 => 'Internal server error',
    //     502 => 'Bad gateway',
    //     503 => 'Service unavailable',
    // ];
    
    // try
    // {
    //     if ($code < 200 || $code > 204) {
    //         throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undefined error', $code);
    //     }
    // }
    // catch(\Exception $e)
    // {
    //     die('Ошибка: ' . $e->getMessage() . PHP_EOL . 'Код ошибки: ' . $e->getCode());
    // }
    
    
    // $response = json_decode($out, true);
    
    // $arrParamsAmo = [
    //     "access_token" => $response['access_token'],
    //     "refresh_token" => $response['refresh_token'],
    //     "token_type" => $response['token_type'],
    //     "expires_in" => $response['expires_in'],
    //     "endTokenTime" => $response['expires_in'] + time(),
    // ];
    
    // print_r($arrParamsAmo);

    $link = 'https://kostenichss.amocrm.ru/oauth2/access_token';
    
    $data = [
        'client_id' => '5d940a1d-8edb-48c9-9383-abe34fd25d0e',
        'client_secret' => '4xpE9UTOW0WTTtRNwnpb6Md2pqlifknDFCO14hNXgm19z37iD0TlaRg6InSTLdyl',
        'grant_type' => 'refresh_token',
        'refresh_token' => 'def50200c68409d2d33bf18a3dda482cb5071bb9a46e71a5f6115042c1c0c309a049ce70494f3b155de49fcb0f82a472a55b82b2fb9f8312e2d5d268ad62ccef4eac3363e1c9bf67962d27944db643e266ede274f60ced0f96490d8ec6c0a59f02f9cd91c2f70e16b0556a1791aabb70408c8a82a6307c1b6df9506a8810d23a35407804168558af93711f8fb31f4aa0e9340677216568cf461f8987558c4db97cca80c19daaf768e7e11b26f2d1fb5d05b16e4262ad83b4cb0bb8492671873741e8325e7c5f06997f7eabfbd184e7872977e30a8d78086413ac66725fe6f7b46bcde603fcb98902d526ad9adf0a431522f5696fd8519f8162b4e0cb144ccc3214af4dd54ed8478eabb4aa91033b03fc5759812741b1ebe6ba85c34550b155f4b6ae1fe24bc8f7fdf646ef8627a348404db7b2ee80f4a6f91b94ddb6d7962f06b91ed1a050f4b0e6a1925ccac50a20ab9baa988359228ebb9cbf6de1ee2e26dc043ec3a9f69721301347ec76b2a0169d4ae50cbc6a33e574c13977124e03ab90be6610ed9e2fe297ae452c9c62459ab8e3060a3f0d82fd895c5901b7e146e05244b27897e26e51f882d2d2bf53b65ac059e83a79d4f3519706575a22a4928f6adf0959cd83509f5947d0d94f6880633e24de9dbcafe78a32b8e6864047f8e31465d161f32d4831c61aaa4b540eddee43',
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

    print_r($response);
?>