<?php

namespace Sealsix\MediaBundle\Searcher;

/**
* This interface represents a class able to search through data and return the results as a raw array
*/
interface SearcherInterface
{
  public function search($search);
}
