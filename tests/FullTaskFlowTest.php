<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 16.12.18
 * Time: 21:35
 */

namespace App\Tests;


use App\Tests\Traits\AuthorizedTestCaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FullTaskFlowTest extends WebTestCase
{
    use AuthorizedTestCaseTrait;

    /**
     * @return array
     */
    public function testPostTaskReturnId()
    {
        $data = [
            "content" => "first test task1",
            "completed" => false,
            "created_at" => "2011-12-05T12:00:00+02:00"
        ];
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
            json_encode($data)
        );
        $response = static::$client->getResponse();

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertJson($response->getContent());

        $obj = json_decode($response->getContent());
        $this->assertEquals("ok", $obj->status);
        $this->assertNotEmpty($obj->id);

        $data['id'] = $obj->id;
        return $data;
    }

    /**
     * @depends testPostTaskReturnId
     * @param array $data
     * @return array
     */
    public function testGetTask($data) {
        static::$client->request(
            'GET',
            '/api/task/'.$data['id'],
            [],
            [],
            [
                'HTTP_JWT_AUTHORIZATION' => static::$token,
                'HTTP_ACCEPT' => 'application/json',
                'CONTENT_TYPE' => 'application/json',
            ]
        );
        $response = static::$client->getResponse();

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getContent());

        $this->assertJsonStringEqualsJsonString(json_encode($data), $response->getContent());

        return $data;
    }

    /**
     * @depends testGetTask
     * @param array $data
     * @return array
     */
    public function testPutTask($data) {
        $data['content'] = 'some new content';

        static::$client->request(
            'PUT',
            '/api/task/'.$data['id'],
            [],
            [],
            [
                'HTTP_JWT_AUTHORIZATION' => static::$token,
                'HTTP_ACCEPT' => 'application/json',
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode(array_diff_key($data, ['id' => true]))
        );
        $response = static::$client->getResponse();

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals(204, $response->getStatusCode());

        return $data;
    }

    /**
     * @depends testPutTask
     * @param array $data
     * @return array
     */
    public function testGetUpdatedTask($data) {
        static::$client->request(
            'GET',
            '/api/task/'.$data['id'],
            [],
            [],
            [
                'HTTP_JWT_AUTHORIZATION' => static::$token,
                'HTTP_ACCEPT' => 'application/json',
                'CONTENT_TYPE' => 'application/json',
            ]
        );
        $response = static::$client->getResponse();

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getContent());

        $this->assertJsonStringEqualsJsonString(json_encode($data), $response->getContent());

        return $data;
    }

    /**
     * @depends testGetUpdatedTask
     * @param array $data
     * @return array
     */
    public function testGetTasks($data) {
        static::$client->request(
            'GET',
            '/api/task',
            [],
            [],
            [
                'HTTP_JWT_AUTHORIZATION' => static::$token,
                'HTTP_ACCEPT' => 'application/json',
                'CONTENT_TYPE' => 'application/json',
            ]
        );
        $response = static::$client->getResponse();

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getContent());

        $this->assertJsonStringEqualsJsonString(json_encode([$data]), $response->getContent());

        return $data;
    }

    /**
     * @depends testGetTasks
     * @param array $data
     * @return array
     */
    public function testDeleteTask($data) {
        static::$client->request(
            'DELETE',
            '/api/task/'.$data['id'],
            [],
            [],
            [
                'HTTP_JWT_AUTHORIZATION' => static::$token,
                'HTTP_ACCEPT' => 'application/json',
                'CONTENT_TYPE' => 'application/json',
            ]
        );
        $response = static::$client->getResponse();

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals(204, $response->getStatusCode());

        return $data;
    }

    /**
     * @depends testDeleteTask
     * @param array $data
     * @return array
     */
    public function testgetDeletedTask($data) {
        static::$client->request(
            'GET',
            '/api/task/'.$data['id'],
            [],
            [],
            [
                'HTTP_JWT_AUTHORIZATION' => static::$token,
                'HTTP_ACCEPT' => 'application/json',
                'CONTENT_TYPE' => 'application/json',
            ]
        );
        $response = static::$client->getResponse();

        $this->assertTrue($response->isClientError());
        $this->assertEquals(404, $response->getStatusCode());

        return $data;
    }

    /**
     * @depends testgetDeletedTask
     * @param array $data
     * @return array
     */
    public function testGetTasksAfterDelete($data) {
        static::$client->request(
            'GET',
            '/api/task',
            [],
            [],
            [
                'HTTP_JWT_AUTHORIZATION' => static::$token,
                'HTTP_ACCEPT' => 'application/json',
                'CONTENT_TYPE' => 'application/json',
            ]
        );
        $response = static::$client->getResponse();

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getContent());

        $this->assertJsonStringEqualsJsonString(json_encode([]), $response->getContent());

        return $data;
    }
}