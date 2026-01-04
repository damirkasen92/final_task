<?php

namespace App\Controller;

use App\Enum\JsonStatuses;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;

class BaseController extends AbstractController
{
    protected array $jsonSuccessData = [
        'status' => JsonStatuses::success,
    ];

    protected array $jsonErrorData = [
        'status' => JsonStatuses::error,
    ];

    public function addRedirect(string $url): void
    {
        $this->jsonSuccessData['redirect'] = $url;
    }

    public function getErrors(FormInterface $form)
    {
        $errors = [];

        foreach ($form->getErrors(true) as $error) {
            $errors[$error->getOrigin()->getConfig()->getOption('label')][] = $error->getMessage();
        }

        return $errors;
    }
}
