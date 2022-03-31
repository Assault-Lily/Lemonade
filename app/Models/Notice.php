<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Notice
 *
 * @property int $id
 * @property string $slug
 * @property string $title
 * @property string $category
 * @property int $importance
 * @property string $body
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Notice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Notice newQuery()
 * @method static \Illuminate\Database\Query\Builder|Notice onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Notice query()
 * @method static \Illuminate\Database\Eloquent\Builder|Notice whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notice whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notice whereImportance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notice whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notice whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notice whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Notice withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Notice withoutTrashed()
 * @mixin \Eloquent
 */
class Notice extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = ['id'];
}
