<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 17.12.18
 * Time: 0:12
 */

namespace App\Tests\Traits;


use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\StringInput;

/**
 * Trait AuthorizedTestCaseTrait
 * @package App\Tests\Traits
 */
trait AuthorizedTestCaseTrait
{
    /**
     * @var Client
     */
    protected static $client;

    /**
     * @var String
     */
    protected static $token;

    /**
     * @var String
     */
    protected static $anotherToken;

    /**
     * @beforeClass
     */
    public static function someInit(){
        static::$client = static::createClient();

        $kernel = static::$client->getKernel();
        $app = new Application($kernel);
        $app->setAutoExit(false);
        $app->run(new StringInput('doctrine:fixtures:load -n'));

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
        static::$token = 'Bearer ' . json_decode(static::$client->getResponse()->getContent())->token;

        static::$client->request(
            'POST',
            '/api/login_check',
            [],
            [],
            [
                'HTTP_ACCEPT' => 'application/json',
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode(['username' => 'spam', 'password' => 'spam'])
        );
        static::$anotherToken = 'Bearer ' . json_decode(static::$client->getResponse()->getContent())->token;
    }

}