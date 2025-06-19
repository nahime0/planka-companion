<?php

namespace App\Models;

use App\Models\Planka\Card;
use App\Models\Planka\UserAccount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationLog extends Model
{
    protected $connection = 'sqlite'; // Explicitly use sqlite connection
    
    protected $fillable = [
        'card_id',
        'user_id',
        'notification_text',
        'custom_message',
        'channel',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the card that was notified
     */
    public function card(): BelongsTo
    {
        return $this->belongsTo(Card::class, 'card_id');
    }

    /**
     * Get the user who was notified
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(UserAccount::class, 'user_id');
    }
}