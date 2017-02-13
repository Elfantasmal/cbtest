<?php

/**
 * 加载辅助函数
 * @param string $class_name
 * @return object
 */
if (!function_exists('load_helper')) {
    function load_helper($class_name)
    {
        require_once app_path() . '/Helper/' . $class_name . '.php';
    }
}

/**
 * 返回错误
 * @param string $error_msg
 * @param string $error_id
 * @param int $status_code
 * @return object
 */
if (!function_exists('response_error')) {
    function response_error($error_msg, $error_id = 'ERROR', $status_code = 400)
    {
        throw new \App\Exceptions\ApiException($error_msg, $error_id, $status_code);
    }
}

/**
 * 设置保存的数据
 * @param Object $model
 * @param array $data
 * @return object
 */
if (!function_exists('set_save_data')) {
    function set_save_data($model, $data)
    {
        foreach ($data as $key => $v) {
            $model->$key = $v;
        }

        return $model;
    }
}
