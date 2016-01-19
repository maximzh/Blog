<?php
/**
 * Created by PhpStorm.
 * User: fumus
 * Date: 19.01.16
 * Time: 14:21
 */

namespace AppBundle\Tests\Controller\Admin;


use AppBundle\Tests\Controller\AbstractController;

class TagController extends AbstractController
{
    public function testNew()
    {
        $this->requestTest(200, "/admin/tag/new", 'GET');
    }
}