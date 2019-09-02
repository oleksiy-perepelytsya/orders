<?php
namespace App\Tests\Controller\API;

use App\Controller\API\OrderController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Order;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;

class OrderControllerTest extends TestCase
{
    /**
     * @return int $resourceId
     */
    public function testOrderCreation()
    {
        $client = new Client([
            'base_uri' => 'http://test-task.com:80',
            'defaults' => [
                'exceptions' => false
            ]
        ]);

        $response = $client->post('/order/create', [
            'form_params' => [
                'status' => Order::STATUS_CREATED
            ]
        ]);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $responseBodyArray = json_decode($response->getBody(true), true);
        $this->assertArrayHasKey('id', $responseBodyArray);

        return $responseBodyArray['id'];
    }

    /**
     * @depends testOrderCreation
     * @param int $resourceId
     * @return int $resourceId
     */
    public function testOrderStatus($resourceId)
    {
        $client = new Client([
            'base_uri' => 'http://test-task.com:80',
            'defaults' => [
                'exceptions' => false
            ]
        ]);

        $response = $client->get("/order/{$resourceId}");

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $responseBodyArray = json_decode($response->getBody(true), true);
        $this->assertArrayHasKey('resource', $responseBodyArray);
        $this->assertArrayHasKey('status', $responseBodyArray['resource']);
        $this->assertContains($responseBodyArray['resource']['status'], Order::ALLOWED_STATUSES);

        return $resourceId;
    }

    /**
     * @depends testOrderStatus
     * @param int $resourceId
     * @return int $resourceId
     */
    public function testOrderCanceling($resourceId)
    {
        $client = new Client([
            'base_uri' => 'http://test-task.com:80',
            'defaults' => [
                'exceptions' => false
            ]
        ]);

        $response = $client->get("/order/{$resourceId}/status/" . Order::STATUS_CANCELED);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        return $resourceId;
    }

    /**
     * @depends testOrderCanceling
     * @param int $resourceId
     * @return int $resourceId
     */
    public function testOrderStatusCanceled($resourceId)
    {
        $client = new Client([
            'base_uri' => 'http://test-task.com:80',
            'defaults' => [
                'exceptions' => false
            ]
        ]);

        $response = $client->get("/order/{$resourceId}");

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $responseBodyArray = json_decode($response->getBody(true), true);

        $this->assertArrayHasKey('resource', $responseBodyArray);
        $this->assertArrayHasKey('status', $responseBodyArray['resource']);
        $this->assertContains($responseBodyArray['resource']['status'], Order::ALLOWED_STATUSES);
        $this->assertEquals(Order::STATUS_CANCELED, $responseBodyArray['resource']['status']);

        return $resourceId;
    }
}