<?php

namespace App\Models\Planka;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $custom_field_group_id
 * @property float $position
 * @property string $name
 * @property string $type
 * @property array|null $options
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class CustomField extends PlankaModel
{
    protected $table = 'custom_field';
    
    protected $fillable = [
        'custom_field_group_id',
        'position',
        'name',
        'type',
        'options',
    ];
    
    protected $casts = [
        'position' => 'float',
        'options' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    public function customFieldGroup(): BelongsTo
    {
        return $this->belongsTo(CustomFieldGroup::class, 'custom_field_group_id');
    }
    
    public function customFieldValues(): HasMany
    {
        return $this->hasMany(CustomFieldValue::class, 'custom_field_id');
    }
}