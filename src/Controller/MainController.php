<?php

namespace App\Controller;

use App\Data\SearchTripData;
use App\Form\SearchTripForm;
use App\Repository\TripRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\NonUniqueResultException;
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
                          Request $request): Response
    {

        $error = $authenticationUtils->getLastAuthenticationError();
        if ($this->getUser() == null) {
//            throw $this->createNotFoundException();
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

        return $this->render('main/index.html.twig', [
            'trips' => $trips,
            'user' => $user,
            'searchForm' => $searchTripForm->createView()
        ]);
    }
}
