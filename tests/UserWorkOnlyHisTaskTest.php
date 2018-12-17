<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 17.12.18
 * Time: 0:45
 */

namespace App\Tests;

use App\Tests\Traits\AuthorizedTestCaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserWorkOnlyHisTaskTest extends WebTestCase
{
    use AuthorizedTestCaseTrait;

    public function testAnotherUserPostTask()
    {
        $data = [
            "content" => "task of the second user",
            "completed" => true,
            "created_at" => "2013-12-05T12:00:00+02:00"
        ];
        static::$client->request(
            'POST',
            '/api/task',
            [],
            [],
            [
                'HTTP_JWT_AUTHORIZATION' => static::$anotherToken,
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
     * @depends testAnotherUserPostTask
     * @param array $anotherData
     * @return array
     */
    public function testGetTask($anotherData)
    {
        //user 1 get another user's task
        static::$client->request(
            'GET',
            '/api/task/'.$anotherData['id'],
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

        return $anotherData;
    }

    /**
     * @depends testGetTask
     * @param array $anotherData
     * @return array
     */
    public function testPutTask($anotherData)
    {
        static::$client->request(
            'PUT',
            '/api/task/'.$anotherData['id'],
            [],
            [],
            [
                'HTTP_JWT_AUTHORIZATION' => static::$token,
                'HTTP_ACCEPT' => 'application/json',
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode(array_diff_key($anotherData, ['id' => true]))
        );
        $response = static::$client->getResponse();

        $this->assertTrue($response->isClientError());
        $this->assertEquals(404, $response->getStatusCode());

        return $anotherData;
    }

    /**
     * @depends testPutTask
     * @param array $anotherData
     * @return array
     */
    public function testGetTasks($anotherData)
    {
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

        return $anotherData;
    }

    /**
     * @depends testGetTasks
     * @param array $anotherData
     * @return array
     */
    public function testDeleteTask($anotherData)
    {
        static::$client->request(
            'DELETE',
            '/api/task/'.$anotherData['id'],
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

        return $anotherData;
    }
}
