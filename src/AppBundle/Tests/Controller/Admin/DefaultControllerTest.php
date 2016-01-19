<?php
/**
 * Created by PhpStorm.
 * User: fumus
 * Date: 19.01.16
 * Time: 12:46
 */

namespace AppBundle\Tests\Controller\Admin;


use AppBundle\Tests\Controller\AbstractController;

class DefaultControllerTest extends AbstractController
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

    public function indexProvider()
    {
        return
            [
                [302, "/admin"],
                [302, "/admin/"],

            ];
    }
}