<?php
namespace JiaLeo\WechatPay;

use App\Exceptions\ApiException;

class Wechatpay{

    public $type = 'mp';

    /**
     * 统一下单接口
     * @author: 亮 <chenjialiang@han-zi.cn>
     * @param $body string 描述
     * @param $attach string 附加消息
     * @param $out_trade_no int 商户订单号
     * @param $total_fee int 订单金额
     * @param $notify_url string 支付回调地址
     * @param $time_start string 订单开始时间
     * @param $time_expire string 订单过期时间
     */
    public function getPaySign($openid='',$body,$attach,$out_trade_no,$total_fee,$notify_url,$time_start=0,$time_expire=0){
        $wxpay = new Lib\WxPayApi($this->type);
        //统一下单
        $input = new Lib\WxPayUnifiedOrder();

        if($this->type === 'mp'){
            if(empty($openid)){
                throw new ApiException('请传入openid','OPENID_EMPTY');
            }
            $input->SetOpenid($openid);
            $input->SetTrade_type('JSAPI');#接口
        }
        elseif($this->type === 'app'){
            $input->SetTrade_type('APP');#接口
        }

        $input->SetBody($body);//设置商品或支付单简要描述
        $input->SetOut_trade_no($out_trade_no);#设置单号
        $input->SetTotal_fee($total_fee);#设置支付金额
        $input->SetAttach($attach);//自定义的参数

        $input->SetNotify_url($notify_url);#回调地址


        if($time_start!==0){
            $input->SetTime_start($time_start);         //交易起始时间
        }

        if($time_expire!==0){
            $input->SetTime_expire($time_expire);       //交易结束时间
        }

        $order = Lib\Wxpayapi::unifiedOrder($input);#生成订单

        if(empty($order['return_code'])||$order['return_code']!=='SUCCESS'){
            throw new ApiException('微信错误,'.$order['return_msg'],'WECHAT_ERROR');
        }

        if(empty($order['result_code'])||$order['result_code']!=='SUCCESS'){
            throw new ApiException('生成微信单号失败,'.$order['return_msg'],'WECHAT_ERROR');
        };

        if(!array_key_exists("appid", $order)|| !array_key_exists("prepay_id", $order)|| $order['prepay_id'] == ""){
            throw new ApiException("参数错误",'PARAMS_ERROR');
        }


        if($this->type == 'mp'){
            $jsObj=new Lib\WxPayJsApiPay();
            $jsObj->SetAppid($order["appid"]);
            $timeStamp = time();
            $jsObj->SetTimeStamp("$timeStamp");
            $jsObj->SetNonceStr(Lib\WxPayApi::getNonceStr());
            $jsObj->SetSignType("MD5");
            $jsObj->SetPackage("prepay_id=" . $order['prepay_id']);
            $jsObj->SetPaySign($jsObj->MakeSign());

            $jsApiParameters = json_encode($jsObj->GetValues());
            //log_message('error',$jsApiParameters);
            if (!$jsApiParameters) {
                throw new ApiException('微信接口对接失败','WECHAT_ERROR');
            }
        }
        elseif($this->type == 'app'){
            $appObj=new Lib\WxPayAppApiPay();
            $appObj->SetAppid($order["appid"]);
            $timeStamp = time();
            $appObj->SetTimeStamp("$timeStamp");
            $appObj->SetNonceStr(Lib\WxPayApi::getNonceStr());
            $appObj->SetPackage("Sign=WXPay");
            $appObj->SetPartnerId(Lib\WxPayConfig::$MCHID);
            $appObj->SetPrepayId($order['prepay_id']);
            $appObj->SetSign();

            $getValues=$appObj->GetValues();
            $getValues['orderid']=$out_trade_no;

            $jsApiParameters = json_encode($getValues);
            //log_message('error',$jsApiParameters);
            if (!$jsApiParameters) {
                throw new ApiException('微信接口对接失败','WECHAT_ERROR');
            }
        }
        else{
            throw new ApiException('位置类型','TYPE_ERROR');
        }

        return $jsApiParameters;
    }

    /**
     * 支付回调通知
     * @author: 亮 <chenjialiang@han-zi.cn>
     */
    public function notify($callback,$needSign = true){
        $replyObj=new Lib\WxPayNotifyReply();

        $get_notify=$replyObj->FromXml($GLOBALS['HTTP_RAW_POST_DATA']);
        if(isset($get_notify['attach'])){
            $attach = json_decode($get_notify['attach'],true);
            if(isset($attach['type'])){
                $this->type = $attach['type'];
            }
        }

        $wxpay = new Lib\WxPayApi($this->type);

        $msg = "OK";
        //当返回false的时候，表示notify中调用NotifyCallBack回调失败获取签名校验失败，此时直接回复失败
        $get_data = Lib\WxpayApi::notify($msg);

        $result=$this->__notifyCallBack($callback,$replyObj,$get_data);
        $this->__replyNotify($replyObj,$needSign);
    }


    /**
     *
     * notify回调方法，该方法中需要赋值需要输出的参数,不可重写
     * @param $replyObj Lib\WxPayNotifyReply 对象
     * @param array $data
     * @return true 回调出来完成不需要继续回调，false回调处理未完成需要继续回调
     */
    private function __notifyCallBack($function,$replyObj,$data)
    {
        $msg = "OK";

        $result = call_user_func($function,$data);
        $replyObj->values = array();
        if($result == true){
            $replyObj->SetReturn_code("SUCCESS");
            $replyObj->SetReturn_msg("OK");

            Lib\WxPayApi::payNotifyLog('处理结果:成功');
        } else {
            $replyObj->SetReturn_code("FAIL");
            $replyObj->SetReturn_msg($msg);
            Lib\WxPayApi::payNotifyLog('处理结果:失败');
        }
        return $result;
    }

    /**
     *
     * 回复通知
     * @param $replyObj Lib\WxPayNotifyReply 对象
     * @param bool $needSign 是否需要签名输出
     */
    private function __replyNotify($replyObj,$needSign = true)
    {
        //如果需要签名
        if($needSign == true &&
            $replyObj->GetReturn_code(/*$return_code*/) == "SUCCESS")
        {
            $replyObj->SetSign();
        }
        Lib\WxpayApi::replyNotify($replyObj->ToXml());
    }

    /**
     * 企业支付
     * @author: 亮 <chenjialiang@han-zi.cn>
     * @param $openid string 用户openid
     * @param $amount int 金额(分)
     * @param $out_trade_no int 商户订单号
     * @param $desc string 订单描述
     * @param $re_user_name string 用户实名名称,传入则验证
     */
    public function enterprisePay($openid,$amount,$out_trade_no,$desc,$re_user_name=null){
        $wxpay = new Lib\WxPayApi($this->type);
        //企业付款
        $input = new Lib\WxPayEnterprise();
        $input->SetOpenid($openid);
        $input->SetAmount($amount);//设置商品或支付单简要描述
        $input->SetTradeNo($out_trade_no);#设置单号
        $input->SetDesc($desc);#设置支付金额

        if($re_user_name!==null){
            $input->SetCheckName('FORCE_CHECK');
            $input->SetReUserName($re_user_name);//自定义的参数
        }

        return Lib\WxPayApi::enterprisePay($input);
    }

    /**
     * 退款
     * @param $out_trade_no int  商户订单号(商户侧传给微信的订单号)
     * @param $out_refund_no int  商户退款单号(商户系统内部的退款单号，商户系统内部唯一，同一退款单号多次请求只退一笔)
     * @param $total_fee int  总金额(分)
     * @param $refund_fee int  退款金额(分)
     * @param $op_user_id int  操作员
     * @author: 亮 <chenjialiang@han-zi.cn>
     */
    public function refund($out_trade_no,$out_refund_no,$total_fee,$refund_fee,$op_user_id=1001){
        $wxpay = new Lib\WxPayApi($this->type);

        //退款
        $input = new Lib\WxPayRefund();
        $input->SetOut_trade_no($out_trade_no);
        $input->SetOut_refund_no($out_refund_no);
        $input->SetTotal_fee($total_fee);
        $input->SetRefund_fee($refund_fee);
        $input->SetOp_user_id($op_user_id);
        $input->SetTransaction_id('');
/*         var_dump($input);
        die; */
        return Lib\WxPayApi::refund($input);
    }
}





