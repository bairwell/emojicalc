<?php
declare(strict_types=1);

namespace Bairwell\Emojicalc;

use Bairwell\Emojicalc\Controllers\About;
use Bairwell\Emojicalc\Controllers\AboutInterface;
use Bairwell\Emojicalc\Controllers\ControllerInterface;
use Bairwell\Emojicalc\Controllers\Index;
use Bairwell\Emojicalc\Controllers\IndexInterface;
use Bairwell\Emojicalc\Entities\Operator;
use Bairwell\Emojicalc\Entities\Operators;
use Bairwell\Emojicalc\Entities\OperatorsInterface;
use Bairwell\Emojicalc\Entities\Symbol;

/**
 * Class App
 * @package Bairwell\Emojicalc
 */
class App
{

    /**
     * Container data.
     *
     * @var ControllerInterface
     */
    protected $container;

    /**
     * Basic App constructor.
     *
     * @param ContainerInterface $container Container/configuration details (to be used instead of default).
     * @throws \InvalidArgumentException If configuration is invalid.
     */
    public function __construct(ContainerInterface $container = null)
    {
        if (null === $container) {
            $container = new Container();
        }
        $this->container = $container;

        $this->populateContainer();
    }

    /**
     * Build the basic configuration taking into account any default settings.
     * @throws \InvalidArgumentException If operators/router are not valid interfaces.
     */
    protected function populateContainer()
    {
        if (false === $this->container->has('environment')) {
            $this->container['environment'] = $_SERVER;
        }
        if (false === $this->container->has('post')) {
            $this->container['post'] = $_POST;
        }
        if (false === $this->container->has('get')) {
            $this->container['get'] = $_GET;
        }
        if (false === $this->container->has('phpinput')) {
            $this->container['phpinput'] = function (): string {
                return file_get_contents('php://input');
            };
        }
        $this->checkAndBuildDefaultViews();
        // only build the operators if they aren't already set. why call the classes
        // unnecessarily?
        if (false === $this->container->has('operators')) {
            $this->container['operators'] = $this->buildDefaultOperators();
        }
        if (false === ($this->container->get('operators') instanceof Operators)) {
            throw new \InvalidArgumentException('Invalid operators.');
        }
        $this->checkAndBuildRequestRequest();
        $this->checkAndBuildRequestResponse();
        // now check the router
        if (false === $this->container->has('router')) {
            $this->container['router'] = new Router(
                $this->container->get('request'),
                $this->container->get('response'),
                $this->container->get('renderViews')
            );
        }
        if (false === ($this->container->get('router') instanceof RouterInterface)) {
            throw new \InvalidArgumentException('Invalid router.');
        }
        $this->checkAndBuildDefaultRouting();
    }

    /**
     * Setup the view renderer.
     * If one is not specified, then use the setting of "views". If that isn't specified,
     * then use a sensible default.
     * @throws \InvalidArgumentException If renderViews cannot be built.
     * @throws \InvalidArgumentException If renderViews is missing and views is not a string.
     * @throws \InvalidArgumentException If renderViews is missing and views does not exist.
     */
    protected function checkAndBuildDefaultViews()
    {
        if (false === $this->container->has('renderViews')) {
            if (false === $this->container->has('views')) {
                $this->container['views'] = __DIR__ . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR;
            }
            if (false === is_string($this->container->get('views'))) {
                throw new \InvalidArgumentException('Invalid views path: should be a string.');
            }
            if (false === realpath($this->container->get('views'))) {
                throw new \InvalidArgumentException('Invalid views path: not real.');
            }
            $this->container['renderViews'] = new RenderView($this->container->get('views'));
        }
        if (false === ($this->container->get('renderViews') instanceof RenderViewInterface)) {
            throw new \InvalidArgumentException('Invalid renderViews.');
        }
    }

    /**
     * Build the list of default operators.
     * @return OperatorsInterface
     * @throws \InvalidArgumentException If it isn't a recognised operator type being passed in.
     */
    protected function buildDefaultOperators(): OperatorsInterface
    {
        $operators = (new Operators())
            ->addOperator(new Operator\Addition(new Symbol("\u{1f47d}", 'Alien')))
            ->addOperator(new Operator\Subtraction(new Symbol("\u{1f480}", 'Skull')))
            ->addOperator(new Operator\Multiply(new Symbol("\u{1f47b}", 'Ghost')))
            ->addOperator(new Operator\Division(new Symbol("\u{1f631}", 'Scream'))
            );
        return $operators;
    }

    /**
     * Check and build the request item.
     * @throws \InvalidArgumentException If the request is not a request interface.
     */
    protected function checkAndBuildRequestRequest()
    {
        if (false === $this->container->has('request')) {
            $this->container['request'] = new Request(
                $this->container->get('environment'),
                $this->container->get('get'),
                $this->container->get('post'),
                $this->container->get('phpinput')
            );
        }
        if (false === ($this->container->get('request') instanceof RequestInterface)) {
            throw new \InvalidArgumentException('Invalid request.');
        }
    }
    /**
     * Check and build the request item.
     * @throws \InvalidArgumentException If the response is not a response interface.
     */
    protected function checkAndBuildRequestResponse()
    {
        if (false === $this->container->has('response')) {
            $this->container['response'] = new Response();
        }
        if (false === ($this->container->get('response') instanceof ResponseInterface)) {
            throw new \InvalidArgumentException('Invalid response.');
        }
    }

    /**
     * Setup the default routes.
     * @throws \InvalidArgumentException If indexController does not support indexInterface.
     */
    protected function checkAndBuildDefaultRouting()
    {
        if (false === $this->container->get('router')->areRoutesDefined()) {
            // if we have no routes, let's build up the default routes using the standard controllers.
            // now check the index controller
            if (false === $this->container->has('indexController')) {
                $this->container['indexController'] = new Index($this->container->get('operators'),
                    $this->container->get('renderViews'));
            }
            if (false === ($this->container->get('indexController') instanceof IndexInterface)) {
                throw new \InvalidArgumentException('Invalid IndexController.');
            }
            if (false === $this->container->has('aboutController')) {
                $this->container['aboutController'] = new About($this->container->get('renderViews'));
            }
            if (false === ($this->container->get('aboutController') instanceof AboutInterface)) {
                throw new \InvalidArgumentException('Invalid aboutController.');
            }
            $this->container->get('router')->registerRoute('GET', '/^\/?$/',
                [$this->container->get('indexController'), 'startAction']);
            $this->container->get('router')->registerRoute('POST', '/^\/?$/',
                [$this->container->get('indexController'), 'calculateAction']);
            $this->container->get('router')->registerRoute('GET', '/^author\/?$/',
                [$this->container->get('aboutController'), 'authorAction']);
            $this->container->get('router')->registerRoute('GET', '/^specification\/?$/',
                [$this->container->get('aboutController'), 'specificationAction']);
            $this->container->get('router')->registerRoute('GET', '/^licence\/?$/',
                [$this->container->get('aboutController'), 'licenceAction']);
        }
    }

    /**
     * Return the container.
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    /**
     * Run the application (after registering routes)
     */
    public function run()
    {
        $this->container->get('router')->run();
    }

}