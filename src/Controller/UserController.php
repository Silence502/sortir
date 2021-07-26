<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileModifierType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
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
    public function profile(UserRepository $userRepository, $id): Response
    {
        //Getting the current user
        $currentUser = $userRepository->findById($id);
        if (!$currentUser) {
            throw $this->createNotFoundException();
        }

        return $this->render('user/profile.html.twig', [
            'currentUser' => $currentUser
        ]);
    }

    /**
     * @param Request $request
     * @param UserRepository $userRepository
     * @param $id
     * @return Response
     * @Route("/", name="update")
     */
    public function profileUpdate(Request $request, UserRepository $userRepository, $id): Response
    {
        //Getting the current user
        $currentUser = $userRepository->findById($id);
        if (!$currentUser) {
            throw $this->createNotFoundException();
        }

        //Building form
        $user = new User();
        $form = $this->createForm(ProfileModifierType::class, $user);
        $form->handleRequest($request);



        return $this->render('user/profile_update.html.twig', [
            'profileModifierType' => $form->createView(),
            'currentUser' => $currentUser
        ]);
    }
}