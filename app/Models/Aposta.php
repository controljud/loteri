<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aposta extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero', 'data', 'dezenas'
    ];
    protected $table = "lt_apostas";
}
