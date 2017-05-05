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
use Sealsix\MediaBundle\Entity\Media;
use Sealsix\MediaBundle\Entity\Image;
use Sealsix\MediaBundle\Entity\Musique;
use Sealsix\MediaBundle\Entity\Video;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Sealsix\MediaBundle\Form\Type\ImageType;
use Sealsix\MediaBundle\Form\Type\VideoType;
use Sealsix\MediaBundle\Form\Type\MusiqueType;
use FOS\RestBundle\View\View;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Delete;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use GetId3\GetId3Core as GetId3;
use Symfony\Component\Security\Acl\Exception\AclAlreadyExistsException;

/**
 * Description of MediaController
 *
 * @author Aurélien
 */
class MediaController extends FOSRestController {

    private $types = array(
        'Image' => array('jpg', 'jpeg', 'gif', 'png'),
        'Musique' => array('mp3', 'ogg', 'flac', 'wav', 'mpga'),
        'Video' => array('mp4', 'avi', 'ogg')
    );


    /**
     * @Delete("/media/image/{id}")
     *
     */
    public function deleteImageAction($id) {
        $em = $this->getDoctrine()->getManager();
        $mediaDelete = $em->getRepository('SealsixMediaBundle:Image')->find($id);

        if($mediaDelete === null) {
            throw $this->createNotFoundException('No media found for id '.$id);
        }
        if($mediaDelete->getOwner() !== $this->getUser()) {
            throw $this->createAccessDeniedException('You can\'t delete this media.');
        }

        $em->remove($mediaDelete);
        $em->flush();

        $file = $this->get('kernel')->getRootDir().DIRECTORY_SEPARATOR.'Upload'.DIRECTORY_SEPARATOR.$mediaDelete->getPath();

        if(file_exists($file)) {
            unlink($file);
        }

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @Delete("/media/musique/{id}")
     *
     */
    public function deleteMusiqueAction($id) {
        $em = $this->getDoctrine()->getManager();
        $mediaDelete = $em->getRepository('SealsixMediaBundle:Musique')->find($id);

        if($mediaDelete === null) {
            throw $this->createNotFoundException('No media found for id '.$id);
        }
        if($mediaDelete->getOwner() !== $this->getUser()) {
            throw $this->createAccessDeniedException('You can\'t delete this media.');
        }

        $em->remove($mediaDelete);
        $em->flush();


        $file = $this->get('kernel')->getRootDir().DIRECTORY_SEPARATOR.'Upload'.DIRECTORY_SEPARATOR.$mediaDelete->getPath();
        if(file_exists($file)) {
            unlink($file);
        }

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @Delete("/media/video/{id}")
     *
     */
    public function deleteVideoAction($id) {
        $em = $this->getDoctrine()->getManager();
        $mediaDelete = $em->getRepository('SealsixMediaBundle:Video')->find($id);

        if($mediaDelete === null) {
            throw $this->createNotFoundException('No media found for id '.$id);
        }
        if($mediaDelete->getOwner() !== $this->getUser()) {
            throw $this->createAccessDeniedException('You can\'t delete this media.');
        }

        $em->remove($mediaDelete);
        $em->flush();


        $file = $this->get('kernel')->getRootDir().DIRECTORY_SEPARATOR.'Upload'.DIRECTORY_SEPARATOR.$mediaDelete->getPath();

        if(file_exists($file)) {
            unlink($file);
        }

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @Post("/media/upload")
     *
     */
    public function uploadAction(Request $request){

        $this->denyAccessUnlessGranted('ROLE_USER', null, 'Unable to access this page.');
        $user = $this->get('security.context')->getToken()->getUser();
        if(!$user) {
            throw new AccessDeniedException("Vous n'êtes pas connecté.");
        }

        $file = $request->files->get('file');
        $visibility = $request->files->get('visibility');
        if($file === null) {
            throw new BadRequestHttpException("Send a file.");
        }

        $media = null;
        $cType = $file->guessExtension();

        foreach($this->types as $key => $type){
            if(in_array($cType, $type)){
                $class = 'Sealsix\MediaBundle\Entity\\'.$key;
                $media = new $class();

                $media->setType(strtolower($key));
                $media->setDatePublication(new \DateTime());

                if($key === 'Musique' || $key === 'Video') {
                    $getId3 = new GetId3();
                    $infoFile = $getId3->analyze($file);
                    $media->setDuree($infoFile['playtime_seconds']);
                }
            }
        }

        if(!$media) {
            throw new BadRequestHttpException("Not good extension.");
        }

        $media->setNom($file->getClientOriginalName());

        $media->setOwner($user);
        $uploader = $this->get('uploader');
        $mediaUpload = $uploader->upload($file);

        $media->setPath($mediaUpload);

        $em = $this->getDoctrine()->getManager();
        $em->persist($media);
        $em->flush();

        $aclProvider = $this->get('security.acl.provider');
        $mediaIdentity = ObjectIdentity::fromDomainObject($media);
        $acl = null;
        try {
          $acl = $aclProvider->createAcl($mediaIdentity);
        }
        catch(AclAlreadyExistsException $e){
          $acl = $aclProvider->findAcl($mediaIdentity);
        }

        $securityIdentity = UserSecurityIdentity::fromAccount($user);

        $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_OWNER);

        if($visibility !== null && $visibility === 'public'){
          $publicSecurityIdentity = new RoleSecurityIdentity('IS_AUTHENTICATED_ANONYMOUSLY');
          $acl->insertObjectAce($publicSecurityIdentity, MaskBuilder::MASK_VIEW);
        }

        $aclProvider->updateAcl($acl);

        return $media;
    }

    /**
     * @Get("/media/me")
     *
     */
    public function personalMediaAction(Request $request) {

        $this->denyAccessUnlessGranted('ROLE_USER', null, 'Unable to access this page.');
        $token = $this->get('security.context')->getToken();
        $user = $token->getUser();
        $em = $this->getDoctrine()->getManager();

        $personalMediasArray = array();
        $ord = array();

        $personalMedias['musique'] = $em->getRepository('SealsixMediaBundle:Musique')->findByOwner($user);
        foreach($personalMedias['musique'] as $value) {
            $personalMediasArray[] = $value;
            $ord[] = $value->getDatePublication()->getTimestamp();
        }

        $personalMedias['image'] = $em->getRepository('SealsixMediaBundle:Image')->findByOwner($user);
        foreach($personalMedias['image'] as $value) {
            $personalMediasArray[] = $value;
            $ord[] = $value->getDatePublication()->getTimestamp();
        }

        $personalMedias['video'] = $em->getRepository('SealsixMediaBundle:Video')->findByOwner($user);
        foreach($personalMedias['video'] as $value) {
            $personalMediasArray[] = $value;
            $ord[] = $value->getDatePublication()->getTimestamp();
        }



        array_multisort($ord, SORT_DESC, $personalMediasArray);

        return $personalMediasArray;
    }

    /**
     * @Get("/media/random/{number}")
     *
     */
    public function randomMediaAction($number) {

        $em = $this->getDoctrine()->getManager();
        $result = $em->getRepository('SealsixMediaBundle:PublicMedia')->findAll();
        shuffle($result);

        $medias = array();
        foreach ($result as $res) {
            array_push($medias, $res->getMedia());
        }

        if(count($medias) < $number) {
            return $medias;
        } else {
            return array_slice($medias, 0, $number);
        }
    }


    /**
     * @Post("/media/image/{id}")
     *
     */
    public function postImageAction(Request $request, $id) {
        $user = $this->get('security.context')->getToken()->getUser();
        if(!$user) {
            throw new AccessDeniedException("Vous n'êtes pas connecté.");
        }

        $em = $this->getDoctrine()->getManager();
        $media = $em->getRepository('SealsixMediaBundle:Image')->find($id);

        if($user !== $media->getOwner()) {
            throw new AccessDeniedException("Vous n'êtes pas le propriétaire de ce media.");
        }

        $form = $this->createForm(new ImageType(), $media);
        $form->bind($request);

        $view = View::create();

        if($form->isValid()) {
            $aclProvider = $this->get('security.acl.provider');
            $mediaIdentity = ObjectIdentity::fromDomainObject($media);
            $aclRemove = $aclProvider->deleteAcl($mediaIdentity);
            $acl = $aclProvider->createAcl($mediaIdentity);

            $securityIdentity = new RoleSecurityIdentity('IS_AUTHENTICATED_ANONYMOUSLY');

            if($form->get('visibility')->getData() === 'public') {
                $securityIdentity = new RoleSecurityIdentity('IS_AUTHENTICATED_ANONYMOUSLY');
                $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_VIEW);
            } else {
                $securityIdentity = UserSecurityIdentity::fromAccount($user);
                $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_OWNER);
            }

            $aclProvider->updateAcl($acl);

            $em->persist($media);
            $em->flush();

            return $media;
        }
        return $form;
    }

    /**
     * @Post("/media/video/{id}")
     *
     */
    public function postVideoAction(Request $request, $id) {
        $user = $this->get('security.context')->getToken()->getUser();
        if(!$user) {
            throw new AccessDeniedException("Vous n'êtes pas connecté.");
        }

        $em = $this->getDoctrine()->getManager();
        $media = $em->getRepository('SealsixMediaBundle:Video')->find($id);

        if($user !== $media->getOwner()) {
            throw new AccessDeniedException("Vous n'êtes pas le propriétaire de ce media.");
        }

        $form = $this->createForm(new VideoType(), $media);
        $form->bind($request);

        $view = View::create();

        if($form->isValid()) {
            $aclProvider = $this->get('security.acl.provider');
            $mediaIdentity = ObjectIdentity::fromDomainObject($media);
            $aclRemove = $aclProvider->deleteAcl($mediaIdentity);
            $acl = $aclProvider->createAcl($mediaIdentity);

            $securityIdentity = new RoleSecurityIdentity('IS_AUTHENTICATED_ANONYMOUSLY');

            if($form->get('visibility')->getData() === 'public') {
                $securityIdentity = new RoleSecurityIdentity('IS_AUTHENTICATED_ANONYMOUSLY');
                $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_VIEW);
            } else {
                $securityIdentity = UserSecurityIdentity::fromAccount($user);
                $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_OWNER);
            }

            $aclProvider->updateAcl($acl);

            $em->persist($media);
            $em->flush();

            return $media;
        }
        return $form;
    }

    /**
     * @Post("/media/sound/{id}")
     *
     */
    public function postMusiqueAction(Request $request, $id) {
        $user = $this->get('security.context')->getToken()->getUser();
        if(!$user) {
            throw new AccessDeniedException("Vous n'êtes pas connecté.");
        }

        $em = $this->getDoctrine()->getManager();
        $media = $em->getRepository('SealsixMediaBundle:Musique')->find($id);

        if($user !== $media->getOwner()) {
            throw new AccessDeniedException("Vous n'êtes pas le propriétaire de ce media.");
        }

        $form = $this->createForm(new MusiqueType(), $media);
        $form->bind($request);

        $view = View::create();

        if($form->isValid()) {
            $aclProvider = $this->get('security.acl.provider');
            $mediaIdentity = ObjectIdentity::fromDomainObject($media);
            $aclRemove = $aclProvider->deleteAcl($mediaIdentity);
            $acl = $aclProvider->createAcl($mediaIdentity);

            $securityIdentity = new RoleSecurityIdentity('IS_AUTHENTICATED_ANONYMOUSLY');

            if($form->get('visibility')->getData() === 'public') {
                $securityIdentity = new RoleSecurityIdentity('IS_AUTHENTICATED_ANONYMOUSLY');
                $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_VIEW);
            } else {
                $securityIdentity = UserSecurityIdentity::fromAccount($user);
                $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_OWNER);
            }

            $aclProvider->updateAcl($acl);

            $em->persist($media);
            $em->flush();

            return $media;
        }
        return $form;
    }

    /**
     * @Get("/media/me/count")
     *
     */
    public function numberMediaAction() {
        $user = $this->get('security.context')->getToken()->getUser();
        if(!$user) {
            throw new AccessDeniedException("Vous n'êtes pas connecté.");
        }

        $em = $this->getDoctrine()->getManager();

        $repoImage = $em->getRepository('SealsixMediaBundle:Image');
        $queryImage = $repoImage->createQueryBuilder('i')
            ->select('count(i.id)')
            ->where('i.owner = :owner')
            ->setParameter('owner', $user)
            ->getQuery();
        $resultImage = $queryImage->getSingleScalarResult();

        $repoMusique = $em->getRepository('SealsixMediaBundle:Musique');
        $queryMusique = $repoMusique->createQueryBuilder('m')
            ->select('count(m.id)')
            ->where('m.owner = :owner')
            ->setParameter('owner', $user)
            ->getQuery();
        $resultMusique = $queryMusique->getSingleScalarResult();

        $repoVideo = $em->getRepository('SealsixMediaBundle:Video');
        $queryVideo = $repoVideo->createQueryBuilder('v')
            ->select('count(v.id)')
            ->where('v.owner = :owner')
            ->setParameter('owner', $user)
            ->getQuery();
        $resultVideo = $queryVideo->getSingleScalarResult();

        $total = $resultImage + $resultMusique + $resultVideo;
        return array('image' => $resultImage, 'musique' => $resultMusique, 'video' => $resultVideo, 'total' => $total);
    }

    /**
     * @Get("/media/me/image")
     *
     */
    public function myImageAction() {
        $user = $this->get('security.context')->getToken()->getUser();
        if(!$user) {
            throw new AccessDeniedException("Vous n'êtes pas connecté.");
        }

        $em = $this->getDoctrine()->getManager();
        return array_reverse($em->getRepository('SealsixMediaBundle:Image')->findByOwner($user));
    }

    /**
     * @Get("/media/me/musique")
     *
     */
    public function myMusiqueAction() {
        $user = $this->get('security.context')->getToken()->getUser();
        if(!$user) {
            throw new AccessDeniedException("Vous n'êtes pas connecté.");
        }

        $em = $this->getDoctrine()->getManager();
        return array_reverse($em->getRepository('SealsixMediaBundle:Musique')->findByOwner($user));
    }

    /**
     * @Get("/media/me/video")
     *
     */
    public function myVideoAction() {
        $user = $this->get('security.context')->getToken()->getUser();
        if(!$user) {
            throw new AccessDeniedException("Vous n'êtes pas connecté.");
        }

        $em = $this->getDoctrine()->getManager();
        return array_reverse($em->getRepository('SealsixMediaBundle:Video')->findByOwner($user));
    }

    public function patchImageAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $img = $em->getRepository('SealsixMediaBundle:Image')->find($id);

        $form = $this->createForm(new ImageType(), $img);
        $form->bind($request);

        if($form->isValid())
        {
            $em->persist($img);
            $em->flush();

            return $img;
        }
        return $form;
    }


    public function patchVideoAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $vid = $em->getRepository('SealsixMediaBundle:Video')->find($id);

        $form = $this->createForm(new VideoType(), $vid);
        $form->bind($request);

        if($form->isValid())
        {
            $em->persist($vid);
            $em->flush();

            return $vid;
        }
        return $form;
    }

    public function patchMusiqueAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $musique = $em->getRepository('SealsixMediaBundle:Musique')->find($id);

        $form = $this->createForm(new MusiqueType(), $musique);
        $form->bind($request);

        if($form->isValid())
        {
            $em->persist($musique);
            $em->flush();

            return $musique;
        }
        return $form;
    }

}
