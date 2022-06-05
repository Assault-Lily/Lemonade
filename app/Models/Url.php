<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Url
 *
 * @property int $id
 * @property string $for
 * @property string $url
 * @property string $title
 * @property string $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Url newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Url newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Url query()
 * @method static \Illuminate\Database\Eloquent\Builder|Url whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Url whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Url whereFor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Url whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Url whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Url whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Url whereUrl($value)
 * @mixin \Eloquent
 */
class Url extends Model
{
    use HasFactory;
}
