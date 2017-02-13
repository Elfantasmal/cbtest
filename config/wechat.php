<?php

return [
    /*
     * Debug 模式，bool 值：true/false
     *
     * 当值为 false 时，所有的日志都不会记录
     */
    'debug'  => true,

    /*
     * 账号基本信息，请从微信公众平台/开放平台获取
     */
    'mp'=>array(
            'appid'  => env('WECHAT_APPID', 'your-app-id'),         // AppID
            'appsecret'  => env('WECHAT_SECRET', 'your-app-secret'),     // AppSecret
            'token'   => env('WECHAT_TOKEN', 'your-token'),          // Token
            'encodingaeskey' => env('WECHAT_AES_KEY', ''),                    // EncodingAESKey
    ),

    /*
     * 网页端设置
     */
    /*'web'=>array(
        'appid'  => env('WEB_WECHAT_APPID', 'your-app-id'),         // AppID
        'appsecret'  => env('WEB_WECHAT_SECRET', 'your-app-secret'),     // AppSecret
    ),*/

    /*
     * APP应用设置
     */
    /*'app'=>array(
        'appid'  => env('APP_WECHAT_APPID', 'your-app-id'),         // AppID
        'appsecret'  => env('APP_WECHAT_SECRET', 'your-app-secret'),     // AppSecret
    )*/

    /*
     * 日志配置
     *
     * level: 日志级别，可选为：
     *                 debug/info/notice/warning/error/critical/alert/emergency
     * file：日志文件位置(绝对路径!!!)，要求可写权限
     */
    'log' => [
        'level' => env('WECHAT_LOG_LEVEL', 'debug'),
        'file'  => env('WECHAT_LOG_FILE', storage_path('logs/wechat.log')),
    ],


    /*
     * 微信支付
     */
     'payment' => [
         'mp' => array(
             'debug' => env('MP_PAY_DEBUG','true'),
             'aapid' => env('MP_PAY_AAPID',''),
             'appsecret'=>env('MP_PAY_APPSECRET',''), //填写高级调用功能的app id
             'mchid' => env('MP_PAY_MCHID',''),
             'key'=>env('MP_PAY_KEY',''), //填写你设定的key
             'sslcert_path'=>env('MP_PAY_SSLCERT_PATH','/disk2/www/meijiuApi/Cert/wecahtpay/apiclient_cert.pem'), //填写高级调用功能的密钥
             'sslkey_path'=>env('MP_PAY_SSLKEY_PATH','/disk2/www/meijiuApi/Cert/wecahtpay/apiclient_key.pem'),//填写加密用的EncodingAESKey
         ),
         'app' => array(
             'debug' => env('APP_PAY_DEBUG','true'),
             'aapid' => env('APP_PAY_AAPID',''),
             'appsecret'=>env('APP_PAY_APPSECRET',''), //填写高级调用功能的app id
             'mchid' => env('APP_PAY_MCHID',''),
             'key'=>env('APP_PAY_KEY',''), //填写你设定的key
             'sslcert_path'=>env('APP_PAY_SSLCERT_PATH','/disk2/www/meijiuApi/Cert/wecahtpay/apiclient_cert.pem'), //填写高级调用功能的密钥
             'sslkey_path'=>env('APP_PAY_SSLKEY_PATH','/disk2/www/meijiuApi/Cert/wecahtpay/apiclient_key.pem'),//填写加密用的EncodingAESKey
         )
     ],

];
