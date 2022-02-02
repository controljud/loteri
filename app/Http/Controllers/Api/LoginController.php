<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Exceptions\JWTException;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (!$token = auth()->attempt($credentials)) {
                return response()->json([
                    'status' => 1,
                    'message' => 'Invalid login credential',
                    'data' => null
                ]);
            }
        } catch (JWTException $e) {
            return response()->json([
                'status' => 2,
                'message' => 'Could not create token',
                'data' => null
            ]);
        }

        $user = $request->user();

        return response()->json([
            'status' => 0,
            'message' => 'Login realizado com sucesso',
            'data' => [
                'user' => $user,
                'token' => $token
            ]
        ]);
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
