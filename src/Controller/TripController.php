<?php

namespace App\Controller;

use App\Data\SearchTripData;
use App\Entity\Trip;
use App\Form\SearchTripForm;
use App\Form\TripType;
use App\Repository\StateRepository;
use App\Repository\TripRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
/**
 * @Route("/sortie", name="sortie_")
 */
class TripController extends AbstractController
{
    /**
     * @Route("", name="liste")
     */
    public function list(
        TripRepository $tripRepository,
        UserRepository $userRepository,
        Request $request
    ): Response
    {
        $user = $userRepository->getCurrentUser(
            $this->getUser()->getUserIdentifier()
        );
        $data = new SearchTripData();
        $searchTripForm = $this->createForm(SearchTripForm::class, $data);
        $searchTripForm->handleRequest($request);

        $trips = $tripRepository->findSearch($data, $user);

        return $this->render('trip/list.html.twig', [
            'trips' => $trips,
            'user' => $user,
            'searchForm' => $searchTripForm->createView()
        ]);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(
        int $id,
        TripRepository $tripRepository
    ): Response
    {
        $sortie = $tripRepository->find($id);

        return $this->render('trip/details.html.twig', [
            "sortie" => $sortie,
        ]);
    }

    /**
     * @Route("/creer", name="creer")
     */
    public function create(
        Request $request,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        StateRepository $stateRepository
    ): Response
    {
        $trip = new Trip();
        $tripForm = $this->createForm(TripType::class, $trip);

        $tripForm->handleRequest($request);

        if ($tripForm->isSubmitted() && $tripForm->isValid()) {
            $organizer = $userRepository->find($this->getUser());

            $trip->setOrganizer($this->getUser());
            $trip->setCampusOrganizer($organizer->getCampus());
            $trip->setState($stateRepository->find(1));
            $trip->setIsPublished(true);

            $entityManager->persist($trip);
            $entityManager->flush();

            $this->addFlash('success', 'Sortie créée');
            return $this->redirectToRoute('main_index');
        }

        return $this->render('trip/create.html.twig', [
            'tripForm' => $tripForm->createView()
        ]);
    }

    /**
     * @Route("/modifier", name="modifier")
     */
    public function modifier(): Response
    {
        return $this->render('sortie/modifier.html.twig', [
            'controller_name' => 'TripController',
        ]);
    }

    /**
     * @Route("/seDesister/{id}", name="seDesister")
     */
    public function desist(
        int $id,
        TripRepository $tripRepository,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager
    ) : Response
    {
        $trip = $tripRepository->find($id);
        $user = $userRepository
            ->getCurrentUser($this->getUser()->getUserIdentifier());
        $trip->removeUsersRegistered($user);
        $entityManager->persist($trip);
        $entityManager->flush();

        $flashMessage = 'Désinscription à la sortie ' . $trip->getName();

        $this->addFlash('success', $flashMessage);

        return $this->redirectToRoute('sortie_liste');

    }

    /**
     * @Route("/sInscrire/{id}", name="sInscrire")
     */
    public function tripRegistration(
        int $id,
        TripRepository $tripRepository,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager
    ) : Response
    {
        $trip = $tripRepository->find($id);
        $user = $userRepository
            ->getCurrentUser($this->getUser()->getUserIdentifier());
        $trip->addUsersRegistered($user);
        $entityManager->persist($trip);
        $entityManager->flush();

        $flashMessage = 'Inscription à la sortie ' . $trip->getName();

        $this->addFlash('success', $flashMessage);
        return $this->redirectToRoute('sortie_liste');

    }
}