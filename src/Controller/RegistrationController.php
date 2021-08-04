<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request,
                             UserPasswordHasherInterface $passwordEncoder,
                             SluggerInterface $slugger): Response
    {

        if ($this->getUser() != null){
            return $this->redirectToRoute('main_index');
        }

        $user = new User();
        $user->setIsActive('true');
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $userImg = $form->get('img')->getData();
            if ($userImg) {
                $originalImg = pathinfo($userImg->getClientOriginalName(), PATHINFO_FILENAME);
                $safeImg = $slugger->slug($originalImg);
                $newImg = $safeImg.'-'.uniqid().'-'.$userImg->guessExtension();

                try {
                    $userImg->move(
                        $this->getParameter('img_directory'),
                        $newImg
                    );
                } catch (FileException $e) {
                    $e->getMessage();
                }
                $user->setImg($newImg);
            }
            // encode the plain password
            $user->setIsActive(true);
            $user->setRoles(["ROLE_USER"]);
            $user->setPassword(
                $passwordEncoder->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('main_index');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
