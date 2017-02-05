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
     * The environment details.
     *
     * @var array
     */
    protected $environment;

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
     * Router constructor.
     *
     * @param array $environment Allow override $_SERVER settings for testing.
     * @param RenderViewInterface $renderView The view rendering system.
     */
    public function __construct(array $environment = [], RenderViewInterface $renderView)
    {
        if (true === empty($environment)) {
            $environment = $_SERVER;
        }

        $this->environment = $environment;
        $this->renderView = $renderView;
    }

    /**
     * Register a route.
     *
     * @param string $method The method we will be matching against.
     * @param string $route The URL we we be matching against.
     * @param callable $callable The callable route.
     */
    public function registerRoute(string $method, string $route, callable $callable)
    {
        $method = strtoupper($method);
        $this->routes[$method][$route] = $callable;
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
     * Run the routes.
     */
    public function run()
    {
        // we do this in a try/catch block as other exceptions may be raised.
        try {
            $request = new Request();
            $request->withMethod(strtoupper($this->environment['REQUEST_METHOD'] ?? '[Unknown]'));

            $request->withQueryParams($_GET);
            // set the content type
            $request->withContentType($this->environment['CONTENT_TYPE'] ?? 'text/html');
            // check if we have inbound json
            if (0 === strpos($request->getContentType(), 'application/json')) {
                $request->setJson(true);
                // read in the JSON
                $input = file_get_contents('php://input');
                $json = json_decode($input, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $request->withParsedBody($json);
                } else {
                    header('HTTP/1.1 500 Internal Server Error');
                    $page = $this->renderView->renderView('500internalServer', ['%DEBUG%' => 'Invalid JSON']);
                    echo $page;
                    return;
                }
            } else {
                $request->withParsedBody($_POST);
            }
            $requestUri = '';
            if (true === isset($this->environment['REQUEST_URI'])) {
                $requestUri = parse_url($this->environment['REQUEST_URI'], PHP_URL_PATH);
            }
            $requestUri = trim($requestUri, '/');
            $found = false;
            $matches = [];
            // now to match the routes
            if (true === array_key_exists($request->getMethod(), $this->routes)) {
                $keys = array_keys($this->routes[$request->getMethod()]);
                foreach ($keys as $routePath) {
                    if (1 === preg_match($routePath, $requestUri, $matches)) {
                        $found = $routePath;
                        break;
                    }
                }
            }

            $request->withUri($requestUri);
            $request->withPathParameters($matches);
            // now to run it if we found it.
            if (false !== $found) {
                $this->runFoundRoute($request, $this->routes[$request->getMethod()][$found]);
            } else {
                header('HTTP/1.1 404 Not Found');
                $page = $this->renderView->renderView('404notFound');
                echo $page;
            }//end if
        } catch (\Throwable $e) {
            header('HTTP/1.1 500 Internal Server Error');
            $page = $this->renderView->renderView('500internalServer', ['%DEBUG%' => $e->getMessage()]);
            echo $page;
        }
    }

    /**
     * Run the found route.
     *
     * @param RequestInterface $request The input request item in case data is needed.
     * @param callable $route The route we are running.
     * @throws \Exception If the route doesn't return a response object.
     */
    protected function runFoundRoute(RequestInterface $request, callable $route)
    {
        $response = new Response();
        // ensure all output is captured.
        ob_start();
        /* @var ResponseInterface $response */
        $response = $route($request, $response);
        if (false === ($response instanceof Response)) {
            throw new \RuntimeException('Invalid response from route');
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