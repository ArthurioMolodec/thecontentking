<?php

$prompt = $_GET['prompt'];

$user = dbHelper->getUser();

$user_type = isset($user['type']) ? $user['type'] : ACCOUNT_TYPE_DEFAULT;

$leftCount = dbHelper->getUserApiCalls(MODULE_NAME_AI_IMAGES, defaulModulesLimitByType[MODULE_NAME_AI_IMAGES][$user_type]);

if ($leftCount <= 0) {
    if (!$_SESSION['id_token']) {
        echo json_encode(['status' => false, 'error' => 'needauth', 'redirect' => null, 'total_count' => 0, 'left_count' => 0]);
        return;
    }
    // $link = stripeApi->createPaymentLink(aiImageGeneratedPremiumPrice, 'Payment for AI Image Generator', PAYMENT_FOR_MODULE_PREMIUM_PREFIX, $user['uid']);
    echo json_encode(['status' => false, 'error' => 'unpayed', 'redirect' => '/premium', 'total_count' => defaulModulesLimitByType[MODULE_NAME_AI_IMAGES][$user_type], 'left_count' => 0]);
    return;
}

dbHelper->incrementApiCalls(MODULE_NAME_AI_IMAGES);

$result = json_decode(file_get_contents('http://178.18.248.235:5005/image-generator?prompt=' . urlencode($prompt)), true);

$result['total_count'] = defaulModulesLimitByType[MODULE_NAME_AI_IMAGES][$user_type];
$result['left_count'] = $leftCount - 1;
// $result['status'] = true;

if ($leftCount - 1 <= 0) {
    if (!$_SESSION['id_token']) {
        $result['error'] = 'needauth';
    } else {
        $result['error'] = 'unpayed';
        $result['redirect'] = '/premium';    
    }
}


echo json_encode($result);