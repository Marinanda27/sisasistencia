<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Binnacle;
use App\Librerias\Libreria;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Menuoptioncategory extends Model
{
    use SoftDeletes;
    protected $table = 'menuoptioncategory';
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


   public function children()
   {
      return $this->hasMany(self::class, 'menuoptioncategory_id')
                  ->orderBy('order');
   }

   public function options()
   {
      return $this->hasMany(Menuoption::class);
   }


   public function menuoptions()
	{
		return $this->hasMany('App\Models\Menuoption');
	}

	public function Fathercategory()
	{
		return $this->belongsTo('App\Models\Menuoptioncategory', 'menuoptioncategory_id');
	}

	public function Soncategory()
	{
		return $this->hasMany('App\Models\Menuoptioncategory', 'menuoptioncategory_id');
	}

	public function scopelistar($query, $name)
    {
        return $query->where(function($subquery) use($name)
		            {
		            	if (!is_null($name)) {
		            		$subquery->where('name', 'LIKE', '%'.$name.'%');
		            	}
		            })
        			->orderBy('menuoptioncategory_id', 'ASC')
        			->orderBy('order', 'ASC');
    }

    
}
