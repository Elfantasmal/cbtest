<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Schema\Blueprint;

class AdminTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        Schema::create('admin', function (Blueprint $table) {
            $table->increments('id');
            $table->string('account')->comment('管理员登录账号');
            $table->string('password')->comment('管理员登录密码');
            $table->string('salt')->comment('组合密码加密用的扰乱串');
            $table->string('name')->comment('管理员昵称');
            $table->bigInteger('last_login_ip')->default(0)->comment('用户最后一次登录的ip');
            $table->integer('last_login_time')->default(0)->comment('最后登录时间');
            $table->integer('created_at')->default(0)->comment('创建时间');
            $table->integer('updated_at')->default(0)->comment('更新时间');
            $table->integer('is_on')->default(1)->comment('管理员帐户状态。0为已删除，1为正常');
        });

        DB::table('admin')->insert([
            'account' => 'admin',
            'password' => '1c9ecb8f7cc2f6cdbbce36d76f13dd9b',
            'salt' => 'BppR5',
            'name' => '管理员',
            'last_login_ip' => '167837963',
            'last_login_time' => '1486543875',
            'created_at' => time(),
            'updated_at' => time()
        ]);
    }
}
