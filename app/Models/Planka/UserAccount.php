<?php

namespace App\Models\Planka;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property string $email
 * @property string|null $password
 * @property string $role
 * @property string $name
 * @property string|null $username
 * @property array|null $avatar
 * @property string|null $phone
 * @property string|null $organization
 * @property string|null $language
 * @property bool $subscribe_to_own_cards
 * @property bool $subscribe_to_card_when_commenting
 * @property bool $turn_off_recent_card_highlighting
 * @property bool $enable_favorites_by_default
 * @property string $default_editor_mode
 * @property string $default_home_view
 * @property string $default_projects_order
 * @property bool $is_sso_user
 * @property bool $is_deactivated
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $password_changed_at
 */
class UserAccount extends PlankaModel
{
    protected $table = 'user_account';
    
    protected $fillable = [
        'email',
        'password',
        'role',
        'name',
        'username',
        'avatar',
        'phone',
        'organization',
        'language',
        'subscribe_to_own_cards',
        'subscribe_to_card_when_commenting',
        'turn_off_recent_card_highlighting',
        'enable_favorites_by_default',
        'default_editor_mode',
        'default_home_view',
        'default_projects_order',
        'is_sso_user',
        'is_deactivated',
    ];
    
    protected $casts = [
        'avatar' => 'array',
        'subscribe_to_own_cards' => 'boolean',
        'subscribe_to_card_when_commenting' => 'boolean',
        'turn_off_recent_card_highlighting' => 'boolean',
        'enable_favorites_by_default' => 'boolean',
        'is_sso_user' => 'boolean',
        'is_deactivated' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'password_changed_at' => 'datetime',
    ];
    
    protected $hidden = [
        'password',
    ];
    
    public function identityProviderUsers(): HasMany
    {
        return $this->hasMany(IdentityProviderUser::class, 'user_id');
    }
    
    public function sessions(): HasMany
    {
        return $this->hasMany(Session::class, 'user_id');
    }
    
    public function projectManagers(): HasMany
    {
        return $this->hasMany(ProjectManager::class, 'user_id');
    }
    
    public function projectFavorites(): HasMany
    {
        return $this->hasMany(ProjectFavorite::class, 'user_id');
    }
    
    public function boardMemberships(): HasMany
    {
        return $this->hasMany(BoardMembership::class, 'user_id');
    }
    
    public function boardSubscriptions(): HasMany
    {
        return $this->hasMany(BoardSubscription::class, 'user_id');
    }
    
    public function cardMemberships(): HasMany
    {
        return $this->hasMany(CardMembership::class, 'user_id');
    }
    
    public function cardSubscriptions(): HasMany
    {
        return $this->hasMany(CardSubscription::class, 'user_id');
    }
    
    public function createdCards(): HasMany
    {
        return $this->hasMany(Card::class, 'creator_user_id');
    }
    
    public function assignedTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'assignee_user_id');
    }
    
    public function createdAttachments(): HasMany
    {
        return $this->hasMany(Attachment::class, 'creator_user_id');
    }
    
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'user_id');
    }
    
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'user_id');
    }
    
    public function createdNotifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'creator_user_id');
    }
    
    public function notificationServices(): HasMany
    {
        return $this->hasMany(NotificationService::class, 'user_id');
    }
    
    public function actions(): HasMany
    {
        return $this->hasMany(Action::class, 'user_id');
    }
}