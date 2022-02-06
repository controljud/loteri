<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\MegaSenaModel;
use Carbon\Carbon;

use Exception;

class MegaSenaController extends Controller
{
    public function getDadosSorteados()
    {
        return 'ok';
    }

    public function putUpdate()
    {
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
            // return response()->json($linhas);
    
            foreach ($linhas as $linha) {
                $megaSena = new MegaSenaModel;
                $megaSena->numero = $linha[0];
                $megaSena->dezenas = $linha[2];
                $megaSena->data = Carbon::createFromFormat('d/m/Y', $linha[1])->format('Y-m-d');
                
                $megaSena->save();
            }
    
            return response()->json([
                "status" => 0,
                "message" => "",
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

    public function postSorteio(Request $request)
    {
        
        $megaSena = new MegaSenaModel;
    }

    private function sortArray($a, $b)
    {   
        if ($a[0] == $b[0]) {
            return 0;
        }
        
        return ($a[0] < $b[0]) ? -1 : 1;
    }
}