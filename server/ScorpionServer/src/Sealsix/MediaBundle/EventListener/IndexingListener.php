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
 */
class IndexingListener implements EventSubscriber{

    private $container;

    /**
     * @Benjamin: Injecting the container is a hack and is related in an issue in doctrine and SF2
     * @see https://github.com/symfony/symfony/issues/8425
    */
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
      $entity = $args->getEntity();

      if($entity instanceof Image || $entity instanceof Video || $entity instanceof Musique){        
        $this->container->get('indexer')->index($entity);
      }
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if($entity instanceof Image || $entity instanceof Video || $entity instanceof Musique){
          $em = $args->getEntityManager();
          $rankEntity = 'SealsixMediaBundle:RankIndex'.$this->getEntityName($entity);
          foreach($entity->getIndexed() as $indexed){
            $em->remove($indexed);
          }
          $em->flush();
          $this->container->get('indexer')->index($entity);
        }
    }


    private function getEntityName($entity){
      $classR = new \ReflectionClass($entity);
      return $classR->getShortName();
    }
}
