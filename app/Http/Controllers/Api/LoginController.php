<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    public function login()
    {
        $retorno = [
            "status" => 0,
            "mensagem" => "Retorno Login OK",
            "data" => [
                
            ]
        ];
        return response()->json($retorno);
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
