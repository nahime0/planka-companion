<?php

namespace App\Auth;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PlankaUserProvider extends EloquentUserProvider
{
    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  array  $credentials
     * @return bool
     */
    public function validateCredentials(UserContract $user, array $credentials)
    {
        $plain = $credentials['password'];
        $hashedPassword = $user->getAuthPassword();
        
        // Planka uses $2b$ prefix for bcrypt, but PHP expects $2y$
        // Convert $2b$ to $2y$ for PHP compatibility
        if (str_starts_with($hashedPassword, '$2b$')) {
            $hashedPassword = '$2y$' . substr($hashedPassword, 4);
        }
        
        return Hash::check($plain, $hashedPassword);
    }
    
    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array  $credentials
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        if (empty($credentials) ||
           (count($credentials) === 1 &&
            Str::contains($this->firstCredentialKey($credentials), 'password'))) {
            return;
        }

        // First, check if 'email' key exists and try to find by email or username
        if (isset($credentials['email'])) {
            $loginValue = $credentials['email'];
            unset($credentials['email']);
            
            $query = $this->newModelQuery();
            
            // Try to find by email OR username
            $query->where(function ($q) use ($loginValue) {
                $q->where('email', $loginValue)
                  ->orWhere('username', $loginValue);
            });
            
            // Add any other credential constraints
            foreach ($credentials as $key => $value) {
                if (Str::contains($key, 'password')) {
                    continue;
                }
                
                if (is_array($value) || $value instanceof Arrayable) {
                    $query->whereIn($key, $value);
                } else {
                    $query->where($key, $value);
                }
            }
            
            return $query->first();
        }
        
        // Fallback to parent implementation if no email field
        return parent::retrieveByCredentials($credentials);
    }
    
    /**
     * Get the first credential key from the credentials array.
     *
     * @param  array  $credentials
     * @return string|null
     */
    protected function firstCredentialKey(array $credentials)
    {
        foreach ($credentials as $key => $value) {
            return $key;
        }
        
        return null;
    }
}