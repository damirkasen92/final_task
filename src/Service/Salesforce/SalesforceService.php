<?php

namespace App\Service\Salesforce;

use App\Dto\SalesforceAccessDataDto;
use App\Dto\ServiceResult;
use App\Entity\User;

class SalesforceService
{
    public function __construct(
        private SalesforceOAuthService $salesforceOAuthService,
        private SalesforceApiService $salesforceApiService
    ) {
    }

    public function addUserToCrm(User $user): ServiceResult
    {
        $response = $this->salesforceOAuthService->getAccessToken();
        $dto = SalesforceAccessDataDto::fromArray($response);

        //TODO if Account is already exists
        $accountData = $this->salesforceApiService->createAccount($dto, $user);

        if (!isset($accountData['success'])) {
            return new ServiceResult()->setFail($accountData[0]['errorCode']);
        }

        $contactData = $this->salesforceApiService->createContact($dto, $user, $accountData['id']);

        if (!isset($contactData['success'])) {
            return new ServiceResult()->setFail($contactData[0]['errorCode']);
        }

        return new ServiceResult()->setSuccess();
    }
}
