<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Departamento extends Model
{
  use SoftDeletes;
  protected $table = 'departamento';
  protected $dates = ['deleted_at'];
  protected $connection = '';

  public function __construct(array $attributes = [])
  {
    $variable = '';
    if (session('base') != null) {
      $variable = session('base');
    }
    $this->connection = $variable;
    parent::__construct($attributes);
  }

  /**
   * método para obtener las provincias hijas
   * @return [type] [description]
   */
  public function provincias()
  {
    return $this->hasMany('App\Models\Provincia', 'departamento_id');
  }
}
