<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{

     /**
     * @Route("/api/user", name="update_user")
     * @Method({"PUT"})
     */
    public function updateUser(Request $request, UserInterface $user, EntityManagerInterface $manager) {

        $data = $request->getContent();
        $data = json_decode($data);

        foreach($data as $email) {
            $user->setEmail($email);
        }
        
        $manager->persist($user);
        $manager->flush();

        return $this->json($user, 200, []);
    }

    /**
    * @Route("/api/user/show", name="show_user")
    * @Method({"GET"})
    */
   public function showUser(UserInterface $user) {

       return $this->json($user, 200, []);
   }
}
