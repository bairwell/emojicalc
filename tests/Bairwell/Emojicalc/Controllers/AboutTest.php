<?php

namespace Bairwell\Emojicalc\Controllers;
use Bairwell\Emojicalc\RenderViewInterface;
use Bairwell\Emojicalc\RequestInterface;
use Bairwell\Emojicalc\Response;
use PHPUnit\Framework\TestCase;
/**
 * Class AboutTest
 * @package Bairwell\Emojicalc\Controllers
 * @coversDefaultClass Bairwell\Emojicalc\Controllers\About
 * @uses \Bairwell\Emojicalc\Request
 * @uses \Bairwell\Emojicalc\Response
 * @uses \Bairwell\Emojicalc\RenderView
 * @uses \Bairwell\Emojicalc\Controllers\About
 */
class AboutTest extends TestCase
{

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
     * Tests the author action outputs HTML as expected.
     *
     * @covers ::authorAction
     * @covers ::__construct
     */
    public function testAuthorAction()
    {
        $sut = new About($this->getRenderView());
        $request = $this->createMock(RequestInterface::class);
        $response = new Response();
        // start the test
        $sentResponse = $sut->authorAction($request, $response);
        // assert we got our response back.
        $this->assertSame($response,$sentResponse,'Response object is different: got '.get_class($sentResponse).' back'.' expected '.get_class($response));

        // body checks
        $body = $sentResponse->getBody();
        // check the body will match up
        $view = $this->getRenderView();
        $output = $view->renderView('author', []);
        $this->assertSame($output, $body);
    }
    /**
     * Tests the specification action outputs HTML as expected.
     *
     * @covers ::specificationAction
     * @covers ::__construct
     */
    public function testSpecificationAction()
    {
        $sut = new About($this->getRenderView());
        $request = $this->createMock(RequestInterface::class);
        $response = new Response();
        // start the test
        $sentResponse = $sut->specificationAction($request, $response);
        // assert we got our response back.
        $this->assertSame($response,$sentResponse,'Response object is different: got '.get_class($sentResponse).' back'.' expected '.get_class($response));

        // body checks
        $body = $sentResponse->getBody();
        // check the body will match up
        $view = $this->getRenderView();
        $output = $view->renderView('specification', []);
        $this->assertSame($output, $body);
    }

    /**
     * Tests the licence action outputs HTML as expected.
     *
     * @covers ::licenceAction
     * @covers ::__construct
     */
    public function testLicenceAction()
    {
        $sut = new About($this->getRenderView());
        $request = $this->createMock(RequestInterface::class);
        $response = new Response();
        // start the test
        $sentResponse = $sut->licenceAction($request, $response);
        // assert we got our response back.
        $this->assertSame($response,$sentResponse,'Response object is different: got '.get_class($sentResponse).' back'.' expected '.get_class($response));

        // body checks
        $body = $sentResponse->getBody();
        // check the body will match up
        $view = $this->getRenderView();
        $output = $view->renderView('licence', []);
        $this->assertSame($output, $body);
    }
}