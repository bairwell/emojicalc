<?php
declare (strict_types=1);

namespace Bairwell\Emojicalc;

/**
 * A very basic router.
 * @package Bairwell\Emojicalc
 */
class Router implements RouterInterface
{

    /**
     * Our list of known routes.
     * @var array
     */
    protected $routes = [];

    /**
     * Our render view object.
     *
     * @var RenderViewInterface
     */
    protected $renderView;

    /**
     * The request object.
     *
     * @var RequestInterface
     */
    protected $request;

    /**
     * The response object.
     *
     * @var ResponseInterface
     */
    protected $response;

    /**
     * Router constructor.
     *
     * @param RequestInterface $request The request.
     * @param ResponseInterface $response The response.
     * @param RenderViewInterface $renderView The view rendering system.
     */
    public function __construct(RequestInterface $request, ResponseInterface $response, RenderViewInterface $renderView)
    {
        $this->request = $request;
        $this->response = $response;
        $this->renderView = $renderView;
    }

    /**
     * Get all current configured routes. Only really useful for testing.
     * @return array
     */
    public function getAllRoutes(): array
    {
        return $this->routes;
    }

    /**
     * Returns all routes for a method. Only really useful for testing.
     * @param string $method The route we are asking about.
     * @return array
     */
    public function getAllRoutesForMethod(string $method): array
    {
        $method = strtoupper($method);
        if (true === isset($this->routes[$method])) {
            return $this->routes[$method];
        } else {
            return [];
        }
    }

    /**
     * Register a route.
     *
     * @param string $method The method we will be matching against.
     * @param string $route The URL we we be matching against.
     * @param callable $callable The callable route.
     * @returns RouterInterface To be fluent
     */
    public function registerRoute(string $method, string $route, callable $callable): RouterInterface
    {
        $method = strtoupper($method);
        $this->routes[$method][$route] = $callable;
        return $this;
    }

    /**
     * Checks and returns true if any routes are defined.
     * @return bool
     */
    public function areRoutesDefined(): bool
    {
        return (!empty($this->routes));
    }

    /**
     * Finds a matching route.
     *
     * @param string $method HTTP Method.
     * @param string $url URL to match against.
     * @param array $matches Returns a list of matches of the path parameter.
     * @return false|callable
     */
    public function findMatchingRoute(string $method, string $url, array &$matches = [])
    {
        $url = parse_url($url, PHP_URL_PATH);
        $url = trim($url, '/');
        $method = strtoupper($method);
        $found = false;
        // now to match the routes
        if (true === array_key_exists($method, $this->routes)) {
            $keys = array_keys($this->routes[$method]);
            foreach ($keys as $routePath) {
                if (1 === preg_match($routePath, $url, $matches)) {
                    $found = $this->routes[$method][$routePath];
                    break;
                }
            }
        }
        return $found;
    }

    /**
     * Run the routes.
     */
    public function run()
    {
        $matches = [];
        $found = $this->findMatchingRoute($this->request->getMethod(), $this->request->getUri(), $matches);
        if (false !== $found) {
            $this->request->withPathParameters($matches);
        }
        // we do this in a try/catch block as other exceptions may be raised.
        try {
            // now to run it if we found it.
            if (false !== $found) {
                $this->runFoundRoute($found);
            } else {
                header('HTTP/1.1 404 Not Found',true,404);
                $page = $this->renderView->renderView('404notFound');
                echo $page;
            }//end if
        } catch (\Throwable $e) {
            header('HTTP/1.1 500 Internal Server Error',true,500);
            $page = $this->renderView->renderView('500internalServer', ['%DEBUG%' => $e->getMessage()]);
            echo $page;
        }
    }

    /**
     * Run the found route.
     *
     * @param callable $route The route we are running.
     * @throws \Exception If the route doesn't return a response object.
     * @throws \Throwable If the route throws an exception.
     */
    protected function runFoundRoute(callable $route)
    {
        // ensure all output is captured.
        $start=ob_get_level();
        ob_start();
        try {
            /* @var ResponseInterface $response */
            $response = $route($this->request, $this->response);
            if (false === ($response instanceof ResponseInterface)) {
                throw new \RuntimeException('Invalid response from route.');
            }
        } catch (\Throwable $e) {
            // if there is an exception in the route, end the buffer.
            ob_end_clean();
            throw $e;
        }
        // append any outputted text to the body "just in case"
        $response->addToBody(ob_get_contents());
        ob_end_clean();
        header('Content-type: ' . $response->getContentType(), true);
        // if it is html, then render it in the template
        if (0 === strpos($response->getContentType(), 'text/html')) {
            $templateParameters = ['%BODY%' => $response->getBody()];
            $page = $this->renderView->renderView('template', $templateParameters);
            echo $page;
        } else {
            // if it isn't html, then just output it "as is".
            echo $response->getBody();
        }
    }

}