<?php

namespace JiaLeo\Wechat;

use App\Exceptions\ApiException;

/**
 * 微信授权
 */
class WechatOauth
{

    public $weObj;  //微信实例

    /**
     * 注释说明
     * @param $params
     * @author: 亮 <chenjialiang@han-zi.cn>
     */
    public function __construct($params)
    {
        //实例化微信类
        $weObj = new Wechat($params['type']);
        $this->weObj = $weObj;
        $this->params = $params;

        $query['callback']=$_GET['callback'];
        $query['type']=$params['type'];
        $query['key']=$params['key'];
        $this->callback = url()->current() . '?'.http_build_query($query);
    }

    public function run()
    {
        if (empty($_GET['code']) && empty($_GET['state'])) {    //第一步
            return $this->firstStep();
        } elseif (!empty($_GET['code']) && $_GET['state'] == 'snsapi_base') { //静默请求获得openid
            return $this->afterSilentOauth();
        } elseif (!empty($_GET['code']) && $_GET['state'] == 'snsapi_userinfo') {   //弹出授权获取用户消息
            return $this->afterClickOauth();
        }
        else{
            return false;
        }
    }

    /**
     * 授权登录第一步
     * @author: 亮 <chenjialiang@han-zi.cn>
     */
    public function firstStep()
    {
        $reurl = $this->weObj->getOauthRedirect($this->callback, "snsapi_base", "snsapi_base");
        return redirect($reurl);
    }

    /**
     * 静默获取授权后逻辑
     * @author: 亮 <chenjialiang@han-zi.cn>
     * @return array  用户给的回调函数返回true,则返回该用户openid,反之则跳转用户点击授权
     */
    public function afterSilentOauth()
    {
        $accessToken = $this->weObj->getOauthAccessToken();
        if (!$accessToken || empty($accessToken['openid'])) {
            throw new ApiException('code错误', 'CODE_ERROR');
        }

        $term = $accessToken['openid'];
        $user_info = array();

        //使用unionid作为用户标识
        if ($this->params['check_for'] === 'unionid') {
            $user_info = $this->weObj->getUserInfo($accessToken['openid']);
            $term = $user_info['unionid'];
        }

        //是否存在用户
        $is_user = $this->checkUser($term);
        if (!$is_user && $this->params['is_oauth_user_info'] === true) {
            $reurl = $this->weObj->getOauthRedirect($this->callback, "snsapi_userinfo", "snsapi_userinfo");
            return redirect($reurl);
        } elseif ($is_user) {
            $is_user['openid'] = $accessToken['openid'];
            $is_user['unionid'] = !isset($user_info['unionid']) ? '' : $user_info['unionid'];
            $result = call_user_func_array($this->params['oauth_get_user_silent_function'], array($is_user));

            if ($result) {
                return redirect(urldecode($_GET['callback']));
            }
        }

        throw new ApiException('授权失败', 'AUTH_ERROR');
    }

    /**
     * 用户点击授权后逻辑
     * @author: 亮 <chenjialiang@han-zi.cn>
     */
    public function afterClickOauth()
    {
        $accessToken = $this->weObj->getOauthAccessToken();
        if (!$accessToken) {
            throw new ApiException('获取access_token错误', 'ERROR_ACCESS_TOKEN');
        }

        $user_info = $this->weObj->getOauthUserinfo($accessToken['access_token'], $accessToken['openid']);
        $term = $accessToken['openid'];

        //使用unionid作为用户标识
        if ($this->params['check_for'] === 'unionid') {
            $term = $user_info['unionid'];
        }

        //检查是否存在用户
        $is_user = $this->checkUser($term);
        if (!$is_user) {
            //创健新用户
            $add_user = call_user_func_array($this->params['create_user_function'], array($user_info));
            $user_id = $add_user;
        } else {
            $user_id = $is_user['user_id'];
        }

        $result = call_user_func_array($this->params['oauth_get_user_info_function'], array($user_id, $user_info));

        if ($result) {
            return redirect(urldecode($_GET['callback']));
        }

        throw new ApiException('授权失败', 'AUTH_ERROR');
    }

    /**
     * 检查是否存在用户
     * @author: 亮 <chenjialiang@han-zi.cn>
     */
    public function checkUser($value)
    {
        $result = call_user_func_array($this->params['check_user_function'], array($value));
        return $result;
    }
}
