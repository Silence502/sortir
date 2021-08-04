<?php

namespace App\Controller;

use App\Data\SearchTripData;
use App\Form\SearchTripForm;
use App\Repository\StateRepository;
use App\Repository\TripRepository;
use App\Repository\UserRepository;
use App\Services\TripHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="main_index")
     */
    public function index(TripRepository $tripRepository,
                          UserRepository $userRepository,
                          AuthenticationUtils $authenticationUtils,
                          Request $request,
                          TripHandler $tripHandler,
                          EntityManagerInterface $entityManager,
                          StateRepository $stateRepository
    ): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        if ($this->getUser() == null) {
            return $this->redirectToRoute('app_login', [
                'error' => $error]);
        }

        $user = $userRepository->getCurrentUser(
            $this->getUser()->getUserIdentifier()
        );

        $data = new SearchTripData();
        $searchTripForm = $this->createForm(SearchTripForm::class, $data);
        $searchTripForm->handleRequest($request);

        $trips = $tripRepository->findSearch($data, $user);

        foreach ($trips as $trip) {
            $tripHandler->setStateAndFlush($trip, $entityManager, $stateRepository);
        }

        return $this->render('main/index.html.twig',[
            'trips' => $trips,
            'user' => $user,
            'searchForm' => $searchTripForm->createView()
        ]);
    }
}