<?php

namespace Bairwell\Emojicalc\Controllers;

use Bairwell\Emojicalc\Entities\Operator;
use Bairwell\Emojicalc\Entities\Operators;
use Bairwell\Emojicalc\Entities\OperatorsInterface;
use Bairwell\Emojicalc\Entities\Symbol;
use Bairwell\Emojicalc\Exceptions\UnrecognisedOperator;
use Bairwell\Emojicalc\RenderViewInterface;
use Bairwell\Emojicalc\RequestInterface;
use Bairwell\Emojicalc\Response;
use PHPUnit\Framework\TestCase;

/**
 * Class IndexTest
 * @package Bairwell\Emojicalc\Controllers
 * @coversDefaultClass Bairwell\Emojicalc\Controllers\Index
 * @uses \Bairwell\Emojicalc\Request
 * @uses \Bairwell\Emojicalc\Response
 * @uses \Bairwell\Emojicalc\RenderView
 * @uses \Bairwell\Emojicalc\Controllers\Index
 * @uses \Bairwell\Emojicalc\Entities\Operator
 */
class IndexTest extends TestCase
{

    /**
     * @coversNothing
     */
    protected function getFirstOperator(): Operator
    {
        $firstSymbol = $this->getMockBuilder(Symbol::class)->disableOriginalConstructor()->getMock();
        $firstSymbol->expects($this->any())->method('getSymbolCode')->willReturn('firstSymbol');
        $firstSymbol->expects($this->any())->method('getSymbolName')->willReturn('firstName');

        $first = $this->getMockBuilder(Operator::class)->setConstructorArgs([$firstSymbol])->getMock();
        $first->expects($this->any())->method('getOperatorType')->willReturn('plus');
        $first->expects($this->any())->method('getOperatorName')->willReturn('addition');
        $first->expects($this->any())->method('performCalculation')->will($this->returnCallback(function ($a, $b) {
            return $a + $b;
        }));
        return $first;
    }

    /**
     * @coversNothing
     */
    protected function getSecondOperator(): Operator
    {
        $secondSymbol = $this->getMockBuilder(Symbol::class)->disableOriginalConstructor()->getMock();
        $secondSymbol->expects($this->any())->method('getSymbolCode')->willReturn('secondSymbol');
        $secondSymbol->expects($this->any())->method('getSymbolName')->willReturn('secondName');

        $second = $this->getMockBuilder(Operator::class)->setConstructorArgs([$secondSymbol])->getMock();
        $second->expects($this->any())->method('getOperatorType')->willReturn('minus');
        $second->expects($this->any())->method('getOperatorName')->willReturn('subtraction');
        $second->expects($this->any())->method('performCalculation')->will($this->returnCallback(function ($a, $b) {
            return $a - $b;
        }));
        return $second;
    }

    /**
     * @coversNothing
     */
    protected function getThirdOperator(): Operator
    {
        $thirdSymbol = $this->getMockBuilder(Symbol::class)->disableOriginalConstructor()->getMock();
        $thirdSymbol->expects($this->any())->method('getSymbolCode')->willReturn('thirdSymbol');
        $thirdSymbol->expects($this->any())->method('getSymbolName')->willReturn('thirdName');

        $third = $this->getMockBuilder(Operator::class)->setConstructorArgs([$thirdSymbol])->getMock();
        $third->expects($this->any())->method('getOperatorType')->willReturn('times');
        $third->expects($this->any())->method('getOperatorName')->willReturn('multiple');
        $third->expects($this->any())->method('performCalculation')->will($this->returnCallback(function () {
            trigger_error('test error');
        }));
        return $third;
    }

    /**
     * @coversNothing
     */
    protected function getFourthOperator(): Operator
    {
        $fourthSymbol = $this->getMockBuilder(Symbol::class)->disableOriginalConstructor()->getMock();
        $fourthSymbol->expects($this->any())->method('getSymbolCode')->willReturn('fourthSymbol');
        $fourthSymbol->expects($this->any())->method('getSymbolName')->willReturn('fourthName');

        $fourth = $this->getMockBuilder(Operator::class)->setConstructorArgs([$fourthSymbol])->getMock();
        $fourth->expects($this->any())->method('getOperatorType')->willReturn('badOperator');
        $fourth->expects($this->any())->method('getOperatorName')->willReturn('badName');
        $fourth->expects($this->any())->method('performCalculation')->will($this->returnCallback(function () {
            throw new UnrecognisedOperator('bad');
        }));
        return $fourth;
    }

    /**
     * Build our list of test operators.
     *
     * @coversNothing
     * @return OperatorsInterface
     */
    protected function getOperators(): OperatorsInterface
    {
        // first
        $first = $this->getFirstOperator();
        $second = $this->getSecondOperator();
        $third = $this->getThirdOperator();
        $fourth = $this->getFourthOperator();

        $operators = $this->createMock(Operators::class);
        $operators->expects($this->any())->method('valid')->will($this->onConsecutiveCalls(true, true, true, true,
            false));
        $operators->expects($this->any())->method('current')->will($this->onConsecutiveCalls($first, $second, $third,
            $fourth));

        // handle the selection
        $callback = function ($input) use ($second, $third, $fourth) {
            switch ($input) {
                case 'secondSymbol':
                    return $second;
                    break;
                case 'thirdSymbol':
                    return $third;
                    break;
                case 'fourthSymbol':
                    return $fourth;
                    break;
                default:
                    throw new UnrecognisedOperator('unrecog');
            }
        };

        $operators->expects($this->any())->method('findOperatorBySymbol')->will($this->returnCallback($callback));
        return $operators;
    }

    /**
     * Build a mock render view.
     * @coversNothing
     * @return RenderViewInterface
     */
    protected function getRenderView(): RenderViewInterface
    {

        $renderView = $this->createMock(RenderViewInterface::class);
        $callback = function (string $fileName, array $parameters = []): string {
            $viewData['viewData:' . $fileName] = $parameters;
            $out = json_encode($viewData);
            return $out;
        };
        $renderView->expects($this->any())->method('renderView')->will($this->returnCallback($callback));
        return $renderView;
    }

    /**
     * Tests the start action outputs HTML as expected.
     *
     * @covers ::startAction
     * @covers ::showEntryPage
     * @covers ::renderShowEntry
     * @covers ::getShowEntryPlaceholders
     * @covers ::__construct
     */
    public function testStartActionHtml()
    {
        $operators = $this->getOperators();
        $sut = new Index($operators, $this->getRenderView());
        $request = $this->createMock(RequestInterface::class);
        $request->method('isJson')->willReturn(false);
        $response = new Response();
        // start the test
        $sentResponse = $sut->startAction($request, $response);
        // assert we got our response back.
        $this->assertSame($response, $sentResponse,
            'Response object is different: got ' . get_class($sentResponse) . ' back' . ' expected ' . get_class($response));

        // body checks
        $body = $sentResponse->getBody();
        // check the body will match up
        $view = $this->getRenderView();
        $operatorsText = '';
        $operators = $this->getOperators();
        foreach ($operators as $operator) {
            $operatorsText .= $view->renderView('operatorOption', [
                    '%OPERATORTYPE%' => $operator->getOperatorType(),
                    '%SYMBOL%' => $operator->getSymbol()->getSymbolCode(),
                    '%SELECTED%' => ''
                ]
            );
        }
        $output = $view->renderView('showEntry', [
            '%ERRORS%' => '',
            '%FIRST%' => '',
            '%SECOND%' => '',
            '%OPERATOR%' => '',
            '%OPERATORS%' => $operatorsText
        ]);
        $this->assertSame($output, $body);
    }

    /**
     *
     * @covers ::__construct
     * @covers ::startAction
     * @covers ::showEntryPage
     * @covers ::renderShowEntry
     * @covers ::getShowEntryPlaceholders
     * @covers ::jsonifyPlaceholders
     */
    public function testStartActionJson()
    {
        $operators = $this->getOperators();
        $sut = new Index($operators, $this->getRenderView());

        $request = $this->getMockBuilder(RequestInterface::class)->getMock();
        $request->expects($this->once())->method('isJson')->willReturn(true);

        $response = new Response();
        $sentResponse = $sut->startAction($request, $response);
        $this->assertInstanceOf(Response::class, $sentResponse);
        // check content type
        $contentType = $sentResponse->getContentType();
        $this->assertContains('application/json', $contentType);
        // body checks
        $body = $sentResponse->getBody();
        //
        $view = $this->getRenderView();
        $operatorsText = '';
        $operators = $this->getOperators();
        foreach ($operators as $operator) {
            $operatorsText .= $view->renderView('operatorOption', [
                    '%OPERATORTYPE%' => $operator->getOperatorType(),
                    '%SYMBOL%' => $operator->getSymbol()->getSymbolCode(),
                    '%SELECTED%' => ''
                ]
            );
        }
        $expectedJson = [
            'errors' => '',
            'first' => '',
            'second' => '',
            'operator' => '',
            'operators' => $operatorsText
        ];
        $decoded = json_decode($body, true);
        foreach ($expectedJson as $key => $value) {
            $this->assertArrayHasKey($key, $decoded, 'Missing json key ' . $key);
            $this->assertEquals($value, $decoded[$key], 'Mismatching values for key ' . $key);
        }
    }

    /**
     *
     * @covers ::__construct
     * @covers ::calculateAction
     * @covers ::getShowEntryPlaceholders
     */
    public function testCalculateActionNoParameters()
    {
        $operators = $this->getOperators();
        $sut = new Index($operators, $this->getRenderView());

        $request = $this->getMockBuilder(RequestInterface::class)->getMock();
        $request->expects($this->once())->method('isJson')->willReturn(false);
        $request->expects($this->once())->method('getParsedBody')->willReturn([]);

        $response = new Response();
        $sentResponse = $sut->calculateAction($request, $response);
        $this->assertInstanceOf(Response::class, $sentResponse);
        // check content type
        $contentType = $sentResponse->getContentType();
        $this->assertContains('text/html', $contentType);
        // body checks
        $body = $sentResponse->getBody();
        // check the body will match up
        $view = $this->getRenderView();
        $operatorsText = '';
        $operators = $this->getOperators();
        foreach ($operators as $operator) {
            $operatorsText .= $view->renderView('operatorOption', [
                    '%OPERATORTYPE%' => $operator->getOperatorType(),
                    '%SYMBOL%' => $operator->getSymbol()->getSymbolCode(),
                    '%SELECTED%' => ''
                ]
            );
        }
        $output = $view->renderView('showEntry', [
            '%ERRORS%' => 'Missing first number<br>Missing second number<br>Missing operator',
            '%FIRST%' => '',
            '%SECOND%' => '',
            '%OPERATOR%' => '',
            '%OPERATORS%' => $operatorsText
        ]);
        $this->assertSame($output, $body);
    }


    /**
     *
     * @covers ::__construct
     * @covers ::calculateAction
     * @covers ::getShowEntryPlaceholders
     */
    public function testCalculateActionFirstParametersNotFloatNoSecondNumberOrOperator()
    {
        $operators = $this->getOperators();
        $sut = new Index($operators, $this->getRenderView());

        $request = $this->getMockBuilder(RequestInterface::class)->getMock();
        $request->expects($this->once())->method('isJson')->willReturn(false);
        $request->expects($this->once())->method('getParsedBody')->willReturn(['first' => 'X']);

        $response = new Response();
        $sentResponse = $sut->calculateAction($request, $response);
        $this->assertInstanceOf(Response::class, $sentResponse);
        // check content type
        $contentType = $sentResponse->getContentType();
        $this->assertContains('text/html', $contentType);
        // body checks
        $body = $sentResponse->getBody();
        // check the body will match up
        $view = $this->getRenderView();
        $operatorsText = '';
        $operators = $this->getOperators();
        foreach ($operators as $operator) {
            $operatorsText .= $view->renderView('operatorOption', [
                    '%OPERATORTYPE%' => $operator->getOperatorType(),
                    '%SYMBOL%' => $operator->getSymbol()->getSymbolCode(),
                    '%SELECTED%' => ''
                ]
            );
        }
        $output = $view->renderView('showEntry', [
            '%ERRORS%' => 'First number must be a float (decimal)<br>Missing second number<br>Missing operator',
            '%FIRST%' => '',
            '%SECOND%' => '',
            '%OPERATOR%' => '',
            '%OPERATORS%' => $operatorsText
        ]);
        $this->assertSame($output, $body);
    }

    /**
     *
     * @covers ::__construct
     * @covers ::calculateAction
     * @covers ::getShowEntryPlaceholders
     */
    public function testCalculateActionSecondParametersNotFloatNoOperator()
    {
        $operators = $this->getOperators();
        $sut = new Index($operators, $this->getRenderView());

        $request = $this->getMockBuilder(RequestInterface::class)->getMock();
        $request->expects($this->once())->method('isJson')->willReturn(false);
        $request->expects($this->once())->method('getParsedBody')->willReturn(['first' => '23.23', 'second' => 'X']);

        $response = new Response();
        $sentResponse = $sut->calculateAction($request, $response);
        $this->assertInstanceOf(Response::class, $sentResponse);
        // check content type
        $contentType = $sentResponse->getContentType();
        $this->assertContains('text/html', $contentType);
        // body checks
        $body = $sentResponse->getBody();
        // check the body will match up
        $view = $this->getRenderView();
        $operatorsText = '';
        $operators = $this->getOperators();
        foreach ($operators as $operator) {
            $operatorsText .= $view->renderView('operatorOption', [
                    '%OPERATORTYPE%' => $operator->getOperatorType(),
                    '%SYMBOL%' => $operator->getSymbol()->getSymbolCode(),
                    '%SELECTED%' => ''
                ]
            );
        }
        $output = $view->renderView('showEntry', [
            '%ERRORS%' => 'Second number must be a float (decimal)<br>Missing operator',
            '%FIRST%' => 23.23,
            '%SECOND%' => '',
            '%OPERATOR%' => '',
            '%OPERATORS%' => $operatorsText
        ]);
        $this->assertSame($output, $body);
    }

    /**
     *
     * @covers ::__construct
     * @covers ::calculateAction
     * @covers ::getShowEntryPlaceholders
     */
    public function testCalculateActionOperatorUnrecognised()
    {
        $operators = $this->getOperators();
        $sut = new Index($operators, $this->getRenderView());

        $request = $this->getMockBuilder(RequestInterface::class)->getMock();
        $request->expects($this->once())->method('isJson')->willReturn(false);
        $request->expects($this->once())->method('getParsedBody')->willReturn([
            'first' => '2.43',
            'second' => '43.32',
            'operator' => 'unrecog'
        ]);

        $response = new Response();
        $sentResponse = $sut->calculateAction($request, $response);
        $this->assertInstanceOf(Response::class, $sentResponse);
        // check content type
        $contentType = $sentResponse->getContentType();
        $this->assertContains('text/html', $contentType);
        // body checks
        $body = $sentResponse->getBody();
        // check the body will match up
        $view = $this->getRenderView();
        $operatorsText = '';
        $operators = $this->getOperators();
        foreach ($operators as $operator) {
            $operatorsText .= $view->renderView('operatorOption', [
                    '%OPERATORTYPE%' => $operator->getOperatorType(),
                    '%SYMBOL%' => $operator->getSymbol()->getSymbolCode(),
                    '%SELECTED%' => ''
                ]
            );
        }
        $output = $view->renderView('showEntry', [
            '%ERRORS%' => 'Unrecognised operator',
            '%FIRST%' => 2.43,
            '%SECOND%' => 43.32,
            '%OPERATOR%' => '',
            '%OPERATORS%' => $operatorsText
        ]);
        $this->assertSame($output, $body);
    }


    /**
     *
     * @covers ::__construct
     * @covers ::calculateAction
     * @covers ::getShowEntryPlaceholders
     */
    public function testCalculateActionAllComplete()
    {
        $operators = $this->getOperators();
        $sut = new Index($operators, $this->getRenderView());

        $request = $this->getMockBuilder(RequestInterface::class)->getMock();
        $request->expects($this->once())->method('isJson')->willReturn(false);
        $request->expects($this->once())->method('getParsedBody')->willReturn([
            'first' => '102.43',
            'second' => '43.32',
            'operator' => 'secondSymbol'
        ]);

        $response = new Response();
        $sentResponse = $sut->calculateAction($request, $response);
        $this->assertInstanceOf(Response::class, $sentResponse);
        // check content type
        $contentType = $sentResponse->getContentType();
        $this->assertContains('text/html', $contentType);
        // body checks
        $body = $sentResponse->getBody();
        // check the body will match up
        $view = $this->getRenderView();
        $operatorsText = '';
        $operators = $this->getOperators();
        foreach ($operators as $operator) {
            $operatorsText .= $view->renderView('operatorOption', [
                    '%OPERATORTYPE%' => $operator->getOperatorType(),
                    '%SYMBOL%' => $operator->getSymbol()->getSymbolCode(),
                    '%SELECTED%' => 'minus' === $operator->getOperatorType() ? 'selected=\'selected\'' : ''
                ]
            );
        }
        $showEntry = $view->renderView('showEntry', [
            '%ERRORS%' => '',
            '%FIRST%' => 102.43,
            '%SECOND%' => 43.32,
            '%OPERATOR%' => 'secondSymbol',
            '%OPERATORNAME%' => 'subtraction',
            '%SYMBOLNAME%' => 'secondName',
            '%RESULT%' => '59.11',
            '%OPERATORS%' => $operatorsText
        ]);
        $output = $view->renderView('results', [
            '%FIRST%' => 102.43,
            '%SECOND%' => 43.32,
            '%OPERATOR%' => 'secondSymbol',
            '%OPERATORNAME%' => 'subtraction',
            '%SYMBOLNAME%' => 'secondName',
            '%RESULT%' => '59.11',
            '%SHOWENTRY%' => $showEntry
        ]);
        $this->assertSame($output, $body);

    }

    /**
     *
     * @covers ::__construct
     * @covers ::calculateAction
     * @covers ::getShowEntryPlaceholders
     * @expectedException
     */
    public function testCalculateActionAllCompleteWithRestoredErrorHandler()
    {
        $operators = $this->getOperators();
        $sut = new Index($operators, $this->getRenderView());

        $request = $this->getMockBuilder(RequestInterface::class)->getMock();
        $request->expects($this->once())->method('isJson')->willReturn(false);
        $request->expects($this->once())->method('getParsedBody')->willReturn([
            'first' => '102.43',
            'second' => '43.32',
            'operator' => 'secondSymbol'
        ]);

        $response = new Response();
        set_error_handler(function ($errno, $errstr, $errfile, $errline) {
            throw new \RuntimeException('Expected error handler');
        });
        $sut->calculateAction($request, $response);
        $exception = null;
        try {
            trigger_error('test', \E_USER_NOTICE);
            restore_error_handler();
        } catch (\RuntimeException $e) {
            $exception = $e;

        }
        restore_error_handler();
        $this->assertInstanceOf('\RuntimeException', $exception, 'Error handler not restored');
        $this->assertSame('Expected error handler', $exception->getMessage(), 'Error handler not restored');
    }

    /**
     *
     * @covers ::__construct
     * @covers ::calculateAction
     * @covers ::getShowEntryPlaceholders
     */
    public function testCalculateActionErrorRaised()
    {
        $operators = $this->getOperators();
        $sut = new Index($operators, $this->getRenderView());

        $request = $this->getMockBuilder(RequestInterface::class)->getMock();
        $request->expects($this->once())->method('isJson')->willReturn(false);
        $request->expects($this->once())->method('getParsedBody')->willReturn([
            'first' => '102.43',
            'second' => '43.32',
            'operator' => 'thirdSymbol'
        ]);

        $response = new Response();
        $sentResponse = $sut->calculateAction($request, $response);
        $this->assertInstanceOf(Response::class, $sentResponse);
        // check content type
        $contentType = $sentResponse->getContentType();
        $this->assertContains('text/html', $contentType);
        // body checks
        $body = $sentResponse->getBody();
        // check the body will match up
        $view = $this->getRenderView();
        $operatorsText = '';
        $operators = $this->getOperators();
        foreach ($operators as $operator) {
            $operatorsText .= $view->renderView('operatorOption', [
                    '%OPERATORTYPE%' => $operator->getOperatorType(),
                    '%SYMBOL%' => $operator->getSymbol()->getSymbolCode(),
                    '%SELECTED%' => 'times' === $operator->getOperatorType() ? 'selected=\'selected\'' : ''
                ]
            );
        }
        $output = $view->renderView('showEntry', [
            '%ERRORS%' => 'Unable to calculate - arithmetic error: Bad sum',
            '%FIRST%' => 102.43,
            '%SECOND%' => 43.32,
            '%OPERATOR%' => 'thirdSymbol',
            '%OPERATORNAME%' => 'multiple',
            '%SYMBOLNAME%' => 'thirdName',
            '%OPERATORS%' => $operatorsText
        ]);
        $this->assertSame($output, $body);

    }


    /**
     *
     * @covers ::__construct
     * @covers ::calculateAction
     * @covers ::getShowEntryPlaceholders
     */
    public function testCalculateActionInvalidOperatorRaised()
    {
        $operators = $this->getOperators();
        $sut = new Index($operators, $this->getRenderView());

        $request = $this->getMockBuilder(RequestInterface::class)->getMock();
        $request->expects($this->once())->method('isJson')->willReturn(false);
        $request->expects($this->once())->method('getParsedBody')->willReturn([
            'first' => '102.43',
            'second' => '43.32',
            'operator' => 'fourthSymbol'
        ]);

        $response = new Response();
        $sentResponse = $sut->calculateAction($request, $response);
        $this->assertInstanceOf(Response::class, $sentResponse);
        // check content type
        $contentType = $sentResponse->getContentType();
        $this->assertContains('text/html', $contentType);
        // body checks
        $body = $sentResponse->getBody();
        // check the body will match up
        $view = $this->getRenderView();
        $operatorsText = '';
        $operators = $this->getOperators();
        foreach ($operators as $operator) {
            $operatorsText .= $view->renderView('operatorOption', [
                    '%OPERATORTYPE%' => $operator->getOperatorType(),
                    '%SYMBOL%' => $operator->getSymbol()->getSymbolCode(),
                    '%SELECTED%' => 'badOperator' === $operator->getOperatorType() ? 'selected=\'selected\'' : ''
                ]
            );
        }
        $output = $view->renderView('showEntry', [
            '%ERRORS%' => 'Unable to calculate - invalid operator',
            '%FIRST%' => 102.43,
            '%SECOND%' => 43.32,
            '%OPERATOR%' => 'fourthSymbol',
            '%OPERATORNAME%' => 'badName',
            '%SYMBOLNAME%' => 'fourthName',
            '%OPERATORS%' => $operatorsText
        ]);
        $this->assertSame($output, $body);

    }

    /**
     *
     * @covers ::__construct
     * @covers ::calculateAction
     * @covers ::getShowEntryPlaceholders
     */
    public function testCalculateActionAllCompleteJson()
    {
        $operators = $this->getOperators();
        $sut = new Index($operators, $this->getRenderView());

        $request = $this->getMockBuilder(RequestInterface::class)->getMock();
        $request->expects($this->once())->method('isJson')->willReturn(true);
        $request->expects($this->once())->method('getParsedBody')->willReturn([
            'first' => '102.43',
            'second' => '43.32',
            'operator' => 'secondSymbol'
        ]);

        $response = new Response();
        $sentResponse = $sut->calculateAction($request, $response);
        $this->assertInstanceOf(Response::class, $sentResponse);
        // check content type
        $contentType = $sentResponse->getContentType();
        $this->assertContains('application/json', $contentType);
        // body checks
        $body = $sentResponse->getBody();
        // check no error
        $renderView = $this->getRenderView();
        $expectedDecoded = [
            '%FIRST%' => 102.43,
            '%SECOND%' => 43.32,
            '%OPERATOR%' => 'secondSymbol',
            '%OPERATORNAME%' => 'subtraction',
            '%SYMBOLNAME%' => 'secondName',
            '%RESULT%' => '59.11',
            '%SHOWENTRY%' => ''
        ];
        $htmlResults = $renderView->renderView('results', $expectedDecoded);
        $expectedJson = [
            'first' => 102.43,
            'second' => 43.32,
            'operator' => 'secondSymbol',
            'operatorname' => 'subtraction',
            'symbolname' => 'secondName',
            'result' => '59.11',
            'showentry' => '',
            'htmlresults' => $htmlResults
        ];
        $decoded = json_decode($body, true);
        foreach ($expectedJson as $key => $value) {
            $this->assertArrayHasKey($key, $decoded, 'Missing json key ' . $key);
            $this->assertSame($value, $decoded[$key], 'Mismatching values for key ' . $key);
        }
    }
}
