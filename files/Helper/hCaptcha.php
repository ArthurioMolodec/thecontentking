<?php

function validateHCaptcha($token, $secret = hCaptchaSecret) {
    $data = array(
        'secret' => $secret,
        'response' => $token
    );
    $verify = curl_init();
    curl_setopt($verify, CURLOPT_URL, "https://hcaptcha.com/siteverify");
    curl_setopt($verify, CURLOPT_POST, true);
    curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($verify);
    // var_dump($response);
    $responseData = json_decode($response);
    if ($responseData->success) {
        return true;
    }
    return false;
}
