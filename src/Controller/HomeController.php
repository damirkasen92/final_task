<?php

namespace App\Controller;

use Symfony\Component\Routing\Attribute\Route;

class HomeController extends BaseController
{
    #[Route('/', name: 'home', methods: ['GET'])]
    public function index()
    {
        return $this->render('home/index.html.twig');
    }
}
