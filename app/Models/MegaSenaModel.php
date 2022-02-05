<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MegaSenaModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero', 'dezenas', 'data'
    ];
    protected $table = 'lt_mega_sena';
}
