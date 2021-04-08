<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Lily
 *
 * @property int $id
 * @property string $slug
 * @property string|null $name
 * @property string|null $name_a
 * @property string|null $name_y
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Lily newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Lily newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Lily query()
 * @method static \Illuminate\Database\Eloquent\Builder|Lily whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lily whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lily whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lily whereNameA($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lily whereNameY($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lily whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lily whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Lily extends Model
{
    use HasFactory;

    protected $table = 'lilies';

    protected $guarded = ['id'];
}
