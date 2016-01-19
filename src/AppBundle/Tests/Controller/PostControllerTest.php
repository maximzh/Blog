<?php
/**
 * Created by PhpStorm.
 * User: fumus
 * Date: 19.01.16
 * Time: 11:21
 */

namespace AppBundle\Tests\Controller;


class PostControllerTest extends AbstractController
{
    /**
     * @dataProvider showProvider
     * @param $expectedStatusCode
     * @param $path
     */
    public function testShow($expectedStatusCode, $path)
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/post/post-one');

        $this->requestTest($expectedStatusCode, $path, 'GET');
        $this->assertEquals(1, $crawler->filter('h1')->count());
        $this->assertEquals(3, $crawler->filter('.comment')->count());
    }

    public function testSearch()
    {
        $client = static::createClient();
        $crawler = $client->request('POST', "search text");
        $this->assertEquals(1, $crawler->filter('h1')->count());

    }

    public function showProvider()
    {
        return
            [
                [200, "/post/post-one"],
                [404, "/post/2"]
            ];
    }

}