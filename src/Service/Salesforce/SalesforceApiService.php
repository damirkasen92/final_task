<?php

namespace App\Service\Salesforce;

use App\Dto\SalesforceAccessDataDto;
use App\Entity\User;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SalesforceApiService
{
    private const string API_URL = "/services/data/v65.0";

    public function __construct(
        private HttpClientInterface $httpClient,
    ) {
    }

    public function createAccount(SalesforceAccessDataDto $dto, User $user)
    {
        // TODO async 
        $accountResponse = $this->httpClient->request('POST', $dto->instanceUrl . static::API_URL . '/sobjects/Account', [
            'headers' => ['Authorization' => 'Bearer ' . $dto->accessToken],
            'json' => [
                'Name' => $user->getName(),
            ],
        ]);

        return $accountResponse->toArray(false);
    }

    public function createContact(SalesforceAccessDataDto $dto, User $user, string $accountId) {
        // TODO async 
        $contactResponse = $this->httpClient->request('POST', $dto->instanceUrl . static::API_URL . '/sobjects/Contact', [
            'headers' => ['Authorization' => 'Bearer ' . $dto->accessToken],
            'json' => [
                'Title' => $user->getName() . '|' . $user->getEmail(),
                'LastName' => $user->getName(),
                'Email' => $user->getEmail(),
                'AccountId' => $accountId,
                'Number_of_inventories__c' => 0,
            ],
        ]);

        return $contactResponse->toArray(false);
    }
}
