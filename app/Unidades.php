<?php

namespace Cmisoft;

use Illuminate\Database\Eloquent\Model;

class Unidades extends Model
{
    protected $table = 'unidades';
    protected $fillable = ['nombre', 'create_id', 'update_id'];
}
