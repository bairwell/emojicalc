<?php

namespace Bairwell\Emojicalc;

use Bairwell\Emojicalc\Entities\Operator\Addition;
use Bairwell\Emojicalc\Entities\Operator\Division;
use Bairwell\Emojicalc\Entities\Operator\Multiply;
use Bairwell\Emojicalc\Entities\Operator\Subtraction;
use Bairwell\Emojicalc\Entities\OperatorsInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Exception\InvalidArgumentException;

/**
 * Class AppTest
 * @package Bairwell\Emojicalc
 * @coversDefaultClass Bairwell\Emojicalc\App
 * @uses \Bairwell\Emojicalc\App
 * @TODO Test to see if the configuration data we pass into the container remains the same.
 */
class AppTest extends TestCase {

    /**
     * Get our mock container.
     * @return ContainerInterface
     */
    protected function getContainer() : ContainerInterface {
        $mockContainer=new class () implements ContainerInterface {
            protected $store=[];

            /**
             * Get an item from the container store.
             * @param string $id
             * @return mixed
             * @throws \Exception
             */
            public function get(string $id)
            {
                if (false===$this->has($id)) {
                    throw new \Exception('Invalid get call to container');
                }
                return $this->store[$id];
            }

            /**
             * Has this container got an item?
             * @param string $id Id of the item.
             * @return bool
             */
            public function has(string $id) : bool
            {
                return array_key_exists($id, $this->store);
            }

            /**
             * Whether a offset exists
             * @link http://php.net/manual/en/arrayaccess.offsetexists.php
             * @param mixed $offset <p>
             * An offset to check for.
             * </p>
             * @return boolean true on success or false on failure.
             * </p>
             * <p>
             * The return value will be casted to boolean if non-boolean was returned.
             * @since 5.0.0
             * @throws \RuntimeException If used.
             */
            public function offsetExists($offset) : bool
            {
                throw new \RuntimeException('offsetExists on containers cannot be used directly. Use "has".');
            }

            /**
             * Offset to retrieve
             * @link http://php.net/manual/en/arrayaccess.offsetget.php
             * @param mixed $offset <p>
             * The offset to retrieve.
             * </p>
             * @return mixed Can return all value types.
             * @since 5.0.0
             * @throws \RuntimeException If used.
             */
            public function offsetGet($offset)
            {
                throw new \RuntimeException('offsetGet on containers cannot be used directly. Use "get".');
            }

            /**
             * Offset to set
             * @link http://php.net/manual/en/arrayaccess.offsetset.php
             * @param mixed $offset <p>
             * The offset to assign the value to.
             * </p>
             * @param mixed $value <p>
             * The value to set.
             * </p>
             * @return void
             * @since 5.0.0
             */
            public function offsetSet($offset, $value)
            {
                $this->store[$offset] = $value;
            }

            /**
             * Offset to unset
             * @link http://php.net/manual/en/arrayaccess.offsetunset.php
             * @param mixed $offset <p>
             * The offset to unset.
             * </p>
             * @return void
             * @since 5.0.0
             */
            public function offsetUnset($offset)
            {
                unset($this->store[$offset]);
            }

        };
        return $mockContainer;
    }

    /**
     * @covers ::__construct
     * @covers ::getContainer
     * @covers ::populateContainer
     * @covers ::checkAndBuildDefaultViews
     * @covers ::buildDefaultOperators
     * @covers ::checkAndBuildRequestRequest
     * @covers ::checkAndBuildRequestResponse
     * @covers ::checkAndBuildDefaultRouting
     * @uses \Bairwell\Emojicalc\Container
     * @uses \Bairwell\Emojicalc\Controllers\About
     * @uses \Bairwell\Emojicalc\Controllers\Index
     * @uses \Bairwell\Emojicalc\Entities\Operator
     * @uses \Bairwell\Emojicalc\Entities\Operators
     * @uses \Bairwell\Emojicalc\Entities\Symbol
     * @uses \Bairwell\Emojicalc\RenderView
     * @uses \Bairwell\Emojicalc\Request
     * @uses \Bairwell\Emojicalc\Response
     * @uses \Bairwell\Emojicalc\Router
     */
    public function testConstructorNoParameter() {
        $app=new App();
        $container=$app->getContainer();
        $this->assertInstanceOf(ContainerInterface::class,$container);
        $this->checkDefaultContainerContents($container);
    }
    /**
     * @covers ::__construct
     * @covers ::getContainer
     * @covers ::populateContainer
     * @covers ::checkAndBuildDefaultViews
     * @covers ::buildDefaultOperators
     * @covers ::checkAndBuildRequestRequest
     * @covers ::checkAndBuildRequestResponse
     * @covers ::checkAndBuildDefaultRouting
     * @uses \Bairwell\Emojicalc\Controllers\About
     * @uses \Bairwell\Emojicalc\Controllers\Index
     * @uses \Bairwell\Emojicalc\Entities\Operator
     * @uses \Bairwell\Emojicalc\Entities\Operators
     * @uses \Bairwell\Emojicalc\Entities\Symbol
     * @uses \Bairwell\Emojicalc\RenderView
     * @uses \Bairwell\Emojicalc\Request
     * @uses \Bairwell\Emojicalc\Response
     * @uses \Bairwell\Emojicalc\Router
     */
    public function testConstructorEmptyContainer() {
        $mockContainer=$this->getContainer();
        $app=new App($mockContainer);
        $container=$app->getContainer();
        $this->assertSame($mockContainer,$container);
        $this->checkDefaultContainerContents($container);
    }

    /**
     * @covers ::run
     * @uses \Bairwell\Emojicalc\Controllers\About
     * @uses \Bairwell\Emojicalc\Controllers\Index
     * @uses \Bairwell\Emojicalc\Entities\Operator
     * @uses \Bairwell\Emojicalc\Entities\Operators
     * @uses \Bairwell\Emojicalc\Entities\Symbol
     * @uses \Bairwell\Emojicalc\RenderView
     * @uses \Bairwell\Emojicalc\Request
     * @uses \Bairwell\Emojicalc\Response
     * @uses \Bairwell\Emojicalc\Router
     */
    public function testRun() {
        $mockContainer=$this->getContainer();
        $app=new App($mockContainer);
        $mockRouter=$this->createMock(RouterInterface::class);
        $mockRouter->expects($this->once())->method('run');
        $mockContainer['router']=$mockRouter;
        $app->run();
    }
    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid operators.
     * @covers ::populateContainer
     * @uses \Bairwell\Emojicalc\Controllers\About
     * @uses \Bairwell\Emojicalc\Controllers\Index
     * @uses \Bairwell\Emojicalc\Entities\Operator
     * @uses \Bairwell\Emojicalc\Entities\Operators
     * @uses \Bairwell\Emojicalc\Entities\Symbol
     * @uses \Bairwell\Emojicalc\RenderView
     * @uses \Bairwell\Emojicalc\Request
     * @uses \Bairwell\Emojicalc\Response
     * @uses \Bairwell\Emojicalc\Router
     */
    public function testConstructBadOperators() {
        $mockContainer=$this->getContainer();
        $mockContainer['operators']=function() { throw new \Exception('Should fail'); };
        $app=new App($mockContainer);

        $this->fail('Should have thrown an exception');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid router.
     * @covers ::populateContainer
     * @uses \Bairwell\Emojicalc\Controllers\About
     * @uses \Bairwell\Emojicalc\Controllers\Index
     * @uses \Bairwell\Emojicalc\Entities\Operator
     * @uses \Bairwell\Emojicalc\Entities\Operators
     * @uses \Bairwell\Emojicalc\Entities\Symbol
     * @uses \Bairwell\Emojicalc\RenderView
     * @uses \Bairwell\Emojicalc\Request
     * @uses \Bairwell\Emojicalc\Response
     * @uses \Bairwell\Emojicalc\Router
     */
    public function testConstructBadRouter() {
        $mockContainer=$this->getContainer();
        $mockContainer['router']=function() { throw new \Exception('Should fail'); };
        $app=new App($mockContainer);
        $this->fail('Should have thrown an exception');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid views path: should be a string.
     * @covers ::checkAndBuildDefaultViews
     * @uses \Bairwell\Emojicalc\Controllers\About
     * @uses \Bairwell\Emojicalc\Controllers\Index
     * @uses \Bairwell\Emojicalc\Entities\Operator
     * @uses \Bairwell\Emojicalc\Entities\Operators
     * @uses \Bairwell\Emojicalc\Entities\Symbol
     * @uses \Bairwell\Emojicalc\RenderView
     * @uses \Bairwell\Emojicalc\Request
     * @uses \Bairwell\Emojicalc\Response
     * @uses \Bairwell\Emojicalc\Router
     */
    public function testConstructBadViewsPath() {
        $mockContainer=$this->getContainer();
        $mockContainer['views']=function() { throw new \Exception('Should fail'); };
        $app=new App($mockContainer);
        $this->fail('Should have thrown an exception');
    }
    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid views path: not real.
     * @covers ::checkAndBuildDefaultViews
     * @uses \Bairwell\Emojicalc\Controllers\About
     * @uses \Bairwell\Emojicalc\Controllers\Index
     * @uses \Bairwell\Emojicalc\Entities\Operator
     * @uses \Bairwell\Emojicalc\Entities\Operators
     * @uses \Bairwell\Emojicalc\Entities\Symbol
     * @uses \Bairwell\Emojicalc\RenderView
     * @uses \Bairwell\Emojicalc\Request
     * @uses \Bairwell\Emojicalc\Response
     * @uses \Bairwell\Emojicalc\Router
     */
    public function testConstructNotRealViewsPath() {
        $mockContainer=$this->getContainer();
        $mockContainer['views']='/invalid/path/here/';
        $app=new App($mockContainer);
        $this->fail('Should have thrown an exception');
    }
    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid renderViews.
     * @covers ::checkAndBuildDefaultViews
     * @uses \Bairwell\Emojicalc\Controllers\About
     * @uses \Bairwell\Emojicalc\Controllers\Index
     * @uses \Bairwell\Emojicalc\Entities\Operator
     * @uses \Bairwell\Emojicalc\Entities\Operators
     * @uses \Bairwell\Emojicalc\Entities\Symbol
     * @uses \Bairwell\Emojicalc\RenderView
     * @uses \Bairwell\Emojicalc\Request
     * @uses \Bairwell\Emojicalc\Response
     * @uses \Bairwell\Emojicalc\Router
     */
    public function testConstructInvalidRenderViews() {
        $mockContainer=$this->getContainer();
        $mockContainer['renderViews']=function() { throw new \Exception('Should fail'); };
        $app=new App($mockContainer);
        $this->fail('Should have thrown an exception');
    }
    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid request.
     * @covers ::checkAndBuildRequestRequest
     * @uses \Bairwell\Emojicalc\Controllers\About
     * @uses \Bairwell\Emojicalc\Controllers\Index
     * @uses \Bairwell\Emojicalc\Entities\Operator
     * @uses \Bairwell\Emojicalc\Entities\Operators
     * @uses \Bairwell\Emojicalc\Entities\Symbol
     * @uses \Bairwell\Emojicalc\RenderView
     * @uses \Bairwell\Emojicalc\Request
     * @uses \Bairwell\Emojicalc\Response
     * @uses \Bairwell\Emojicalc\Router
     */
    public function testConstructInvalidRequest() {
        $mockContainer=$this->getContainer();
        $mockContainer['request']=function() { throw new \Exception('Should fail'); };
        $app=new App($mockContainer);
        $this->fail('Should have thrown an exception');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid response.
     * @covers ::checkAndBuildRequestResponse
     * @uses \Bairwell\Emojicalc\Controllers\About
     * @uses \Bairwell\Emojicalc\Controllers\Index
     * @uses \Bairwell\Emojicalc\Entities\Operator
     * @uses \Bairwell\Emojicalc\Entities\Operators
     * @uses \Bairwell\Emojicalc\Entities\Symbol
     * @uses \Bairwell\Emojicalc\RenderView
     * @uses \Bairwell\Emojicalc\Request
     * @uses \Bairwell\Emojicalc\Response
     * @uses \Bairwell\Emojicalc\Router
     */
    public function testConstructInvalidResponse() {
        $mockContainer=$this->getContainer();
        $mockContainer['response']=function() { throw new \Exception('Should fail'); };
        $app=new App($mockContainer);
        $this->fail('Should have thrown an exception');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid IndexController.
     * @covers ::checkAndBuildDefaultRouting
     * @uses \Bairwell\Emojicalc\Controllers\About
     * @uses \Bairwell\Emojicalc\Controllers\Index
     * @uses \Bairwell\Emojicalc\Entities\Operator
     * @uses \Bairwell\Emojicalc\Entities\Operators
     * @uses \Bairwell\Emojicalc\Entities\Symbol
     * @uses \Bairwell\Emojicalc\RenderView
     * @uses \Bairwell\Emojicalc\Request
     * @uses \Bairwell\Emojicalc\Response
     * @uses \Bairwell\Emojicalc\Router
     */
    public function testConstructInvalidIndexController() {
        $mockContainer=$this->getContainer();
        $mockContainer['indexController']=function() { throw new \Exception('Should fail'); };
        $app=new App($mockContainer);
        $this->fail('Should have thrown an exception');
    }
    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid aboutController.
     * @covers ::checkAndBuildDefaultRouting
     * @uses \Bairwell\Emojicalc\Controllers\About
     * @uses \Bairwell\Emojicalc\Controllers\Index
     * @uses \Bairwell\Emojicalc\Entities\Operator
     * @uses \Bairwell\Emojicalc\Entities\Operators
     * @uses \Bairwell\Emojicalc\Entities\Symbol
     * @uses \Bairwell\Emojicalc\RenderView
     * @uses \Bairwell\Emojicalc\Request
     * @uses \Bairwell\Emojicalc\Response
     * @uses \Bairwell\Emojicalc\Router
     */
    public function testConstructInvalidAboutController() {
        $mockContainer=$this->getContainer();
        $mockContainer['aboutController']=function() { throw new \Exception('Should fail'); };
        $app=new App($mockContainer);
        $this->fail('Should have thrown an exception');
    }
    /**
     * Performs a series of checks on the container to ensure it has what we are expecting.
     * @param ContainerInterface $container
     */
    protected function checkDefaultContainerContents(ContainerInterface $container) {
        $this->assertSame($_SERVER,$container->get('environment'));
        $this->assertSame($_POST,$container->get('post'));
        $this->assertSame($_GET,$container->get('get'));
        $this->assertTrue(is_callable($container->get('phpinput')));
        // views
        $viewPath=$container->get('views');
        $this->assertNotFalse(realpath($viewPath));
        /* @var RenderViewInterface $views */
        $views=$container->get('renderViews');
        $this->assertInstanceOf(RenderViewInterface::class,$views);
        // perhaps add more verification to the views object.
        // operators
        $this->assertInstanceOf(OperatorsInterface::class,$container->get('operators'));
        /* @var OperatorsInterface $operators */
        $operators=$container->get('operators');
        $this->assertInstanceOf(Addition::class,$operators->findOperatorBySymbol("\u{1f47d}"));
        $this->assertInstanceOf(Subtraction::class,$operators->findOperatorBySymbol("\u{1f480}"));
        $this->assertInstanceOf(Multiply::class,$operators->findOperatorBySymbol("\u{1f47b}"));
        $this->assertInstanceOf(Division::class,$operators->findOperatorBySymbol("\u{1f631}"));
        // request

        /* @var \Bairwell\Emojicalc\RequestInterface $request */
        $request=$container->get('request');
        $this->assertInstanceOf(RequestInterface::class,$request);
        // perhaps add more verification to the request object.
        // response
        $this->assertInstanceOf(ResponseInterface::class,$container->get('response'));
        // router
        $this->assertInstanceOf(RouterInterface::class,$container->get('router'));
        // check routes
        /* @var \Bairwell\Emojicalc\RouterInterface $router */
        $router=$container->get('router');
        $this->assertTrue($router->areRoutesDefined());


    }

}