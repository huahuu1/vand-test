<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    /**
     * Get user by username or email
     *
     * @param $value
     * @return User
     */
    public function getUserByUsernameOrEmail($value)
    {
        return User::select('id', 'username', 'email', 'password')
            ->where('username', $value)
            ->orWhere('email', $value)
            ->first();
    }
}
