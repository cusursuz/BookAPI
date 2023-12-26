<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

use FOS\RestBundle\Controller\Annotations as Rest;

class MainController extends AbstractController
{
    #[Rest\Get('/', name: 'app_main')]
    public function index(): Response
    {
        return $this->json(
            [
                'App name'=>'BooksAPI',
                'App vesion'=>'1.0.0']
        );
    }
}
