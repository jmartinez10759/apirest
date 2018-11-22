<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysSkillsModel extends Model
{
    public $table = "sys_skills";
    public $fillable = [
        'id', 'id_empleado', 'nombre', 'calificacion'
    ];

    public function empleados()
    {
        return $this->belongsTo(SysEmpleadosModel::class, 'id', 'id_empleado');
    }

}
