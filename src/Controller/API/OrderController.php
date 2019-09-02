<?php
namespace App\Controller\API;

use App\Entity\Order;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use GuzzleHttp\Client;

class OrderController extends AbstractController
{
    const ORDER_PROCESSING_CONFIRMED  = 'confirmed';
    const ORDER_PROCESSING_DECLINED = 'declined';

    const STATUS_DELIVERED_DELAY_SECONDS = 5;

    /**
     * @Route("/order/create", name="order_create")
     */
    public function orderCreate()
    {
        $request = Request::createFromGlobals();
        $status = $request->request->get('status');

        if(!$status || $status != Order::STATUS_CREATED){
            return $this->json(['status' => Response::HTTP_BAD_REQUEST]);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $order = new Order();
        $order->setStatus($status);
        $entityManager->persist($order);
        $entityManager->flush();

        $client = new Client([
            'base_uri' => 'http://payments.com:80',
            'defaults' => [
                'exceptions' => false
            ]
        ]);

        $response = $client->post('/payment/process-order', [
            'headers' => [
                'access_token' => 'RsT5Oj4zRn438zqMDgV3Aa'
            ],
            'form_params' => [
                'order' => $order->getId()
            ]
        ]);

        $responseBodyArray = json_decode($response->getBody(true), true);

        if($response->getStatusCode() != Response::HTTP_OK || !isset($responseBodyArray['result'])){
            return $this->json(['status' => Response::HTTP_BAD_REQUEST]);
        }

        switch($responseBodyArray['result']){
            case self::ORDER_PROCESSING_CONFIRMED :
                $this->setOrderStatus($order->getId(), Order::STATUS_CONFIRMED);
                break;
            case self::ORDER_PROCESSING_DECLINED :
                $this->setOrderStatus($order->getId(), Order::STATUS_CANCELED);
                break;
        }

        return $this->json(['status' => Response::HTTP_OK, 'id' => $order->getId()]);
    }

    /**
     * @Route("/order/{id}", name="order_get")
     */
    public function orderGet($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $order = $entityManager->find(Order::class,(int) $id);

        if(!$order){
            return $this->json(['status' => Response::HTTP_NOT_FOUND ]);
        }

        return $this->json(['status' => Response::HTTP_OK, 'resource' => $order->toArray()]);
    }

    /**
     * @Route("/order/{id}/status/{status}", name="order_set_status")
     */
    public function setOrderStatus($id, $status)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $order = $entityManager->find(Order::class,(int) $id);

        if(!$order || !in_array($status, Order::ALLOWED_STATUSES)){
            return $this->json(['status' => Response::HTTP_NOT_FOUND]);
        }

        $order->setStatus($status);
        $entityManager->persist($order);
        $entityManager->flush();

        if($status == Order::STATUS_CONFIRMED){
            $client = new Client([
                'base_uri' => 'http://test-task.com:80',
                'defaults' => [
                    'exceptions' => false
                ],
                'delay' => self::STATUS_DELIVERED_DELAY_SECONDS * 1000
            ]);

            $client->get("/order/{$order->getId()}/status/" . Order::STATUS_DELIVERED);
        }

        return $this->json(['status' => Response::HTTP_OK]);
    }
}