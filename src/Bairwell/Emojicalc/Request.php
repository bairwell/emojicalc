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
    public $queryParameters = [];

    /**
     * Post data.
     *
     * @var array
     */
    public $postData = [];

}//end class
