<?php

namespace App\Service\Salesforce;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class SalesforceOAuthService
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private JWTGenerator $jwtGenerator,
        private string $instanceUri,
    ) {
    }

    public function getAccessToken(): array
    {
        $jwt = $this->jwtGenerator->generate();

        $response = $this->httpClient->request('POST', $this->instanceUri . '/services/oauth2/token', [
            'body' => [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ],
        ]);

        return $response->toArray(false);
    }


}
