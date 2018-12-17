<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 16.12.18
 * Time: 20:45
 */

namespace App\Tests;


use App\Tests\Traits\AuthorizedTestCaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CreateTaskTest extends WebTestCase
{
    use AuthorizedTestCaseTrait;

    public function testPostTask()
    {
        static::$client->request(
            'POST',
            '/api/task',
            [],
            [],
            [
                'HTTP_JWT_AUTHORIZATION' => static::$token,
                'HTTP_ACCEPT' => 'application/json',
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode([
                "content" => "first test task1",
                "completed" => false,
                "created_at" => "2011-12-05T12:00:00+00:00"
            ])
        );
        $response = static::$client->getResponse();

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    public function testPostTaskWithoutContent()
    {
        static::$client->request(
            'POST',
            '/api/task',
            [],
            [],
            [
                'HTTP_JWT_AUTHORIZATION' => static::$token,
                'HTTP_ACCEPT' => 'application/json',
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode([
//                "content" => "first test task1",
                "completed" => false,
                "created_at" => "2011-12-05T12:00:00+00:00"
            ])
        );
        $response = static::$client->getResponse();

        $this->assertTrue($response->isClientError());
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertJson($response->getContent());

        $obj = json_decode($response->getContent());
        $this->assertEquals(400, $obj->code);
        $this->assertEquals("Validation Failed", $obj->message);
    }
}
