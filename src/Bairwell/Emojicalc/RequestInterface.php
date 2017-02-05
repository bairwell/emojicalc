<?php
/**
 * Created by PhpStorm.
 * User: Richard Bairwell
 * Date: 05/02/2017
 * Time: 17:26
 */

namespace Bairwell\Emojicalc;


/**
 * Class Request.
 *
 * Basic "inspired by" PSR 7 style request object, but massively simplified.
 */
interface RequestInterface
{
    /**
     * Text returned if this is an unset method.
     */
    const DEFAULTMETHOD = '[UNKNOWN]';

    /**
     * Text returned if this is an unset content type.
     */
    const DEFAULTCONTENTTYPE = 'text/html';

    /**
     * What content type JSON has.
     */
    const JSONCONTENTTYPE = 'application/json';

    /**
     * Request constructor.
     * @param array $environment Environment data (instead of using _SERVER)
     * @param array $get GET data (instead of using $_GET)
     * @param array $post POST data (instead of using $_POST)
     * @param callable $phpInput How to get the input data.
     */
    public function __construct(array $environment, array $get, array $post, callable $phpInput);

    /**
     * Set the content type.
     * @param string $contentType New content type.
     * @return \Bairwell\Emojicalc\RequestInterface
     */
    public function withContentType(string $contentType): RequestInterface;

    /**
     * Get the set content type.
     * @return string
     */
    public function getContentType(): string;

    /**
     * Set the status of the is json request flag.
     * @param bool $isJson Is this a json request or not.
     * @return \Bairwell\Emojicalc\RequestInterface
     */
    public function setJson(bool $isJson): RequestInterface;

    /**
     * Is this a JSON request?
     * @return bool
     */
    public function isJson(): bool;

    /**
     * Return the request method.
     * @return string
     */
    public function getMethod(): string;

    /**
     * Return an instance with the provided HTTP method.
     *
     * @param string $method Method to set.
     * @return \Bairwell\Emojicalc\RequestInterface
     */
    public function withMethod(string $method): RequestInterface;

    /**
     * Get the parsed data.
     *
     * @return array
     */
    public function getParsedBody(): array;

    /**
     * Set the parsed data.
     * @param array $data The input data.
     * @return \Bairwell\Emojicalc\RequestInterface
     */
    public function withParsedBody(array $data): RequestInterface;

    /**
     * Return an instance with the specified query string parameters.
     *
     * @param array $query
     * @return \Bairwell\Emojicalc\RequestInterface
     */
    public function withQueryParams(array $query): RequestInterface;

    /**
     * Get the query string parameters.
     * @return array
     */
    public function getQueryParams(): array;

    /**
     * Get the uri.
     * @return string
     */
    public function getUri(): string;

    /**
     * Set the uri.
     * @param string $uri
     * @return \Bairwell\Emojicalc\RequestInterface
     */
    public function withUri(string $uri): RequestInterface;

    /**
     * Set the path parameters.
     * @param array $parameters
     * @return \Bairwell\Emojicalc\RequestInterface
     */
    public function withPathParameters(array $parameters): RequestInterface;

    /**
     * Get the path parameters.
     * @return array
     */
    public function getPathParameters(): array;
}