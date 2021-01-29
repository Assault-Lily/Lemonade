<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
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
 */
	class Lily extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Triple
 *
 * @property int $id
 * @property int $lily_id
 * @property string $predicate
 * @property string $object
 * @property int $spoiler
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Lily $lily
 * @method static \Illuminate\Database\Eloquent\Builder|Triple newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Triple newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Triple query()
 * @method static \Illuminate\Database\Eloquent\Builder|Triple whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Triple whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Triple whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Triple whereLilyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Triple whereObject($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Triple wherePredicate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Triple whereSpoiler($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Triple whereUpdatedAt($value)
 */
	class Triple extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

