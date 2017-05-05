<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Sealsix\MediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Sealsix\MediaBundle\Entity\MediaLikes;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\FOSRestController;

/**
 * Description of MediaLikesController
 *
 * @author Aurélien
 */
class MediaLikesController extends FOSRestController {



  /**
   * @Post("/likes/{id}/image")
   */
    public function postLikeImageAction($id)
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'Unable to access this page.');

        $em = $this->getDoctrine()->getManager();
        $mediaLike = $em->getRepository('SealsixMediaBundle:Image')->find($id);
        $userInteraction = $em->getRepository('SealsixMediaBundle:MediaLikes')->findOneBy(array('user' => $this->getUser(), 'image' => $mediaLike));

        if(!$userInteraction) {
            $like = new MediaLikes();
            $like->setUser($this->getUser());
            $like->setImage($mediaLike);
            $like->setDate(new \DateTime());

            $em->persist($like);
            $em->flush();
        } else {
            $em->remove($userInteraction);
            $em->flush();
        }

        return new Response();
    }

    /**
     * @Post("/likes/{id}/musique")
     */
    public function postLikeMusiqueAction($id)
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'Unable to access this page.');

        $em = $this->getDoctrine()->getManager();
        $mediaLike = $em->getRepository('SealsixMediaBundle:Musique')->find($id);
        $userInteraction = $em->getRepository('SealsixMediaBundle:MediaLikes')->findOneBy(array('user' => $this->getUser(), 'musique' => $mediaLike));

        if(!$userInteraction) {
            $like = new MediaLikes();
            $like->setUser($this->getUser());
            $like->setMusique($mediaLike);
            $like->setDate(new \DateTime());

            $em->persist($like);
            $em->flush();
        } else {
            $em->remove($userInteraction);
            $em->flush();
        }

        return new Response();
    }

    /**
     * @Post("/likes/{id}/video")
     */
    public function postLikeVideoAction($id)
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'Unable to access this page.');

        $em = $this->getDoctrine()->getManager();
        $mediaLike = $em->getRepository('SealsixMediaBundle:Video')->find($id);
        $userInteraction = $em->getRepository('SealsixMediaBundle:MediaLikes')->findOneBy(array('user' => $this->getUser(), 'video' => $mediaLike));

        if(!$userInteraction) {
            $like = new MediaLikes();
            $like->setUser($this->getUser());
            $like->setVideo($mediaLike);
            $like->setDate(new \DateTime());

            $em->persist($like);
            $em->flush();
        } else {
            $em->remove($userInteraction);
            $em->flush();
        }

        return new Response();
    }

    public function getIsLikeImageAction($id)
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'Unable to access this page.');

        $em = $this->getDoctrine()->getManager();
        $mediaLike = $em->getRepository('SealsixMediaBundle:Image')->find($id);
        $userInteraction = $em->getRepository('SealsixMediaBundle:MediaLikes')->findBy(array($this->getUser(), $mediaLike));

        if(!$userInteraction) {
            return false;
        }

        return true;
    }

    public function getIsLikeMusiqueAction($id)
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'Unable to access this page.');

        $em = $this->getDoctrine()->getManager();
        $mediaLike = $em->getRepository('SealsixMediaBundle:Musique')->find($id);
        $userInteraction = $em->getRepository('SealsixMediaBundle:MediaLikes')->findBy(array($this->getUser(), $mediaLike));

        if(!$userInteraction) {
            return false;
        }

        return true;
    }

    public function getIsLikeVideoAction($id)
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'Unable to access this page.');

        $em = $this->getDoctrine()->getManager();
        $mediaLike = $em->getRepository('SealsixMediaBundle:Video')->find($id);
        $userInteraction = $em->getRepository('SealsixMediaBundle:MediaLikes')->findBy(array($this->getUser(), $mediaLike));

        if(!$userInteraction) {
            return false;
        }

        return true;
    }

    /**
     * @Get("/media/me/likes")
     */
    public function myLikesAction() {

        $em = $this->getDoctrine()->getManager();
        $like = $em->getRepository('SealsixMediaBundle:MediaLikes')->findByUser($this->getUser());
        $mediaLike = array();

        foreach ($like as $med) {
            array_push($mediaLike, $med->getMedia());
        }

        return array_reverse($mediaLike);
    }

}
