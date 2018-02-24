<?php

namespace Cmisoft;

use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    protected $table = 'agendas';
    protected $fillable = ['actividad', 'estado', 'fecha_limite', 'responsable', 'create_id', 'update_id'];
}
