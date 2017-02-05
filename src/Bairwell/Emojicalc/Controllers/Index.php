<?php
declare(strict_types=1);

namespace Bairwell\Emojicalc\Controllers;

use Bairwell\Emojicalc\Entities\Operators;
use Bairwell\Emojicalc\Exceptions\UnrecognisedOperator;
use Bairwell\Emojicalc\RenderViewInterface;
use Bairwell\Emojicalc\RequestInterface;
use Bairwell\Emojicalc\Response;
use Bairwell\Emojicalc\ResponseInterface;

/**
 * Class Index Controller.
 *
 * Main index system.
 *
 * @package Bairwell\Emojicalc\Controllers
 */
class Index implements IndexInterface
{

    /**
     * Render view
     * @var RenderViewInterface
     */
    protected $renderView;
    /**
     * List of all valid operators.
     * @var Operators
     */
    private $operators;

    /**
     * Index constructor.
     * @param Operators $operators List of valid operators.
     * @param RenderViewInterface $renderView The view rendering system.
     */
    public function __construct(Operators $operators, RenderViewInterface $renderView)
    {
        $this->operators = $operators;
        $this->renderView = $renderView;
    }

    /**
     * Start/homepage action.
     *
     * Just a public "proxy" for the renderStartPage function.
     *
     * @param RequestInterface $request The request object (unused here).
     * @param ResponseInterface $response The response object for populating.
     * @throws \Exception If the views do not exist.
     * @return ResponseInterface For chaining.
     */
    public function startAction(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->showEntryPage($request, $response);
    }

    /**
     * Actually outputs the start/entry page, but allows parameters to be passed in.
     *
     * @param RequestInterface $request The request object (unused here).
     * @param ResponseInterface $response The response object for populating.
     * @param array $placeholders Additional placeholders.
     * @return ResponseInterface For chaining.
     */
    private function showEntryPage(
        RequestInterface $request,
        ResponseInterface $response,
        array $placeholders = []
    ): ResponseInterface {
        // compose the JSON
        if (true === $request->isJson()) {
            $response = new Response('application/json;charset=utf-8');
            $placeholders = $this->getShowEntryPlaceholders($placeholders);
            $placeholders = $this->jsonifyPlaceholders($placeholders);

            return $response->addToBody(json_encode($placeholders));
        } else {
            return $response->addToBody($this->renderShowEntry($placeholders));
        }
    }

    /**
     * Set all the necessary placeholders for rendering the show entry page.
     * @param array $placeholders Any existing placeholders.
     * @return array Populated placeholders array.
     */
    private function getShowEntryPlaceholders(array $placeholders = []): array
    {
        // set a sensible default for the placeholders
        $defaults = [
            '%ERRORS%' => '',
            '%FIRST%' => '',
            '%SECOND%' => '',
            '%OPERATOR%' => ''
        ];
        $placeholders = array_merge($defaults, $placeholders);
        $htmlOperators = '';
        /* @var \Bairwell\Emojicalc\Entities\Operator $currentOperator */
        foreach ($this->operators as $currentOperator) {
            $selected = '';
            if ($currentOperator->getSymbol()->getSymbolCode() === $placeholders['%OPERATOR%']) {
                $selected = 'selected=\'selected\'';
            }
            $htmlOperators .= $this->renderView->renderView('operatorOption',
                [
                    '%OPERATORTYPE%' => $currentOperator->getOperatorType(),
                    '%SYMBOL%' => $currentOperator->getSymbol()->getSymbolCode(),
                    '%SELECTED%' => $selected
                ]
            );
        }
        $placeholders['%OPERATORS%'] = $htmlOperators;
        return $placeholders;
    }

    /**
     * Make the placeholders JSON friendly.
     * @param array $placeholders
     * @return array
     */
    private function jsonifyPlaceholders(array $placeholders): array
    {
        $newPlaceholders = [];
        foreach ($placeholders as $name => $value) {
            $name = strtolower(str_replace('%', '', $name));
            $newPlaceholders[$name] = $value;
        }
        return $newPlaceholders;
    }

    /**
     * Actually renders and then returns the show entry page.
     * @param array $placeholders Additional placeholders.
     * @return string The rendered start page.
     * @throws \Exception If the views do not exist.
     */
    private function renderShowEntry(array $placeholders = []): string
    {
        $placeholders = $this->getShowEntryPlaceholders($placeholders);
        return $this->renderView->renderView('showEntry', $placeholders);
    }

    /**
     * Do the calculations.
     *
     * @param RequestInterface $request The inbound request object.
     * @param ResponseInterface $response The response object.
     * @return ResponseInterface Populated response object.
     */
    public function calculateAction(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $query = $request->getParsedBody();
        $placeholders = [];
        $errors = [];
        // set some defaults "just in case"
        $first = 0;
        $second = 0;
        $operator = null;
        // check the first digit
        if (false === array_key_exists('first', $query)) {
            $errors[] = 'Missing first number';
        } else {
            $first = filter_var($query['first'], FILTER_VALIDATE_FLOAT);
            if (false === $first) {
                $errors[] = 'First number must be a float (decimal)';
            } else {
                $placeholders['%FIRST%'] = $first;
            }
        }
        // check the second digit
        if (false === array_key_exists('second', $query)) {
            $errors[] = 'Missing second number';
        } else {
            $second = filter_var($query['second'], FILTER_VALIDATE_FLOAT);
            if (false === $second) {
                $errors[] = 'Second number must be a float (decimal)';
            } else {
                $placeholders['%SECOND%'] = $second;
            }
        }
        // check the operator
        if (false === array_key_exists('operator', $query)) {
            $errors[] = 'Missing operator';
        } else {
            try {
                $operator = $this->operators->findOperatorBySymbol($query['operator']);
                $placeholders['%OPERATOR%'] = $operator->getSymbol()->getSymbolCode();
                $placeholders['%OPERATORNAME%'] = $operator->getOperatorName();
                $placeholders['%SYMBOLNAME%'] = $operator->getSymbol()->getSymbolName();
            } catch (UnrecognisedOperator $e) {
                // we could pass back the operator, but without validation, we daren't.
                $errors[] = 'Unrecognised operator';
            }
        }
        // handle if we have an error
        if (count($errors) > 0) {
            $errorString = implode('<br>', $errors);
            $placeholders['%ERRORS%'] = $errorString;
            return $this->showEntryPage($request, $response, $placeholders);

        }

        // do the calculation (if there are no errors)
        if (count($errors) === 0) {
            try {
                // set our own error handler to check things.
                set_error_handler(function () {
                    throw new \ArithmeticError('Bad sum');
                });
                $placeholders['%RESULT%'] = (string)$operator->performCalculation($first, $second);
            } catch (\ArithmeticError $e) {
                $errors[] = 'Unable to calculate - arithmetic error: ' . $e->getMessage();
            } catch (UnrecognisedOperator $e) {
                $errors[] = 'Unable to calculate - invalid operator';
            } finally {
                restore_error_handler();
            }
        }
        // handle errors
        if (count($errors) > 0) {
            $errorString = implode('<br>', $errors);
            $placeholders['%ERRORS%'] = $errorString;
            return $this->showEntryPage($request, $response, $placeholders);

        }
        // compose the JSON
        if (true === $request->isJson()) {
            $response->reset('application/json;charset=utf-8');
            $placeholders['%SHOWENTRY%'] = '';
            $placeholders['htmlResults'] = $this->renderView->renderView('results', $placeholders);
            $placeholders = $this->jsonifyPlaceholders($placeholders);

            $response->addToBody(json_encode($placeholders));
        } else {
            // if not, do the HTML
            $placeholders['%SHOWENTRY%'] = $this->renderShowEntry($placeholders);
            $rendered = $this->renderView->renderView('results', $placeholders);
            $response->addToBody($rendered);
        }
        return $response;
    }
}