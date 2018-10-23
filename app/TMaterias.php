<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TMaterias extends Model
{
    public $table = "t_materias";
    public $primaryKey = 'id_t_materias';
    public $timestamps = false;
	public $fillable = [
        'id_t_materias'
        ,'nombre'
        ,'activo'
	];

	public function calificaciones(){
		return $this->belongsTo(TCalificaciones::class,'id_t_calificaciones','id_t_materias');
	}
	public function alumnos(){
		return $this->belongsTo(TCalificaciones::class,'id_t_calificaciones','id_t_usuarios');
	}

}
