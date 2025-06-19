<?php

namespace App\Models\Planka;

use App\Models\NotificationLog;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * @property int $id
 * @property int $board_id
 * @property int $list_id
 * @property int|null $cover_attachment_id
 * @property int|null $creator_user_id
 * @property float $position
 * @property string $name
 * @property string|null $description
 * @property \Carbon\Carbon|null $due_date
 * @property \Carbon\Carbon|null $timer_started_at
 * @property int|null $timer_total
 * @property bool $is_timer_active
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class Card extends PlankaModel
{
    protected $table = 'card';
    
    protected $fillable = [
        'board_id',
        'list_id',
        'cover_attachment_id',
        'creator_user_id',
        'position',
        'name',
        'description',
        'due_date',
        'timer_started_at',
        'timer_total',
        'is_timer_active',
    ];
    
    protected $casts = [
        'position' => 'float',
        'timer_total' => 'integer',
        'is_timer_active' => 'boolean',
        'due_date' => 'datetime',
        'timer_started_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    public function board(): BelongsTo
    {
        return $this->belongsTo(Board::class, 'board_id');
    }
    
    public function list(): BelongsTo
    {
        return $this->belongsTo(ListModel::class, 'list_id');
    }
    
    public function coverAttachment(): BelongsTo
    {
        return $this->belongsTo(Attachment::class, 'cover_attachment_id');
    }
    
    public function creatorUser(): BelongsTo
    {
        return $this->belongsTo(UserAccount::class, 'creator_user_id');
    }
    
    public function labels(): BelongsToMany
    {
        return $this->belongsToMany(Label::class, 'card_label', 'card_id', 'label_id')
            ->withPivot('id', 'created_at', 'updated_at');
    }
    
    public function cardLabels(): HasMany
    {
        return $this->hasMany(CardLabel::class, 'card_id');
    }
    
    public function cardMemberships(): HasMany
    {
        return $this->hasMany(CardMembership::class, 'card_id');
    }
    
    public function cardSubscriptions(): HasMany
    {
        return $this->hasMany(CardSubscription::class, 'card_id');
    }
    
    public function tasks(): HasManyThrough
    {
        return $this->hasManyThrough(Task::class, TaskList::class, 'card_id', 'task_list_id');
    }
    
    public function taskLists(): HasMany
    {
        return $this->hasMany(TaskList::class, 'card_id');
    }
    
    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class, 'card_id');
    }
    
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'card_id');
    }
    
    public function customFieldValues(): HasMany
    {
        return $this->hasMany(CustomFieldValue::class, 'card_id');
    }
    
    public function actions(): HasMany
    {
        return $this->hasMany(Action::class, 'card_id');
    }
    
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'card_id');
    }
    
    /**
     * Get the updated_at attribute, returning created_at if updated_at is null
     */
    public function getUpdatedAtAttribute($value)
    {
        return $value ?: $this->created_at;
    }
    
    public function notificationLogs(): HasMany
    {
        return $this->hasMany(NotificationLog::class, 'card_id');
    }
}