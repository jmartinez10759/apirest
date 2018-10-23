<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

abstract class MasterController extends Controller
{
	public static $_client;
	public $_tipo_user;
	public static $_domain = "";
	protected $tipo = "application/json";
	public $_http;
	public static $_model;
	protected static $message_success;
	protected static $message_error;
	protected static $ssl_ruta = [];

	public function __construct()
	{
      #self::$ssl_ruta = ["verify" => $_SERVER['DOCUMENT_ROOT']. "/cacert.pem"];
		self::$ssl_ruta = ["verify" => false];
		self::$_client = new Client(self::$ssl_ruta);
		self::$message_success = "¡Transacción Exitosa!";
		self::$message_error = "¡Ocurrio un error, favor de verificar!";
	}
	/**
	 * Metodo general para consumir endpoint utilizando una clase de laravel
	 * @access protected
	 * @param  url [description]
	 * @param  header [description]
	 * @param  data [description]
	 * @return json [description]
	 */
	protected static function endpoint($url = false, $headers = [], $data = [], $method = false)
	{
		$response = self::$_client->$method($url, ['headers' => $headers, 'body' => json_encode($data)]);
		$zonerStatusCode = $response->getStatusCode();
		return json_decode($response->getBody());
	}
	/**
	 *Metodo donde muestra el mensaje de success
	 *@access protected
	 *@param integer $code [Envia la clave de codigo.]
	 *@param array $data [envia la informacion correcta ]
	 *@return json
	 */
	protected function _message_success($code = false, $data = [], $message = false)
	{
		$code = ($code) ? $code : 200;
		$datos = [
			"success" => true,
			"message" => ($message) ? $message : "Transacción exitosa",
			"code" => "SYS-" . $code . "-" . $this->setCabecera($code),
			"result" => $data
		];
		return response()->json($datos, $code);
	}
	/**
	 *Metodo para establecer si se realizo con exito la peticion
	 *@access private
	 *@param $codigo [description]
	 *@return string [description]
	 */
	private function get_status_message($codigo = false)
	{
		$estado = array(
			200 => 'OK',
			201 => 'Created',
			202 => 'Accepted',
			204 => 'No Content',
			301 => 'Moved Permanently',
			302 => 'Found',
			303 => 'See Other',
			304 => 'Not Modified',
			400 => 'Bad Request',
			401 => 'Unauthorized',
			403 => 'Forbidden',
			404 => 'Not Found',
			405 => 'Method Not Allowed',
			409 => 'Conflict',
			412 => 'Precondition Failed',
			500 => 'Internal Server Error'
		);
		return ($estado[$codigo]) ? $estado[$codigo] : $estado[500];
	}
	/**
	 *Se crea un metodo para mostrar los errores dependinedo la accion a realizar
	 *@access protected
	 *@param integer $id [ Coloca el indice para mandar el error que corresponde. ]
	 *@param array $datos [ Envia la informacion para pintar el error. ]
	 *@return string $errores
	 */
	protected function show_error($id = false, $datos = [], $message = false)
	{

		switch ($id) {
			case 0:
				$codigo = 401;
				break;
			case 1:
				$codigo = 409;
				break;
			case 2:
				$codigo = 500;
				break;
			case 3:
				$codigo = 412;
				break;
			case 4:
				$codigo = 400;
				break;
			case 5:
				$codigo = 400;
				break;
			case 6:
												#$codigo = 304;
				$codigo = 400;
				break;
		}

		$errors = [
			#0
			[
				'success' => false,
				'message' => ($message) ? $message : "Acceso Denegado",
				'code' => "SYS-" . $codigo . "-" . $this->setCabecera($codigo),
				'error' => ['description' => "No tiene permisos para realizar esta acción"],
				'result' => $datos
			],
			#1
			[
				'success' => false,
				'message' => ($message) ? $message : "Error en la transacción",
				'code' => "SYS-" . $codigo . "-" . $this->setCabecera($codigo),
				'error' => ['description' => "Token expiro, favor de verificar"],
				'result' => $datos

			],
			#2
			[
				'success' => false,
				'message' => ($message) ? $message : "Petición Incorrecta",
				'code' => "SYS-" . $codigo . "-" . $this->setCabecera($codigo),
				'error' => ['description' => "El Servicio de Internet es Incorrecto"],
				'result' => $datos
			],
			#3
			[
				'success' => false,
				'message' => ($message) ? $message : "Registros ingresados incorrectos",
				'code' => "SYS-" . $codigo . "-" . $this->setCabecera($codigo),
				'error' => ['description' => "Verificar los campos solicitados."],
				'result' => $datos

			],
			#4
			[
				'success' => false,
				'message' => ($message) ? $message : "Sin Registros",
				'code' => "SYS-" . $codigo . "-" . $this->setCabecera($codigo),
				'error' => ['description' => "No se encontro ningún registro"],
				'result' => $datos
			],
			#5
			[
				'success' => false,
				'message' => ($message) ? $message : "Sin Registros",
				'code' => "SYS-" . $codigo . "-" . $this->setCabecera($codigo),
				'error' => ['description' => "Ingrese datos para poder realizar la acción"],
				'result' => $datos
			],
			#6
			[
				'success' => false,
				'message' => ($message) ? $message : "Error en la Transacción",
				'code' => "SYS-" . $codigo . "-" . $this->setCabecera($codigo),
				'error' => ['description' => "Ocurrio un error en el registro solicitado"],
				'result' => $datos
			]

		];
		return response()->json($errors[$id], $codigo);

	}
	/**
	 * Se crea un metodo en el cual se establece el formato en el que se enviara la informacion del REST
	 * @access protected
	 * @param $codigo int [description]
	 * @return void
	 */
	protected function setCabecera($codigo)
	{
		header("HTTP/1.0 " . $codigo . " " . $this->get_status_message($codigo));
		header("Content-Type:" . $this->tipo);
		header("status:" . $codigo);
		return $this->get_status_message($codigo);
	}


}
