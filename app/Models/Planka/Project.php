<?php

namespace App\Models\Planka;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property int|null $owner_project_manager_id
 * @property int|null $background_image_id
 * @property string $name
 * @property string|null $description
 * @property string|null $background_type
 * @property string|null $background_gradient
 * @property bool $is_hidden
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class Project extends PlankaModel
{
    protected $table = 'project';
    
    protected $fillable = [
        'owner_project_manager_id',
        'background_image_id',
        'name',
        'description',
        'background_type',
        'background_gradient',
        'is_hidden',
    ];
    
    protected $casts = [
        'is_hidden' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    public function ownerProjectManager(): BelongsTo
    {
        return $this->belongsTo(ProjectManager::class, 'owner_project_manager_id');
    }
    
    public function backgroundImage(): BelongsTo
    {
        return $this->belongsTo(BackgroundImage::class, 'background_image_id');
    }
    
    public function projectManagers(): HasMany
    {
        return $this->hasMany(ProjectManager::class, 'project_id');
    }
    
    public function projectFavorites(): HasMany
    {
        return $this->hasMany(ProjectFavorite::class, 'project_id');
    }
    
    public function boards(): HasMany
    {
        return $this->hasMany(Board::class, 'project_id');
    }
    
    public function boardMemberships(): HasMany
    {
        return $this->hasMany(BoardMembership::class, 'project_id');
    }
    
    public function baseCustomFieldGroups(): HasMany
    {
        return $this->hasMany(BaseCustomFieldGroup::class, 'project_id');
    }
    
    public function backgroundImages(): HasMany
    {
        return $this->hasMany(BackgroundImage::class, 'project_id');
    }
}