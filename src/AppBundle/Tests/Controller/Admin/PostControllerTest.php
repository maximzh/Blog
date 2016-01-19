<?php
/**
 * Created by PhpStorm.
 * User: fumus
 * Date: 19.01.16
 * Time: 12:45
 */

namespace AppBundle\Tests\Controller\Admin;


use AppBundle\Tests\Controller\AbstractController;
use AppBundle\Tests\TestBaseWeb;

class PostControllerTest extends AbstractController
{
    /**
     * @dataProvider indexProvider
     * @param $expectedStatusCode
     * @param $path
     */
    public function testIndex($expectedStatusCode, $path)
    {
        //$client = static::createClient();
        //$crawler = $client->request('GET', '/admin/post');
        $this->requestTest($expectedStatusCode, $path, 'GET');
    }

    public function indexProvider()
    {
        return [
            [200, "/admin/post"],
            ];
    }
}