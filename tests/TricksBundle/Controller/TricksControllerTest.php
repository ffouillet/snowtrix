<?php
namespace Tests\TricksBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;
use Tests\LoggedInWebTestCase;
use UserBundle\Entity\User;

class TricksControllerTest extends LoggedInWebTestCase
{

    public function setUp(){
        $this->client = static::createClient();
        // Required in order to access application secured areas.
        $this->logIn($this->client,'main','main',['ROLE_USER']);
    }

    public function testTrickView(){

        $trickSlug = "50-50";
        $url = '/trick/'.$trickSlug;

        $crawler = $this->client->request(Request::METHOD_GET, $url);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

    }

    public function testTrickCreate(){

        $crawler = $this->client->request(Request::METHOD_GET, '/add-trick');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        // We will have to get the form with its button
        $formButtonCrawlerNode = $crawler->selectButton('trick_submit');
        $form = $formButtonCrawlerNode->form();

        // Uncomplete, have to create the form completion and submission process.
    }




}