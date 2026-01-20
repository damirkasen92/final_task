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
        // a very simple implementation without additional tests
        $result = $salesforceService->addUserToCrm($this->getUser());

        if ($result->isFaulty()) {
            $this->addFlash('error', $result->getErrors());
        }

        return $this->redirectToRoute('show_user', ['id' => $this->getUser()->getId()]);
    }
}
