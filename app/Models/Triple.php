<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Triple
 *
 * @property int $id
 * @property string $predicate
 * @property string $object
 * @property int $synced
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $lily_slug
 * @method static \Illuminate\Database\Eloquent\Builder|Triple newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Triple newQuery()
 * @method static \Illuminate\Database\Query\Builder|Triple onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Triple query()
 * @method static \Illuminate\Database\Eloquent\Builder|Triple whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Triple whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Triple whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Triple whereLilySlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Triple whereObject($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Triple wherePredicate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Triple whereSynced($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Triple whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Triple withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Triple withoutTrashed()
 * @mixin \Eloquent
 */
class Triple extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'triples';

    protected $guarded = ['id'];
}
