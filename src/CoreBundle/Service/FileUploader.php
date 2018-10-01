<?php

namespace CoreBundle\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    private $targetDirectory;

    public function __construct($targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }

    public function upload(UploadedFile $file, $subFolderName = null)
    {

        $fileName = md5(uniqid()).'.'.$file->guessExtension();

        try {
            $targetDir = $this->getTargetDirectory();

            // Move the file to a subfolder if required.
            if (null != $subFolderName) {
                if($subFolderName == "") {
                    throw new \InvalidArgumentException("The subfolderpath cannot be empty.");
                } else {
                    $targetDir .= '/'.$subFolderName.'/';
                }
            }

            $file->move($targetDir, $fileName);

        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }

        return $fileName;
    }

    public function getGeneratedFileName(){
        return $this->fileName;
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }
}