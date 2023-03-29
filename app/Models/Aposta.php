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
        $apostas = $this::select('lt_apostas.id', 'lt_apostas.numero', DB::raw("date_format(lt_sorteios.data, '%d/%m/%Y') AS data"), DB::raw("date_format(lt_apostas.data, '%d/%m/%Y') AS data_aposta"), 'lt_apostas.dezenas as apostado', 'lt_apostas.descricao', 'lt_sorteios.dezenas as sorteado')
            ->leftJoin('lt_sorteios', 'lt_sorteios.numero', 'lt_apostas.numero')
            ->where('id_user', $id_user);
        
        if ($filter != 0) {
            $apostas = $apostas->where(function($query) use ($filter) {
                $query->orWhere('lt_apostas.numero', $filter)
                    ->orWhere('lt_apostas.descricao', 'like', "%$filter%")
                    ->orWhere('lt_apostas.dezenas', 'like', "%$filter%")
                    ->orWhere('lt_sorteios.dezenas', 'like', "%$filter%");
            });
        }

        $apostas = $apostas->orderBy('lt_apostas.numero', 'desc')
            ->orderBy('lt_apostas.id', 'desc')
            ->paginate($per_page);

        return $apostas;
    }

    public function setAposta($aposta)
    {
        $this->create($aposta);
    }

    public function getTotalMensal()
    {
        return self::select(DB::raw("date_format(data, '%Y-%m') as data_formatada"), DB::raw('count(id) as quantidade'))
            ->groupBy(DB::raw("date_format(data, '%Y-%m')"))
            ->orderBy('data_formatada', 'desc')
            ->limit(12)
            ->get();
    }
}
