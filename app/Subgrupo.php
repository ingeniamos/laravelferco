<?php

namespace Cmisoft;

use Illuminate\Database\Eloquent\Model;

class Subgrupo extends Model
{
    protected $table = 'subgrupos';
    protected $fillable = ['nombre', 'grupo_id', 'create_id', 'update_id'];
}