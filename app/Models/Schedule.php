<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $table = 'schedule';

    protected $connection = '';

    protected $fillable = [
        'start_time',
        'grace_minutes',
    ];

    protected $casts = [
        'start_time'     => 'string',
        'grace_minutes'  => 'integer',
    ];

    public function __construct(array $attributes = [])
    {
        $this->connection = session('base') ?? '';
        parent::__construct($attributes);
    }
}
