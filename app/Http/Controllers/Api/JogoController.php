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

class JogoController extends Controller
{
    const JOGO = 'Mega Sena';
    private $id_jogo;

    public function __construct()
    {
        $jogo = Jogos::where('jogo', self::JOGO)->first();

        $this->id_jogo = $jogo->id;
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
            ], 400);
        } catch (Exception $ex) {
            Log::error("Erro na gravação do jogo: " . $ex->getMessage());

            return response()->json([
                "status" => 1,
                "message" => "Erro na gravação do jogo",
                "data" => null
            ], 500);
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
            ], 400);
        } catch (Exception $ex) {
            Log::error("Erro retorno dos dados: " . $ex->getMessage());

            return response()->json([
                "status" => 1,
                "message" => "Erro no retorno dos dados",
                "data" => null
            ], 500);
        }
    }

    public function putJogo(Request $request)
    {
        if ($this->isAdminUser()) {
            set_time_limit(0);
            try {
                $id_jogo = $request->id_jogo;
                // todo: Atualizar de acordo com o ID enviado

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
                    "data" => [
                        'adicionados' => $adicionados
                    ]
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
            $dezenas = $this->mountDezenas($request->dezenas);

            $sorteio = new Sorteios;
            $sorteio->id_jogo = $request->id_jogo;
            $sorteio->numero = $request->numero;
            $sorteio->dezenas = $dezenas;
            $sorteio->data = $request->data;
            $sorteio->premio = $request->premio;

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
            ], 400);
        } catch (Exception $ex) {
            Log::error("Erro retorno dos dados: " . $ex->getMessage());

            return response()->json([
                "status" => 1,
                "message" => "Erro no retorno dos dados",
                "data" => null
            ], 500);
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
            ], 400);
        } catch (Exception $ex) {
            Log::error("Erro retorno dos dados: " . $ex->getMessage());

            return response()->json([
                "status" => 1,
                "message" => "Erro no retorno dos dados",
                "data" => null
            ], 500);
        }
    }

    public function getSorteioAtual()
    {
        try {
            $sorteio = Sorteios::orderBy('data', 'desc')->first();
            $now = Carbon::now();

            if ($sorteio) {
                return response()->json([
                    "status" => 0,
                    "message" => "Sorteio recuperado com sucesso",
                    "data" => [
                        $sorteio,
                        $now->toDateString()
                    ]
                ]);
            }

            return response()->json([
                "status" => 1,
                "message" => "Não há sorteios futuros",
                "data" => null
            ], 400);
        } catch (Exception $ex) {
            Log::error("Erro na recuperação do sorteio: " . $ex->getMessage());

            return response()->json([
                "status" => 1,
                "message" => "Erro na recuperação do sorteio",
                "data" => null
            ], 500);
        }
    }

    public function getSorteios(Request $request, $id_jogo)
    {
        try {
            $sorteiosModel = new Sorteios;
            $sorteios = $sorteiosModel->getSorteios($id_jogo);

            if ($sorteios) {
                return response()->json([
                    "status" => 0,
                    "message" => "Sorteios recuperados com sucesso",
                    "data" => $sorteios
                ]);
            }

            return response()->json([
                "status" => 1,
                "message" => "Não há sorteios na base",
                "data" => null
            ], 400);
        } catch (Exception $ex) {
            Log::error("Erro na recuperação do sorteio: " . $ex->getMessage());

            return response()->json([
                "status" => 1,
                "message" => "Erro na recuperação do sorteio",
                "data" => null
            ], 500);
        }
    }

    public function getQuantidadeJogos()
    {
        try {
            if ($this->isAdminUser()) {
                $quantidade = Jogos::count();

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
            Log::error("QUANTIDADE JOGOS: " . $ex->getMessage());
            
            return response()->json([
                'status' => 1,
                'message' => "Ocorreu um erro na execução do serviço",
                'data' => null
            ], 500);
        }
    }

    public function getQuantidadeSorteios()
    {
        try {
            if ($this->isAdminUser()) {
                $quantidade = Sorteios::count();

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
            Log::error("QUANTIDADE SORTEIOS: " . $ex->getMessage());
            
            return response()->json([
                'status' => 1,
                'message' => "Ocorreu um erro na execução do serviço",
                'data' => null
            ], 500);
        }
    }

    public function deleteSorteio(Request $request, $id)
    {
        try {
            $sorteio = Sorteios::find($id);
            if ($id != null && $sorteio = Sorteios::where('id', $id)->first()) {
                $sorteio->delete();

                return response()->json([
                    "status" => 1,
                    "message" => "Aposta excluída com sucesso",
                    "data" => null
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

    private function mountDezenas($dezenas)
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

    private function sortArraySimple($a, $b)
    {
        if ($a == $b) {
            return 0;
        }
        
        return ($a < $b) ? -1 : 1;
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