<?php
namespace JiaLeo\Sms;

class Sms
{
    private $config; //配置
    private $code;   //验证码

    private $errorMsg;

    public function __construct()
    {
        //读取配置
        $this->config = config('sms');

    }

    public function send($phone, $template_code, $param = array())
    {
        //生成随机验证码
        $this->code = $this->generateMsgAuthCode(6);

        //选择的运营商(当前只选择阿里大鱼)
        $sms_obj = new Lib\Alidayu(
            $this->config['alidayu']['app_key'],
            $this->config['alidayu']['app_secret'],
            $this->config['alidayu']['sign_name'],
            $param, $template_code, $phone
        );

        $res = $sms_obj->send();

        //如发送失败，打印错误查看对应错误代码;
        if (!$res) {
            $this->errorMsg = $sms_obj->error;
            return false;
        }

        return true;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return mixed
     */
    public function getErrorMsg()
    {
        return $this->errorMsg;
    }

    /**
     * 生成验证码
     * @author: 亮 <chenjialiang@han-zi.cn>
     */
    public function generateMsgAuthCode($limit)
    {
        $rand_array = range(0, 9);
        shuffle($rand_array); //调用现成的数组随机排列函数
        return implode('', array_slice($rand_array, 0, $limit)); //截取前$limit个
    }
}