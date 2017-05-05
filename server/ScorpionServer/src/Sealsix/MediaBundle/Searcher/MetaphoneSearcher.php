<?php

namespace Sealsix\MediaBundle\Searcher;
/**
* This class implements Metaphone Search Algorithme
* Inspired from Fuzzy Search algorithm
*/
class MetaphoneSearcher implements SearcherInterface
{

  private $em;

  private $rawText;

  private $words;

  private $phones;

  private $fuzzyRatio;

    public function __construct($em, $fuzzyRatio = 50)  {
    $this->em = $em;
    $this->fuzzyRatio = $fuzzyRatio;
  }

  public function search($search)  {
    $this->words = array();
    $this->phones = array();
    $this->encodeSearchString($search);
    $results = array();
    //select e.*, SUM(r.weight) AS "weight" from index_meta_image i, rank_index_image r, image e WHERE i.meta IN('TST') AND i.id = r.word_id AND e.id = r.image_id GROUP BY e.id;
   $results += $this->searchInImages();
   $results += $this->searchInVideos();
   $results += $this->searchInMusiques();
   
    return $this->sort($results);
  }

  private function searchInImages(){
    $q = $this->em->createQuery('SELECT m, SUM(r.weight) AS weight FROM Sealsix\MediaBundle\Entity\IndexMetaImage i, Sealsix\MediaBundle\Entity\RankIndexImage r, Sealsix\MediaBundle\Entity\Image m WHERE levenshtein_ratio_in(i.meta, :metas, :ratio) = 1 AND i = r.word AND m = r.image GROUP BY m');
    $q->setParameter('metas', join(',', $this->phones));
    $q->setParameter('ratio', $this->fuzzyRatio);
    return $q->getResult();
  }

  private function searchInVideos(){
    $q = $this->em->createQuery('SELECT m, SUM(r.weight) AS weight FROM Sealsix\MediaBundle\Entity\IndexMetaVideo i, Sealsix\MediaBundle\Entity\RankIndexVideo r, Sealsix\MediaBundle\Entity\Video m WHERE levenshtein_ratio_in(i.meta, :metas, :ratio) = 1 AND i = r.word AND m = r.video GROUP BY m');
    $q->setParameter('metas', join(',', $this->phones));
    $q->setParameter('ratio', $this->fuzzyRatio);
    return $q->getResult();
  }

  private function searchInMusiques(){
    $q = $this->em->createQuery('SELECT m, SUM(r.weight) AS weight FROM Sealsix\MediaBundle\Entity\IndexMetaMusique i, Sealsix\MediaBundle\Entity\RankIndexMusique r, Sealsix\MediaBundle\Entity\Musique m WHERE levenshtein_ratio_in(i.meta, :metas, :ratio) = 1 AND i = r.word AND m = r.musique GROUP BY m');
    $q->setParameter('metas', join(',', $this->phones));
    $q->setParameter('ratio', $this->fuzzyRatio);
    return $q->getResult();
  }

  private function encodeSearchString($search){

    $this->rawText = trim(preg_replace('!\s+!', ' ', str_replace(array('\r', '\t', '\n'), ' ', $search)));
    $content = explode(' ', $this->rawText);
    foreach($content as $w){
      if(1 < strlen($w)){
        $this->words[] = $w;
        $this->phones[] = metaphone($w);
      }
    }
  }

  private function sort($raw){

    $ord = array();
    $data = array();

    foreach ($raw as $e) {
      $ord[] = $e['weight'];
      $data[] = $e[0];
    }

    array_multisort($ord, SORT_DESC, $data);

    return $data;
  }
}
