<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\Triple
 *
 * @property int $id
 * @property string $predicate
 * @property string $object
 * @property int $synced
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string $lily_slug
 * @method static Builder|Triple newModelQuery()
 * @method static Builder|Triple newQuery()
 * @method static \Illuminate\Database\Query\Builder|Triple onlyTrashed()
 * @method static Builder|Triple query()
 * @method static Builder|Triple whereCreatedAt($value)
 * @method static Builder|Triple whereDeletedAt($value)
 * @method static Builder|Triple whereId($value)
 * @method static Builder|Triple whereLilySlug($value)
 * @method static Builder|Triple whereObject($value)
 * @method static Builder|Triple wherePredicate($value)
 * @method static Builder|Triple whereSynced($value)
 * @method static Builder|Triple whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Triple withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Triple withoutTrashed()
 * @mixin Eloquent
 */
class Triple extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'triples';

    protected $guarded = ['id'];
}
