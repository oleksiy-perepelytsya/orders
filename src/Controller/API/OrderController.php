<?php
namespace App\Controller\API;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends AbstractController
{
    /**
     * @Route("/order/{action}", name="order")
     */
    public function orderResource($action)
    {
        return $this->json(['response' => $action]);
    }
}