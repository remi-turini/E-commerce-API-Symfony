<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\OrderRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{
    /**
     * @Route("/api/orders", name="order_list")
     * @Method({"GET"})
     */
    public function listOrder(UserInterface $user, OrderRepository $orderRepository) {

        $order = $orderRepository->findBy(['user' => $user]);
        //dd($order);
        
        return $this->json($order, 200, []);
    }

        /**
     * @Route("/api/order/{id}", name="order_show")
     * @Method({"GET"})
     */
    public function showOrder($id, UserInterface $user, OrderRepository $orderRepository) {

        $order = $orderRepository->findBy(['user' => $user, 'id' => $id]);

        //dd($order);
        
        return $this->json($order, 200, []);
    }
}
