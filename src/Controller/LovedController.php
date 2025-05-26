<?php

namespace App\Controller;

use App\Entity\Lovemessage;
use App\Repository\LovemessageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/loved')]
final class LovedController extends AbstractController
{
    #[Route('/', name: 'app_loved')]
    public function index(LovemessageRepository $lovemessageRepository, Security $security): Response
    {
        $user = $security->getUser();
        $lovemessage = $lovemessageRepository
            ->createQueryBuilder('lm')
            ->andWhere(':user MEMBER OF lm.users')
            ->setParameter('user', $user)
            ->orderBy('lm.id', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $this->render('loved/index.html.twig', [
            'controller_name' => 'LovedController',
            'lovemessage' => $lovemessage,
            'user_email' => $user,
        ]);
    }
}
