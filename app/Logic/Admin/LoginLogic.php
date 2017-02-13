<?php
namespace App\Logic\Admin;

use App\Exceptions\ApiException;

class LoginLogic
{

    /**
     * 后台管理员登录
     * @param  string $account 管理员帐号
     * @param  string $password 登陆密码
     * @return
     */
    public static function login($account, $password)
    {
        $admin = \App\Model\AdminModel::where('account', $account)->first(['id', 'account', 'password', 'salt', 'name']);

        if (!$admin) {
            throw new ApiException("用户不存在!");
        }

        load_helper('Password');
        $get_password = encrypt_password($password, $admin->salt);

        if ($admin->password != $get_password) {
            throw new ApiException("密码错误!");
        }

        load_helper('Network');

        //更新信息
        set_save_data($admin, [
            'last_login_ip' => get_client_ip(),
            'last_login_time' => time()
        ]);

        $update= $admin->save();
        if (!$update) {
            throw new ApiException("登录失败,请稍后重试!");
        }

        \Jwt::set('admin_info', ['admin_id' => $admin->id]);
        return $admin->name;
    }

}
