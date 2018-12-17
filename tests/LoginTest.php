<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 16.12.18
 * Time: 16:27
 */

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginTest extends WebTestCase
{
    /**
     * @var Client
     */
    protected static $client;

    /**
     * @beforeClass
     * @throws \Exception
     */
    public static function someInit()
    {
        static::$client = static::createClient();

        $kernel = static::$client->getKernel();
        $app = new Application($kernel);
        $app->setAutoExit(false);
        $app->run(new StringInput('doctrine:fixtures:load -n'));
    }

    public function testGetWithoutToken()
    {
        static::$client->request('GET', '/api');
        $response = static::$client->getResponse();

        $this->assertTrue($response->isClientError());
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertJson($response->getContent());

        $json = <<<JSON
{
  "code": 401,
  "message": "JWT Token not found"
}
JSON;

        $this->assertJsonStringEqualsJsonString($json, $response->getContent());
    }

    public function testLoginBadMethod()
    {
        static::$client->request(
            'GET',
            '/api/login_check',
            [],
            [],
            [
                'HTTP_ACCEPT' => 'application/json',
                'CONTENT_TYPE' => 'application/json',
            ]
        );
        $response = static::$client->getResponse();

        $this->assertTrue($response->isClientError());
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    public function testLoginBadCredential()
    {
        static::$client->request(
            'POST',
            '/api/login_check',
            [],
            [],
            [
                'HTTP_ACCEPT' => 'application/json',
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode(['username' => 'alex', 'password' => 'wrong_password'])
        );
        $response = static::$client->getResponse();

        $this->assertTrue($response->isClientError());
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    public function testLoginGoodCredential()
    {
        static::$client->request(
            'POST',
            '/api/login_check',
            [],
            [],
            [
                'HTTP_ACCEPT' => 'application/json',
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode(['username' => 'alex', 'password' => 'alex'])
        );
        $response = static::$client->getResponse();

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getContent());

        $data = json_decode($response->getContent(), true);
        $this->assertTrue(array_key_exists('token', $data));
    }
}
