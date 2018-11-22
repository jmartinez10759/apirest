<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\MasterController;
use App\SysSkillsModel;
use App\SysEmpleadosModel;

class EmpleadosController extends MasterController
{
   #se crea las propiedades
    public function __construct()
    {
        parent::__construct();
        $this->middleware('token', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
    }
    /**
     *Metodo para obtener la vista y cargar los datos
     *@access public
     *@param Request $request [Description]
     *@return void
     */
    public function index()
    {
        try {
            $response = SysEmpleadosModel::with(['skills:id,id_empleado,nombre,calificacion'])->get();
            //dd($response);
            $data = [];
            foreach ($response as $responses) {
                $data[] = [
                    'id'                 => $responses->id
                    , 'nombre'           => $responses->nombre
                    , 'email'            => $responses->email
                    , 'puesto'           => $responses->puesto
                    , 'fecha_nacimiento' => date('d/m/Y', strtotime($responses->fecha_nacimiento))
                    , 'domicilio'        => $responses->domicilio
                    , 'skills'           => $responses->skills   
                ];
            }
            return $this->_message_success(200, $data, self::$message_success);
        } catch (\Exception $e) {
            $error = $e->getMessage() . " " . $e->getLine() . " " . $e->getFile();
            return $this->show_error(6, $error, self::$message_error);
        }
    }
    /**
     *Metodo para obtener los datos de manera asicronica.
     *@access public
     *@param Request $request [Description]
     *@return void
     */
    public function all(Request $request)
    {

        try {


            return $this->_message_success(201, $response, self::$message_success);
        } catch (\Exception $e) {
            $error = $e->getMessage() . " " . $e->getLine() . " " . $e->getFile();
            return $this->show_error(6, $error, self::$message_error);
        }

    }
    /**
     *Metodo para realizar la consulta por medio de su id
     *@access public
     *@param Request $request [Description]
     *@return void
     */
    public function show($id)
    {

        try {
            $response = SysEmpleadosModel::with(['skills:id,id_empleado,nombre,calificacion'])->where(['id' => $id])->get();
            $data = [];
            foreach ($response as $responses) {
                $data[] = [
                    'id'                 => $responses->id
                    , 'nombre'           => $responses->nombre
                    , 'email'            => $responses->email
                    , 'puesto'           => $responses->puesto
                    , 'fecha_nacimiento' => date('d/m/Y', strtotime($responses->fecha_nacimiento))
                    , 'domicilio'        => $responses->domicilio
                    , 'skills'           => $responses->skills   
                ];
            }
            return $this->_message_success(200, $data, self::$message_success);
        } catch (\Exception $e) {
            $error = $e->getMessage() . " " . $e->getLine() . " " . $e->getFile();
            return $this->show_error(6, $error, self::$message_error);
        }

    }
    /**
     *Metodo para
     *@access public
     *@param Request $request [Description]
     *@return void
     */
    public function store(Request $request)
    {
        $error = null;
        DB::beginTransaction();
        try {
            $data = []; 
            foreach ($request->all() as $key => $value) {
                if( $key != "skills"){
                    if( $value == ""){
                        return $this->show_error(6, "Verificar campo vacio: " . $key, self::$message_error);
                    }else{
                        $data[$key] = $value; 
                    }
                }
            }
            if( !$this->_emailValidate( $request->email ) ){
                return $this->show_error(6,"Verificar correo electronico: ". $request->email , self::$message_error);
            }
            foreach ($request->skills as $skills) {
                if( $skills['calificacion'] > 5 ){
                    return $this->show_error(6, "Verificar Calificacion es de " . $skills['nombre'] . " es mayor a 5 : " . $skills['calificacion'], self::$message_error);
                }
            }
            $response = SysEmpleadosModel::create($data);
            $data_skills = [];
            foreach ($request->skills as $skills) {
                $data_skills = [
                    'id_empleado'       => $response->id
                    ,'nombre'           => $skills['nombre']
                    ,'calificacion'     => $skills['calificacion']
                ];
                SysSkillsModel::create($data_skills);
            }

            DB::commit();
            $success = true;
        } catch (\Exception $e) {
            $success = false;
            $error = $e->getMessage() . " " . $e->getLine() . " " . $e->getFile();
            DB::rollback();
        }

        if ($success) {
            return $this->show($response->id);
            #return $this->_message_success(201, $data, self::$message_success);
        }
        return $this->show_error(6, $error, self::$message_error);


    }
    /**
     *Metodo para la actualizacion de los registros
     *@access public
     *@param Request $request [Description]
     *@return void
     */
    public function update($id, Request $request)
    {

        $error = null;
        DB::beginTransaction();
        try {

            $data = [];
            foreach ($request->all() as $key => $value) {
                if ($key != "skills") {
                    if ($value == "") {
                        return $this->show_error(6, "Verificar campo vacio: " . $key, self::$message_error);
                    } else {
                        $data[$key] = $value;
                    }
                }
            }
            if (!$this->_emailValidate($request->email)) {
                return $this->show_error(6, "Verificar correo electronico: " . $request->email, self::$message_error);
            }
            foreach ($request->skills as $skills) {
                if ($skills['calificacion'] > 5) {
                    return $this->show_error(6, "Verificar Calificacion es de ".$skills['nombre']." es mayor a 5 : " . $skills['calificacion'], self::$message_error);
                }
            }
            $response = SysEmpleadosModel::where(['id' => $id])->update($data);
            SysSkillsModel::where(['id_empleado' => $id])->delete();
            $data_skills = [];
            foreach ($request->skills as $skills) {
                $data_skills = [
                    'id_empleado'       => $id
                    ,'nombre'           => $skills['nombre']
                    ,'calificacion'     => $skills['calificacion']
                ];
                SysSkillsModel::create($data_skills);
            }

            DB::commit();
            $success = true;
        } catch (\Exception $e) {
            $success = false;
            $error = $e->getMessage() . " " . $e->getLine() . " " . $e->getFile();
            DB::rollback();
        }

        if ($success) {
            return $this->show($id);
        }
        return $this->show_error(6, $error, self::$message_error);

    }
    /**
     * Metodo para borrar el registro
     * @access public
     * @param Request $request [Description]
     * @return void
     */
    public function destroy($id)
    {

        $error = null;
        DB::beginTransaction();
        try {
            SysEmpleadosModel::where(['id' => $id])->delete();
            SysSkillsModel::where(['id_empleado' => $id])->delete();
            $response = "Se Borro con exito el registro";
            DB::commit();
            $success = true;
        } catch (\Exception $e) {
            $success = false;
            $error = $e->getMessage() . " " . $e->getLine() . " " . $e->getFile();
            DB::rollback();
        }

        if ($success) {
            return $this->_message_success(201, $response, self::$message_success);
        }
        return $this->show_error(6, $error, self::$message_error);

    } 
    private function _emailValidate( $email )
    {
        $regex = '/^[-\w.%+]{1,64}@(?:[A-Z0-9-]{1,63}\.){1,125}[A-Z]{2,63}$/i';
        return preg_match($regex, $email);
    }
    

}
