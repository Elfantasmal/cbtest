<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;


/**
 * Model AdminModel
 * 
 * @property int $id
 * @property string $account
 * @property string $password
 * @property string $salt
 * @property string $name
 * @property int $last_login_ip
 * @property int $last_login_time
 * @property int $created_at
 * @property int $updated_at
 * @property int $is_on
 *
 * @method static \Illuminate\Database\Query\Builder | \App\Model\AdminModel where($column, $operator = null, $value = null, $boolean = 'and')
 * @method static \Illuminate\Database\Query\Builder | \App\Model\AdminModel whereIn($column, $values, $boolean = 'and', $not = false)
 * @method static \Illuminate\Database\Query\Builder | \App\Model\AdminModel leftJoin($table, $first, $operator = null, $second = null)
 * @method static \Illuminate\Database\Query\Builder | \App\Model\AdminModel rightJoin($table, $first, $operator = null, $second = null)
 * @method static \Illuminate\Database\Query\Builder | \App\Model\AdminModel get($columns = ['*'])
 * @method static \Illuminate\Database\Query\Builder | \App\Model\AdminModel paginate($perPage = 15, $columns = ['*'], $pageName = 'page', $page = null)
 * @method static \Illuminate\Database\Query\Builder | \App\Model\AdminModel find($id, $columns = ['*'])
 * @method static \Illuminate\Database\Query\Builder | \App\Model\AdminModel first($columns = ['*'])
 * @package App\Model
 */
class AdminModel extends Model
{
    protected $table = 'admin';
    protected $dateFormat = 'U';

    /**
     * 获取对应数据库链接对象
     * @eg 用于分库分表时获取数据所在的数据库对象
     * @param $id
     * @return object
     */
    /*public static function getShardingConnection($id)
    {
        $mod = $id % 4;
        $model = '\App\Model\Mysql2\User_'.$mod.'Model';

        return new $model;
    }*/

}