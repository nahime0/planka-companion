<?php

namespace App\Models\Planka;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Support\Facades\Hash;

class AuthenticatableUserAccount extends UserAccount implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword, MustVerifyEmail;

    /**
     * Disable timestamps to prevent any updates during authentication
     */
    public $timestamps = false;

    /**
     * Override fillable to exclude password field
     * This prevents password from being mass-assigned during authentication
     */
    protected $fillable = [
        'email',
        // 'password', // Explicitly excluded
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

    /**
     * Completely override the guarded property to protect password
     */
    protected $guarded = ['password', 'password_changed_at'];

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'id';
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return string|null
     */
    public function getRememberToken()
    {
        return $this->remember_token ?? null;
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param  string  $value
     * @return void
     */
    public function setRememberToken($value)
    {
        // Planka doesn't have a remember_token column by default
        // You can add it to the database or use sessions instead
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    /**
     * Check if the user's account is active.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return !$this->is_deactivated;
    }

    /**
     * Get the email address for notifications.
     *
     * @return string
     */
    public function getEmailForPasswordReset()
    {
        return $this->email;
    }

    /**
     * Override to prevent access for deactivated users.
     */
    public function canAccessFilament(): bool
    {
        return $this->isActive();
    }

    /**
     * Override save to prevent any modifications during authentication.
     * This ensures we don't accidentally overwrite Planka data.
     */
    public function save(array $options = [])
    {
        // Check if we're in an authentication context
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10);
        $inAuthContext = false;
        
        foreach ($backtrace as $trace) {
            if (isset($trace['class']) && (
                str_contains($trace['class'], 'Auth') ||
                str_contains($trace['class'], 'Guard') ||
                str_contains($trace['class'], 'Login')
            )) {
                $inAuthContext = true;
                break;
            }
        }
        
        // If we're in authentication context, prevent ALL saves
        if ($inAuthContext) {
            return true; // Pretend save was successful
        }
        
        // Always remove password from being saved
        unset($this->attributes['password']);
        
        // If no attributes are dirty, skip save
        if (!$this->isDirty()) {
            return true;
        }
        
        return parent::save($options);
    }

    /**
     * Override to prevent password from being set during authentication
     */
    public function setAttribute($key, $value)
    {
        // Prevent password from being set to null or empty during authentication
        if ($key === 'password' && empty($value) && $this->exists) {
            return $this;
        }
        
        return parent::setAttribute($key, $value);
    }

    /**
     * Override update to prevent any updates
     */
    public function update(array $attributes = [], array $options = [])
    {
        // Remove password from attributes if present
        unset($attributes['password']);
        
        // If only trying to update password, skip entirely
        if (empty($attributes)) {
            return true;
        }
        
        return parent::update($attributes, $options);
    }

    /**
     * Override fill to prevent password from being filled
     */
    public function fill(array $attributes)
    {
        // Remove password from attributes
        unset($attributes['password']);
        
        return parent::fill($attributes);
    }
}