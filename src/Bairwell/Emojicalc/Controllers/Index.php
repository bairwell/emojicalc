<?php
declare(strict_types=1);

namespace Bairwell\Emojicalc\Controllers;

use Bairwell\Emojicalc\Entities\Operators;
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
     */
    public function startAction(Request $request,Response $response) {
        $response->addToBody('<select>');
        /* @var \Bairwell\Emojicalc\Entities\Operator $currentOperator */
        foreach ($this->operators as $currentOperator) {
            $response->addToBody(
                '<option value="'.
                $currentOperator->getOperatorType().
                '">'.
                $currentOperator->getSymbol()->getSymbolCode().
            '</option>'
        );
        }
        $response->addToBody('</select>');
        $response->addToBody('Hello this is a test');
    }
}