<?php

namespace App\Models;

use Baopham\DynamoDb\DynamoDbModel as Model;

/**
 * App\Models\Image
 *
 * @method static \BaoPham\DynamoDb\DynamoDbCollection|static[] all($columns = ['*'])
 * @method static \BaoPham\DynamoDb\DynamoDbCollection|static[] get($columns = ['*'])
 * @method static \Illuminate\Database\Eloquent\Builder|Image newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Image newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Image query()
 * @mixin \Eloquent
 */
class Image extends Model
{
    protected $table = 'Lemonade-images';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'id',
        'for',
        'title',
        'type',
        'author',
        'author_info',
        'image_url'
    ];
}
