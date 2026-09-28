<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $table = 'permission';
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
}
