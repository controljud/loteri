<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\Sorteios;
use App\Models\Jogos;
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

    public function putTotais()
    {
        return 'ok';
    }
}