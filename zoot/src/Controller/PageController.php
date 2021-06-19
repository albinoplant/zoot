<?php

namespace App\Controller;

use App\Entity\Animal;
use App\Entity\User;
use App\Entity\Vet;
use App\Form\AppointmentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractApiController
{
    /**
     * @Route("/", name="page")
     */
    public function index(EntityManagerInterface $em): Response
    {
        $user = $em->find(User::class,1);
        $animals = $user->getAnimals();
        $vets = $em->getRepository(Vet::class)->findAll();
        $vets = array_map(function ($a){
            return $a->getUser();
        },$vets);

        return $this->render('page/index.html.twig', [
            'animals' => $animals,
            'vets'=>$vets
        ]);
    }
}
