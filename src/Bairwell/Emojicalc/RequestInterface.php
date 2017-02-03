<?php

namespace Bairwell\Emojicalc;


/**
 * Class Request.
 *
 * Basic "inspired by" PSR 7 style request object, but massively simplified.
 */
interface RequestInterface
{
    /**
     * Set the content type.
     * @param string $contentType New content type.
     * @return RequestInterface
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
     * @return RequestInterface
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
     * @return RequestInterface
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
     * @return RequestInterface Self to be fluent.
     */
    public function withParsedBody(array $data): RequestInterface;

    /**
     * Return an instance with the specified query string parameters.
     *
     * @param array $query
     * @return RequestInterface
     */
    public function withQueryParams(array $query): RequestInterface;

    /**
     * Get the query string parameters.
     * @return array
     */
    public function getQueryParams(): array;
}