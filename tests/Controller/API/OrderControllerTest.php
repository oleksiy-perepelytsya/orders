<?php
namespace App\Tests\Controller\API;

use App\Controller\API\OrderController;
use PHPUnit\Framework\TestCase;

class OrderControllerTest extends TestCase
{
    public function testOrder()
    {
        $client = new \GuzzleHttp\Client([
            'base_uri' => 'http://test-task.com:80',
            'defaults' => [
                'exceptions' => false
            ]
        ]);

        $action = 'test';

        $response = $client->get("/order/{$action}");

        $this->assertEquals(200, $response->getStatusCode());

        $finishedData = json_decode($response->getBody(true), true);
        $this->assertArrayHasKey('response', $finishedData);
    }
}