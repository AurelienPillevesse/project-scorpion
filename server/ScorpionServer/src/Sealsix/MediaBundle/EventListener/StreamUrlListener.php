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
class StreamUrlListener{

    private $container;

    private $routes = array(
      'Image' => 'api_stream_image',
      'Video' => 'api_stream_video',
      'Musique' => 'api_stream_son'
    );

    /**
     * @Benjamin: Injecting the container is a hack and is related in an issue in doctrine and SF2
     * @see https://github.com/symfony/symfony/issues/8425
    */
    public function __construct(ContainerInterface $container) {
      $this->container = $container;
    }

    public function postLoad(LifecycleEventArgs $args)
    {
      $entity = $args->getEntity();

      if($entity instanceof Image || $entity instanceof Video || $entity instanceof Musique){
        $entity->setStreamUrl($this->container->get('router')->generate($this->getRouteName($entity), array('id' => $entity->getId(), 'key' => $this->container->get('request')->query->get('key')), true));
      }
      else {
        return;
      }
    }

    private function getRouteName($entity){
      return $this->routes[$this->getEntityName($entity)];
    }

    private function getEntityName($entity){
      $classR = new \ReflectionClass($entity);
      return $classR->getShortName();
    }
}
