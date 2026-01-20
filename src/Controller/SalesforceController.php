<?php

namespace App\Controller;

use App\Dto\ServiceResult;
use App\Service\Salesforce\SalesforceService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SalesforceController extends BaseController
{
    #[Route('/salesforce/create/account', name: 'salesforce_create_account')]
    public function createAccountWithContact(
        SalesforceService $salesforceService,
    ): Response {
        $this->denyAccessUnlessGranted('CREATE_SALESFORCE_ACCOUNT', $this->getUser());

        /**
         * @var ServiceResult $result
         */
        $result = $salesforceService->addUserToCrm($this->getUser());

        if ($result->isFaulty()) {
            return $this->json([
                ...$this->jsonErrorData,
                'errors' => $result->getErrors(),
            ]);
        }

        return $this->json($this->jsonSuccessData);
    }
}
