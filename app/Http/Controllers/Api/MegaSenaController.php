<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\Sorteios;
use App\Models\Jogos;
use App\Models\Totais;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

use Exception;

class MegaSenaController extends Controller
{
    const JOGO = 'Mega Sena';
    private $id_jogo;

    public function __construct()
    {
        $jogo = Jogos::where('jogo', self::JOGO)->first();

        $this->id_jogo = $jogo->id;
    }

    public function putUpdate()
    {
        if ($this->isAdminUser()) {
            set_time_limit(0);
            try {
                $url = file_get_contents('https://asloterias.com.br/lista-de-resultados-da-mega-sena');

                $regexp = "/A lista abaixo mostra, o concurso, a data do sorteio e os números sorteados!(.+)Se você quiser fazer o download todos resultados para seu computador, clique no link abaixo/";
        
                preg_match_all($regexp, $url, $conteudo);
                $result = $conteudo[0][0];
                $res = substr($result, 77, strlen($result)-(95+77));
        
                // $res = str_replace("<div class=\"espacamento_altura\"></div>", "", $res);
        
                $sorteios = explode('<div class="limpar_flutuacao"></div>', $res);
                $sorteados = [];
                $numUltimo = '';
                $dtUltimo = '';
                $ultimoSorteio = '';
        
                $linhas = [];
                foreach($sorteios as $sorteio){
                    $sort = str_replace('<div class="espacamento_altura"></div>', '', $sorteio);
                    $dados = explode(" - ", $sort);
        
                    $retorno = [];
                    $x = 0;
                    foreach($dados as $dado) {
                        $dado = trim(str_replace("</strong>", "", str_replace("<strong>", "", $dado)));
        
                        if ($x == 2) {
                            $dado = explode(" ", $dado);
                            // $dado = $dado;
                            $dado = json_encode($dado);
                        }
        
                        $retorno[] = $dado;
        
                        $x++;
                    }
        
                    if (count($retorno) > 1) {
                        $linhas[] = $retorno;
                    }
                }
        
                usort($linhas, array($this, "sortArray"));
        
                $numeros = Sorteios::where('id_jogo', $this->id_jogo)->pluck('numero')->toArray();
                $adicionados = 0;
                foreach ($linhas as $linha) {
                    if (!in_array($linha[0], $numeros)) {
                        $sorteio = new Sorteios;
                        $sorteio->id_jogo = $this->id_jogo;
                        $sorteio->numero = $linha[0];
                        $sorteio->dezenas = $linha[2];
                        $sorteio->data = Carbon::createFromFormat('d/m/Y', $linha[1])->format('Y-m-d');
                        
                        $sorteio->save();

                        $adicionados++;
                    }
                }
        
                return response()->json([
                    "status" => 0,
                    "message" => $adicionados . " sorteios criados com sucesso",
                    "data" => null
                ]);
            } catch (Exception $ex) {
                Log::error("Erro na execução do serviço: " . $ex->getMessage());
                return response()->json([
                    "status" => 1,
                    "message" => "Erro na execução do serviço",
                    "data" => null
                ]);
            }
        }
        
        return response()->json([
            "status" => 1,
            "message" => "Usuário sem acesso a essa funcionalidade",
            "data" => null
        ]);
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