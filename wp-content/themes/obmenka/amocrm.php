<?php
    $subdomain = 'kostenichss';
    $link = 'https://' . $subdomain . '.amocrm.ru/oauth2/access_token';
    
    $data = [
        'client_id' => '5d940a1d-8edb-48c9-9383-abe34fd25d0e',
        'client_secret' => '4xpE9UTOW0WTTtRNwnpb6Md2pqlifknDFCO14hNXgm19z37iD0TlaRg6InSTLdyl',
        'grant_type' => 'authorization_code',
        'code' => 'def5020018ddfa90925309e8642f7d2db6f95b0f09205fb003c8205ba5c127cd180fbc3627cc5fa889723710ee898a2af8a4ecce7269d6e598a2345debfb49c9517f535867036ba705ea3c80e147c153503f6d6fee8ce998352982d45f4da2e99383f73087dd9a94e61ee9c6c7c4f36652fc6782bd5e0c11c5c83eb2f299430b4f4cb092c54193c9bcf638b1ca723771913c6fc353366b54d664ab9dadc834a0aad8d1116b601e4252fe28668c283cb16a687053b50dcc55ff78d639b6c45d34f16607b12320a178b312a87c16c734d912e463a9188daf36e5ed940e72a3eb120013356bd5bcc3c858cb43984b7b7473dc0b63df7840d963cb77e7e24c7ba135bbb94dcbcce2ef5c50e36e6efa076782ae120d25ec85b5a8f193be6e28d64a48f25a6ba1bf6c1c1f4f6093087607a41b99b64965ed4a1ee518e7d58b94944f2e3277570a626ed58e53662f4ce73bae4065114c5428861da0b8b3d0d65bf16ceb13633302137b379b3a587337470204a585d4dac50e227cbdc6c07156dcc17a15718b981d487b22a6bab7465a65a0a36fe208953969ad3f7c38a260bb6a87a6801e19ff282395b99c892d1f883b5f07d387e56016755b5dd0d8f793cde8545a61c0b0b25b5c017eb1c3d2387df6caa98ca096e1ee661a30f326ae17b616923edd994dcadd2f46',
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
    
    print_r($arrParamsAmo);
?>