<?php


namespace App\Services;


use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

class UploadImg extends AbstractController
{
//    public function UplaodImgProfile(User $user,
//                                     SluggerInterface $slugger,
//                                     $userImg)
//    {
//        $userImg = $form->get('img')->getData();
//        if ($userImg) {
//            $originalImg = pathinfo($userImg->getClientOriginalName(), PATHINFO_FILENAME);
//            $safeImg = $slugger->slug($originalImg);
//            $newImg = $safeImg . '-' . uniqid() . '-' . $userImg->guessExtension();
//
//            try {
//                $userImg->move(
//                    $this->getParameter('img_directory'),
//                    $newImg
//                );
//            } catch (FileException $e) {
//                $e->getMessage();
//            }
//            $user->setImg($newImg);
//        }
//    }
}