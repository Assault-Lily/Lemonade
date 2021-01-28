<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lily extends Model
{
    use HasFactory;

    protected $table = 'lilies';

    protected $guarded = ['id'];

    public function triples(){
        return $this->hasMany(Triple::class);
    }
}
