<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TCalificaciones extends Model
{
    public $table = "t_calificaciones";
    public $primaryKey = 'id_t_calificaciones';
    public $timestamps = false;
	public $fillable = [
 		'id_t_calificaciones'
 		,'id_t_materias'
 		,'id_t_usuarios'
 		,'calificacion'
 		,'fecha_registro'
	];

	public function materias(){
		return $this->hasOne( TMaterias::class,'id_t_materias','id_t_calificaciones');
	}
	
	public function alumnos(){
		return $this->hasOne(TAlumnos::class,'id_t_usuarios','id_t_calificaciones');
	}


}
