<?php

namespace App\Models\Planka;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $board_id
 * @property int $base_custom_field_group_id
 * @property float $position
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class CustomFieldGroup extends PlankaModel
{
    protected $table = 'custom_field_group';
    
    protected $fillable = [
        'board_id',
        'base_custom_field_group_id',
        'position',
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
    
    public function baseCustomFieldGroup(): BelongsTo
    {
        return $this->belongsTo(BaseCustomFieldGroup::class, 'base_custom_field_group_id');
    }
    
    public function customFields(): HasMany
    {
        return $this->hasMany(CustomField::class, 'custom_field_group_id');
    }
}