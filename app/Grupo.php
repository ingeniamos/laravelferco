<?php

namespace Cmisoft;

use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    protected $table = 'grupos';
    protected $fillable = ['nombre', 'create_id', 'update_id'];
}
