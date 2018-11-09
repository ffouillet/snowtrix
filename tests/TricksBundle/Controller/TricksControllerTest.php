<?php
namespace Tests\TricksBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;
use Tests\RequiredAuthenticationWebTestCase;
use UserBundle\Entity\User;

class TricksControllerTest extends RequiredAuthenticationWebTestCase
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

    public function testTrickAdd(){

        $crawler = $this->client->request(Request::METHOD_GET, '/add-trick');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        // Get the form
        $formButtonCrawlerNode = $crawler->selectButton('trick_submit');
        $form = $formButtonCrawlerNode->form();

        // Fill in the form.
        $form['trick[name]'] = 'Functional Test Trick';
        $form['trick[description]'] = 'A trick used for functional tests purpose only.';
        $form['trick[groups]']->select([7,8]); // Select one or more Trick Group id.

        // Add an embed video field and value to the form
        // This field doesn't exists initially here because it is generated via javascript
        $values = $form->getPhpValues();
        // Value required is an embed code.
        $values['trick']['videos'][0]['video'] = '<iframe width="560" height="315" src="https://www.youtube.com/embed/n0F6hSpxaFc" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';

        $trickPhoto = $this->createMock('Symfony\Component\HttpFoundation\File\UploadedFile');
        $files = ['trick[photos][0][photo]' => $trickPhoto];

        $crawler = $this->client->request($form->getMethod(), $form->getUri(), $values,
            $files);

        dump($crawler);

        //$trickTestPhoto = array('tmp_name' => '/path/to/photo.jpg', 'name' => 'photo.jpg', 'type' => 'image/jpeg', 'size' => 123, 'error' => UPLOAD_ERR_OK);
        //$form['trick[photos][1][photo]'] = $trickTestPhoto['tmp_name'];

        //dump($form['trick[photos][1][photo]']);

        // Uncomplete, have to create the form completion and submission process.
    }




}