<?php
namespace Tests\CoreBundle\Service;

use CoreBundle\Service\FileUploader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploaderTest extends TestCase {

    private $fileUploader;
    private $localFileToUseForTest; // File to simulate the 'file upload' done by end user.
    private $uploadedFile; // Simulated Symfony's UploadedFile created with localFileToUseForTest
    private $uploadedFileName; // We need to store it un order to delete it after tests.
    private $targetDirectory;

    public function setUp() {

        $this->targetDirectory = dirname(__FILE__).'/../../../web/';

        $this->fileUploader = new FileUploader($this->targetDirectory);

        // Create a file in order to test (in the same directory as where the file will be uploaded by the service)
        $this->localFileToUseForTest = tempnam($this->targetDirectory, 'uploaded-file-for-test'); // Create the file

        imagejpeg(imagecreatetruecolor(200,200), $this->localFileToUseForTest);

        // Use test mode as the UploadedFile is a local file. Not setting test to true will result in an error.
        $this->uploadedFile = new UploadedFile($this->localFileToUseForTest, 'trick-test-photo', 'image/jpeg', null, null, true);

    }

    public function testUploadFile() {

        $this->uploadedFileName = $this->fileUploader->upload($this->uploadedFile);

        $uploadedFilePath = $this->targetDirectory . $this->uploadedFileName;

        $this->assertSame(true, file_exists($uploadedFilePath));
    }

    public function testUploadFileInvalidSubfolder() {

        $this->expectException('InvalidArgumentException');

        $this->fileUploader->upload($this->uploadedFile, "");

    }

    public function tearDown() {

        if(file_exists($this->localFileToUseForTest)) {
            unlink($this->localFileToUseForTest);
        }

        $uploadedFilePath = $this->targetDirectory . $this->uploadedFileName;

        if(file_exists($uploadedFilePath) && is_file($uploadedFilePath)) {
            unlink($uploadedFilePath);
        }
    }
}