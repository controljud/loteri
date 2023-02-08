<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Sorteios extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_jogo', 'numero', 'dezenas', 'data'
    ];
    protected $table = 'lt_sorteios';

    public function getSorteios($id_jogo, $per_page = 10)
    {
        return $this::select('numero', 'dezenas', DB::raw("date_format(data, '%d/%m/%Y') AS data"), 'premio')
            ->where('id_jogo', $id_jogo)->orderBy('numero', 'desc')->paginate($per_page);
    }
}
