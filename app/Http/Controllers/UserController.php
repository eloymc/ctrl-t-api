<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Persona;
use App\Models\Direccion;
use App\Models\Telefono;
use App\Mail\NuevoAcceso;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Exception;

class UserController extends Controller
{
    public function all()
    {
        return response(User::all());
    }

    public function register(Request $request)
    {
        $cuentaEmail = User::where('email', $request->email)->count();
        if ($cuentaEmail > 0) {
            return response([
                "mensage" => "Ya existe un correo registrado"
            ], 402);
        }
        $user = new User;
        $password = Hash::make($request->password);
        $user->email = $request->email;
        $user->password = $password;
        $user->name = $request->name;
        if ($user->save()) {
            return response([
                "mensage" => "Usuario guardado con exito"
            ], 200);
        } else {
            return response([
                "mensage" => "Error al guardar el usuario"
            ], 402);
        }
    }

    public function activo(Request $request)
    {
        User::where('email', $request->email)
            ->update(['activo' => $request->activo]);
        return response([
            "mensage" => "Usuario guardado con exito"
        ], 200);
    }

    public function informacion(Request $request)
    {
        $id = $request->id;
        $usuario = User::where('id', $id)->select(
            'id',
            'name',
            'email',
            'activo',
            'id_persona',
            'id_direccion',
            'id_telefono_1',
            'id_telefono_2'
        )->first();
        $usuario->persona = null;
        $usuario->direccion = null;
        $usuario->telefono_1 = null;
        $usuario->telefono_2 = null;
        if ($usuario->id_persona)
            $usuario->persona = Persona::where('id', $usuario->id_persona)->first();
        if ($usuario->id_direccion)
            $usuario->direccion = Direccion::where('id', $usuario->id_direccion)->first();
        if ($usuario->id_telefono_1)
            $usuario->telefono_1 = Telefono::where('id', $usuario->id_telefono_1)->first();
        if ($usuario->id_telefono_2)
            $usuario->telefono_2 = Telefono::where('id', $usuario->id_telefono_2)->first();
        return response()->json($usuario, 200);
    }

    public function buscarUsuario(Request $request)
    {
        $buscar = $request->q;
        $usuarios = User::join('personas', 'personas.id', 'users.id_persona')
            ->select('users.id', DB::raw("concat(personas.nombre,' ',personas.apellido_p,' ',personas.apellido_m) as nombre_completo"));
        $otros_usuarios = User::whereNull('id_persona')
            ->select('id', 'name as nombre_completo')
            ->union($usuarios);
        $users = $otros_usuarios->get();
        $usuarios_filtrados = $users->filter(function ($item) use ($buscar) {
            return false !== stristr($item->nombre_completo, $buscar);
        })->values();
        return response()->json($usuarios_filtrados, 200);
    }

    public function guardar(Request $request)
    {
        $datos = $request->datos;
        DB::beginTransaction();

        try {
            $persona = ($datos['persona']['id'] > 0) ? Persona::find($datos['persona']['id']) : new Persona;
            $persona->nombre = $datos['persona']['nombre'];
            $persona->apellido_p = $datos['persona']['apellido_p'];
            $persona->apellido_m = $datos['persona']['apellido_m'];
            $persona->fecha_nacimiento = $datos['persona']['fecha_nacimiento'];
            $persona->genero = $datos['persona']['genero'];
            $persona->save();
            $direccion = ($datos['direccion']['id'] > 0) ? Direccion::find($datos['direccion']['id']) : new Direccion;
            $direccion->calle = $datos['direccion']['calle'];
            $direccion->no_ext = $datos['direccion']['no_ext'];
            $direccion->no_int = $datos['direccion']['no_int'];
            $direccion->colonia = $datos['direccion']['colonia'];
            $direccion->municipio = $datos['direccion']['municipio'];
            $direccion->estado = $datos['direccion']['estado'];
            $direccion->pais = $datos['direccion']['pais'];
            $direccion->cp = $datos['direccion']['cp'];
            $direccion->referencia = $datos['direccion']['referencia'];
            $direccion->save();
            $tel1 = $tel2 = false;
            if ($datos['telefono_1']['numero'] != null) {
                $telefono_1 = ($datos['telefono_1']['id'] > 0) ? Telefono::find($datos['telefono_1']['id']) : new Telefono;
                $telefono_1->numero = $datos['telefono_1']['numero'];
                $telefono_1->tipo = $datos['telefono_1']['tipo'];
                $telefono_1->save();
                $tel1 = true;
            }
            if ($datos['telefono_2']['numero'] != null) {
                $telefono_2 = ($datos['telefono_1']['id'] > 0) ? Telefono::find($datos['telefono_2']['id']) : new Telefono;
                $telefono_2->numero = $datos['telefono_2']['numero'];
                $telefono_2->tipo = $datos['telefono_2']['tipo'];
                $telefono_2->save();
                $tel2 = true;
            }
            $usuario = ($datos['id'] > 0) ? User::find($datos['id']) : new User;
            $usuario->name = ($datos['name'] == null) ? $datos['persona']['nombre'] : $datos['name'];
            $usuario->email = $datos['email'];
            $nuevo_password = $this->nuevoPassword();
            $hash_password = Hash::make($nuevo_password);
            $usuario->password = $hash_password;
            $usuario->activo = $datos['activo'];
            if ($datos['persona']['id'] == 0) {
                $usuario->id_persona = $persona->id;
            }
            if ($datos['direccion']['id'] == 0) {
                $usuario->id_direccion = $direccion->id;
            }
            if ($tel1 && $datos['telefono_1']['id'] == 0) {
                $usuario->id_telefono_1 = $telefono_1->id;
            }
            if ($tel2 && $datos['telefono_2']['id'] == 0) {
                $usuario->id_telefono_2 = $telefono_2->id;
            }
            $usuario->save();
            DB::commit();
            if ($datos['id'] == 0) {
                
                $this->enviarCorreoPassword([$datos['email']],$nuevo_password);
            }
            return response()->json($request->datos, 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json("Error", 400);
        }   
    }

    public function reiniciarPasssword(Request $request){
        $user = User::where($request->email);
        $nuevo_password = $this->nuevoPassword();
        $user->password = Hash::make($nuevo_password);
        $this->enviarCorreoPassword($request->email,$nuevo_password);
        return response()->json("Correo enviado", 200);
    }

    public function enviarCorreoPassword($email,$password){
        $info = array(
            "email" => $email,
            "password" => $password
        );
        Mail::to($email)->send(new NuevoAcceso($info));
    }

    public function nuevoPassword(){
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 10; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
