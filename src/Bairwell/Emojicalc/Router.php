<?php
declare (strict_types = 1);
namespace Bairwell\Emojicalc;

/**
 * A very basic router.
 * @package Bairwell\Emojicalc
 */
class Router {
    use RenderViewTrait;

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
    protected $routes=[];

    /**
     * Router constructor.
     *
     * @param array $environment Allow override $_SERVER settings for testing.
     */
    public function __construct(array $environment = [])
    {
        if (true === empty($environment)) {
            $environment = $_SERVER;
        }

        $this->environment = $environment;
    }

    /**
     * Register a route.
     *
     * @param string $route The URL we we be matching against.
     * @param callable $callable The callable route.
     */
    public function registerRoute(string $route,callable $callable) {
        $this->routes[$route]=$callable;
    }

    public function run() {
        $request=new Request();
        $request->method=strtoupper($this->environment['REQUEST_METHOD'] ?? '[Unknown]');
        $request->postData=$_POST;
        $request->queryParameters=$_GET;
        if (true === isset($this->environment['REQUEST_URI'])) {
            $requestUri = parse_url($this->environment['REQUEST_URI'], PHP_URL_PATH);
        } else {
            $requestUri='';
        }
        $requestUri = trim($requestUri, '/');
        // now to match the routes
        $keys    = array_keys($this->routes);
        $found   = false;
        $matches = [];
        foreach ($keys as $routePath) {
            if (1 === preg_match($routePath, $requestUri, $matches)) {
                $found = $routePath;
                break;
            }
        }

        $request->url            = $requestUri;
        $request->pathParameters = $matches;
        // now to run it if we found it.
        // we do this in a try/catch block as other exceptions may be raised.
        try {
            if (false !== $found) {
                $this->runFoundRoute($request, $this->routes[$found]);
            } else {
                header('HTTP/1.1 404 Not Found');
                $page = $this->renderView('404notFound');
                echo $page;
            }//end if
        } catch (\Throwable $e) {
            header('HTTP/1.1 500 Internal Server Error');
            $page = $this->renderView('500internalServer', ['%DEBUG%' => $e->getMessage()]);
            echo $page;
        }
    }

    /**
     * Run the found route.
     *
     * @param Request $request The input request item in case data is needed.
     * @param callable $route The route we are running.
     */
    protected function runFoundRoute(Request $request,callable $route) {
        $response=new Response();
        // ensure all output is captured.
        ob_start();
        // PHPStorm suggests $route($request,$response), but I prefer using call_user_func
        // as I find it a bit clear.
        call_user_func($route, $request, $response);
        // append any outputted text to the body "just in case"
        $response->addToBody(ob_get_contents());
        ob_end_clean();
        header('Content-type: '.$response->getContentType(), true);
        // if it is html, then render it in the template
        if (0===strpos($response->getContentType(),'text/html')) {
            $templateParameters = ['%BODY%' => $response->getBody()];
            $page = $this->renderView('template', $templateParameters);
            echo $page;
        } else {
            // if it isn't html, then just output it "as is".
            echo $response->getBody();
        }
    }

}