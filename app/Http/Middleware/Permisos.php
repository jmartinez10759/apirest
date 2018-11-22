<?php

namespace App\Http\Middleware;

use Closure;

class Permisos
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //$http_usuario   = isset( $_SERVER['HTTP_USUARIO'] )? $_SERVER['HTTP_USUARIO']:null;
        $http_token     = isset( $_SERVER['HTTP_TOKEN'] )? $_SERVER['HTTP_TOKEN']:null;
        #dd($http_token);
        $token = "rMKEVjXUjuFnnFnHRp6J9hjrLS4EdBXT7ox3kBs4pSPzCRPZ86";
        if( $http_token == $token){
            return $next($request);
        }else{
            $datos = [
                'success' => false,
                'message' => "Error en la transacción",
                'code' => "SYS-" . "409",
                'error' => ['description' => "Token expiro, favor de verificar"],
            ];
            return response()->json($datos, 409);
        }

    }
}
