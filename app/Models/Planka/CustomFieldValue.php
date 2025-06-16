<?php

namespace App\Models\Planka;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $card_id
 * @property int $custom_field_id
 * @property string|null $value
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class CustomFieldValue extends PlankaModel
{
    protected $table = 'custom_field_value';
    
    protected $fillable = [
        'card_id',
        'custom_field_id',
        'value',
    ];
    
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    public function card(): BelongsTo
    {
        return $this->belongsTo(Card::class, 'card_id');
    }
    
    public function customField(): BelongsTo
    {
        return $this->belongsTo(CustomField::class, 'custom_field_id');
    }
}