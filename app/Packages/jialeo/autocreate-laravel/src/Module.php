<?php

namespace JiaLeo\AutoCreate;

use Illuminate\Console\Command;


class Module extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:module {module_name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create module file';

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

        //模块名
        $moduler_name = class_basename($arg['module_name']);

        $result = false;

        switch ($moduler_name) {
            case 'admin_login' :

                break;
            default :
                $this->error('不存在模块' . $moduler_name);
                break;
        }

        //处理结果
        if ($result === true) {
            $this->info('模块生成成功!');
        }

        if (is_array($result) && $result['result'] === false) {
            $this->warn('模块生成出错!错误原因:' . $result['msg']);
        }
    }
}
