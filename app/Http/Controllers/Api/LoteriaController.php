<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\Sorteios;
use App\Models\Jogos;
use App\Models\Totais;
use Carbon\Carbon;

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
                        if (isset($totaisJogo[$dezena])) {
                            $totaisJogo[$dezena] = $totaisJogo[$dezena] + 1;
                        } else {
                            $totaisJogo[$dezena] = 1;
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
                "message" => "",
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

    private function sortArray($a, $b)
    {   
        if ($a[0] == $b[0]) {
            return 0;
        }
        
        return ($a[0] < $b[0]) ? -1 : 1;
    }
}