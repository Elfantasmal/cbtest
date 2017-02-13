<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class TestController extends Controller
{

    /**
     * 导出
     */
    public function index()
    {

        $export_data = array(

            array(
                '订单号', '支付时间', '商品id', '商品名称'
            ),

            array(
                '1',
                2,
                array(
                    '10', '11'
                ),
                array(
                    '商品1', '商品2'
                )
            ),
            array(
                '12222',
                211111111,
                12,
                '商品3'
            )
        );

        //只是保存到本地
        $result= \Excel::export('aaaa',$export_data);
        dump($result);

        //直接浏览器输出
        $result=  \Excel::export('aaaa',$export_data,true);
        dump($result);

        //上传到阿里云(请设置好阿里云配置)
        $result= \Excel::export('aaaa',$export_data,false,true);
        dump($result);
    }

    /**
     * 导入
     */
    public function index2()
    {
        $result = \Excel::import(base_path().'/storage/aaaa.xls');
        dump($result);
    }

}
