<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Embedding extends Model
{
    use SoftDeletes;

    protected $table = 'embeddings';

    protected $connection = '';

    protected $fillable = [
        'person_id',
        'embedding',
        'etiqueta',
    ];

    protected $casts = [
        'embedding' => 'array',
    ];

    public function __construct(array $attributes = [])
    {
        $this->connection = session('base') ?? '';
        parent::__construct($attributes);
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_id');
    }
}
