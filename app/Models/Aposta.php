<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Aposta extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_jogo', 'numero', 'data', 'dezenas'
    ];
    protected $table = "lt_apostas";

    public function getApostas($id_user, $id_jogo = null, $filter, $per_page = 10)
    {
        $apostas = $this::select('lt_apostas.id', 'lt_apostas.numero', DB::raw("date_format(lt_sorteios.data, '%d/%m/%Y') AS data"), DB::raw("date_format(lt_apostas.data, '%d/%m/%Y') AS data_aposta"), 'lt_apostas.dezenas as apostado', 'lt_apostas.descricao', 'lt_sorteios.dezenas as sorteado')
            ->leftJoin('lt_sorteios', 'lt_sorteios.numero', 'lt_apostas.numero')
            ->where('id_user', $id_user);

        if ($id_jogo) {
            $apostas = $apostas->where('lt_apostas.id_jogo', $id_jogo);
        }

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
        return self::select(DB::raw("date_format(created_at, '%Y-%m') as data_formatada"), DB::raw('count(id) as quantidade'))
            ->groupBy(DB::raw("date_format(created_at, '%Y-%m')"))
            ->orderBy('data_formatada', 'desc')
            ->limit(12)
            ->get();
    }

    public function getTotalMensalGeral()
    {
        return self::select(DB::raw("date_format(created_at, '%Y') as ano_unitario"), DB::raw("date_format(created_at, '%c') as mes_unitario"), DB::raw('count(id) as quantidade'))
            ->groupBy(DB::raw("date_format(created_at, '%Y')"), DB::raw("date_format(created_at, '%c')"))
            ->get();
    }
}
