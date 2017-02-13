<?php

Route::group(['middleware' => 'cors'], function () {
    //初始化
    Route::get('api/init', function () {
        $token = Jwt::createToken();
        return array('result' => true, 'token' => $token);
    });
});