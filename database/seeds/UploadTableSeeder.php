<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Schema\Blueprint;

class UploadTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        Schema::create('upload', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('part_num')->nullable()->default(1)->comment('分块总数');
            $table->integer('total_size')->nullable()->comment('总大小(K)');
            $table->integer('part_size')->nullable()->default(1)->comment('分块大小(K)');
            $table->string('origin_filename', 255)->nullable();
            $table->string('filename', 255)->nullable()->comment('生成文件名');
            $table->string('path', 255)->nullable()->comment('文件完整路径');
            $table->string('file_type', 255)->nullable()->comment('文件类型');
            $table->string('type', 255)->nullable()->default('local')->comment('上传方式:cloud,云;local,本地;');
            $table->string('dir', 255)->nullable()->comment('保存目录');
            $table->integer('part_now')->nullable()->comment('当前分块进度');
            $table->integer('status')->nullable()->comment('状态:0,未完成;1,已完成;');
            $table->tinyInteger('is_multi')->nullable()->comment('是否分块上传');
            $table->tinyInteger('is_cloud')->nullable()->comment('是否上传到云盘');
            $table->string('oss_upload_id', 255)->nullable()->comment('阿里云upload_id');
            $table->text('oss_part_upload_ids')->nullable()->comment('oss分块id');
            $table->string('part_temp_dir', 255)->nullable()->comment('分块临时目录');
            $table->integer('created_at')->nullable();
            $table->integer('updated_at')->nullable();
            $table->tinyInteger('is_on')->nullable()->default(1);
        });

    }
}
