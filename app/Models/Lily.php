<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\Lily
 *
 * @property int $id
 * @property string $slug
 * @property string|null $name
 * @property string|null $name_a
 * @property string|null $name_y
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Lily newModelQuery()
 * @method static Builder|Lily newQuery()
 * @method static Builder|Lily query()
 * @method static Builder|Lily whereCreatedAt($value)
 * @method static Builder|Lily whereId($value)
 * @method static Builder|Lily whereName($value)
 * @method static Builder|Lily whereNameA($value)
 * @method static Builder|Lily whereNameY($value)
 * @method static Builder|Lily whereSlug($value)
 * @method static Builder|Lily whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Lily extends Model
{
    use HasFactory;

    protected $table = 'lilies';

    protected $guarded = ['id'];
}
