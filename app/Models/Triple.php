<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Triple extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'triples';

    protected $guarded = ['id'];

    public function lily(){
        return $this->belongsTo(Lily::class);
    }
}
