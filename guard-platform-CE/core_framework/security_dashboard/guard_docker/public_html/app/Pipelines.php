<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pipelines extends Model
{
    protected $fillable = ['uuid', 'name', 'description', 'status', 'user_id', 'agents', 'created_at', 'updated_at'];

    protected $casts = [
        'agents' => 'json',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
