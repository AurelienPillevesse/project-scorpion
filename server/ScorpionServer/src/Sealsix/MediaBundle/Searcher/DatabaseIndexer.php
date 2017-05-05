<?php

namespace Sealsix\MediaBundle\Searcher;

use Sealsix\MediaBundle\Entity\Media;

class DatabaseIndexer implements IndexerInterface
{
  protected $em;

  protected $rankEntityPrefix;

  protected $indexEntityPrefix;

  protected $titleWeight;

  protected $tagWeight;

  protected $descWeight;

  public function __construct($em, $rankEntityPrefix, $indexEntityPrefix, $titleWeight = 0.5, $tagWeight = 0.7, $descWeight = 0.3){
    $this->em = $em;
    $this->rankEntityPrefix = $rankEntityPrefix;
    $this->indexEntityPrefix = $indexEntityPrefix;
    $this->titleWeight = $titleWeight;
    $this->tagWeight = $tagWeight;
    $this->descWeight = $descWeight;
  }

  protected function getManager(){
    return $this->em;
  }

  public function index(Media $entity){
    $entityClass = $this->getEntityName($entity);

    $titleWords = $this->encodeSearchString($entity->getNom());
    $descWords = $this->encodeSearchString($entity->getDescription());

    $em = $this->getManager();

    foreach ($titleWords as $key => $value) {
      $class = $this->getIndexEntity($entityClass);
      $indexed = new $class;
      $indexed->setWord($key);
      $indexed->setMeta($value);
      $class = $this->getRankEntity($entityClass);
      $rank = new $class;
      $rank->setWord($indexed);
      $rank->setWeight($this->titleWeight);
      $method = 'set'.$entityClass;
      $rank->$method($entity);
      $em->persist($indexed);
      $em->persist($rank);
    }

    foreach ($descWords as $key => $value) {
      $class = $this->getIndexEntity($entityClass);
      $indexed = new $class;
      $indexed->setWord($key);
      $indexed->setMeta($value);
      $class = $this->getRankEntity($entityClass);
      $rank = new $class;
      $rank->setWord($indexed);
      $rank->setWeight($this->titleWeight);
      $method = 'set'.$entityClass;
      $rank->$method($entity);
      $em->persist($indexed);
      $em->persist($rank);
    }

    $em->flush();
  }

  private function getIndexEntity($entityName){
    return 'Sealsix\MediaBundle\Entity\\'.$this->indexEntityPrefix.$entityName;
  }

  private function getRankEntity($entityName){
    return 'Sealsix\MediaBundle\Entity\\'.$this->rankEntityPrefix.$entityName;
  }

  private function encodeSearchString($search){
    $words = array();
    $search = trim(preg_replace('!\s+!', ' ', str_replace(array('\r', '\t', '\n'), ' ', $search)));
    $content = explode(' ', $search);
    foreach($content as $w){
      if(1 < strlen($w)){
        $words[$w] = metaphone($w);
      }
    }
    return $words;
  }

  protected function getEntityName($entity){
    $classR = new \ReflectionClass($entity);
    return $classR->getShortName();
  }
}
