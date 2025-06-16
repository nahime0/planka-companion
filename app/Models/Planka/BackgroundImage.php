<?php

namespace App\Models\Planka;

use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string $file_reference_id
 * @property string $url
 * @property int $width
 * @property int $height
 * @property string $thumb_url
 * @property int $thumb_width
 * @property int $thumb_height
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class BackgroundImage extends PlankaModel
{
    protected $table = 'background_image';
    
    protected $fillable = [
        'name',
        'file_reference_id',
        'url',
        'width',
        'height',
        'thumb_url',
        'thumb_width',
        'thumb_height',
    ];
    
    protected $casts = [
        'width' => 'integer',
        'height' => 'integer',
        'thumb_width' => 'integer',
        'thumb_height' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    public function boards(): HasMany
    {
        return $this->hasMany(Board::class, 'background_image_id');
    }
}