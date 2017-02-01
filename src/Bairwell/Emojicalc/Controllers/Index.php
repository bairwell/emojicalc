<?php
declare(strict_types=1);

namespace Bairwell\Emojicalc\Controllers;

use Bairwell\Emojicalc\Entities\Operators;
use Bairwell\Emojicalc\RenderViewTrait;
use Bairwell\Emojicalc\Request;
use Bairwell\Emojicalc\Response;

/**
 * Class Index Controller.
 *
 * Main index system.
 *
 * @package Bairwell\Emojicalc\Controllers
 */
class Index
{

    use RenderViewTrait;

    /**
     * List of all valid operators.
     * @var Operators
     */
    private $operators;

    /**
     * Index constructor.
     * @param Operators $operators List of valid operators.
     */
    public function __construct(Operators $operators) {
        $this->operators=$operators;
    }
    /**
     * Start/homepage action.
     *
     * @param Request $request
     * @param Response $response
     * @throws \Exception If the views do not exist.
     */
    public function startAction(Request $request,Response $response) {
        $htmlOperators='';
        /* @var \Bairwell\Emojicalc\Entities\Operator $currentOperator */
        foreach ($this->operators as $currentOperator) {
            $htmlOperators .= $this->renderView('operatorOption',
                [
                    '%OPERATORTYPE%' => $currentOperator->getOperatorType(),
                    '%SYMBOL%' => $currentOperator->getSymbol()->getSymbolCode()
                ]
            );
        }
        $response->addToBody($this->renderView('showEntry',['%OPERATORS%'=>$htmlOperators]));
    }

    public function calculateAction(Request $request,Response $response) {
        $response->addToBody('Showing');
    }
}