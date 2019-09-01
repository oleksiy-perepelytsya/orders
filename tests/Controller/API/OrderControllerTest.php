<?php
namespace App\Tests\Controller\API;

use App\Controller\API\OrderController;
use App\Entity\Order;
use PHPUnit\Framework\TestCase;

class OrderControllerTest extends TestCase
{
    /**
     * @return int $resourceId
     */
    public function testOrderCreation()
    {
        $client = new \GuzzleHttp\Client([
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

        $this->assertEquals(200, $response->getStatusCode());

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
        $client = new \GuzzleHttp\Client([
            'base_uri' => 'http://test-task.com:80',
            'defaults' => [
                'exceptions' => false
            ]
        ]);

        $response = $client->get("/order/{$resourceId}");

        $this->assertEquals(200, $response->getStatusCode());

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
        $client = new \GuzzleHttp\Client([
            'base_uri' => 'http://test-task.com:80',
            'defaults' => [
                'exceptions' => false
            ]
        ]);

        $response = $client->get("/order/{$resourceId}/status/" . Order::STATUS_CANCELED);

        $this->assertEquals(200, $response->getStatusCode());

        return $resourceId;
    }

    /**
     * @depends testOrderCanceling
     * @param int $resourceId
     * @return int $resourceId
     */
    public function testOrderStatusCanceled($resourceId)
    {
        $client = new \GuzzleHttp\Client([
            'base_uri' => 'http://test-task.com:80',
            'defaults' => [
                'exceptions' => false
            ]
        ]);

        $response = $client->get("/order/{$resourceId}");

        $this->assertEquals(200, $response->getStatusCode());

        $responseBodyArray = json_decode($response->getBody(true), true);

        $this->assertArrayHasKey('resource', $responseBodyArray);
        $this->assertArrayHasKey('status', $responseBodyArray['resource']);
        $this->assertContains($responseBodyArray['resource']['status'], Order::ALLOWED_STATUSES);
        $this->assertEquals(Order::STATUS_CANCELED, $responseBodyArray['resource']['status']);

        return $resourceId;
    }
}