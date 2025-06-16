<?php

namespace App\Models\Planka;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $card_id
 * @property int $user_id
 * @property bool $is_permanent
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class CardSubscription extends PlankaModel
{
    protected $table = 'card_subscription';
    
    protected $fillable = [
        'card_id',
        'user_id',
        'is_permanent',
    ];
    
    protected $casts = [
        'is_permanent' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    public function card(): BelongsTo
    {
        return $this->belongsTo(Card::class, 'card_id');
    }
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(UserAccount::class, 'user_id');
    }
}