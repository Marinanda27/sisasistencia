<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branchoffice extends Model
{
    use SoftDeletes;

    protected $table = 'branchoffice';
    protected $dates = ['deleted_at'];
    protected $connection = '';

    public function __construct(array $attributes = [])
    {
        $variable = '';
        if(session('base') != null){
            $variable = session('base');
        }
        $this->connection = $variable;
        parent::__construct($attributes);
    }


}

?>
