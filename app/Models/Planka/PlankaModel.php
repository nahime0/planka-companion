<?php

namespace App\Models\Planka;

use Illuminate\Database\Eloquent\Model;

abstract class PlankaModel extends Model
{
    protected $connection = 'planka';
    
    public $timestamps = false;
    
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}