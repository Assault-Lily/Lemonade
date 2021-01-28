<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Triple extends Model
{
    use HasFactory;

    protected $table = 'triples';

    public function lily(){
        return $this->belongsTo(Lily::class);
    }
}
