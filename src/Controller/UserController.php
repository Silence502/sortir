<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileModifierType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

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
     * @param $id
     * @return Response
     * @throws NonUniqueResultException
     * @Route("/update/{id}", name="update")
     */
    public function profileUpdate(Request $request,
                                  EntityManagerInterface $entityManager,
                                  UserRepository $userRepository,
                                  UserPasswordHasherInterface $passwordEncoder,
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
}