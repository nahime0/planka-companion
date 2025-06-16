<?php

namespace App\Models\Planka;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $project_id
 * @property int $position
 * @property string $name
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class Board extends PlankaModel
{
    protected $table = 'board';
    
    protected $fillable = [
        'project_id',
        'position',
        'name',
    ];
    
    protected $casts = [
        'position' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }
    
    public function lists(): HasMany
    {
        return $this->hasMany(ListModel::class, 'board_id');
    }
    
    public function boardMemberships(): HasMany
    {
        return $this->hasMany(BoardMembership::class, 'board_id');
    }
    
    public function boardSubscriptions(): HasMany
    {
        return $this->hasMany(BoardSubscription::class, 'board_id');
    }
    
    public function labels(): HasMany
    {
        return $this->hasMany(Label::class, 'board_id');
    }
    
    public function cards(): HasMany
    {
        return $this->hasMany(Card::class, 'board_id');
    }
    
    public function actions(): HasMany
    {
        return $this->hasMany(Action::class, 'board_id');
    }
}