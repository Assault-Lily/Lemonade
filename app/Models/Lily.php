<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Lily
 *
 * @property int $id
 * @property string $slug
 * @property string $name
 * @property string $name_a
 * @property string $name_y
 * @property string|null $color
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Triple[] $triples
 * @property-read int|null $triples_count
 * @method static \Illuminate\Database\Eloquent\Builder|Lily newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Lily newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Lily query()
 * @method static \Illuminate\Database\Eloquent\Builder|Lily whereColor($value)
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

    public function triples(){
        return $this->hasMany(Triple::class,'lily_slug');
    }
}
