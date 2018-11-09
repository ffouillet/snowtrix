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

    private $temporaryTrickPhoto;

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
        $values['trick']['videos'][0]['embedCode'] = '<iframe width="560" height="315" src="https://www.youtube.com/embed/n0F6hSpxaFc" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';

        // Create a photo and use it as a trick photo for the test.
        $this->temporaryTrickPhoto = tempnam(sys_get_temp_dir(), 'uploaded-func-test-trick-photo'); // Create the file
        imagejpeg(imagecreatetruecolor(200,200), $this->temporaryTrickPhoto);
        $trickPhoto = new UploadedFile($this->temporaryTrickPhoto, 'trick-test-photo', 'image/jpeg');

        // Submit the form with values and the trick photo.
        $crawler = $this->client->request($form->getMethod(), $form->getUri(), $values,
            ['trick[photos][0][photo]' => $trickPhoto]);

        dump($crawler);
        dump($this->client->getResponse()->getStatusCode());

        //$trickTestPhoto = array('tmp_name' => '/path/to/photo.jpg', 'name' => 'photo.jpg', 'type' => 'image/jpeg', 'size' => 123, 'error' => UPLOAD_ERR_OK);
        //$form['trick[photos][1][photo]'] = $trickTestPhoto['tmp_name'];

        //dump($form['trick[photos][1][photo]']);

        // Uncomplete, have to create the form completion and submission process.
    }

    public function tearDown()
    {
        parent::tearDown();

        if(file_exists($this->temporaryTrickPhoto)) {
            unlink($this->temporaryTrickPhoto);
        }

    }


}