<?php

namespace Bairwell\Emojicalc;


/**
 * Class Response.
 *
 * Basic "inspired by" PSR 7 style response object, but massively massively simplified.
 */
interface ResponseInterface
{
    /**
     * Response constructor.
     * @param string $contentType The content type we are returning.
     */
    public function __construct(string $contentType = 'text/html;charset=utf-8');

    /**
     * Gets the content type.
     * @return string
     */
    public function getContentType(): string;

    /**
     * Sets the http content type.
     *
     * @param string $contentType
     * @return ResponseInterface Return self to be fluent.
     */
    public function setContentType(string $contentType): ResponseInterface;

    /**
     * Gets the body.
     *
     * @return string
     */
    public function getBody(): string;

    /**
     * Add a string to the body.
     *
     * @param string $string String to add.
     * @return ResponseInterface
     */
    public function addToBody(string $string): ResponseInterface;

    /**
     * Reset the response.
     * @param string $contentType
     */
    public function reset(string $contentType = 'text/html;charset=utf-8');
}