<?php
namespace Tests\CoreBundle\Service;

use CoreBundle\Service\FileUploader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploaderTest extends TestCase {

    private $fileUploader;
    private $uploadedFile;
    private $targetDirectory;

    public function setUp() {

        $this->targetDirectory = dirname(__FILE__).'/../../../web/';

        $this->fileUploader = new FileUploader($this->targetDirectory);

        // Create a file in order to test (in the same directory as where the file will be uploaded by the service)
        $this->localUploadedFile = tempnam($this->targetDirectory, 'uploaded-file-for-test'); // Create the file

        imagejpeg(imagecreatetruecolor(200,200), $this->localUploadedFile);

        // Use test mode as the UploadedFile is a local file. Not setting test to true will result in an error.
        $this->localUploadedFile = new UploadedFile($this->localUploadedFile, 'trick-test-photo', 'image/jpeg', null, null, true);

    }

    public function testUploadFile() {

        $uploadedFileName = $this->fileUploader->upload($this->localUploadedFile);

        $uploadedFilePath = $this->targetDirectory . $uploadedFileName;

        $this->assertSame(true, file_exists($uploadedFilePath));
    }

    public function testUploadFileInvalidSubfolder() {

        $this->expectException('InvalidArgumentException');

        $this->fileUploader->upload($this->localUploadedFile, "");

    }

    public function tearDown() {
        if(file_exists($this->localUploadedFile)) {
            unlink($this->localUploadedFile);
        }
    }
}