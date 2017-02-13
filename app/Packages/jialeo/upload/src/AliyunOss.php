<?php
namespace JiaLeo\Upload;

use App\Exceptions\ApiException;
use OSS\OssClient;
use OSS\Core\OssException;

class AliyunOss
{

    /**
     * @var
     */
    public $uploadId;  //数据库model中的id
    public $partNum = 0;   //分块数量
    public $uploadSign = array();  //上传签名列表

    public function __construct()
    {
        //加载配置
        $this->config = \Config::get('aliyun.oss');
    }


    /**
     * 获取分块id
     * @param \App\Model\UploadModel $upload_model
     * @param $total_size
     * @param $part_size
     * @param $file_type
     * @param $dir
     * @param $filename
     * @param $callback
     * @param bool $is_multi
     * @param string $part_temp_dir
     * @return bool
     * @throws ApiException
     */
    public function getUploadId($upload_model = \App\Model\UploadModel::class, $total_size, $part_size, $file_type, $dir, $filename, $callback, $is_multi = false, $part_temp_dir = 'tempMulti/')
    {
        $data = compact('total_size', 'part_size', 'file_type', 'filename', 'dir', 'path_temp_dir');

        //计算分块数量
        $data['part_num'] = ceil($data['total_size'] / $data['part_size']);
        $data['origin_filename'] = $data['filename'];
        $data['filename'] = md5(time() . rand(10000, 99999) . $data['origin_filename']);
        $data['type'] = 'cloud';

        //分析扩展名
        $ext = pathinfo($data['origin_filename'])['extension'];
        if (empty($ext)) {
            throw new ApiException('文件扩展名获取失败!');
        }

        //完整路径
        $data['path'] = '/' . $data['dir'] . $data['filename'] . '.' . $ext;

        //如果是分块且分块数大于1
        if ($is_multi && $data['part_num'] > 1) {
            $access_id = $this->config['access_key_id'];
            $access_key = $this->config['access_key_secret'];
            $endpoint = $this->config['endpoint'];
            $bucket = $this->config['bucket'];

            $ossClient = new OssClient($access_id, $access_key, $endpoint);
            $multi_path = trim($data['path'], '/');
            //获取一个upload_id
            $data['oss_upload_id'] = $ossClient->initiateMultipartUpload($bucket, $multi_path);
            $data['part_temp_dir'] = $part_temp_dir;

            $data['is_multi'] = 1;
        }
        else{
            $data['is_multi'] = 0;
            $data['part_num'] = 1;
        }

        $upload_model = new $upload_model();
        set_save_data($upload_model, $data);
        $upload_model->save();
        $upload_id = $upload_model->id;

        $sign = array();
        if ($is_multi && $data['part_num'] > 1) {
            //生成各块签名
            for ($i = 1; $i <= $data['part_num']; $i++) {
                $temp_sign = $this->getSign($upload_id, $callback, $part_temp_dir . $upload_id . '/', $data['filename'], $i, true);
                $temp_sign['is_multi'] = 1;
                $temp_sign['type'] = 'cloud';
                $sign[] = $temp_sign;
                unset($temp_sign);
            }
        } else {
            $temp_sign = $this->getSign($upload_id, $callback, $dir, $data['filename']);
            $temp_sign['is_multi'] = 0;
            $temp_sign['type'] = 'cloud';
            $sign[] = $temp_sign;
            unset($temp_sign);
        }

        $this->uploadId = $upload_id;
        $this->partNum = $data['part_num'];
        $this->uploadSign = $sign;

        return true;
    }

    /**
     * 获取上传加密字符
     * @param string $callbackUrl 回调地址
     * @param string $dir 目录
     * @param string $maxSize 文件最大字节
     * @author: 亮 <chenjialiang@han-zi.cn>
     */
    public function getSign($upload_id, $callbackUrl, $dir = 'temp/', $filename, $part_now = 1, $is_multi = false, $maxSize = 1048576000)
    {
        $access_id = $this->config['access_key_id'];
        $access_key = $this->config['access_key_secret'];
        $host = $this->config['host'];

        if ($is_multi) {
            $filename = $filename . '_' . $part_now;
        }

        $callback_param = array(
            'callbackUrl' => $callbackUrl,
            'callbackBody' => 'object=${object}&size=${size}&mimeType=${mimeType}&height=${imageInfo.height}&width=${imageInfo.width}&filename=' . $filename . '&format=${imageInfo.format}&upload_id=' . $upload_id . '&part_now=' . $part_now,
            'callbackBodyType' => "application/x-www-form-urlencoded");
        $callback_string = json_encode($callback_param);

        $base64_callback_body = base64_encode($callback_string);
        $now = time();
        $expire = 30; //设置该policy超时时间是10s. 即这个policy过了这个有效时间，将不能访问
        $end = $now + $expire;
        $expiration = $this->__gmtIso8601($end);

        //最大文件大小.用户可以自己设置
        $condition = array(0 => 'content-length-range', 1 => 0, 2 => $maxSize);
        $conditions[] = $condition;

        //表示用户上传的数据,必须是以$dir开始, 不然上传会失败,这一步不是必须项,只是为了安全起见,防止用户通过policy上传到别人的目录
        $start = array(0 => 'starts-with', 1 => '$key', 2 => $dir);
        $conditions[] = $start;


        $arr = array('expiration' => $expiration, 'conditions' => $conditions);
        //echo json_encode($arr);
        //return;
        $policy = json_encode($arr);
        $base64_policy = base64_encode($policy);
        $string_to_sign = $base64_policy;
        $signature = base64_encode(hash_hmac('sha1', $string_to_sign, $access_key, true));

        $response = array();
        $response['accessid'] = $access_id;
        $response['host'] = $host;
        $response['policy'] = $base64_policy;
        $response['signature'] = $signature;
        $response['expire'] = $end;
        $response['callback'] = $base64_callback_body;
        //这个参数是设置用户上传指定的前缀
        $response['dir'] = $dir;
        $response['filename'] = $filename;
        $response['guid'] = $filename;

        return $response;
    }


    /**
     * 分块上传完成
     * @param string $path 文件路径
     * @param int $part_num 分块数量
     * @param string $part_filename 分块文件名
     * @param string string $temp_path 分块临时路径
     * @return string $upload_id
     * @throws OssException
     */
    public function multiUploadComplete($upload_model, $id)
    {
        $access_id = $this->config['access_key_id'];
        $access_key = $this->config['access_key_secret'];
        $endpoint = $this->config['endpoint'];
        $bucket = $this->config['bucket'];

        $ossClient = new OssClient($access_id, $access_key, $endpoint);

        $field = ['id', 'dir', 'part_num', 'part_now', 'filename', 'path', 'oss_upload_id', 'oss_part_upload_ids'];
        $upload_info = $upload_model::where('is_on', 1)->where('status', 0)->where('id', $id)->first($field);
        if (!$upload_info) {
            throw new ApiException('上传id不符合!');
        }

        /*if ($upload_info->is_multi != 1) {
            throw new ApiException('上传id不符合!');
        }*/

        if ($upload_info->part_num != $upload_info->part_now) {
            throw new ApiException('上传还没完成!');
        }

        if($upload_info->is_multi){
            $path = trim($upload_info->path, '/');
            $id = $upload_info->id;

            $upload_id = $upload_info->oss_upload_id;

            $responseUploadPart = explode(',', $upload_info->oss_part_upload_ids);

            $uploadParts = array();
            foreach ($responseUploadPart as $i => $eTag) {
                $uploadParts[] = array(
                    'PartNumber' => ($i + 1),
                    'ETag' => $eTag,
                );
            }

            //完成分块上传
            $ossClient->completeMultipartUpload($bucket, $path, $upload_id, $uploadParts);
        }

        //更新为完成状态
        $update = $upload_model::where('id', $id)->update(['status' => 1]);
        if (!$update) {
            throw new ApiException('上传图片失败!');
        }
        load_helper('File');

        return file_url($upload_info->path,true);
    }

    /**
     * 接受回调
     * @author: 亮 <chenjialiang@han-zi.cn>
     */
    public function notify($upload_model)
    {
        // 1.获取OSS的签名header和公钥url header
        $authorizationBase64 = "";
        $pubKeyUrlBase64 = "";
        /*
         * 注意：如果要使用HTTP_AUTHORIZATION头，你需要先在apache或者nginx中设置rewrite，以apache为例，修改
         * 配置文件/etc/httpd/conf/httpd.conf(以你的apache安装路径为准)，在DirectoryIndex index.php这行下面增加以下两行
            RewriteEngine On
            RewriteRule .* - [env=HTTP_AUTHORIZATION:%{HTTP:Authorization},last]
         * */
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $authorizationBase64 = $_SERVER['HTTP_AUTHORIZATION'];
        }
        if (isset($_SERVER['HTTP_X_OSS_PUB_KEY_URL'])) {
            $pubKeyUrlBase64 = $_SERVER['HTTP_X_OSS_PUB_KEY_URL'];
        }

        if ($authorizationBase64 == '' || $pubKeyUrlBase64 == '') {
            header("http/1.1 403 Forbidden");
            exit();
        }

        // 2.获取OSS的签名
        $authorization = base64_decode($authorizationBase64);

        // 3.获取公钥
        $pubKeyUrl = base64_decode($pubKeyUrlBase64);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $pubKeyUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $pubKey = curl_exec($ch);
        if ($pubKey == "") {
            //header("http/1.1 403 Forbidden");
            exit();
        }

        // 4.获取回调body
        $body = file_get_contents('php://input');

        // 5.拼接待签名字符串
        $authStr = '';
        $path = $_SERVER['REQUEST_URI'];
        $pos = strpos($path, '?');
        if ($pos === false) {
            $authStr = urldecode($path) . "\n" . $body;
        } else {
            $authStr = urldecode(substr($path, 0, $pos)) . substr($path, $pos, strlen($path) - $pos) . "\n" . $body;
        }

        // 6.验证签名
        $ok = openssl_verify($authStr, $authorization, $pubKey, OPENSSL_ALGO_MD5);
        if (!$ok) {
            throw new ApiException('Authorization_Fail');
        }

        $data = request()->request->all();

        //查询数据库
        $field = ['id', 'dir', 'part_num', 'part_now', 'filename', 'path', 'is_multi', 'oss_upload_id', 'oss_part_upload_ids', 'part_temp_dir'];
        $upload_info = $upload_model::where('is_on', 1)->where('status', 0)->where('id', $data['upload_id'])->first($field);
        if (!$upload_info) {
            throw new ApiException('上传id不符合');
        }

        if ($upload_info->is_multi) {
            if ($upload_info->part_now + 1 != $data['part_now']) {
                throw new ApiException('上传分块错误');
            }

            //复制对象
            $access_id = $this->config['access_key_id'];
            $access_key = $this->config['access_key_secret'];
            $endpoint = $this->config['endpoint'];
            $bucket = $this->config['bucket'];

            $ossClient = new OssClient($access_id, $access_key, $endpoint);
            $object = $upload_info->part_temp_dir . $upload_info->id . '/' . $upload_info->filename . '_' . $data['part_now'];
            $path = trim($upload_info->path, '/');
            $time1 = time();
            $part_upload_id = $ossClient->uploadPartCopy($bucket, $object, $bucket, $path, $data['part_now'], $upload_info->oss_upload_id);
            $time2 = time();
            \Log::debug($part_upload_id);
            \Log::debug($time2 - $time1);
            //组装upload_id
            $part_upload_ids = $upload_info->oss_part_upload_ids;
            if (!empty($part_upload_ids)) {
                $part_upload_ids = explode(',', $part_upload_ids);
                $part_upload_ids[] = $part_upload_id;
                $oss_part_upload_ids = implode(',', $part_upload_ids);
            } else {
                $oss_part_upload_ids = $part_upload_id;
            }

            \Log::debug($oss_part_upload_ids);

            $update_data = ['part_now' => $data['part_now'], 'oss_part_upload_ids' => $oss_part_upload_ids];

        } else {
            $update_data = ['part_now' => 1];
        }

        $update = $upload_model::where('id', $data['upload_id'])->update($update_data);

        \Log::debug($update);
        if (!$update) {
            throw new ApiException('上传图片失败,请稍后再试!');
        }

        $data = array("Status" => "Ok");
        return $data;

    }

    private function __gmtIso8601($time)
    {
        $dtStr = date("c", $time);
        $mydatetime = new \DateTime($dtStr);
        $expiration = $mydatetime->format(\DateTime::ISO8601);
        $pos = strpos($expiration, '+');
        $expiration = substr($expiration, 0, $pos);
        return $expiration . "Z";
    }
}