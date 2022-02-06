<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TotaisModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'mega_sena'
    ];
    protected $table = 'lt_sorteio_totais';
}
