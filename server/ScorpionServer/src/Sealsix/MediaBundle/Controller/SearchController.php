<?php

namespace Sealsix\MediaBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Get;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class SearchController extends FOSRestController
{

    /**
     * @Get("/media/search")
     *
     */
    public function searchAction(Request $request) {

          $searchPayload = $request->query->get('search');

          if (NULL === $searchPayload){
              throw new \InvalidArgumentException("Search bar empty");
          }

          $searcher = $this->get('searcher');
          $results = $searcher->search($searchPayload);

          return $results;
    }
}
