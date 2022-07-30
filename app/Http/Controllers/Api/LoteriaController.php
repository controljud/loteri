<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\Sorteios;
use App\Models\Jogos;
use App\Models\Totais;
use App\Models\Aposta;
use Carbon\Carbon;
use DB;

use Exception;

class LoteriaController extends Controller
{
    public function postJogo(Request $request)
    {
        try {
            $jogo = new Jogos;
            $jogo->jogo = $request->jogo;
            $jogo->status = $request->status;

            $jogo->save();

            return response()->json([
                "status" => 0,
                "message" => "Jogo criado com sucesso",
                "data" => null
            ]);
        } catch (Exception $ex) {
            Log::error("Erro na gravação do jogo: " . $ex->getMessage());

            return response()->json([
                "status" => 1,
                "message" => "Erro na gravação do jogo",
                "data" => null
            ]);
        }
    }

    public function getJogos()
    {
        try {
            $jogos = Jogos::all();

            return response()->json([
                "status" => 0,
                "message" => "Jogo criado com sucesso",
                "data" => $jogos
            ]);
        } catch (Exception $ex) {
            Log::error("Erro retorno dos dados: " . $ex->getMessage());

            return response()->json([
                "status" => 1,
                "message" => "Erro no retorno dos dados",
                "data" => null
            ]);
        }
    }

    public function getUltimoJogo(Request $request, $id_jogo)
    {
        try {
            // $ultimo = Sorteios::where('id_jogo', $id_jogo)->last();
            $ultimo = Sorteios::where('id_jogo', $id_jogo)->orderBy('numero', 'desc')->first();

            return response()->json([
                "status" => 0,
                "message" => "Último sorteio Mega Sena",
                "data" => $ultimo
            ]);
        } catch (Exception $ex) {

        }
    }

    public function getTotais(Request $request, $id_jogo)
    {
        try {
            $totais = Totais::where('id_jogo', $id_jogo)->get();

            return response()->json([
                "status" => 0,
                "message" => "",
                "data" => $totais
            ]);
        } catch (Exception $ex) {
            Log::error("Erro retorno dos dados: " . $ex->getMessage());

            return response()->json([
                "status" => 1,
                "message" => "Erro no retorno dos dados",
                "data" => null
            ]);
        }
    }

    public function putTotais(Request $request)
    {
        try {
            Totais::where('id', '<>', null)->delete();

            $jogos = Jogos::all();

            foreach ($jogos as $jogo) {
                $sorteios = Sorteios::where('id_jogo', $jogo->id)->get();

                $totaisJogo = [];
                foreach ($sorteios as $sorteio) {
                    $dezenas = json_decode($sorteio->dezenas);
                    
                    foreach ($dezenas as $dezena) {
                        $indice = intval($dezena);
                        if (isset($totaisJogo[$indice])) {
                            $totaisJogo[$indice] = $totaisJogo[$indice] + 1;
                        } else {
                            $totaisJogo[$indice] = 1;
                        }
                    }
                }

                $totais = new Totais;
                $totais->id_jogo = $jogo->id;
                $totais->totais = json_encode($totaisJogo);

                $totais->save();
            }

            return response()->json([
                "status" => 0,
                "message" => "Totais gerados com sucesso",
                "data" => $totaisJogo
            ]);
        } catch (Exception $ex) {
            Log::error("Erro retorno dos dados: " . $ex->getMessage());

            return response()->json([
                "status" => 1,
                "message" => "Erro no retorno dos dados",
                "data" => null
            ]);
        }
    }

    public function postAposta(Request $request)
    {
        try {
            $aposta = new Aposta;
            $aposta->id_user = $request->id_user;
            $aposta->numero = $request->numero;
            $aposta->data = $request->data;
            $aposta->dezenas = $request->dezenas;

            $aposta->save();

            return response()->json([
                "status" => 0,
                "message" => "Aposta incluída com sucesso",
                "data" => null
            ]);
        } catch (Exception $ex) {
            Log::error("Erro na gravação da aposta: " . $ex->getMessage());

            return response()->json([
                "status" => 1,
                "message" => "Erro na gravação da aposta",
                "data" => null
            ]);
        }
    }

    public function getApostas(Request $request, $id_user)
    {
        try {
            $apostas = Aposta::select('numero', DB::raw("date_format(data, '%d/%m/%Y') AS data"), 'dezenas')
                ->where('id_user', $id_user)->get();

            if ($apostas) {
                return response()->json([
                    "status" => 0,
                    "message" => "Apostas recuperadas com sucesso",
                    "data" => $apostas
                ]);
            }

            return response()->json([
                "status" => 0,
                "message" => "Não há apostas cadastradas",
                "data" => null
            ]);
        } catch (Exception $ex) {
            Log::error("Erro no retorno das apostas: " . $ex->getMessage());

            return response()->json([
                "status" => 1,
                "message" => "Erro no retorno das apostas",
                "data" => null
            ]);
        }
    }

    public function getSorteioAtual()
    {
        try {
            $sorteio = Sorteios::orderBy('data', 'desc')->first();

            if ($sorteio) {
                return response()->json([
                    "status" => 0,
                    "message" => "Sorteio recuperado com sucesso",
                    "data" => $sorteio
                ]);
            }

            return response()->json([
                "status" => 1,
                "message" => "Não há sorteios futuros",
                "data" => null
            ]);
        } catch (Exception $ex) {
            Log::error("Erro na recuperação do sorteio: " . $ex->getMessage());

            return response()->json([
                "status" => 1,
                "message" => "Erro na recuperação do sorteio",
                "data" => null
            ]);
        }
    }

    private function sortArray($a, $b)
    {   
        if ($a[0] == $b[0]) {
            return 0;
        }
        
        return ($a[0] < $b[0]) ? -1 : 1;
    }
}