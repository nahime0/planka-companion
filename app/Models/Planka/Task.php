<?php

namespace App\Models\Planka;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $card_id
 * @property int $task_list_id
 * @property float $position
 * @property string $name
 * @property bool $is_completed
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class Task extends PlankaModel
{
    protected $table = 'task';
    
    protected $fillable = [
        'card_id',
        'task_list_id',
        'position',
        'name',
        'is_completed',
    ];
    
    protected $casts = [
        'position' => 'float',
        'is_completed' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    public function card(): BelongsTo
    {
        return $this->belongsTo(Card::class, 'card_id');
    }
    
    public function taskList(): BelongsTo
    {
        return $this->belongsTo(TaskList::class, 'task_list_id');
    }
}