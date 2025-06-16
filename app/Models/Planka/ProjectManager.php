<?php

namespace App\Models\Planka;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $project_id
 * @property int $user_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class ProjectManager extends PlankaModel
{
    protected $table = 'project_manager';
    
    protected $fillable = [
        'project_id',
        'user_id',
    ];
    
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(UserAccount::class, 'user_id');
    }
    
    public function ownedProjects(): HasMany
    {
        return $this->hasMany(Project::class, 'owner_project_manager_id');
    }
    
    public function actions(): HasMany
    {
        return $this->hasMany(Action::class, 'project_manager_id');
    }
}