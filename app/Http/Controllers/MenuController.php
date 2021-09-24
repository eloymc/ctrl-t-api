<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class MenuController extends Controller
{
    public function add(Request $request)
    {

        $validated = Validator::make($request->all(), [
            'label' => 'required|string',
            'nivel' => 'required|integer',
        ]);
        if (!$validated->fails()) {
            $menu = new Menu;
            $menu->label = $request->label;
            $menu->nivel = $request->nivel;
            $menu->orden = $request->orden;
            $menu->id_padre = $request->id_padre;
            $menu->icon = $request->icon;
            $menu->routerLink = $request->routerLink;
            $menu->url = $request->url;

            if ($menu->save()) {
                return response([
                    "mensage" => "Menu guardado con exito"
                ], 200);
            } else {
                return response([
                    "mensage" => "Error al guardar el menu"
                ], 402);
            }
        } else {
            return response([
                "mensage" => "falta informacion menu"
            ], 402);
        }
    }

    public function edit()
    {
    }

    public function delete()
    {
    }

    public function menu(Request $request)
    {
        $usuario = $request->usuario;
        $roles = User::find($usuario)->userRoles()->pluck('id_rol');
        $nivel_1 = Menu::where('menu.nivel', 1)
            ->join('menu as m2', 'menu.id','=','m2.id_padre')
            ->join('menu_roles', 'm2.id', '=', 'menu_roles.id_menu')
            ->whereIn('menu_roles.id_rol', $roles)
            ->select('menu.*')
            ->orderBy('menu.orden');
        $menu = $nivel_1->get();
        foreach ($menu as $n1) {
            
            $submenu = Menu::where('menu.nivel', 2)
                ->join('menu_roles', 'menu.id', '=', 'menu_roles.id_menu')
                ->where('menu.id_padre', $n1->id)
                ->whereIn('menu_roles.id_rol', $roles)
                ->select('menu.*')
                ->orderBy('menu.orden')
                ->get();

            $n1->items = $submenu;
        }
        return response()->json($menu);
    }
}
