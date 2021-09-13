<?php

namespace App\Models;

use Baopham\DynamoDb\DynamoDbModel as Model;

class Image extends Model
{
    protected $table = 'Lemonade-images';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'id',
        'for',
        'type',
        'author',
        'author_info',
        'image_url'
    ];
}
