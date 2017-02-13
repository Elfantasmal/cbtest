<?php

/**
 * 登录密码加密
 * @author: 亮 <chenjialiang@han-zi.cn>
 */
if (!function_exists('encrypt_password')) {
    /**
     * @param string $password 密码
     * @param string $salt 扰乱码
     * @return string
     */
    function encrypt_password($password, $salt)
    {
        return md5(sha1($password . $salt));
    }
}

/**
 * 生成一个密码
 * @author: 亮 <chenjialiang@han-zi.cn>
 */
if (!function_exists('create_password')) {
    /**
     * @param string $password 密码
     * @param string $salt 扰乱码
     * @return string
     */
    function create_password($password, &$salt)
    {
        $salt = str_random(5);
        return encrypt_password($password, $salt);
    }
}

/**
 * 生成guid
 * @author: 亮 <chenjialiang@han-zi.cn>
 */
if (!function_exists('create_guid')) {
    /**
     * @return string
     */
    function create_guid()
    {
        $charid = strtolower(md5(uniqid(mt_rand(), true)));
        $hyphen = chr(45);// "-"
        $guid = substr($charid, 0, 8) . $hyphen . substr($charid, 8, 4) . $hyphen . substr($charid, 12, 4) .
            $hyphen . substr($charid, 16, 4) . $hyphen . substr($charid, 20, 12);
        return $guid;
    }
}


