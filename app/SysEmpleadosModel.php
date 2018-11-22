<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysEmpleadosModel extends Model
{
    public $table = "sys_empleados";
    public $fillable = [
        'id', 'nombre', 'email', 'puesto','fecha_nacimiento','domicilio'
    ];

    public function skills()
    {
        return $this->hasMany(SysSkillsModel::class, 'id_empleado', 'id');
    }
}
