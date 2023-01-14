<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Aposta extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero', 'data', 'dezenas'
    ];
    protected $table = "lt_apostas";

    public function getApostas($id_user, $filter, $per_page = 10)
    {
        $apostas = $this::select('lt_apostas.numero', DB::raw("date_format(lt_apostas.data, '%d/%m/%Y') AS data"), 'lt_apostas.dezenas as apostado', 'lt_sorteios.dezenas as sorteado')
            ->join('lt_sorteios', 'lt_sorteios.numero', 'lt_apostas.numero')
            ->where('id_user', $id_user);
        
        if ($filter != 0) {
            $apostas = $apostas->where(function($query) use ($filter) {
                $query->orWhere('lt_apostas.numero', $filter)
                    // ->orWhere('lt_apostas.descricao', 'like', "%$filter%")
                    ->orWhere('lt_apostas.dezenas', 'like', "%$filter%")
                    ->orWhere('lt_sorteios.dezenas', 'like', "%$filter%");
            });
        }

        $apostas = $apostas->orderBy('lt_apostas.id', 'desc')
            ->paginate($per_page);

        return $apostas;
    }

    public function setAposta($aposta)
    {
        $this->create($aposta);
    }
}
