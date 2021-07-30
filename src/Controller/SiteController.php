<?php

namespace App\Controller;

use App\Data\SearchSiteData;
use App\Entity\Campus;
use App\Form\SearchSiteFormType;
use App\Form\SiteType;
use App\Repository\CampusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SiteController
 * @package App\Controller
 * @Route("/site", name="site_")
 */
class SiteController extends AbstractController
{
    /**
     * @Route("/list", name="list")
     */
    public function list(Request $request, CampusRepository $campusRepository): Response
    {
        $site = new Campus();

        $data = new SearchSiteData();
        $searchForm = $this->createForm(SearchSiteFormType::class, $data);
        $searchForm->handleRequest($request);

        $form = $this->createForm(SiteType::class, $site);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($site);
            $entityManager->flush();

            return $this->redirectToRoute('site_list');
        }

        $siteList = $campusRepository->findSearch($data, $site);

        return $this->render('site/list.html.twig', [
            'siteListType' => $form->createView(),
            'siteList' => $siteList,
            'searchForm' => $searchForm->createView(),
            'site' => $site
        ]);
    }

    /**
     * @param $id
     * @param CampusRepository $campusRepository
     * @param EntityManagerInterface $entityManager
     * @return Response
     * @Route("/delete/{id}", name="delete")
     */
    public function delete($id,
                           CampusRepository $campusRepository,
                           EntityManagerInterface $entityManager): Response
    {
        $site = $campusRepository->find($id);
        $entityManager->remove($site);
        $entityManager->flush();

        return $this->redirectToRoute('site_list');
    }

    /**
     * @param $id
     * @param Request $request
     * @param CampusRepository $campusRepository
     * @param EntityManagerInterface $entityManager
     * @return Response
     * @Route("/update/{id}", name="update")
     */
    public function update($id,
                           Request $request,
                           CampusRepository $campusRepository,
                           EntityManagerInterface $entityManager): Response
    {
        $currentSite = $campusRepository->find($id);
        if (!$currentSite){
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(SiteType::class, new Campus());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $currentSite->setName($form->get('name')->getData());
            $entityManager->flush();

            return $this->redirectToRoute('site_list');
        }

        return $this->render('site/update.html.twig', [
            'siteListTypeUpdate' => $form->createView(),
            'currentSite' => $currentSite
        ]);
    }
}
