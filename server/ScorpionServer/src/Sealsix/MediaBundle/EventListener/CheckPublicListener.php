<?php

namespace Sealsix\MediaBundle\EventListener;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Sealsix\MediaBundle\Entity\Image;
use Sealsix\MediaBundle\Entity\Video;
use Sealsix\MediaBundle\Entity\Musique;
use Sealsix\MediaBundle\Entity\PublicMedia;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Description of CheckPublicListener
 *
 * @author Aurélien
 */
class CheckPublicListener implements EventSubscriber{
    
    private $container;
    
    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }


    public function getSubscribedEvents() {
        return array(
            'postPersist',
            'postUpdate'
        );
    }
    
    public function postPersist(LifecycleEventArgs $args)
    {
        $this->add($args);
    }
    
    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->update($args);
    }
    
    public function preRemove(LifecycleEventArgs $args)
    {
        $this->remove($args);
    }
    
    public function add(LifecycleEventArgs $args) {
        $entity = $args->getEntity();
        
        if ($this->container->get('security.authorization_checker')->isGranted('VIEW', $entity)) {
            $entityManager = $args->getEntityManager();
            $public = new PublicMedia();
            
            if ($entity instanceof Image) {
                $public->setImage($entity);
                $entityManager->persist($public);
                $entityManager->flush();
            }
            if ($entity instanceof Video) {
                $public->setVideo($entity);
                $entityManager->persist($public);
                $entityManager->flush();
            }
            if ($entity instanceof Musique) {
                $public->setMusique($entity);
                $entityManager->persist($public);
                $entityManager->flush();
            }
        }
    }
    
    public function update(LifecycleEventArgs $args) {
        $entity = $args->getEntity();
        
        $entityManager = $args->getEntityManager();
        
        $media = $entityManager->getRepository('SealsixMediaBundle:PublicMedia')->findOneByImage($entity);
        if($media === null) {
            $media = $entityManager->getRepository('SealsixMediaBundle:PublicMedia')->findOneByVideo($entity);
            if($media === null) {
                $media = $entityManager->getRepository('SealsixMediaBundle:PublicMedia')->findOneByMusique($entity);
            }
        }
        
        if($media !== null) {
            if($this->container->get('security.authorization_checker')->isGranted('VIEW', $entity)) {
                return;
            } else {
                $entityManager->remove($media);
                $entityManager->flush();
                return;
            }
        } else {
            if($this->container->get('security.authorization_checker')->isGranted('VIEW', $entity)) {
                $public = new PublicMedia();
                
                if ($entity instanceof Image) {
                    $public->setImage($entity);
                }
                if ($entity instanceof Musique) {
                    $public->setMusique($entity);
                }
                if ($entity instanceof Video) {
                    $public->setVideo($entity);
                }
                
                $entityManager->persist($entity);
                $entityManager->flush();
            }
        }
    }
    
    public function remove(LifecycleEventArgs $args) {
        $entity = $args->getEntity();
        
        if ($this->container->get('security.authorization_checker')->isGranted('VIEW', $entity)) {
            $entityManager = $args->getEntityManager();
            
            if ($entity instanceof Image) {
                $entityManager->getRepository('SealsixMediaBundle:PublicMedia')->findOneByImage($entity);
                $entityManager->remove($entity);
                $entityManager->flush();
            }
            if ($entity instanceof Video) {
                $entityManager->getRepository('SealsixMediaBundle:PublicMedia')->findOneByVideo($entity);
                $entityManager->remove($entity);
                $entityManager->flush();
            }
            if ($entity instanceof Musique) {
                $entityManager->getRepository('SealsixMediaBundle:PublicMedia')->findOneByMusique($entity);
                $entityManager->remove($entity);
                $entityManager->flush();
            }
        }
    }
}
