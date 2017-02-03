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
     * @var string
     */
    public $url='';

    /**
     * Holds the path parameters.
     * @var array
     */
    public $pathParameters=[];
    /**
     * Request method.
     *
     * @var string
     */
    protected $method='';
    /**
     * Query string parameters.
     *
     * @var array
     */
    protected $queryParameters = [];

    /**
     * Post data.
     *
     * @var array
     */
    protected $postData = [];

    /**
     * Stores if this is a json request or not.
     * @var bool
     */
    protected $isJson=false;

    /**
     * Stores the content type.
     * @var string
     */
    protected $contentType='text/html';

    /**
     * Set the content type.
     * @param string $contentType New content type.
     * @return RequestInterface
     */
    public function withContentType(string $contentType) : RequestInterface
    {
        $this->contentType=$contentType;
        return $this;
    }

    /**
     * Get the set content type.
     * @return string
     */
    public function getContentType() : string {
        return $this->contentType;
    }

    /**
     * Set the status of the is json request flag.
     * @param bool $isJson Is this a json request or not.
     * @return RequestInterface
     */
    public function setJson(bool $isJson) : RequestInterface
    {
        $this->isJson=$isJson;
        return $this;
    }

    /**
     * Is this a JSON request?
     * @return bool
     */
    public function isJson() : bool {
        return $this->isJson;
    }
    /**
     * Return the request method.
     * @return string
     */
    public function getMethod() : string {
        return $this->method;
    }

    /**
     * Return an instance with the provided HTTP method.
     *
     * @param string $method Method to set.
     * @return RequestInterface
     */
    public function withMethod(string $method) : RequestInterface
    {
        $this->method=$method;
        return $this;
    }
    /**
     * Get the parsed data.
     *
     * @return array
     */
    public function getParsedBody(): array
    {
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
        return $this->queryParameters;
    }

}//end class
