<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/loved')]
final class LovedController extends AbstractController
{
    #[Route('/', name: 'app_loved')]
    public function index(): Response
    {
        return $this->render('loved/index.html.twig', [
            'controller_name' => 'LovedController',
        ]);
    }
}
