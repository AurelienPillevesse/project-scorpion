<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Sealsix\MediaBundle\Uploader;

use Symfony\Component\HttpFoundation\File\File;
use Sealsix\MediaBundle\Uploader\Exception\BadExtensionException;

/**
 * Description of FileUploader
 *
 * @author Aurélien
 */
class FileUploader implements UploaderInterface {

    private $rootDir;
    private $authorizedExtensions = array(
        "jpg",
        "jpeg",
        "gif",
        "png",
        "mp3",
        "ogg",
        "flac",
        "wav",
        "mp4",
        "avi",
        "ogg",
        "mpga"
       );

    private function getAuthorizedExtensions() {
        return $this->authorizedExtensions;
    }

    public function upload(File $file) {
        $guessExtension = $file->guessExtension();

        if(!in_array($guessExtension, $this->getAuthorizedExtensions())) {
            throw new BadExtensionException(sprintf("File extension %s is not accepted", $guessExtension));
        }

        $fileName = md5(uniqid()).'.'.$guessExtension;

        $gzcontent = gzdeflate(file_get_contents($file), 9);
        file_put_contents($file, $gzcontent);

        $fileDir = $this->rootDir.DIRECTORY_SEPARATOR.'Upload';
        $file->move($fileDir, $fileName);
        return $fileName;
    }

    public function __construct($pathDir) {
        $this->rootDir = $pathDir;
    }
}
