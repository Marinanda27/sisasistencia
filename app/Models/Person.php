<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Person extends Model
{
    use SoftDeletes;
    protected $table = 'person';
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

    public function workertype(){
        return $this->belongsTo('App\Models\Workertype', 'workertype_id');
    }

    public function distrito(){
        return $this->belongsTo('App\Models\Distrito', 'distrito_id');
    }

    public function country(){
        return $this->belongsTo('App\Models\Country', 'country_id');
    }

    public function embeddings(): HasMany
    {
        return $this->hasMany(Embedding::class, 'person_id');
    }

    public function assistances(): HasMany
    {
        return $this->hasMany(Assistance::class, 'person_id');
    }

    public function company(){
        return $this->belongsTo('App\Models\Person', 'company_id');
    }

    /**
     * Método para listar
     * @param  model $query modelo
     * @param  string $name  nombre
     * @return sql        sql
     */
    public function scopelistar($query, $name, $type)
    {
        return $query->where(function($subquery) use($name)
		            {
		            	if (!is_null($name)) {
		            		$subquery->where(DB::raw('CONCAT(lastname," ",firstname)'), 'LIKE', '%'.$name.'%')->orWhere('bussinesname','LIKE','%'.$name.'%');
		            	}
		            })
        			->where(function($subquery) use($type)
		            {
		            	if (!is_null($type)) {
		            		$subquery->where('type', '=', $type);
		            	}
		            })
        			->orderBy('firstname', 'ASC')->orderBy('lastname', 'ASC')->orderBy('bussinesname', 'ASC');
    }
}
