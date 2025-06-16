<?php

namespace App\Models\Planka;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $board_id
 * @property float $position
 * @property string $name
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class ListModel extends PlankaModel
{
    protected $table = 'list';
    
    protected $fillable = [
        'board_id',
        'position',
        'name',
    ];
    
    protected $casts = [
        'position' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    public function board(): BelongsTo
    {
        return $this->belongsTo(Board::class, 'board_id');
    }
    
    public function cards(): HasMany
    {
        return $this->hasMany(Card::class, 'list_id');
    }
    
    public function actions(): HasMany
    {
        return $this->hasMany(Action::class, 'list_id');
    }
}