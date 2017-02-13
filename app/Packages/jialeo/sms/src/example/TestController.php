<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;

class TestController extends Controller
{

    public function index()
    {
        $result = \Sms::send('15918721789','SMS_40550018',array(
            'name' => '测试',
            'bill_num' => '20176677889922')
        );
        dump($result);
        dump(\Sms::getErrorMsg(),\Sms::getCode());
    }

}
