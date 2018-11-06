<?php

namespace UserBundle\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class UserAvatarUploader
{
    private $targetDirectory;
    private $webDirectory;

    public function __construct($targetDirectory, $webDirectory)
    {
        $this->targetDirectory = $targetDirectory;
        $this->webDirectory = $webDirectory;
    }

    public function upload(UploadedFile $file, $username)
    {
        $fileName = $username.'-avatar.'.$file->guessExtension();

        $file->move($this->getTargetDirectory($username), $fileName);

        return $fileName;
    }

    /*
     * Avatar will be stored in a folder named with the username
     */
    public function getTargetDirectory($username)
    {
        return $this->targetDirectory .'/'. $username ;
    }

    public function getWebDirectory($username)
    {
        return $this->webDirectory .'/'. $username ;
    }
}