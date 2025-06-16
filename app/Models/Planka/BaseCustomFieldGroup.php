<?php

namespace App\Models\Planka;

use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class BaseCustomFieldGroup extends PlankaModel
{
    protected $table = 'base_custom_field_group';
    
    protected $fillable = [
        'name',
    ];
    
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    public function customFieldGroups(): HasMany
    {
        return $this->hasMany(CustomFieldGroup::class, 'base_custom_field_group_id');
    }
}