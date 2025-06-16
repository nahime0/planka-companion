<?php

namespace App\Models\Planka;

use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $id
 * @property string $file_name
 * @property string $extension
 * @property int $size
 * @property string|null $mime_type
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class FileReference extends PlankaModel
{
    protected $table = 'file_reference';
    
    protected $keyType = 'string';
    public $incrementing = false;
    
    protected $fillable = [
        'id',
        'file_name',
        'extension',
        'size',
        'mime_type',
    ];
    
    protected $casts = [
        'size' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class, 'file_reference_id');
    }
}