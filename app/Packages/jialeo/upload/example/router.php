<?php

Route::post('upload', 'Admin\UploadController@getUploadID');
Route::get('upload/sign/{id}', 'Admin\UploadController@getUploadSign');
Route::put('upload/cloudcomplete/{id}', 'Admin\UploadController@putCloudUploadComplete');
Route::put('upload/localcomplete/{id}', 'Admin\UploadController@putLocalUploadComplete');
Route::any('upload/callback', 'Admin\UploadController@uploadCallback');
Route::post('files', 'Admin\UploadController@upload');