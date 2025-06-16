<?php

namespace App\Models\Planka;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property int $card_id
 * @property int $creator_user_id
 * @property string $name
 * @property string $file_reference_id
 * @property string $url
 * @property int|null $image_width
 * @property int|null $image_height
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class Attachment extends PlankaModel
{
    protected $table = 'attachment';
    
    protected $fillable = [
        'card_id',
        'creator_user_id',
        'name',
        'file_reference_id',
        'url',
        'image_width',
        'image_height',
    ];
    
    protected $casts = [
        'image_width' => 'integer',
        'image_height' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    public function card(): BelongsTo
    {
        return $this->belongsTo(Card::class, 'card_id');
    }
    
    public function creatorUser(): BelongsTo
    {
        return $this->belongsTo(UserAccount::class, 'creator_user_id');
    }
    
    public function fileReference(): BelongsTo
    {
        return $this->belongsTo(FileReference::class, 'file_reference_id');
    }
    
    public function coveringCard(): HasOne
    {
        return $this->hasOne(Card::class, 'cover_attachment_id');
    }
}