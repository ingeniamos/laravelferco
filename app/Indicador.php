<?php

namespace Cmisoft;

use Illuminate\Database\Eloquent\Model;

class Indicador extends Model
{
    protected $table = 'indicadors';
    protected $fillable = ['nombre', 'valor', 'escala', 'query', 'fecha_inicio', 'fecha_fin', 'unidad', 'responsable', 'subgrupo_id', 'create_id', 'update_id'];
}
