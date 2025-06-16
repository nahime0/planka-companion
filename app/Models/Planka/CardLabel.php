<?php

namespace App\Models\Planka;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $card_id
 * @property int $label_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class CardLabel extends PlankaModel
{
    protected $table = 'card_label';
    
    protected $fillable = [
        'card_id',
        'label_id',
    ];
    
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    public function card(): BelongsTo
    {
        return $this->belongsTo(Card::class, 'card_id');
    }
    
    public function label(): BelongsTo
    {
        return $this->belongsTo(Label::class, 'label_id');
    }
}