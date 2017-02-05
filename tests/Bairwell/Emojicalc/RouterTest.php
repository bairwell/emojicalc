<?php

namespace Bairwell\Emojicalc;

use PHPUnit\Framework\TestCase;

function header(string $string, bool $replace = false, $http_response_code = null)
{
    RouterTest::processHeader($string, $replace, $http_response_code);
}

/**
 * Class RouterTest
 * @package Bairwell\Emojicalc
 * @coversDefaultClass Bairwell\Emojicalc\Router
 * @uses \Bairwell\Emojicalc\Router
 */
class RouterTest extends TestCase
{

    /**
     * Where we store the headers generated.
     * @var array
     */
    protected static $headers = [];

    /**
     * Are we currently in a test for process header to do its interception?
     * @var bool
     */
    protected static $inTest = false;

    /**
     * Process a header - store it if we are in a test, just output if not.
     *
     * @param string $string Header string.
     * @param bool $replace Should this be replaced.
     * @param int|null $http_response_code What the new status code should be.
     */
    public static function processHeader(string $string, bool $replace = false, $http_response_code = null)
    {
        if (self::$inTest) {
            self::$headers[] = ['s' => $string, 'r' => $replace, 'c' => $http_response_code];
        } else {
            \header($string, $replace, $http_response_code);
        }
    }

    /**
     * Setup for a single test.
     *
     * Clear the headers and set our test flag.
     */
    protected function setUp()
    {
        parent::setUp();
        self::$headers = [];
        self::$inTest = true;
    }

    /**
     * Tear down.
     *
     * Remove our test flag "just in case".
     */
    protected function tearDown()
    {
        parent::tearDown();
        self::$inTest = false;
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
     * @coversNothing
     * @return RequestInterface
     */
    public function getRequest(): RequestInterface
    {
        $request = $this->createMock(RequestInterface::class);
        return $request;
    }

    /**
     * Get a request with a protocol and path
     * @coversNothing
     * @return RequestInterface
     */
    public function getRequestWithProtocolAndPath(string $protocol, string $uri): RequestInterface
    {
        $request = $this->createMock(RequestInterface::class);
        $request->method('getMethod')->willReturn($protocol);
        $request->method('getUri')->willReturn($uri);
        return $request;
    }

    /**
     * @coversNothing
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        $response = $this->createMock(ResponseInterface::class);
        return $response;
    }

    /**
     * @param string $contentType Mock content type.
     * @coversNothing
     * @return ResponseInterface
     */
    public function getResponseWithContentType(string $contentType = 'text/html;charset=utf-8')
    {
        // use an anonymous class as it's a bit easier than making a stub or mock.
        $response = new class($contentType) implements ResponseInterface
        {
            protected $body = '';
            protected $contentType = '';

            /**
             * Response constructor.
             * @param string $contentType The content type we are returning.
             */
            public function __construct(string $contentType = 'text/html;charset=utf-8')
            {
                $this->contentType = $contentType;
            }

            /**
             * Gets the content type.
             * @return string
             */
            public function getContentType(): string
            {
                return $this->contentType;
            }

            /**
             * Sets the http content type.
             *
             * @param string $contentType
             * @return ResponseInterface Return self to be fluent.
             */
            public function setContentType(string $contentType): ResponseInterface
            {
                $this->contentType = $contentType;
                return $this;
            }

            /**
             * Gets the body.
             *
             * @return string
             */
            public function getBody(): string
            {
                return $this->body;
            }

            /**
             * Add a string to the body.
             *
             * @param string $string String to add.
             * @return ResponseInterface
             */
            public function addToBody(string $string): ResponseInterface
            {
                $this->body .= $string;
                return $this;
            }

            /**
             * Reset the response.
             * @param string $contentType
             */
            public function reset(string $contentType = 'text/html;charset=utf-8')
            {
                $this->contentType = $contentType;
                $this->body = '';
            }

        };
        return $response;
    }

    /**
     * @covers ::__construct
     * @covers ::getAllRoutes
     * @covers ::getAllRoutesForMethod
     * @covers ::areRoutesDefined
     */
    public function testConstructor()
    {
        $renderView = $this->getRenderView();
        $sut = new Router($this->getRequest(), $this->getResponse(), $renderView);
        $this->assertInstanceOf(RouterInterface::class, $sut);
        $this->assertFalse($sut->areRoutesDefined());
        $this->assertEmpty($sut->getAllRoutes());
        $this->assertEmpty($sut->getAllRoutesForMethod('gEt'));
    }

    /**
     * @covers ::registerRoute
     * @covers ::getAllRoutes
     * @covers ::getAllRoutesForMethod
     * @covers ::areRoutesDefined
     */
    public function testRegisterRoute()
    {
        $renderView = $this->getRenderView();
        $sut = new Router($this->getRequest(), $this->getResponse(), $renderView);
        $this->assertInstanceOf(RouterInterface::class, $sut);
        $this->assertFalse($sut->areRoutesDefined());
        $this->assertEmpty($sut->getAllRoutes());
        $this->assertEmpty($sut->getAllRoutesForMethod('gEt'));
        $firstRoute = function () {
            print 'test';
        };
        $return = $sut->registerRoute('GeT', '/hello', $firstRoute);
        $this->assertSame($sut, $return);
        $this->assertTrue($sut->areRoutesDefined());
        $this->assertSame(['GET' => ['/hello' => $firstRoute]], $sut->getAllRoutes());
        $this->assertSame(['/hello' => $firstRoute], $sut->getAllRoutesForMethod('gEt'));
        $secondRoute = function () {
            print 'testHere';
        };
        $return = $sut->registerRoute('pOsT', '/hello', $secondRoute);
        $this->assertSame($sut, $return);
        $this->assertTrue($sut->areRoutesDefined());
        $this->assertSame(['GET' => ['/hello' => $firstRoute], 'POST' => ['/hello' => $secondRoute]],
            $sut->getAllRoutes());
        $this->assertSame(['/hello' => $firstRoute], $sut->getAllRoutesForMethod('gEt'));
        $this->assertSame(['/hello' => $secondRoute], $sut->getAllRoutesForMethod('PoST'));
    }


    /**
     * @covers ::findMatchingRoute
     */
    public function testFindMatchingRoute()
    {
        $renderView = $this->getRenderView();
        $sut = new Router($this->getRequest(), $this->getResponse(), $renderView);
        $firstRoute = function () {
            print 'test';
        };
        $secondRoute = function () {
            print 'testHere';
        };
        $thirdRoute = function () {
            print 'third';
        };
        $fourthRoute = function () {
            print 'fourth';
        };
        $fifthRoute = function () {
            print 'fifth';
        };
        $sut->registerRoute('GeT', '/^hello$/', $firstRoute)
            ->registerRoute('pOsT', '/^hello$/', $secondRoute)
            ->registerRoute('GET', '/^here$/', $thirdRoute)
            ->registerRoute('GET', '/^there\/?$/', $fourthRoute)
            ->registerRoute('GET', '/^there\/is\/([A-Za-z]+)\/([A-Za-z]+)\/here\/?$/', $fifthRoute);
        $this->assertSame($secondRoute, $sut->findMatchingRoute('PosT', 'hello'));
        $this->assertFalse($sut->findMatchingRoute('GeT', 'there/we/go'));
        // check regexp
        $this->assertSame($fourthRoute, $sut->findMatchingRoute('GeT', 'there/'));
        $this->assertSame($fourthRoute, $sut->findMatchingRoute('GeT', 'there'));
        // check matches
        $matches = [];
        $this->assertSame($fifthRoute, $sut->findMatchingRoute('GeT', '/there/is/an/elephant/here', $matches));
        $this->assertSame(['there/is/an/elephant/here', 'an', 'elephant'], $matches);
    }

    /**
     *
     * @covers ::run
     * @covers ::runFoundRoute
     */
    public function testRunInvalidResponseFromRoute()
    {
        $renderView = $this->getRenderView();
        $request = $this->getRequestWithProtocolAndPath('GET', 'hello');
        $response = $this->getResponseWithContentType();
        $sut = new Router($request, $response, $renderView);
        $firstRoute = function () {
            print 'test';
        };
        $secondRoute = function () {
            print 'testHere';
        };
        $fourthRoute = function () {
            print 'fourth';
        };
        $fifthRoute = function () {
            print 'fifth';
        };
        $sut->registerRoute('GeT', '/^hello$/', $firstRoute)
            ->registerRoute('pOsT', '/^hello$/', $secondRoute)
            ->registerRoute('GET', '/^there\/?$/', $fourthRoute)
            ->registerRoute('GET', '/^there\/is\/([A-Za-z]+)\/([A-Za-z]+)\/here\/?$/', $fifthRoute);
        $sut->run();
        $this->expectOutputString('{"viewData:500internalServer":{"%DEBUG%":"Invalid response from route."}}');

        $this->assertSame(1, count(self::$headers));
        $this->assertSame('HTTP/1.1 500 Internal Server Error', self::$headers[0]['s']);
        $this->assertSame(true, self::$headers[0]['r']);
        $this->assertSame(500, self::$headers[0]['c']);
    }

    /**
     *
     * @covers ::run
     * @covers ::runFoundRoute
     */
    public function testRunNotFoundRoute()
    {
        $renderView = $this->getRenderView();
        $request = $this->getRequestWithProtocolAndPath('GET', 'sasdasdasdas');
        $response = $this->getResponseWithContentType('text/html');
        $sut = new Router($request, $response, $renderView);
        $firstRoute = function () {
            print 'test';
        };
        $secondRoute = function () {
            print 'testHere';
        };
        $fourthRoute = function () {
            print 'fourth';
        };
        $fifthRoute = function () {
            print 'fifth';
        };
        $sut->registerRoute('GeT', '/^hello$/', $firstRoute)
            ->registerRoute('pOsT', '/^hello$/', $secondRoute)
            ->registerRoute('GET', '/^there\/?$/', $fourthRoute)
            ->registerRoute('GET', '/^there\/is\/([A-Za-z]+)\/([A-Za-z]+)\/here\/?$/', $fifthRoute);
        $sut->run();
        $this->expectOutputString('{"viewData:404notFound":[]}');
        $this->assertSame(1, count(self::$headers));
        $this->assertSame('HTTP/1.1 404 Not Found', self::$headers[0]['s']);
        $this->assertSame(true, self::$headers[0]['r']);
        $this->assertSame(404, self::$headers[0]['c']);
    }

    /**
     *
     * @covers ::run
     * @covers ::runFoundRoute
     */
    public function testRunErrorInRoute()
    {
        $renderView = $this->getRenderView();
        $request = $this->getRequestWithProtocolAndPath('GET', 'hello');
        $response = $this->getResponseWithContentType('text/html');
        $sut = new Router($request, $response, $renderView);
        $firstRoute = function ($request, ResponseInterface $response): ResponseInterface {
            throw new \Exception('exception in route');
        };
        $sut->registerRoute('GeT', '/^hello$/', $firstRoute);
        $sut->run();
        $line = '{"viewData:500internalServer":{"%DEBUG%":"exception in route"}}';
        $this->expectOutputString($line);
        $this->assertSame(1, count(self::$headers));
        $this->assertSame('HTTP/1.1 500 Internal Server Error', self::$headers[0]['s']);
        $this->assertSame(true, self::$headers[0]['r']);
        $this->assertSame(500, self::$headers[0]['c']);
    }


    /**
     *
     * @covers ::run
     * @covers ::runFoundRoute
     */
    public function testRunHtml()
    {
        $renderView = $this->getRenderView();
        $request = $this->getRequestWithProtocolAndPath('GET', 'hello');
        $response = $this->getResponseWithContentType('text/html');
        $sut = new Router($request, $response, $renderView);
        $firstRoute = function ($request, ResponseInterface $response): ResponseInterface {
            $response->addToBody('hello');
            return $response;
        };
        $sut->registerRoute('GeT', '/^hello$/', $firstRoute);
        $sut->run();
        $line = '{"viewData:template":{"%BODY%":"hello"}}';
        $this->expectOutputString($line);
        $this->assertSame(1, count(self::$headers));
        $this->assertSame('Content-type: text/html;charset=utf-8', self::$headers[0]['s']);
        $this->assertSame(true, self::$headers[0]['r']);
        $this->assertSame(null, self::$headers[0]['c']);
    }

    /**
     *
     * @covers ::run
     * @covers ::runFoundRoute
     */
    public function testRunHtmlWithExtraOutput()
    {
        $renderView = $this->getRenderView();
        $request = $this->getRequestWithProtocolAndPath('GET', 'hello');
        $response = $this->getResponseWithContentType();
        $sut = new Router($request, $response, $renderView);
        $firstRoute = function ($request, ResponseInterface $response): ResponseInterface {
            $response->addToBody('hello');
            print 'extra Output';
            return $response;
        };
        $sut->registerRoute('GeT', '/^hello$/', $firstRoute);
        $sut->run();
        $line = '{"viewData:template":{"%BODY%":"helloextra Output"}}';
        $this->expectOutputString($line);
        $this->assertSame(1, count(self::$headers));
        $this->assertSame('Content-type: text/html;charset=utf-8', self::$headers[0]['s']);
        $this->assertSame(true, self::$headers[0]['r']);
        $this->assertSame(null, self::$headers[0]['c']);
    }

    /**
     *
     * @covers ::run
     * @covers ::runFoundRoute
     */
    public function testRunNotHtml()
    {
        $renderView = $this->getRenderView();
        $request = $this->getRequestWithProtocolAndPath('GET', 'hello');
        $response = $this->getResponseWithContentType('random');
        $sut = new Router($request, $response, $renderView);
        $firstRoute = function ($request, ResponseInterface $response): ResponseInterface {
            $response->addToBody('hello');
            return $response;
        };
        $sut->registerRoute('GeT', '/^hello$/', $firstRoute);
        $sut->run();
        $line = 'hello';
        $this->expectOutputString($line);
        $this->assertSame(1, count(self::$headers));
        $this->assertSame('Content-type: random', self::$headers[0]['s']);
        $this->assertSame(true, self::$headers[0]['r']);
        $this->assertSame(null, self::$headers[0]['c']);
    }
}