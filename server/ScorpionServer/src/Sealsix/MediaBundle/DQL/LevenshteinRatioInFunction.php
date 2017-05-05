<?php

namespace Sealsix\MediaBundle\DQL;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

class LevenshteinRatioInFunction extends FunctionNode {

  public $stringExpression = null;
  public $listStringExpression = null;
  public $minRationExpression = null;

  public function getSql(SqlWalker $sqlWalker){
      return 'LEVENSHTEIN_RATIO_IN('.
              $this->stringExpression->dispatch($sqlWalker).', '.
              $this->listStringExpression->dispatch($sqlWalker).', '.
              $this->minRationExpression->dispatch($sqlWalker).
              ')';
  }

  public function parse(Parser $parser){
    //levenshtein_ratio_in(str1, lststr, minratio)
    $parser->match(Lexer::T_IDENTIFIER);
    $parser->match(Lexer::T_OPEN_PARENTHESIS);
    $this->stringExpression = $parser->ArithmeticPrimary();
    $parser->match(Lexer::T_COMMA);
    $this->listStringExpression = $parser->ArithmeticPrimary();
    $parser->match(Lexer::T_COMMA);
    $this->minRationExpression = $parser->ArithmeticPrimary();
    $parser->match(Lexer::T_CLOSE_PARENTHESIS);
  }


}
