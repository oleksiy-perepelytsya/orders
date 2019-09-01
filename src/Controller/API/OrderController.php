<?php
namespace App\Controller\API;

use App\Entity\Order;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class OrderController extends AbstractController
{
    /**
     * @Route("/order/create", name="order_create")
     */
    public function orderCreate()
    {
        $request = Request::createFromGlobals();
        $status = $request->request->get('status');

        if(!$status || $status != Order::STATUS_CREATED){
            return $this->json(['status' => 400]);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $order = new Order();
        $order->setStatus($status);
        $entityManager->persist($order);
        $entityManager->flush();

        return $this->json(['status' => 200, 'id' => $order->getId()]);
    }

    /**
     * @Route("/order/{id}", name="order_get")
     */
    public function orderGet($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $order = $entityManager->find(Order::class,(int) $id);

        if(!$order){
            return $this->json(['status' => 404]);
        }

        return $this->json(['status' => 200, 'resource' => $order->toArray()]);
    }

    /**
     * @Route("/order/{id}/status/{status}", name="order_set_status")
     */
    public function setOrderStatus($id, $status)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $order = $entityManager->find(Order::class,(int) $id);

        if(!$order || !in_array($status, Order::ALLOWED_STATUSES)){
            return $this->json(['status' => 404]);
        }

        $order->setStatus($status);
        $entityManager->persist($order);
        $entityManager->flush();

        return $this->json(['status' => 200]);
    }
}