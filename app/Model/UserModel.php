<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;


/**
 * Model UserModel
 *
 * @property int $id
 * @property string $nickname
 * @property int $created_at
 * @property int $updated_at
 *
 * @method static \Illuminate\Database\Query\Builder | \App\Model\UserModel where($column, $operator = null, $value = null, $boolean = 'and')
 * @method static \Illuminate\Database\Query\Builder | \App\Model\UserModel leftJoin($table, $first, $operator = null, $second = null)
 * @method static \Illuminate\Database\Query\Builder | \App\Model\UserModel rightJoin($table, $first, $operator = null, $second = null)
 * @method static \Illuminate\Database\Query\Builder | \App\Model\UserModel get($columns = ['*'])
 * @method static \Illuminate\Database\Query\Builder | \App\Model\UserModel paginate($perPage = 15, $columns = ['*'], $pageName = 'page', $page = null)
 * @method static \Illuminate\Database\Query\Builder | \App\Model\UserModel find($id, $columns = ['*'])
 * @method static \Illuminate\Database\Query\Builder | \App\Model\UserModel first($columns = ['*'])
 * @package App\Model
 */
class UserModel extends Model
{
    protected $table = 'user';
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