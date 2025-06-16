<?php

namespace App\Models\Planka;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int|null $card_id
 * @property int $action_id
 * @property bool $is_read
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class Notification extends PlankaModel
{
    protected $table = 'notification';
    
    protected $fillable = [
        'user_id',
        'card_id',
        'action_id',
        'is_read',
    ];
    
    protected $casts = [
        'is_read' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(UserAccount::class, 'user_id');
    }
    
    public function card(): BelongsTo
    {
        return $this->belongsTo(Card::class, 'card_id');
    }
    
    public function action(): BelongsTo
    {
        return $this->belongsTo(Action::class, 'action_id');
    }
}