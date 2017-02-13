<?php
return array(

    /* OSS云盘 */
    'oss' => array(
        //bucket 名称
        'bucket' => env('OSS_BUCKET', 'hanzi'),
        //地区是深圳oss
        'region' => env('OSS_REGION', 'cn-shenzhen'),
        //bucket 地区的访问接口
        'endpoint' => env('OSS_ENDPOINT', 'http://oss-cn-shenzhen.aliyuncs.com'),
        //阿里云的api 授权id.
        'access_key_id' => env('OSS_ACCESS_KEY_ID', ''),
        //阿里云的api 授权key
        'access_key_secret' => env('OSS_ACCESS_KEY_SECRET', ''),

        //图片上传时用到
        'host' => env('OSS_HOST', 'http://nicewine.oss-cn-shenzhen.aliyuncs.com'),
        //文件cdn域名
        'file_domain' => env('OSS_FILE_DOMAIN', 'http://test.ping-qu.com'),
        //图片cdn域名
        'img_domain' => env('OSS_IMG_DOMAIN', 'http://test.ping-qu.com'),
    )
);
?>

