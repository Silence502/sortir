<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Place;
use App\Form\CityType;
use App\Form\PlaceType;
use App\Repository\PlaceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/place", name="place_")
 */
class PlaceController extends AbstractController
{
    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request,
                           EntityManagerInterface $entityManager,
                           PlaceRepository $placeRepository): Response
    {
        $place = new Place();
        $placeForm = $this->createForm(PlaceType::class, $place);
        $placeForm->handleRequest($request);

        if ($placeForm->isSubmitted() && $placeForm->isValid()){
            $entityManager->persist($place);
            $entityManager->flush();
            return $this->redirectToRoute('sortie_creer');
        }

        return $this->render('place/create.html.twig', [
            'placeForm' => $placeForm->createView(),
        ]);
    }
}
