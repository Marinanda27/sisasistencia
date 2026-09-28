<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Provincia extends Model
{
    use SoftDeletes;
    protected $table = 'provincia';
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
     * método para obtener las distritos hijas
     * @return [type] [description]
     */
    public function distritos()
	{
		return $this->hasMany('App\Models\Distrito');
	}
}
