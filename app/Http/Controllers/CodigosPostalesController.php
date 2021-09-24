<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\CodigosPostales;

class CodigosPostalesController extends Controller
{
    
    
    public function dameCP(Request $request){
        $cp = $request->cp;
        $estado = CodigosPostales::where('cp',$cp)->select('estado')->groupBy('estado')->first();
        $municipio = CodigosPostales::where('cp',$cp)->select('municipio')->groupBy('municipio')->first();
        $colonias = CodigosPostales::where('cp',$cp)->select('asentamiento','tipo_asentamiento')->get();
        $respuesta = array(
            "estado"=>$estado,
            "municipio"=>$municipio,
            "colonias"=>$colonias
        );
        return response()->json($respuesta,200);
    }

    public static function subirCP(){
        $contenido = Storage::get('cps/CPdescarga.txt');
        $contenido_separado = explode("\n",$contenido);
        foreach($contenido_separado as $key=>$linea){
            if($key > 1){
                $datos = explode("|",$linea);
                if($datos[0] == ''){
                    continue;
                }
                $cp = new CodigosPostales();
                $cp->cp = utf8_encode($datos[0]);
                $cp->asentamiento = utf8_encode($datos[1]);
                $cp->tipo_asentamiento = utf8_encode($datos[2]);
                $cp->municipio = utf8_encode($datos[3]);
                $cp->estado = utf8_encode($datos[4]);
                $cp->ciudad = utf8_encode($datos[5]);
                $cp->save();
                echo utf8_encode($datos[0])." ".utf8_encode($datos[1])."\n";
            }
        }
        return "Listo... ya acabo";
    }
}
