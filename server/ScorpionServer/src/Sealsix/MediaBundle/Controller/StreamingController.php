<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Sealsix\MediaBundle\Controller;

use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations\Get;
/**
 * Description of streamingController
 *
 * @author Aurélien
 */
class StreamingController extends FOSRestController {

    /**
     * @Get("/media/image/{id}")
     *
     */
    public function streamImageAction($id) {

        $em = $this->getDoctrine()->getManager();
        $img = $em->getRepository('SealsixMediaBundle:Image')->find($id);

        if($img === null) {
            throw $this->createNotFoundException(sprintf("No media found for id %d", $id));
        }

        $this->autorizationChecker($img);

        $imgPath = $img->getPath();
        $foundImg = $this->container->getParameter('kernel.root_dir').DIRECTORY_SEPARATOR.'Upload'.DIRECTORY_SEPARATOR.$imgPath;

        $file = new File($foundImg);
        $content = $this->uncompressFile($file);

        $extension = $file->getMimeType();

        //$content = file_get_contents($file);

        return new Response(
            $content,
            Response::HTTP_OK,
            array('Content-Type' => $extension)
        );

    }

    /**
     * @Get("/media/video/{id}")
     *
     */
    public function streamVideoAction($id) {
        $em = $this->getDoctrine()->getManager();
        $video = $em->getRepository('SealsixMediaBundle:Video')->find($id);

        if($video === null) {
            throw $this->createNotFoundException(sprintf("No media found for id %d", $id));
        }

        $this->autorizationChecker($video);

        $vidPath = $video->getPath();
        $foundVid = $this->container->getParameter('kernel.root_dir').'/Upload/'.$vidPath;

        $file = new File($foundVid);
        //$this->uncompressFile($file);
        $content = $this->uncompressFile($file);

        $extension = $file->getMimeType();

        $response = new StreamedResponse();
        /*$response->setCallback(function () use ($file) {
            readfile($file->getPathname());
        });*/
        $response->setCallback(function () use ($content) {
            echo $content;
        });

        $response->headers->set('Content-Type', $extension);
        return $response;
    }

    /**
     * @Get("/media/sound/{id}")
     *
     */
    public function streamSonAction($id) {
        $em = $this->getDoctrine()->getManager();
        $son = $em->getRepository('SealsixMediaBundle:Musique')->find($id);

        if($son === null){
            throw $this->createNotFoundException(sprintf("No media found for id %d", $id));
        }

        $this->autorizationChecker($son);

	$path = $son->getPath();
        $foundSon = $this->container->getParameter('kernel.root_dir').'/Upload/'.$path;

        $file = new File($foundSon);
        //$this->uncompressFile($file);
        $content = $this->uncompressFile($file);
        
        $extension = $file->getMimeType();

        $response = new StreamedResponse();
        /*$response->setCallback(function () use ($file) {
        readfile($file->getPathname());
        });*/
        $response->setCallback(function () use ($content) {
            echo $content;
        });

        $response->headers->set('Content-Type', $extension);
        return $response;
    }


    private function autorizationChecker($media) {
        $authorizationChecker = $this->get('security.authorization_checker');

        if (false === $authorizationChecker->isGranted('VIEW', $media)) {
            throw new AccessDeniedException();
        }
    }

    private function uncompressFile(File $file) {
      //var_dump($file);
        $content = gzinflate(file_get_contents($file));
        //file_put_contents($file, $content);
        return $content;

    }
}
