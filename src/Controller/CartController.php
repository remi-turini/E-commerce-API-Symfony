<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{
    /**
     * @Route("/api/cart/validate", name="product_add")
     * @Method({"POST"})
     */
    public function validate(ProductRepository $productRepository, UserInterface $user, SessionInterface $session, EntityManagerInterface $manager, NormalizerInterface $normalizer) {

        $panier = $session->get('cart');

        $panierWithData = [];

        foreach($panier as $id => $quantity) {
            $panierWithData[] = [
                'product' => $productRepository->find($id),
                'quantity' => $quantity
            ];
        }

        $totalPrice = 0;

        foreach($panierWithData as $item) {
            $totalItem = $item['product']->getPrice() * $item['quantity'];
            $totalPrice += $totalItem;
        }
        
        $panierWithData = [];
        
        foreach($panier as $id => $quantity) {
            $panierWithData[] = [
                'product' => $normalizer->normalize($productRepository->find($id)),
                'quantity' => $quantity
            ];
        }
        
        $order = new Order();

        $order->setUser($user)
              ->setTotalPrice($totalPrice)
              ->setCreatedAt(new \DateTime())
              ->setProducts($panierWithData)
              ;

        $manager->persist($order);
        $manager->flush();

        return $this->json($order , 201, []);

    }

    /**
     * @Route("/api/cart/{id}", name="cart_add")
     * @Method({"PUT"})
     */
    public function add($id, SessionInterface $session) {

        $panier = $session->get('cart', []);

        if(!empty($panier[$id])) {
            $panier[$id]++;
        } else {
            $panier[$id] = 1;
        }

        $session->set('cart', $panier);

        return $this->json($session, 201, []);
    }

    /**
     * @Route("/api/cart", name="cart_list")
     * @Method({"GET"})
     */
    public function list(SessionInterface $session, ProductRepository $productRepository) {

        $panier = $session->get('cart', []);

        $panierWithData = [];

        foreach($panier as $id => $quantity) {
            $panierWithData[] = [
                'product' => $productRepository->find($id),
                'quantity' => $quantity
            ];
        }

        // $totalPrice = 0;

        // foreach($panierWithData as $item) {
        //     $totalItem = $item['product']->getPrice() * $item['quantity'];
        //     $totalPrice += $totalItem;
        // }

        return $this->json($panierWithData, 200, []);
    }

    /**
     * @Route("/api/cart/delete/{id}", name="cart_delete")
     * @Method({"DELETE"})
     */
    public function delete($id, SessionInterface $session) {

        $panier = $session->get('cart', []);

        if(!empty($panier[$id])) {
            if($panier[$id] > 1) {
                $panier[$id]--;
            } else {
                unset($panier[$id]);
            }
        }

        $session->set('cart', $panier);

        return $this->json($session, 200, []);
    }
    
}
