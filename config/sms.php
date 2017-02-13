<?php
return [
    'run' => 'alidayu',

    //阿里大于
    'alidayu' => array(
        'app_key' => env('SMS_APP_KEY', null),
        'app_secret' => env('SMS_APP_SECRET', null),
        'sign_name' => env('SMS_SIGN_NAME', null)
    )
];