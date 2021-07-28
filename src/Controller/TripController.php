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
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sortie", name="sortie_")
 */
class TripController extends AbstractController
{
//    /**
//     * @Route("/", name="liste")
//     */
//    public function list(
//        TripRepository $tripRepository,
//        Request $request
//    ): Response
//    {
//        $user = $userRepository->getCurrentUser(
//            $this->getUser()->getUserIdentifier()
//        );
//        $data = new SearchTripData();
//        $searchTripForm = $this->createForm(SearchTripForm::class, $data);
//        $searchTripForm->handleRequest($request);

//        $trips = $tripRepository->findSearch($data, $user);

//        return $this->render('trip/list.html.twig', [
//            'trips' => $trips,
//            'user' => $user,
//            'searchForm' => $searchTripForm->createView()
//        ]);
//    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(
        int $id,
        TripRepository $tripRepository
    ): Response
    {
        $trip = $tripRepository->find($id);

        return $this->render('trip/details.html.twig', [
            "trip" => $trip,
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

            $trip->setState($stateRepository->find(1));

            $trip->setOrganizer($this->getUser());
            $trip->setCampusOrganizer($organizer->getCampus());

            if ($tripForm->get('enregistrer')->isClicked()) {
                $trip->setIsPublished(false);
            }

            if ($tripForm->get('publier_la_sortie')->isClicked()) {
                $trip->setIsPublished(true);
            }

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
     * @Route("/modifier/{id}", name="modifier")
     */
    public function modify(
        Request $request,
        int $id,
        TripRepository $tripRepository,
        UserRepository $userRepository,
        StateRepository $stateRepository,
        EntityManagerInterface $entityManager
    ): Response
    {
        $trip = $tripRepository->find($id);

        $tripForm = $this->createForm(TripType::class, $trip);

        $tripForm->handleRequest($request);

        if ($tripForm->isSubmitted() && $tripForm->isValid()) {
            $organizer = $userRepository->find($this->getUser());

            $trip->setState($stateRepository->find(1));

            $trip->setOrganizer($this->getUser());
            $trip->setCampusOrganizer($organizer->getCampus());

            if ($tripForm->get('enregistrer')->isClicked()) {
                $trip->setIsPublished(false);
            }

            if ($tripForm->get('publier_la_sortie')->isClicked()) {
                $trip->setIsPublished(true);
            }

            if ($tripForm->get('supprimer_la_sortie')->isClicked()) {
                $entityManager->remove($trip);
                $entityManager->flush();
                $this->addFlash('success', 'Sortie supprimée');
                return $this->redirectToRoute('main_index');
            }

            $entityManager->persist($trip);
            $entityManager->flush();

            $this->addFlash('success', 'Sortie créée');
            return $this->redirectToRoute('main_index');
        }

        return $this->render('trip/create.html.twig', [
            'trip' => $trip,
            'tripForm' => $tripForm->createView()
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
    ): Response
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
    ): Response
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
