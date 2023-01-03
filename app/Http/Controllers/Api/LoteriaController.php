<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
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

            $arraySend = [
                'id_user' => Auth::id(),
                'numero' => $request->numero,
                'data' => $request->data,
                'dezenas' => $dezenas
            ];

            return response()->json($arraySend);

            $aposta = $this->aposta->setAposta($arraySend);
            // $aposta->id_user = $request->id_user;
            // $aposta->numero = $request->numero;
            // $aposta->data = $request->data;
            // $aposta->dezenas = $dezenas;

            // $aposta->save();

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
            $apostas = $this->aposta->getApostas($id_user);

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

    public function postSorteio(Request $request)
    {
        try {
            $sorteio = new Sorteios;
            $sorteio->id_jogo = $request->id_jogo;
            $sorteio->numero = $request->numero;
            $sorteio->dezenas = $request->dezenas;
            $sorteio->data = $request->data;

            $sorteio->save();

            return response()->json([
                "status" => 0,
                "message" => "Sorteio salvo com sucesso",
                "data" => null
            ]);
        } catch (Exception $ex) {
            Log::error("Erro na inclusão do sorteio: " . $ex->getMessage());

            return response()->json([
                "status" => 1,
                "message" => "Erro na inclusão do sorteio",
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

        $arrayNumero = explode('-', $dezenas);

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

    private function isAdminUser()
    {
        return User::where('users.id', Auth::id())->join('lt_admin_users', 'users.id', 'lt_admin_users.id_user')->exists();
    }
}