<?php

namespace Sealsix\MediaBundle\Searcher;

use Sealsix\MediaBundle\Entity\Media;

interface IndexerInterface
{
  public function index(Media $entity);
}
