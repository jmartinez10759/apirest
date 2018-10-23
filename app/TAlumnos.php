<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TAlumnos extends Model
{
    public $table = "t_alumnos";
    public $primaryKey = 'id_t_usuarios';
    public $timestamps = false;
	public $fillable = [
        'id_t_usuarios'
		,'nombre'
		,'ap_paterno'
		,'ap_materno'
		,'activo'
	];

	public function calificaciones(){
		return $this->belongsTo(TCalificaciones::class,'id_t_calificaciones','id_t_usuarios');
	}
	public function materias(){
		return $this->belongsTo(TCalificaciones::class,'id_t_calificaciones','id_t_materias');
	}
	

}
