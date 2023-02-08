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

class ApostaController extends Controller
{
    private $aposta;

    public function __construct()
    {
        $this->aposta = new Aposta;
    }

    public function postAposta(Request $request)
    {
        try {
            $dezenas = $this->mountAposta($request->dezenas);

            $id = $request->id;
            if ($id != null) {
                $aposta = Aposta::find($id);
            } else {
                $aposta = new Aposta;
            }

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
            ], 500);
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
            ], 400);
        } catch (Exception $ex) {
            Log::error("Erro no retorno das apostas: " . $ex->getMessage());

            return response()->json([
                "status" => 1,
                "message" => "Erro no retorno das apostas",
                "data" => null
            ], 500);
        }
    }

    public function deleteAposta(Request $request, $id)
    {
        try {
            $aposta = Aposta::find($id);
            if ($id != null && $aposta = Aposta::where('id', $id)->first()) {
                $aposta->delete();

                return response()->json([
                    "status" => 1,
                    "message" => "Aposta excluída com sucesso",
                    "data" => $aposta
                ]);
            }

            return response()->json([
                "status" => 0,
                "message" => "Aposta não encontrada",
                "data" => null
            ], 400);
        } catch (Exception $ex) {
            Log::error("Erro ao excluir aposta: " . $ex->getMessage());

            return response()->json([
                "status" => 0,
                "message" => "Erro ao excluir aposta",
                "data" => null
            ], 500);
        }
    }

    public function getQuantidadeApostas()
    {
        try {
            if ($this->isAdminUser()) {
                $quantidade = Aposta::count();

                return response()->json([
                    'status' => 0,
                    'message' => "Quantidade retornada com sucesso",
                    'data' => [
                        'quantidade' => $quantidade
                    ]
                ]);
            }

            return response()->json([
                'status' => 1,
                'message' => "Você não tem acesso para usar essa funcionalidade",
                'data' => null
            ], 400);
        } catch (Exception $ex) {
            Log::error("QUANTIDADE APOSTAS: " . $ex->getMessage());
            
            return response()->json([
                'status' => 1,
                'message' => "Ocorreu um erro na execução do serviço",
                'data' => null
            ], 500);
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

    private function isAdminUser()
    {
        return User::where('users.id', Auth::id())->join('lt_admin_users', 'users.id', 'lt_admin_users.id_user')->exists();
    }
}