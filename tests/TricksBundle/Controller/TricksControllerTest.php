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
    private $temporaryTrickEditPhoto;

    public function setUp(){
        $this->client = static::createClient();
        // Required in order to access application secured areas.
        $this->logIn($this->client,'main','main',['ROLE_USER']);
    }

    public function testTrickView(){

        $trickSlug = "50-50";
        $url = '/trick/'.$trickSlug;

        $crawler = $this->client->request(Request::METHOD_GET, $url);

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

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
        $form['trick[groups]']->select([1,2]); // Select one or more Trick Group id.

        // Add an embed video field and value to the form
        // This field doesn't exists initially here because it is generated via javascript
        $values = $form->getPhpValues();
        // Value required is an embed code.
        $values['trick']['videos'][0]['embedCode'] = '<iframe width="560" height="315" src="https://www.youtube.com/embed/n0F6hSpxaFc" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';

        // Create a photo and use it as a trick photo for the test.
        $this->temporaryTrickPhoto = tempnam(sys_get_temp_dir(), 'uploaded-func-test-trick-photo'); // Create the file
        imagejpeg(imagecreatetruecolor(200,200), $this->temporaryTrickPhoto);
        $trickPhoto = new UploadedFile($this->temporaryTrickPhoto, 'trick-test-photo', 'image/jpeg');

        $fileValues = [];
        $fileValues['trick']['photos']['0']['photo'] = $trickPhoto;

        // Submit the form with values and the trick photo.
        $crawler = $this->client->request($form->getMethod(), $form->getUri(), $values, $fileValues);

        // Check if redirection occurs.
        $this->assertSame(302, $this->client->getResponse()->getStatusCode());

        // Move now to the trick view url and check if it exists;
        $crawler = $this->client->request(Request::METHOD_GET, '/trick/functional-test-trick');

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());


    }

    public function testTrickEdit() {

        $trickSlug = 'functional-test-trick';
        $url = '/edit-trick/'.$trickSlug;

        $crawler = $this->client->request(Request::METHOD_GET, $url);

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        // Get the edit form
        $formButtonCrawlerNode = $crawler->selectButton('trick_edit_submit');
        $form = $formButtonCrawlerNode->form();

        $form['trick_edit[name]'] = 'Functional Test Trick Edited';
        $form['trick_edit[description]'] = 'An edited trick used for functional tests purpose only.';
        $form['trick_edit[groups]']->select([1]); // Select one or more Trick Group id.

        // Add an embed video field and value to the form
        // This field doesn't exists initially here because it is generated via javascript
        $values = $form->getPhpValues();

        // Value required is an embed code.
        $values['trick_edit']['videos'][1]['embedCode'] = '<iframe width="560" height="315" src="https://www.youtube.com/embed/n0F6hSpxaFc" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';

        // Add a new trickPhoto to the trick
        $this->temporaryTrickEditPhoto = tempnam(sys_get_temp_dir(), 'uploaded-func-test-trick-edit-photo'); // Create the file
        imagejpeg(imagecreatetruecolor(200,200), $this->temporaryTrickEditPhoto);
        $trickEditPhoto = new UploadedFile($this->temporaryTrickEditPhoto, 'trick-test-photo', 'image/jpeg');

        $fileValues = [];
        $fileValues['trick_edit']['photos']['1']['photo'] = $trickEditPhoto;

        // Submit the form with values and the trick photo.
        $crawler = $this->client->request($form->getMethod(), $form->getUri(), $values, $fileValues);

        // Check if redirection occurs.
        $this->assertSame(302, $this->client->getResponse()->getStatusCode());

        // Move now to the trick view url and check if it exists;
        $crawler = $this->client->request(Request::METHOD_GET, '/trick/functional-test-trick-edited');

        // Check if edited values are correct.

        dump($form->get('trick_edit[description]')->getValue());

        $this->assertSame(1, $crawler->filter('html:contains("'.$form->get('trick_edit[name]')->getValue().'")')->count());
        $this->assertSame(1, $crawler->filter('html:contains("'.$form->get('trick_edit[description]')->getValue().'")')->count());

        //static::assertSame(1, $crawler->filter('html:contains("Grabs")')->count());
        //static::assertSame(1, $crawler->filter('html:contains("Rotations")')->count());

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

    }

    public function testTrickDelete() {

        $trickSlug = 'functional-test-trick-edited';
        $url = '/delete-trick/'.$trickSlug;

        $crawler = $this->client->request(Request::METHOD_GET, $url);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        // Get the delete form button.
        $formButtonCrawlerNode = $crawler->selectButton('form_deleteTrick');
        $form = $formButtonCrawlerNode->form();

        // Confirm the trick deletion
        $crawler = $this->client->submit($form);

        // Check if deletion redirected us to homepage.
        $this->assertSame(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('h2:contains("SnowTrix")')->count());

    }

    public function tearDown()
    {
        parent::tearDown();

        if(file_exists($this->temporaryTrickPhoto)) {
            unlink($this->temporaryTrickPhoto);
        }

        if(file_exists($this->temporaryTrickEditPhoto)) {
            unlink($this->temporaryTrickEditPhoto);
        }

    }


}