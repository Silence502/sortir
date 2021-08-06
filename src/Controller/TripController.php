<?php

namespace App\Controller;

use App\Data\SearchTripData;
use App\Entity\City;
use App\Entity\Place;
use App\Entity\Trip;
use App\Entity\User;
use App\Form\CancelTripType;
use App\Form\CityType;
use App\Form\PlaceType;
use App\Form\SearchTripForm;
use App\Form\TripType;
use App\Repository\CityRepository;
use App\Repository\StateRepository;
use App\Repository\TripRepository;
use App\Repository\UserRepository;
use App\Services\TripHandler;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
        int            $id,
        TripRepository $tripRepository
    ): Response
    {
        $trip = $tripRepository->find($id);
        $user = new User();

        return $this->render('trip/details.html.twig', [
            "trip" => $trip,
            'user' => $user
        ]);
    }

    /**
     * @Route("/creer", name="creer")
     */
    public function create(
        Request                $request,
        EntityManagerInterface $entityManager,
        UserRepository         $userRepository,
        StateRepository        $stateRepository,
        TripHandler            $tripHandler,
        CityRepository         $cityRepository
    ): Response
    {
        $trip = new Trip();
        $trip->setDateStartTime(new \DateTime('now'));
        $tripForm = $this->createForm(TripType::class, $trip);
        $tripForm->handleRequest($request);

//        $place = new Place();
//        $placeForm = $this->createForm(PlaceType::class, $place);
//        $placeForm->handleRequest($request);
//
//        if ($placeForm->isSubmitted() && $placeForm->isValid()) {
//            $entityManager->persist($place);
//            $entityManager->flush();
//            $this->redirectToRoute('sortie_creer');
//        }

//        $city = $cityRepository->find($id);
        $organizer = $userRepository->find($this->getUser());
        $site = $trip->setCampusOrganizer($organizer->getCampus());
//        $cityForm = $this->createForm(CityType::class, $city);
//        $cityForm->handleRequest($request);
//
//        if ($cityForm->isSubmitted() && $cityForm->isValid()){
//            $entityManager->persist($city);
//            $entityManager->flush();
//        }

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

            $trip->setState(
                $stateRepository->find($tripHandler->setTripState($trip))
            );

            $entityManager->persist($trip);
            $entityManager->flush();

            $this->addFlash('success', 'Sortie créée');
            return $this->redirectToRoute('main_index');
        }
        $localDate = new \DateTime();

        return $this->render('trip/create.html.twig', [
            'tripForm' => $tripForm->createView(),
            'localDate' => $localDate,
//            'placeForm' => $placeForm->createView(),
//            'cityForm' => $cityForm->createView(),
//            'city' => $city,
            'site' => $site
        ]);
    }

    /**
     * @Route("/modifier/{id}", name="modifier")
     */
    public function modify(
        Request                $request,
        int                    $id,
        TripRepository         $tripRepository,
        UserRepository         $userRepository,
        StateRepository        $stateRepository,
        EntityManagerInterface $entityManager,
        TripHandler            $tripHandler
    ): Response
    {
        $trip = $tripRepository->find($id);

        $tripForm = $this->createForm(TripType::class, $trip);

        $tripForm->handleRequest($request);

        if ($tripForm->get('supprimer_la_sortie')->isClicked()) {
            $entityManager->remove($trip);
            $entityManager->flush();

            $this->addFlash('success', 'Sortie supprimée');
            return $this->redirectToRoute('main_index');
        }

        if ($tripForm->isSubmitted() && $tripForm->isValid()) {
            $organizer = $userRepository->find($this->getUser());


            $trip->setOrganizer($this->getUser());
            $trip->setCampusOrganizer($organizer->getCampus());

            if ($tripForm->get('enregistrer')->isClicked()) {
                $trip->setIsPublished(false);
            }

            if ($tripForm->get('publier_la_sortie')->isClicked()) {
                $trip->setIsPublished(true);
//                $trip = $tripHandler->publish($trip);
            }

            $trip->setState(
                $stateRepository->find($tripHandler->setTripState($trip))
            );

            $entityManager->persist($trip);
            $entityManager->flush();

            $this->addFlash('success', 'Sortie créée');
            return $this->redirectToRoute('main_index');
        }

        return $this->render('trip/modify.html.twig', [
            'trip' => $trip,
            'tripForm' => $tripForm->createView()
        ]);
    }

    /**
     * @Route("/publier/{id}", name="publier")
     */
    public function publish(
        int                    $id,
        TripRepository         $tripRepository,
        EntityManagerInterface $entityManager
    ): Response
    {
        $trip = $tripRepository->find($id);
        $trip->setIsPublished(true);

        $entityManager->persist($trip);
        $entityManager->flush();

        return $this->redirectToRoute('sortie_details', [
            'id' => $trip->getId()
        ]);
    }

    /**
     * @Route("/annuler/{id}", name="annuler")
     */
    public function cancel(
        Request                $request,
        int                    $id,
        TripRepository         $tripRepository,
        UserRepository         $userRepository,
        StateRepository        $stateRepository,
        EntityManagerInterface $entityManager,
        TripHandler            $tripHandler
    ): Response
    {

        $trip = $tripRepository->find($id);
        $tripForm = $this->createForm(CancelTripType::class, $trip);
        $tripForm->handleRequest($request);

        if ($tripForm->isSubmitted() && $tripForm->isValid()) {

            $trip->setState($stateRepository->find(6));

            $entityManager->persist($trip);
            $entityManager->flush();

            $this->addFlash('success', 'Sortie supprimée');
            return $this->redirectToRoute('main_index');
        }
        return $this->render('trip/cancel.html.twig', [
            'trip' => $trip,
            'tripForm' => $tripForm->createView()
        ]);
    }

    /**
     * @Route("/seDesister/{id}", name="seDesister")
     */
    public function desist(
        int                    $id,
        TripRepository         $tripRepository,
        UserRepository         $userRepository,
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

        return $this->redirectToRoute('main_index');

    }

    /**
     * @Route("/sInscrire/{id}", name="sInscrire")
     */
    public function tripRegistration(
        int                    $id,
        TripRepository         $tripRepository,
        UserRepository         $userRepository,
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
        return $this->redirectToRoute('main_index');

    }
}
