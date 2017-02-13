<?php

namespace JiaLeo\AutoCreate;

use Illuminate\Console\Command;

class Logic extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:logic {logic_name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create logic file';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //获取参数
        $arg = $this->arguments();

        //类名
        $class_name = class_basename($arg['logic_name']) . 'Logic';

        //文件路径
        $file_path = app_path() . '/Logic/' . $arg['logic_name'] . 'Logic.php';

        //文件目录路径
        $dir_path = dirname($file_path);

        //分析命名空间
        if ($class_name == $arg['logic_name'] . 'Logic') {
            $name_space = 'App\Logic';
        } else {
            $name_space = 'App\Logic\\' . $arg['logic_name'];
            $name_space = str_replace('/', '\\', substr($name_space, 0, strrpos($name_space, '/')));
        }


        $template = file_get_contents(dirname(__FILE__) . '/Template/logic.php');

        $source = str_replace('{{class_name}}', $class_name, $template);
        $source = str_replace('{{name_space}}', $name_space, $source);

        //加载helper
        require_once app_path() . '/Helper/File.php';

        //写入文件
        if (!dir_exists($dir_path)) {
            $this->error('目录' . $dir_path . ' 没有写入权限');
            exit;
        }

        //判断文件是否存在
        if (file_exists($file_path)) {
            $this->error('文件' . $file_path . ' 已存在');
            exit;
        }

        if (file_put_contents($file_path, $source)) {
            $this->info($class_name . '添加Logic成功');
        } else {
            $this->error($class_name . '添加Logic失败');
        }
    }
}
