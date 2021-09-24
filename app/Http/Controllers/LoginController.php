<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function login(Request $request){
        if( !Auth::attempt(['email' => $request->email, 'password' => $request->password])){

            return response([
                "mensage" => "Usuario y/o Contraseña son invalidas"
            ],401);
        }
        $accessToken = Auth::user()->createToken('authTestToken')->accessToken;
        return response([
            "user" => Auth::user(),
            "access_token" => $accessToken,
        ],200);
    }

    public function accessFail(){
        return response([
            "mensage" => "Error al ingresar"
        ],401);
    }

    public function updatePassword(Request $request){
        if( !Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            return response([
                "mensage" => "Error al actualizar la contraseña"
            ],402);
        }
        $password = Hash::make($request->new_password);
        $user = User::where('email',$request->email)
            ->update(['password' => $password]);
        return response([
            "mensage" => "Usuario guardado con exito"
        ],200);
    }
}
