<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Auth;
use Exception;

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
            'email' => $request->email,
            'status' => 1
        ));

        $retorno = [
            "status" => 0,
            "mensagem" => "Retorno Cadastro OK",
            "data" => null
        ];
        
        return response()->json($retorno);
    }

    public function getQuantidadeUsuarios()
    {
        try {
            if ($this->isAdminUser()) {
                $quantidade = User::count();

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
            Log::error("QUANTIDADE USUARIO: " . $ex->getMessage());
            
            return response()->json([
                'status' => 1,
                'message' => "Ocorreu um erro na execução do serviço",
                'data' => null
            ], 500);
        }
    }

    public function getUsuarios($per_page = 10)
    {
        try {
            if ($this->isAdminUser()) {
                return response()->json([
                    'status' => 0,
                    'message' => '',
                    'data' => User::select('id', 'name', 'email', 'status', 'imagem')->paginate($per_page)
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

    private function isAdminUser()
    {
        return User::where('users.id', Auth::id())->join('lt_admin_users', 'users.id', 'lt_admin_users.id_user')->exists();
    }
}
