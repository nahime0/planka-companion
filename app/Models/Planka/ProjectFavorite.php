<?php

namespace App\Models\Planka;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $project_id
 * @property int $user_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class ProjectFavorite extends PlankaModel
{
    protected $table = 'project_favorite';
    
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
}