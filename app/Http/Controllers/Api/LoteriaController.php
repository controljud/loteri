<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use App\Models\Sorteios;
use App\Models\Jogos;
use App\Models\Totais;
use App\Models\Aposta;
use App\Models\User;
use Carbon\Carbon;

use Exception;

class LoteriaController extends Controller
{
    private $aposta;

    public function __construct()
    {
        $this->aposta = new Aposta;
    }

    public function postJogo(Request $request)
    {
        try {
            if ($this->isAdminUser()) {
                $jogo = new Jogos;
                $jogo->jogo = $request->jogo;
                $jogo->status = $request->status;

                $jogo->save();

                return response()->json([
                    "status" => 0,
                    "message" => "Jogo criado com sucesso",
                    "data" => null
                ]);
            }

            return response()->json([
                "status" => 1,
                "message" => "Usuário sem acesso a essa funcionalidade",
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
            if ($this->isAdminUser()) {
                $jogos = Jogos::all();

                return response()->json([
                    "status" => 0,
                    "message" => "Jogo criado com sucesso",
                    "data" => $jogos
                ]);
            }

            return response()->json([
                "status" => 1,
                "message" => "Usuário sem acesso a essa funcionalidade",
                "data" => null
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
            $ultimo = Sorteios::where('id_jogo', $id_jogo)->whereNotNull('dezenas')->orderBy('numero', 'desc')->first();

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
            if ($this->isAdminUser()) {
                $totais = Totais::where('id_jogo', $id_jogo)->get();

                return response()->json([
                    "status" => 0,
                    "message" => "",
                    "data" => $totais
                ]);
            }

            return response()->json([
                "status" => 1,
                "message" => "Usuário sem acesso a essa funcionalidade",
                "data" => null
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
            if ($this->isAdminUser()) {
                Totais::where('id', '<>', null)->delete();

                $jogos = Jogos::all();

                foreach ($jogos as $jogo) {
                    $sorteios = Sorteios::where('id_jogo', $jogo->id)->get();

                    $totaisJogo = [];
                    foreach ($sorteios as $sorteio) {
                        $dezenas = json_decode($sorteio->dezenas);
                        
                        if ($dezenas) {
                            foreach ($dezenas as $dezena) {
                                $indice = intval($dezena);
                                if (isset($totaisJogo[$indice])) {
                                    $totaisJogo[$indice] = $totaisJogo[$indice] + 1;
                                } else {
                                    $totaisJogo[$indice] = 1;
                                }
                            }
                        }
                    }

                    $arraySort = [];
                    foreach ($totaisJogo as $key => $total) {
                        if (!in_array($total, $arraySort)) {
                            $arraySort[] = $total;
                        }
                    }

                    usort($arraySort, array($this, "sortArraySimple"));

                    $x1 = 240; //RED and BLUE - subtrair
                    $x2 = 20;
                    $y1 = 250; //GREEN - somar
                    $y2 = 140;
                    $totalArray = count($arraySort);

                    $stepX = ($x1 - $x2) / $totalArray;
                    $stepY = ($y1 - $y2) / $totalArray;

                    $arrayColor = [];
                    $xStep = $x1;
                    $yStep = $y1;
                    foreach ($arraySort as $valor) {
                        $xStep -= $stepX;
                        $yStep -= $stepY;
                        $arrayColor[$valor] = 'rgb(' . round($xStep) . ', ' . round($yStep) . ', ' . round($xStep) . ')';
                    }

                    foreach ($totaisJogo as $keyx => $total) {
                        $cor = '';
                        foreach ($arrayColor as $keyy => $color) {
                            if ($total == $keyy) {
                                $cor = $color;
                            }
                        }

                        $totaisJogo[$keyx] = [
                            $total,
                            $cor
                        ];
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
            }

            return response()->json([
                "status" => 1,
                "message" => "Usuário sem acesso a essa funcionalidade",
                "data" => null
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
            $dezenas = $this->mountAposta($request->dezenas);
            Log::info($dezenas);

            // $arraySend = [
            //     'id_user' => Auth::id(),
            //     'numero' => $request->numero,
            //     'data' => $request->data,
            //     'descricao' => $request->descricao,
            //     'dezenas' => $dezenas
            // ];
            // $aposta = $this->aposta->setAposta($arraySend);

            $aposta = new Aposta;
            $aposta->id_user = $request->id_user;
            $aposta->numero = $request->numero;
            $aposta->data = $request->data;
            $aposta->descricao = $request->descricao;
            $aposta->dezenas = $dezenas;

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

    public function getApostas(Request $request, $filter)
    {
        try {
            $id_user = Auth::id();

            $apostas = $this->aposta->getApostas($id_user, $filter);

            if (count($apostas) > 0) {
                return response()->json([
                    "status" => 0,
                    "message" => "Apostas recuperadas com sucesso",
                    "data" => $apostas
                ]);
            }

            return response()->json([
                "status" => 0,
                "message" => "Não há apostas cadastradas para o usuário informado",
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

    private function mountAposta($dezenas)
    {
        $dezenas = str_replace('.', '-', str_replace(',', '-', str_replace(' ', '-', $dezenas)));

        $arrayNum = explode('-', $dezenas);
        $arrayNumero = [];
        foreach ($arrayNum as $numero) {
            if ($numero < 10 && strlen($numero) < 2) {
                $numero = "0" . $numero;
            }

            $arrayNumero[] = $numero;
        }

        usort($arrayNumero, array($this, "sortArray"));

        return json_encode($arrayNumero);
    }

    private function sortArray($a, $b)
    {
        if ($a[0] == $b[0]) {
            return 0;
        }
        
        return ($a[0] < $b[0]) ? -1 : 1;
    }

    private function sortArraySimple($a, $b)
    {
        if ($a == $b) {
            return 0;
        }
        
        return ($a < $b) ? -1 : 1;
    }

    private function isAdminUser()
    {
        return User::where('users.id', Auth::id())->join('lt_admin_users', 'users.id', 'lt_admin_users.id_user')->exists();
    }
}