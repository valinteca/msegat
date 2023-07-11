<?php

return [
    'username' => env('MSEGAT_USERNAME'),
    'api_key' => env('MSEGAT_API_KEY'),
    'sender_name' => env('MSEGAT_SENDER_NAME'),

    'lang' => env('MSEGAT_LANG', 'Ar'),

    /**
     * API
     */
    'base_url' => 'https://www.msegat.com/gw/',
    'endpoints' => [
        'balance_inquiry' => 'Credits.php',
        'send' => 'sendsms.php',
        'send_personalized' => 'sendVars.php',
        'send_otp' => 'sendOTPCode.php',
        'verify_otp' => 'verifyOTPCode.php',
        'add_sender' => 'addSender.php',
        'get_senders' => 'senders.php',
        'get_messages' => 'getMessages.php',
        'calculate_cost' => 'calculateCost.php',
    ],
];
