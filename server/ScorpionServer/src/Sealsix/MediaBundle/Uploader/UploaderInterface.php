<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Sealsix\MediaBundle\Uploader;

use Symfony\Component\HttpFoundation\File\File;

/**
 * Description of UploaderInterface
 *
 * @author Aurélien
 */
interface UploaderInterface {
    
    public function upload(File $file);
    
}