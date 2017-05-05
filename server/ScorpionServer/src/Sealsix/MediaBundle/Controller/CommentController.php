<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Sealsix\MediaBundle\Controller;

/**
 * Description of CommentController
 *
 * @author Aurélien
 */
use FOS\RestBundle\Controller\FOSRestController;
use Sealsix\MediaBundle\Entity\Comment;
use Sealsix\MediaBundle\Form\Type\CommentType;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;

class CommentController extends FOSRestController {

  /**
   * @Post("/media/image/{id}/comment")
   *
   */
    public function postCommentImageAction($id, Request $request)
    {
        $comment = new Comment();
        $form = $this->createForm(new CommentType(), $comment);
        $form->bind($request);

        $view = View::create();

        if($form->isValid())
        {
            $comment->setCreatedAt(new \DateTime());
            $comment->setOwner($this->getUser());

            $em = $this->getDoctrine()->getManager();
            $mediaComment = $em->getRepository('SealsixMediaBundle:Image')->find($id);
            $comment->setImage($mediaComment);

            $em->persist($comment);
            $em->flush();

            return $comment;
        }
        return $form;
    }

    /**
     * @Post("/media/musique/{id}/comment")
     *
     */
    public function postCommentMusiqueAction($id, Request $request)
    {
        $comment = new Comment();
        $form = $this->createForm(new CommentType(), $comment);
        $form->bind($request);

        $view = View::create();

        if($form->isValid())
        {
            $comment->setCreatedAt(new \DateTime());
            $comment->setOwner($this->getUser());

            $em = $this->getDoctrine()->getManager();
            $mediaComment = $em->getRepository('SealsixMediaBundle:Musique')->find($id);
            $comment->setMusique($mediaComment);

            $em->persist($comment);
            $em->flush();

            return $comment;
        }
        return $form;
    }


    /**
     * @Post("/media/video/{id}/comment")
     *
     */
    public function postCommentVideoAction($id, Request $request)
    {
        $comment = new Comment();
        $form = $this->createForm(new CommentType(), $comment);
        $form->bind($request);

        $view = View::create();

        if($form->isValid())
        {
            $comment->setCreatedAt(new \DateTime());
            $comment->setOwner($this->getUser());

            $em = $this->getDoctrine()->getManager();
            $mediaComment = $em->getRepository('SealsixMediaBundle:Video')->find($id);
            $comment->setVideo($mediaComment);

            $em->persist($comment);
            $em->flush();

            return $comment;
        }
        return $form;
    }

    /**
     * @Get("/media/image/{id}/comment/count")
     *
     */
    public function numberCommentImageAction($id) {
        $em = $this->getDoctrine()->getManager();
        $img = $em->getRepository('SealsixMediaBundle:Image')->find($id);
        //return count($em->getRepository('SealsixMediaBundle:Comment')->findByImage($img));

        $repoComment = $em->getRepository('SealsixMediaBundle:Comment');
        $queryComment = $repoComment->createQueryBuilder('c')
            ->select('count(c.id)')
            ->where('c.image = :image')
            ->setParameter('image', $img)
            ->getQuery();
        $resultComment = $queryComment->getSingleScalarResult();

        return array('comment' => $resultComment);
    }

    /**
     * @Get("/media/video/{id}/comment/count")
     *
     */
    public function numberCommentVideoAction($id) {
        $em = $this->getDoctrine()->getManager();
        $vid = $em->getRepository('SealsixMediaBundle:Video')->find($id);
        //return count($em->getRepository('SealsixMediaBundle:Comment')->findByVideo($vid));

        $repoComment = $em->getRepository('SealsixMediaBundle:Comment');
        $queryComment = $repoComment->createQueryBuilder('c')
            ->select('count(c.id)')
            ->where('c.video = :video')
            ->setParameter('video', $vid)
            ->getQuery();
        $resultComment = $queryComment->getSingleScalarResult();

        return array('comment' => $resultComment);
    }

    /**
     * @Get("/media/musique/{id}/comment/count")
     *
     */
    public function numberCommentMusiqueAction($id) {
        $em = $this->getDoctrine()->getManager();
        $musique = $em->getRepository('SealsixMediaBundle:Musique')->find($id);
        //return count($em->getRepository('SealsixMediaBundle:Comment')->findByMusique($musique));

        $repoComment = $em->getRepository('SealsixMediaBundle:Comment');
        $queryComment = $repoComment->createQueryBuilder('c')
            ->select('count(c.id)')
            ->where('c.musique = :musique')
            ->setParameter('musique', $musique)
            ->getQuery();
        $resultComment = $queryComment->getSingleScalarResult();

        return array('comment' => $resultComment);
    }

    /**
     * @Get("/media/image/{id}/comment")
     *
     */
    public function getCommentImageAction($id) {
        $em = $this->getDoctrine()->getManager();
        $img = $em->getRepository('SealsixMediaBundle:Image')->find($id);
        return $em->getRepository('SealsixMediaBundle:Comment')->findByImage(array($img), array("createdAt" => 'DESC'));
    }

    /**
     * @Get("/media/video/{id}/comment")
     *
     */
    public function getCommentVideoAction($id) {
        $em = $this->getDoctrine()->getManager();
        $vid = $em->getRepository('SealsixMediaBundle:Video')->find($id);
        return $em->getRepository('SealsixMediaBundle:Comment')->findByVideo(array($vid), array("createdAt" => 'DESC'));
    }

    /**
     * @Get("/media/musique/{id}/comment")
     *
     */
    public function getCommentMusiqueAction($id) {
        $em = $this->getDoctrine()->getManager();
        $musique = $em->getRepository('SealsixMediaBundle:Musique')->find($id);
        return $em->getRepository('SealsixMediaBundle:Comment')->findByMusique(array($musique), array("createdAt" => 'DESC'));
    }
}
