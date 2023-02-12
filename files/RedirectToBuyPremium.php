<?php

$user = dbHelper->getUser();

$link = stripeApi->createPaymentLink(aiImageGeneratedPremiumPrice, 'Payment for AI', PAYMENT_FOR_MODULE_PREMIUM_PREFIX, $user['uid']);

header('Location: ' . $link);