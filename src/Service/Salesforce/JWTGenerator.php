<?php

namespace App\Service\Salesforce;

use Firebase\JWT\JWT;

class JWTGenerator
{
    private const int EXP = 300;

    public function __construct(private JWT $jwt, private string $privateKey, private string $customerKey, private string $username)
    {
    }

    public function generate(): string
    {
        $now = time();
        $payload = [
            'iss' => $this->customerKey,
            'sub' => $this->username,
            'aud' => 'https://login.salesforce.com',
            'exp' => $now + static::EXP,
        ];

        $jwt = JWT::encode($payload, $this->privateKey, 'RS256');

        return $jwt;
    }
}
