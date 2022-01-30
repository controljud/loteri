<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        try {
            $user = auth('api')->attempt(
                ['email' => $request->email, 'password' => $request->password]
            );

            return response()->json([
                "status" => 0,
                "mensagem" => "Retorno Login OK",
                "data" => $user
            ]);
        } catch (Exception $ex) {
            Log::error("Erro no login: " . $ex->getMessage());
            return response()->json([
                "status" => 1,
                "mensagem" => "Erro no retorno do Login",
                "data" => []
            ]);
        }
    }

    public function cadastro(Request $request)
    {
        User::create(array(
            'name' => $request->name,
            'password' => Hash::make($request->password),
            'email'    => $request->email
        ));

        $retorno = [
            "status" => 0,
            "mensagem" => "Retorno Cadastro OK",
            "data" => [
                
            ]
        ];
        return response()->json($retorno);
    }
}
