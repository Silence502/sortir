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
     * @param UserRepository $userRepository
     * @param $id
     * @return Response
     * @Route("/update/{id}", name="update")
     * @throws NonUniqueResultException
     * @throws ORMException
     */
    public function profileUpdate(Request $request,
                                  EntityManagerInterface $entityManager,
                                  $id): Response
    {
        //Getting the current user
//        $currentUser = $userRepository->findById($id);
        $currentUser = $entityManager->getRepository(User::class)->findById($id);
        if (!$currentUser) {
            throw $this->createNotFoundException();
        }

        //Building form
        $user = new User();
        $form = $this->createForm(ProfileModifierType::class, new User());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $currentUser->setNickname($form->get('nickname')->getData());
            $currentUser->setFirstname($form->get('firstname')->getData());
            $currentUser->setLastname($form->get('lastname')->getData());
            $currentUser->setPhoneNumber($form->get('phoneNumber')->getData());
//            $currentUser->setEmail($form->get('email')->getData());
            $entityManager->flush();

            return $this->redirectToRoute('main_index');
        }


        return $this->render('user/profile_update.html.twig', [
            'profileModifierType' => $form->createView(),
            'currentUser' => $currentUser
        ]);
    }
}