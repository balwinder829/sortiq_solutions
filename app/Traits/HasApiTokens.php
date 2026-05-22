<?php

namespace App\Traits;

use App\Models\ApiToken;
use Illuminate\Support\Str;

trait HasApiTokens
{
    public function tokens()
    {
        return $this->morphMany(ApiToken::class, 'tokenable');
    }

    public function createToken(string $name = 'default', array $abilities = ['*'])
    {
        $token = Str::random(64);

        $this->tokens()->create([
            'token' => $token,
            'last_used_at' => null,
            'expires_at' => null, // Optional: add logic for expiration if needed
        ]);

        return $token;
    }
}