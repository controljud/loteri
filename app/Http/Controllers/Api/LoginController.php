<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\DB;

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
        try {
            if ($this->isAdminUser()) {
                $usuario = new User;
                $usuario->name = $request->name;
                $usuario->email = $request->email;
                $usuario->password = Hash::make($request->password);

                $usuario->status = 1;
                $usuario->user_type = 2;

                $usuario->save();

                $retorno = [
                    "status" => 0,
                    "message" => "Cadastro realizado com sucesso",
                    "data" => null
                ];
                
                return response()->json($retorno);
            }

            return response()->json([
                "status" => 1,
                "message" => "Usuário sem acesso a essa funcionalidade",
                "data" => null
            ], 400);
        } catch (Exception $ex) {
            Log::error("Erro na gravação do usuário: " . $ex->getMessage());

            return response()->json([
                "status" => 1,
                "message" => "Erro na gravação do usuário",
                "data" => null
            ], 500);
        }
    }

    public function postUsuario(Request $request)
    {
        try {
            $now = Carbon::now();
            if ($this->isAdminUser()) {
                if ($request->id) {
                    $usuario = User::where('id', $request->id)->first();
                } else {
                    $usuario = new User;
                }

                $usuario->name = $request->name;
                $usuario->email = $request->email;
                $usuario->password = Hash::make("123456");

                if ($request->status) {
                    $usuario->status = 1;
                } else {
                    $usuario->status = null;
                }

                if ($request->tipo) {
                    if ($tipo = DB::table('lt_user_types')->where('tipo', $request->tipo)->first()) {
                        $usuario->user_type = $tipo->id;
                    } else {
                        $usuario->user_type = 2;
                    }
                }

                $usuario->save();

                $retorno = [
                    "status" => 0,
                    "message" => "Cadastro realizado com sucesso",
                    "data" => null
                ];
                
                return response()->json($retorno);
            }

            return response()->json([
                "status" => 1,
                "message" => "Usuário sem acesso a essa funcionalidade",
                "data" => null
            ], 400);
        } catch (Exception $ex) {
            Log::error("Erro na gravação do usuário: " . $ex->getMessage());

            return response()->json([
                "status" => 1,
                "message" => "Erro na gravação do usuário",
                "data" => null
            ], 500);
        }
    }

    public function deleteUsuario($id)
    {
        try {
            if ($this->isAdminUser()) {
                if ($usuario = User::where('id', $id)->first()) {
                    $usuario->delete();

                    return response()->json([
                        "status" => 0,
                        "message" => "Usuário excluído com sucesso",
                        "data" => null
                    ]);
                }

                return response()->json([
                    "status" => 1,
                    "message" => "Usuário não encontrado",
                    "data" => null
                ], 400);
            }

            return response()->json([
                "status" => 1,
                "message" => "Usuário sem acesso a essa funcionalidade",
                "data" => null
            ], 400);
        } catch (Exception $ex) {
            Log::error("Erro na exclusão do usuário: " . $ex->getMessage());

            return response()->json([
                "status" => 1,
                "message" => "Erro na exclusão do usuário",
                "data" => null
            ], 500);
        }
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
                $usuarios = User::select('users.id', 'name', 'email', 'status', 'users.user_type', 'lt_user_types.tipo', 'imagem')
                    ->leftJoin('lt_user_types', 'lt_user_types.id', 'users.user_type')
                    ->paginate($per_page);

                return response()->json([
                    'status' => 0,
                    'message' => '',
                    'data' => $usuarios
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

    public function getTiposUsuario()
    {
        try {
            if ($this->isAdminUser()) {
                $tipos = DB::table('lt_user_types')->get();

                return response()->json([
                    'status' => 0,
                    'message' => 'Tipos retornados com sucesso',
                    'data' => $tipos
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
        return User::where('users.id', Auth::id())
            ->join('lt_user_types', 'users.user_type', 'lt_user_types.id')
            ->where('lt_user_types.id', 1)
            ->exists();
    }
}
