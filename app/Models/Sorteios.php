<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sorteios extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_jogo', 'numero', 'dezenas', 'data'
    ];
    protected $table = 'lt_sorteios';
}
