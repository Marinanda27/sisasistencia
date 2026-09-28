<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Librerias\Libreria;
use Illuminate\Support\Facades\Auth;

class Menuoption extends Model
{
	use SoftDeletes;
    protected $table = 'menuoption';
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

	public function menuoptioncategory()
	{
		return $this->belongsTo('App\Models\Menuoptioncategory', 'menuoptioncategory_id');
	}

   public function permissions()
   {
      return $this->hasMany(Permission::class);
   }

	/**
	 * Método para listar las opciones de menu
	 * @param  [type] $query [description]
	 * @return [type]        [description]
	 */
	public function scopelistar($query, $name, $menuoptioncategory_id)
    {
        return $query->where(function($subquery) use($name)
		            {
		            	if (!is_null($name)) {
		            		$subquery->where('name', 'LIKE', '%'.$name.'%');
		            	}
		            })
        			->where(function($subquery) use($menuoptioncategory_id)
		            {
		            	if (!is_null($menuoptioncategory_id)) {
		            		$subquery->where('menuoptioncategory_id', '=', $menuoptioncategory_id);
		            	}
		            })
        			->orderBy('menuoptioncategory_id', 'ASC')
        			->orderBy('order', 'ASC');
    }

    // public static function boot()
	// {
	// 	parent::boot();

	// 	static::created(function($menuoption)
	// 	{
	// 		$binnacle             = new Binnacle();
	// 		$binnacle->action     = 'I';
	// 		$binnacle->date      = date('Y-m-d H:i:s');
	// 		$binnacle->ip         = Libreria::get_client_ip();
	// 		$binnacle->user_id = Auth::user()->id;
	// 		$binnacle->table      = 'menuoption - opcion menu';
	// 		$binnacle->detail    = $menuoption->toJson(JSON_UNESCAPED_UNICODE);
	// 		$binnacle->recordid = $menuoption->id;
	// 		$binnacle->save();
	// 	});

	// 	static::updated(function($menuoption)
	// 	{
	// 		$binnacle             = new Binnacle();
	// 		$binnacle->action     = 'U';
	// 		$binnacle->date      = date('Y-m-d H:i:s');
	// 		$binnacle->ip         = Libreria::get_client_ip();
	// 		$binnacle->user_id = Auth::user()->id;
	// 		$binnacle->table      = 'menuoption - opcion menu';
	// 		$binnacle->detail    = $menuoption->toJson(JSON_UNESCAPED_UNICODE);
	// 		$binnacle->recordid = $menuoption->id;
	// 		$binnacle->save();
	// 	});

	// 	static::deleted(function($menuoption)
	// 	{
	// 		$binnacle             = new Binnacle();
	// 		$binnacle->action     = 'D';
	// 		$binnacle->date      = date('Y-m-d H:i:s');
	// 		$binnacle->ip         = Libreria::get_client_ip();
	// 		$binnacle->user_id = Auth::user()->id;
	// 		$binnacle->table      = 'menuoption - opcion menu';
	// 		$binnacle->detail    = $menuoption->toJson(JSON_UNESCAPED_UNICODE);
	// 		$binnacle->recordid = $menuoption->id;
	// 		$binnacle->save();
	// 	});
	// }
}
