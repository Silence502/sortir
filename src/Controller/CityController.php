<?php

namespace App\Controller;

use App\Data\SearchCityData;
use App\Entity\City;
use App\Form\CityType;
use App\Form\SearchCityFormType;
use App\Repository\CityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CityController
 * @package App\Controller
 * @Route("/city", name="city_")
 */
class CityController extends AbstractController
{
    /**
     * @Route("/admin/list", name="list")
     */
    public function list(CityRepository $cityRepository,
                         Request $request): Response
    {
        $city = new City();

        $data = new SearchCityData();
        $searchForm = $this->createForm(SearchCityFormType::class, $data);
        $searchForm->handleRequest($request);

        $form = $this->createForm(CityType::class, $city);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($city);
            $entityManager->flush();

            return $this->redirectToRoute('city_list');
        }

        $cityList = $cityRepository->findSearch($data, $city);

        return $this->render('city/list.html.twig', [
            'cityListType' => $form->createView(),
            'cityList' => $cityList,
            'searchForm' => $searchForm->createView()
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete($id,
                           CityRepository $cityRepository,
                           EntityManagerInterface $entityManager): Response{

        $city = $cityRepository->find($id);
        $entityManager->remove($city);
        $entityManager->flush();

        return $this->redirectToRoute('city_list');
    }

    /**
     * @return RedirectResponse
     * @Route("/update/{id}", name="update")
     */
    public function update($id,
                           Request $request,
                           CityRepository $cityRepository,
                           EntityManagerInterface $entityManager): Response{

        $currentCity = $cityRepository->find($id);
        if (!$currentCity){
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(CityType::class, new City());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $currentCity->setName($form->get('name')->getData());
            $currentCity->setZipCode($form->get('zipCode')->getData());
            $entityManager->flush();

            return $this->redirectToRoute('city_list');
        }

        return $this->render('city/update.html.twig', [
            'cityListTypeUpdate' => $form->createView(),
            'currentCity' => $currentCity
        ]);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     * @Route("/create", name="create")
     */
    public function create(Request $request,
                           EntityManagerInterface  $entityManager): Response
    {
        $city = new City();
        $cityForm = $this->createForm(CityType::class, $city);
        $cityForm->handleRequest($request);

        if ($cityForm->isSubmitted() && $cityForm->isValid()){
            $entityManager->persist($city);
            $entityManager->flush();
            return $this->redirectToRoute('place_create');
        }
        return $this->render('city/create.html.twig', [
            'cityForm' => $cityForm->createView()
        ]);
    }
}
