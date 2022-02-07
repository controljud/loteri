<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jogos extends Model
{
    use HasFactory;

    protected $fillable = [
        'jogo', 'status'
    ];
    protected $table = "lt_jogos";
}
