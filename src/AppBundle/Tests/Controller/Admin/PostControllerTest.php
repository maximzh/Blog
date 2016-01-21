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
        $this->requestTest($expectedStatusCode, $path, 'GET');
    }

    public function testNew()
    {
        $this->requestTest(200, "/admin/post/new", 'GET');
    }

    public function testEdit()
    {
        $this->requestTest(200, "/admin/post/edit/1", 'GET');
    }

    public function indexProvider()
    {
        return [
            [200, "/admin/post"],
            ];
    }
}