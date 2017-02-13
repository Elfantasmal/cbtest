<?php
namespace JiaLeo\Excel;

use App\Exceptions\ApiException;
use OSS\OssClient;
use OSS\Core\OssException;

class Excel
{
    public $creator = 'hanzi';      //设置excel创始人
    public $lastModifiedBy = 'hanzi';      //设置excel最后修改人
    public $title = 'hanzi';      //设置excel标题
    public $subject = '';      //设置excel题目
    public $description = '';      //设置excel描述
    public $keywords = '';      //设置excel关键字
    public $category = '';      //设置excel种类
    public $activeSheetIndex = 0;  //设置当前的sheet

    public $cellName = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
        'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ');

    public $uploadPath = 'admin/excel/';    //上传路径
    public $savePath;      //本地保存路径

    private $aliyunConfig = array();  //阿里云配置

    public function __construct()
    {
        //默认保存位置
        $this->savePath = base_path() . '/storage/excel/';
    }

    /**
     * 导出excel
     * @param string $file_name 文件名
     * @param array $table_data 数据
     * @param bool $is_out_put 是否文件输出
     * @param bool $is_upload 是否上传到远端(本地文件会被删除)
     * @eg:传入的数据格式
     * $export_data=array(
     *
     *       array(
     *          '订单号','支付时间','商品id','商品名称'
     *       ),
     *
     *       array(
     *          '1',
     *           2,
     *          array(
     *              '10','11'
     *          ),
     *          array(
     *             '商品1','商品2'
     *          )
     *       ),
     *       array(
     *           '12222',
     *           211111111,
     *           12,
     *           '商品3'
     *       )
     * );
     *
     *
     * @author: 亮 <chenjialiang@han-zi.cn>
     */
    public function export($file_name, $table_data = array(), $is_out_put = false, $is_upload = false)
    {
        require_once dirname(__FILE__) . '/Phpexcel/PHPExcel.php';

        $objPHPExcel = new \PHPExcel();
        $objWriter = new \PHPExcel_Writer_Excel5($objPHPExcel);

        $this->setTitle = $file_name;

        //创建人
        $objPHPExcel->getProperties()->setCreator($this->creator);
        //最后修改人
        $objPHPExcel->getProperties()->setLastModifiedBy($this->lastModifiedBy);
        //标题
        $objPHPExcel->getProperties()->setTitle($this->title);
        //题目
        $objPHPExcel->getProperties()->setSubject($this->subject);
        //描述
        $objPHPExcel->getProperties()->setDescription($this->description);
        //关键字
        $objPHPExcel->getProperties()->setKeywords($this->keywords);
        //种类
        $objPHPExcel->getProperties()->setCategory();

        //设置当前的sheet
        $objPHPExcel->setActiveSheetIndex($this->activeSheetIndex);

        //设置文字对齐
        $objPHPExcel->setActiveSheetIndex()->getDefaultStyle()
            ->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex()->getDefaultStyle()
            ->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);


        //$cellNum = count($cell_name);
        $dataNum = count($table_data);
        $cellNum = count($table_data[0]);

        //表头模板输出
        $objPHPExcel->getActiveSheet()->mergeCells('A1:' . $this->cellName[$cellNum - 1] . '1');//合并单元格
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', 'Export time:' . date('Y-m-d H:i:s'));

        //数据输出
        $col_index = 3;
        foreach ($table_data as $key => $v) {

            if ($key == 0) {  //表头
                foreach ($v as $key2 => $vv) {
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($this->cellName[$key2] . '2', $vv);
                    $objPHPExcel->getActiveSheet()->getColumnDimension($this->cellName[$key2])->setWidth(30);
                    $objPHPExcel->getActiveSheet()->getStyle($this->cellName[$key2] . '2')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
                    $objPHPExcel->getActiveSheet()->getStyle($this->cellName[$key2] . '2')->getFill()->getStartColor()->setARGB('FF1AE694');
                }
            } else {       //数据
                //循环一次,是否有数组
                $is_array = false;
                $col_count = 0;
                foreach ($v as $key2 => $vv) {
                    if (is_array($vv)) {
                        $is_array = true;
                        $col_count = count($vv);
                        break;
                    }
                }

                if (!$is_array) {    //没数组
                    foreach ($v as $key2 => $vv) {
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValueExplicit($this->cellName[$key2] . $col_index, $vv, \PHPExcel_Cell_DataType::TYPE_STRING);
                        //$objPHPExcel->setActiveSheetIndex(0)->setCellValue($this->cellName[$key2].$col_index, $vv);
                    }

                    $col_index += 1;
                } else {    //有数组
                    foreach ($v as $key2 => $vv) {
                        if (!is_array($vv)) {
                            $objPHPExcel->setActiveSheetIndex(0)->setCellValueExplicit($this->cellName[$key2] . $col_index, $vv, \PHPExcel_Cell_DataType::TYPE_STRING);
                            //$objPHPExcel->setActiveSheetIndex(0)->setCellValue($this->cellName[$key2].$col_index, $vv);
                            //合拼单元格
                            $objPHPExcel->getActiveSheet()->mergeCells($this->cellName[$key2] . $col_index . ':' . $this->cellName[$key2] . ($col_index + $col_count - 1));

                        } else {
                            foreach ($vv as $key3 => $vvv) {
                                $objPHPExcel->setActiveSheetIndex(0)->setCellValueExplicit($this->cellName[$key2] . ($col_index + $key3), $vvv, \PHPExcel_Cell_DataType::TYPE_STRING);
                                //$objPHPExcel->setActiveSheetIndex(0)->setCellValue($this->cellName[$key2].($col_index+$key3), $vvv);
                            }
                        }
                    }

                    $col_index += $col_count;
                }
            }
        }

        //直接输出下载
        if ($is_out_put) {
            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
            header("Content-Type:application/force-download");
            header("Content-Type:application/vnd.ms-execl");
            header("Content-Type:application/octet-stream");
            header("Content-Type:application/download");;
            header('Content-Disposition:attachment;filename="' . $file_name . '.xls"');
            header("Content-Transfer-Encoding:binary");
            $objWriter->save('php://output');
            exit;
        }

        //保存到本地
        $file_name = $file_name . '-' . date('YmdHis') . '.xls';
        $save_path = $this->savePath . $file_name;
        $objWriter->save($save_path);

        //上传到阿里云
        if ($is_upload) {
            $return_url = $this->fileUpload($file_name, $save_path);
            @unlink($save_path);
            return $return_url;
        }

        return true;
    }

    /**
     * 获取导入文件,返回对象
     * @author: 亮 <chenjialiang@han-zi.cn>
     */
    public function import($file)
    {
        if (!file_exists($file)) {
            throw new ApiException('文件不存在', 'FILE_ERROR');
        }

        require_once dirname(__FILE__) . '/Phpexcel/PHPExcel.php';
        $fileType = \PHPExcel_IOFactory::identify($file);//自动获取文件的类型提供给phpexcel用
        $objReader = \PHPExcel_IOFactory::createReader($fileType);//获取文件读取操作对象
        @$objPHPExcel = $objReader->load($file);//加载文件

        $sheetName = $objPHPExcel->getSheet(0)->toArray(); //默认拿第一个sheet

        return $sheetName;  //返回对象
    }

    /**
     * 从远程服务器下载excel
     * @param string $url 远程地址
     * @param string $save_path 保存路径
     * @return bool
     * @throws ApiException
     */
    public function downloadExcel($url, $save_path)
    {
        $content = $this->httpGet($url);
        if (!$content) {
            throw new ApiException('excel下载失败', 'DOWN_ERROR');
        }
        $result = file_put_contents($save_path, $content);
        if (!$result) {
            throw new ApiException('excel保存失败', 'SAVE_ERROR');
        }
        return true;
    }

    /**
     * 上传到阿里云
     * @param string $file_name 文件名
     * @param string $path 文件本地路径
     * @author: 亮 <chenjialiang@han-zi.cn>
     */
    private function fileUpload($file_name, $path)
    {
        if (!file_exists($path)) {
            throw new ApiException('文件不存在', 'FILE_ERROR');
        }

        $this->aliyunConfig = \Config::get('aliyun.oss');

        $access_id = $this->aliyunConfig['access_key_id'];
        $access_key = $this->aliyunConfig['access_key_secret'];
        $endpoint = $this->aliyunConfig['endpoint'];
        $bucket = $this->aliyunConfig['bucket'];

        $oss_client = new OssClient($access_id, $access_key, $endpoint);
        $object = $this->uploadPath . $file_name;
        $file_domain = $this->aliyunConfig['file_domain'];

        try {
            $oss_client->uploadFile($bucket, $object, $path);
        } catch (OssException $e) {
            throw new ApiException("上传错误", 'UPLOAD_FILE_FAIL');
        }

        //返回上传后的文件地址
        $return_url = $file_domain . '/' . $object;
        return $return_url;
    }

    /**
     * GET 请求
     * @param string $url
     */
    private function httpGet($url)
    {
        $oCurl = curl_init();
        if (stripos($url, "https://") !== FALSE) {
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        if (intval($aStatus["http_code"]) == 200) {
            return $sContent;
        } else {
            return false;
        }
    }

    public function __call($name, $arguments)
    {
        if (substr($name, 0, 3) === 'set') {
            $setter = lcfirst(ltrim($name, 'set'));
            if (isset($this->$setter)) {
                $this->$setter = $arguments[0];
                return true;
            } else {
                throw new ApiException('属性 ' . $setter . ' 不存在!');
            }
        }

        throw new ApiException('方法 ' . $name . ' 不存在!');
    }

}