<?php

namespace App\Dto;

readonly class SalesforceAccessDataDto
{
    public function __construct(
        public string $accessToken,
        public string $instanceUrl
    ) {
    }

    public static function fromArray(array $data): SalesforceAccessDataDto
    {
        return new self($data["access_token"], $data["instance_url"]);
    }
}
