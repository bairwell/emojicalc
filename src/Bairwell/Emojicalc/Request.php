<?php
declare (strict_types = 1);

namespace Bairwell\Emojicalc;

/**
 * Class Request.
 *
 * Basic "inspired by" PSR 7 style request object, but massively simplified.
 */
class Request
{

    /**
     * Request method.
     *
     * @var string
     */
    public $method;
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
     * Get the parsed data.
     *
     * @return array
     */
    public function getParsedBody() : array  {
        return $this->postData;
    }

    /**
     * Set the parsed data.
     * @param array $data The input data.
     * @return Request Self to be fluent.
     */
    public function withParsedBody(array $data) : self {
        $this->postData=$data;
        return $this;
    }
    /**
     * Return an instance with the specified query string parameters.
     *
     * @param array $query
     * @return Request
     */
    public function withQueryParams(array $query) : self {
        $this->queryParameters=$query;
        return $this;
    }
    /**
     * Get the query string parameters.
     * @return array
     */
    public function getQueryParams() : array {
        return $this->queryParameters;
    }

}//end class
