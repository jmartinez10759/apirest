<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\MasterController;
use App\TAlumnos;
use App\TCalificaciones;
use App\TMaterias;

class AlumnosController extends MasterController
{
 #se crea las propiedades
    public function __construct(){
        parent::__construct();
        $this->middleware('token', ['only' => ['index','show','store','update','destroy'] ]);
    }
    /**
    *Metodo para obtener la vista y cargar los datos
    *@access public
    *@param Request $request [Description]
    *@return void
    */
    public function index(){
        try {
        	$response = TCalificaciones::get();
        	$data = [];
        	foreach ($response as $responses) {        		
	        	$data[] = [
	        		'id_t_usuarios' 	=> $responses->id_t_usuarios
	        		,'nombre' 			=> $this->alumnos($responses->id_t_usuarios)->nombre
	        		,'apellido' 		=> $this->alumnos($responses->id_t_usuarios)->ap_paterno
	        		,'materia' 			=> $this->materias($responses->id_t_materias)->nombre
	        		,'calificacion' 	=> $responses->calificacion
	        		,'fecha_registro' 	=> date('d/m/Y',strtotime($responses->fecha_registro))
	        	];
        	}
          return $this->_message_success( 200, $data , self::$message_success );
        } catch (\Exception $e) {
            $error = $e->getMessage()." ".$e->getLine()." ".$e->getFile();
            return $this->show_error(6, $error, self::$message_error );
        } 
    }
    /**
     *Metodo para obtener los datos de manera asicronica.
     *@access public
     *@param Request $request [Description]
     *@return void
     */
    public function all( Request $request ){

        try {


          return $this->_message_success( 201, $response , self::$message_success );
        } catch (\Exception $e) {
            $error = $e->getMessage()." ".$e->getLine()." ".$e->getFile();
            return $this->show_error(6, $error, self::$message_error );
        }

    }
    /**
    *Metodo para realizar la consulta por medio de su id
    *@access public
    *@param Request $request [Description]
    *@return void
    */
    public function show( $id ){

        try { 
        	$promedio = [];       	
        	$response = TCalificaciones::where(['id_t_usuarios' => $id ])->get();        	
        	foreach ($response as $responses) {
	        	$promedio[] = $responses->calificacion;
	        	$data[] = [
	        		'id_t_usuarios' 	=> $responses->id_t_usuarios
	        		,'nombre' 			=> $this->alumnos($responses->id_t_usuarios)->nombre
	        		,'apellido' 		=> $this->alumnos($responses->id_t_usuarios)->ap_paterno
	        		,'materia' 			=> $this->materias($responses->id_t_materias)->nombre
	        		,'calificacion' 	=> $responses->calificacion
	        		,'fecha_registro' 	=> date('d/m/Y',strtotime($responses->fecha_registro))
	        	];
        	}
        	$suma = 0;
        	for ($i=0; $i < count($promedio); $i++) { 
        		$suma += $promedio[$i];
        	}
        	$data[ $this->endKey($data) ]['promedio'] =  number_format($suma/count($promedio),2);

        return $this->_message_success( 200, $data , self::$message_success );
        } catch (\Exception $e) {
        $error = $e->getMessage()." ".$e->getLine()." ".$e->getFile();
        return $this->show_error(6, $error, self::$message_error );
        }

    }
    /**
    *Metodo para
    *@access public
    *@param Request $request [Description]
    *@return void
    */
    public function store( Request $request){

        $error = null;
        DB::beginTransaction();
        try {

        	$datos = [];
        	$key_values = ['materias'];
        	foreach ($request->all() as $key => $value) {
        		if(!in_array($key, $key_values)){
        			if( $value == ""){
        				return $this->show_error(3, $key, self::$message_error );
        			}
        			$datos[$key] = $value;
        		}
        	}
        	#dd($request->materias);
        	$alumnos = TAlumnos::create($datos);
        	$id_materias = [];
        	foreach ($request->materias as $key => $value) {
        		$materias = TMaterias::select('id_t_materias')->where(['nombre' => $key])->get();
        		$id_materias[] = (isset($materias[0]) )?$materias[0]->id_t_materias: 0 ;
        		$calificacion[] = $value; 
        	}
        	for ($i=0; $i < count($id_materias); $i++) { 
	        	$data = [
	        		'id_t_materias'   => $id_materias[$i]
			 		,'id_t_usuarios'  => $alumnos->id_t_usuarios
			 		,'calificacion'   => $calificacion[$i]
			 		,'fecha_registro' => date('Y-m-d')
	        	];
	        	$select = TCalificaciones::where(['id_t_materias' => $id_materias[$i],'id_t_usuarios' => $alumnos->id_t_usuarios])->get();
	        	#dd($select);
	        	if( count($select) > 0){
        			TCalificaciones::where(['id_t_materias' => $id_materias[$i],'id_t_usuarios' => $alumnos->id_t_usuarios])->update($data);
	        	}else{
        			TCalificaciones::create($data);
	        	}
        		
        	}
        	$response = "Se creo el registro con exito";
        DB::commit();
        $success = true;
        } catch (\Exception $e) {
        $success = false;
        $error = $e->getMessage()." ".$e->getLine()." ".$e->getFile();
        DB::rollback();
        }

        if ($success) {
        return $this->_message_success( 201, $response , self::$message_success );
        }
        return $this->show_error(6, $error, self::$message_error );


    }
    /**
    *Metodo para la actualizacion de los registros
    *@access public
    *@param Request $request [Description]
    *@return void
    */
    public function update( $id ,Request $request){

        $error = null;
        DB::beginTransaction();
        try {
        	$datos = [];
        	$key_values = ['materias'];
        	foreach ($request->all() as $key => $value) {
        		if(!in_array($key, $key_values)){
        			if( $value == ""){
        				return $this->show_error(3, $key, self::$message_error );
        			}
        			$datos[$key] = $value;
        		}
        	}
        	#dd($request->materias);
        	$alumnos = TAlumnos::where(['id_t_usuarios' => $id])->update($datos);
        	$id_materias = [];
        	foreach ($request->materias as $key => $value) {
        		$materias = TMaterias::select('id_t_materias')->where(['nombre' => $key])->get();
        		$id_materias[] = (isset($materias[0]) )?$materias[0]->id_t_materias: 0 ;
        		$calificacion[] = $value; 
        	}
        	#dd($id_materias);
        	for ($i=0; $i < count($id_materias); $i++) { 
	        	$data = [
	        		'id_t_materias'   => $id_materias[$i]
			 		,'id_t_usuarios'  => $id
			 		,'calificacion'   => $calificacion[$i]
			 		,'fecha_registro' => date('Y-m-d')
	        	];
	        	$select = TCalificaciones::where(['id_t_materias' => $id_materias[$i],'id_t_usuarios' => $id])->get();
	        	#dd($select);
	        	if( count($select) > 0){
        			TCalificaciones::where(['id_t_materias' => $id_materias[$i],'id_t_usuarios' => $id])->update($data);
	        	}else{
        			TCalificaciones::create($data);
	        	}
        		
        	}

        	// $response = TAlumnos::with(['materias' => function($query){
        	// 	return $query->with(['calificaciones'])->get();
        	// }])->where(['id_t_usuarios' => $id])->get();
        	$response = "Se actualizo el registro con exito";
        DB::commit();
        $success = true;
        } catch (\Exception $e) {
        $success = false;
        $error = $e->getMessage()." ".$e->getLine()." ".$e->getFile();
        DB::rollback();
        }

        if ($success) {
        return $this->_message_success( 201, $response , self::$message_success );
        }
        return $this->show_error(6, $error, self::$message_error );

    }
    /**
    * Metodo para borrar el registro
    * @access public
    * @param Request $request [Description]
    * @return void
    */
    public function destroy( $id ){

        $error = null;
        DB::beginTransaction();
        try {
        	TAlumnos::where(['id_t_usuarios' => $id])->delete();
        	TCalificaciones::where(['id_t_usuarios' => $id])->delete();
        	$response = "Se Borro con exito el registro";
        DB::commit();
        $success = true;
        } catch (\Exception $e) {
        $success = false;
        $error = $e->getMessage()." ".$e->getLine()." ".$e->getFile();
        DB::rollback();
        }

        if ($success) {
        return $this->_message_success( 201, $response , self::$message_success );
        }
        return $this->show_error(6, $error, self::$message_error );

    }
    /**
    * Metodo para borrar el registro
    * @access public
    * @param Request $request [Description]
    * @return void
    */
    public function alumnos( $id ){
         try {        	
        	return TAlumnos::where(['id_t_usuarios' => $id ])->get()[0];        
        } catch (\Exception $e) {
        	return $e->getMessage()." ".$e->getLine()." ".$e->getFile();
        }

    }

    /**
    * Metodo para borrar el registro
    * @access public
    * @param Request $request [Description]
    * @return void
    */
    public function materias( $id ){
         try {        	
        	return TMaterias::where(['id_t_materias' => $id ])->get()[0];        
        } catch (\Exception $e) {
        $error = $e->getMessage()." ".$e->getLine()." ".$e->getFile();
        	return $error;
        }

    }

   public function endKey( $array ){
    	end( $array );
    	return key( $array );
	}


}
