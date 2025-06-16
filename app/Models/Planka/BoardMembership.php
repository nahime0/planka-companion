<?php

namespace App\Models\Planka;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $project_id
 * @property int $board_id
 * @property int $user_id
 * @property string $role
 * @property bool $can_comment
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class BoardMembership extends PlankaModel
{
    protected $table = 'board_membership';
    
    protected $fillable = [
        'project_id',
        'board_id',
        'user_id',
        'role',
        'can_comment',
    ];
    
    protected $casts = [
        'can_comment' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }
    
    public function board(): BelongsTo
    {
        return $this->belongsTo(Board::class, 'board_id');
    }
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(UserAccount::class, 'user_id');
    }
    
    public function actions(): HasMany
    {
        return $this->hasMany(Action::class, 'board_membership_id');
    }
}