<?php

namespace App\Models\Planka;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $board_id
 * @property float $position
 * @property string $name
 * @property string $color
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class Label extends PlankaModel
{
    protected $table = 'label';
    
    protected $fillable = [
        'board_id',
        'position',
        'name',
        'color',
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
    
    public function cards(): BelongsToMany
    {
        return $this->belongsToMany(Card::class, 'card_label', 'label_id', 'card_id')
            ->withPivot('id', 'created_at', 'updated_at');
    }
    
    public function cardLabels(): HasMany
    {
        return $this->hasMany(CardLabel::class, 'label_id');
    }
    
    public function actions(): HasMany
    {
        return $this->hasMany(Action::class, 'label_id');
    }
}