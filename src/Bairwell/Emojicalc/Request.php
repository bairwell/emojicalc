<?php
declare (strict_types=1);

namespace Bairwell\Emojicalc;

/**
 * Class Request.
 *
 * Basic "inspired by" PSR 7 style request object, but massively simplified.
 */
class Request implements RequestInterface
{

    /**
     * Holds the URL.
     * @var string|null
     */
    protected $url;

    /**
     * Holds the path parameters.
     *
     * Set by the router if necessary.
     *
     * @var array
     */
    protected $pathParameters = [];
    /**
     * Request method.
     *
     * @var string|null
     */
    protected $method;
    /**
     * Query string parameters.
     *
     * @var array|null
     */
    protected $queryParameters;

    /**
     * Post data.
     *
     * @var array|null
     */
    protected $postData;

    /**
     * Stores if this is a json request or not.
     * @var bool|null
     */
    protected $isJson;

    /**
     * Stores the content type.
     * @var string|null
     */
    protected $contentType;

    /**
     * Holds the environment/$_SERVER data.
     * @var array
     */
    protected $environment = [];

    /**
     * Holds input data (if provided). Ideal for unit testing.
     * @var null|string
     */
    protected $phpInput;

    /**
     * Have we done the setup yet?
     *
     * Allows us to have a light constructor and
     * to save checking individual properties.
     *
     * @var bool
     */
    protected $isSetup = false;

    /**
     * Request constructor.
     * @param array $environment Environment data (instead of using _SERVER)
     * @param array $get GET data (instead of using $_GET)
     * @param array $post POST data (instead of using $_POST)
     * @param callable $phpInput How to get the input data.
     */
    public function __construct(array $environment, array $get, array $post, callable $phpInput)
    {
        $this->environment = $environment;
        $this->queryParameters = $get;
        $this->postData = $post;
        $this->phpInput = $phpInput;
        $this->isSetup = false;
    }

    /**
     * Set the content type.
     * @param string $contentType New content type.
     * @return RequestInterface
     */
    public function withContentType(string $contentType): RequestInterface
    {
        $this->contentType = $contentType;
        return $this;
    }

    /**
     * Get the set content type.
     * @return string
     */
    public function getContentType(): string
    {
        $this->setUp();
        return $this->contentType;
    }

    /**
     * Performs the setup.
     */
    protected function setUp()
    {
        // instantly return if we are already setup.
        if ($this->isSetup) {
            return;
        }
        // set method if not already set
        if (null === $this->method) {
            $this->method = strtoupper($this->environment['REQUEST_METHOD'] ?? self::DEFAULTMETHOD);
        }
        // set the content type  if not already set
        if (null === $this->contentType) {
            $this->contentType = $this->environment['CONTENT_TYPE'] ?? self::DEFAULTCONTENTTYPE;
        }
        // check if we have inbound json
        if (null === $this->isJson) {
            $this->isJson = false;
            if (0 === strpos($this->contentType, self::JSONCONTENTTYPE)) {
                // read in the JSON
                $json = json_decode(call_user_func($this->phpInput), true);
                // only set the json if valid json is received
                if (json_last_error() === JSON_ERROR_NONE) {
                    $this->isJson = true;
                    $this->postData = $json;
                }
            }
        }
        // setup the url if not already setup
        if (null === $this->url) {
            $this->url = $this->environment['REQUEST_URI'] ?? '';
        }
        // store that we are set up.
        $this->isSetup = true;

    }

    /**
     * Set the status of the is json request flag.
     * @param bool $isJson Is this a json request or not.
     * @return RequestInterface
     */
    public function setJson(bool $isJson): RequestInterface
    {
        $this->isJson = $isJson;
        return $this;
    }

    /**
     * Is this a JSON request?
     * @return bool
     */
    public function isJson(): bool
    {
        $this->setUp();
        return $this->isJson;
    }

    /**
     * Return the request method.
     * @return string
     */
    public function getMethod(): string
    {
        $this->setUp();
        return $this->method;
    }

    /**
     * Return an instance with the provided HTTP method.
     *
     * @param string $method Method to set.
     * @return RequestInterface
     */
    public function withMethod(string $method): RequestInterface
    {
        $this->method = $method;
        return $this;
    }

    /**
     * Get the parsed data.
     *
     * @return array
     */
    public function getParsedBody(): array
    {
        $this->setUp();
        return $this->postData;
    }

    /**
     * Set the parsed data.
     * @param array $data The input data.
     * @return RequestInterface Self to be fluent.
     */
    public function withParsedBody(array $data): RequestInterface
    {
        $this->postData = $data;
        return $this;
    }

    /**
     * Return an instance with the specified query string parameters.
     *
     * @param array $query
     * @return RequestInterface
     */
    public function withQueryParams(array $query): RequestInterface
    {
        $this->queryParameters = $query;
        return $this;
    }

    /**
     * Get the query string parameters.
     * @return array
     */
    public function getQueryParams(): array
    {
        $this->setUp();
        return $this->queryParameters;
    }

    /**
     * Get the uri.
     * @return string
     */
    public function getUri(): string
    {
        $this->setUp();
        return $this->url;
    }

    /**
     * Set the uri.
     * @param string $uri
     * @return RequestInterface
     */
    public function withUri(string $uri): RequestInterface
    {
        $this->url = $uri;
        return $this;
    }

    /**
     * Set the path parameters.
     * @param array $parameters
     * @return RequestInterface
     */
    public function withPathParameters(array $parameters): RequestInterface
    {
        $this->pathParameters = $parameters;
        return $this;
    }

    /**
     * Get the path parameters.
     * @return array
     */
    public function getPathParameters(): array
    {
        $this->setUp();
        return $this->pathParameters;
    }
}//end class
