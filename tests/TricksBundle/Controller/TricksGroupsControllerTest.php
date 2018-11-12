<?php
namespace Tests\TricksBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TricksGroupsControllerTest extends WebTestCase
{
    public function testHomePage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertGreaterThan(0, $crawler->filter('h2:contains("SnowTrix")')->count());
    }
}