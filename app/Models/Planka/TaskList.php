<?php

namespace App\Models\Planka;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $card_id
 * @property float $position
 * @property string $name
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class TaskList extends PlankaModel
{
    protected $table = 'task_list';
    
    protected $fillable = [
        'card_id',
        'position',
        'name',
    ];
    
    protected $casts = [
        'position' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    public function card(): BelongsTo
    {
        return $this->belongsTo(Card::class, 'card_id');
    }
    
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'task_list_id');
    }
}