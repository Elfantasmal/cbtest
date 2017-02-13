<?php

Route::group(['middleware' => 'cors'], function () {
    //初始化
    Route::get('api/debug', function () {
        return \JiaLeo\Core\Debuger::getLog();
    });
});