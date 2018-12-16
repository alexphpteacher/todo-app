<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 16.12.18
 * Time: 1:10
 */

namespace App\Controller;


use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\Annotations;

class IndexController extends FOSRestController
{
    /**
     * @Annotations\Get("/", name="api_info")
     */
    public function index()
    {
        return new JsonResponse([
            'status' => 'ok',
            'api_version' => 'super.puper.parapuper',
        ]);
    }
}