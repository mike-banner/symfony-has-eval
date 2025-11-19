<?php

namespace App\Controller;

use App\Entity\Establishment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EstablishmentController extends AbstractController
{
    #[Route('/establishments', name: 'establishments_index')]
    public function index(EntityManagerInterface $em): Response
    {
        $establishments = $em->getRepository(Establishment::class)->findAll();

        return $this->render('establishment/index.html.twig', [
            'establishments' => $establishments
        ]);
    }
}
