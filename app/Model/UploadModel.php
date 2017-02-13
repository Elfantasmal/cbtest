<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;


/**
 * Model UploadModel
 * 
 * @property int $id
 * @property int $part_num
 * @property int $total_size
 * @property int $part_size
 * @property string $origin_filename
 * @property string $filename
 * @property string $path
 * @property string $file_type
 * @property string $type
 * @property string $dir
 * @property int $part_now
 * @property int $status
 * @property int $is_multi
 * @property int $is_cloud
 * @property string $oss_upload_id
 * @property string $oss_part_upload_ids
 * @property string $part_temp_dir
 * @property int $created_at
 * @property int $updated_at
 * @property int $is_on
 *
 * @method static \Illuminate\Database\Query\Builder | \App\Model\UploadModel where($column, $operator = null, $value = null, $boolean = 'and')
 * @method static \Illuminate\Database\Query\Builder | \App\Model\UploadModel whereIn($column, $values, $boolean = 'and', $not = false)
 * @method static \Illuminate\Database\Query\Builder | \App\Model\UploadModel leftJoin($table, $first, $operator = null, $second = null)
 * @method static \Illuminate\Database\Query\Builder | \App\Model\UploadModel rightJoin($table, $first, $operator = null, $second = null)
 * @method static \Illuminate\Database\Query\Builder | \App\Model\UploadModel get($columns = ['*'])
 * @method static \Illuminate\Database\Query\Builder | \App\Model\UploadModel paginate($perPage = 15, $columns = ['*'], $pageName = 'page', $page = null)
 * @method static \Illuminate\Database\Query\Builder | \App\Model\UploadModel find($id, $columns = ['*'])
 * @method static \Illuminate\Database\Query\Builder | \App\Model\UploadModel first($columns = ['*'])
 * @package App\Model
 */
class UploadModel extends Model
{
    protected $table = 'upload';
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