<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileModifierAdminType;
use App\Form\ProfileModifierType;
use App\Repository\TripRepository;
use App\Repository\UserRepository;
use App\Services\UploadImg;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * Class UserController
 * @package App\Controller
 * @Route("/user", name="user_")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/{id}", name="profile")
     */
    public function profile(): Response
    {
        return $this->render('user/profile.html.twig');
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param UserRepository $userRepository
     * @param UserPasswordHasherInterface $passwordEncoder
     * @param SluggerInterface $slugger
     * @param $id
     * @return Response
     * @throws NonUniqueResultException
     * @Route("/update/{id}", name="update")
     */
    public function profileUpdate(Request $request,
                                  EntityManagerInterface $entityManager,
                                  UserRepository $userRepository,
                                  UserPasswordHasherInterface $passwordEncoder,
                                  SluggerInterface $slugger,
                                  $id): Response
    {
        //Getting the current user
        $currentUser = $userRepository->findById($id);
        if (!$currentUser) {
            throw $this->createNotFoundException();
        }

        //Building form
        $form = $this->createForm(ProfileModifierType::class, new User());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $currentUserImg = $form->get('img')->getData();
            if ($currentUserImg) {
                $originalImg = pathinfo($currentUserImg->getClientOriginalName(), PATHINFO_FILENAME);
                $safeImg = $slugger->slug($originalImg);
                $newImg = $safeImg.'-'.uniqid().'-'.$currentUserImg->guessExtension();

                try {
                    $currentUserImg->move(
                        $this->getParameter('img_directory'),
                        $newImg
                    );
                } catch (FileException $e) {
                    $e->getMessage();
                }
                $currentUser->setImg($newImg);
            }
            $currentUser->setNickname($form->get('nickname')->getData());
            $currentUser->setFirstname($form->get('firstname')->getData());
            $currentUser->setLastname($form->get('lastname')->getData());
            $currentUser->setPhoneNumber($form->get('phoneNumber')->getData());
            $currentUser->setPassword($passwordEncoder->hashPassword($currentUser,
                $form->get('plainPassword')->getData()));
            $entityManager->flush();

            return $this->redirectToRoute('main_index');
        }


        return $this->render('user/profile_update.html.twig', [
            'profileModifierType' => $form->createView(),
            'currentUser' => $currentUser
        ]);
    }

    /**
     * @param UserRepository $userRepository
     * @param $id
     * @return Response
     * @throws NonUniqueResultException
     * @Route("/show/{id}", name="show")
     */
    public function profileShow(UserRepository $userRepository,
                                $id): Response
    {
        $user = $userRepository->findById($id);
        if (!$user) {
            throw $this->createNotFoundException();
        }

        return $this->render('user/profile_show.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * @param UserRepository $userRepository
     * @return Response
     * @Route("/admin/list/", name="admin_list")
     */
    public function list(UserRepository $userRepository,
                         TripRepository $tripRepository): Response
    {
        $user = $userRepository->findAll();
        $trip = $tripRepository->findAll();
        if (!$user){
            throw $this->createNotFoundException();
        }

        return $this->render('user/list.html.twig', [
            'users' => $user,
            'trips' => $trip
        ]);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param UserRepository $userRepository
     * @param UserPasswordHasherInterface $passwordEncoder
     * @param SluggerInterface $slugger
     * @param $id
     * @return Response
     * @throws NonUniqueResultException
     * @Route("/admin/update/{id}", name="admin_update")
     */
    public function profileUpdateAdmin(Request $request,
                                  EntityManagerInterface $entityManager,
                                  UserRepository $userRepository,
                                  UserPasswordHasherInterface $passwordEncoder,
                                  SluggerInterface $slugger,
                                  $id): Response
    {
        //Getting the current user
        $currentUser = $userRepository->findById($id);
        if (!$currentUser) {
            throw $this->createNotFoundException();
        }

        $testUpdate = true;

        //Building form
        $form = $this->createForm(ProfileModifierAdminType::class, new User());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $currentUserImg = $form->get('img')->getData();
            if ($currentUserImg) {
                $originalImg = pathinfo($currentUserImg->getClientOriginalName(), PATHINFO_FILENAME);
                $safeImg = $slugger->slug($originalImg);
                $newImg = $safeImg.'-'.uniqid().'-'.$currentUserImg->guessExtension();

                try {
                    $currentUserImg->move(
                        $this->getParameter('img_directory'),
                        $newImg
                    );
                } catch (FileException $e) {
                    $e->getMessage();
                }
                $currentUser->setImg($newImg);
            }
            $currentUser->setNickname($form->get('nickname')->getData());
            $currentUser->setFirstname($form->get('firstname')->getData());
            $currentUser->setLastname($form->get('lastname')->getData());
            $currentUser->setPhoneNumber($form->get('phoneNumber')->getData());
            $currentUser->setIsActive($form->get('isActive')->getData());
            $entityManager->flush();

            return $this->redirectToRoute('user_admin_list');
        }


        return $this->render('user/profile_update_admin.html.twig', [
            'profileModifierAdminType' => $form->createView(),
            'currentUser' => $currentUser,
            'testUpdate' => $testUpdate
        ]);
    }
}

