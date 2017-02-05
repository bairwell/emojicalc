<?php
declare (strict_types=1);

namespace Bairwell\Emojicalc;

/**
 * Class Response.
 *
 * Basic "inspired by" PSR 7 style response object, but massively massively simplified.
 */
class Response implements ResponseInterface
{
    /**
     * Content type.
     *
     * @var string
     */
    public $contentType;
    /**
     * Body text.
     *
     * @var string
     */
    private $body;

    /**
     * Response constructor.
     * @param string $contentType The content type we are returning.
     */
    public function __construct(string $contentType = 'text/html;charset=utf-8')
    {
        $this->reset($contentType);
    }

    /**
     * Reset the response.
     * @param string $contentType
     */
    public function reset(string $contentType = 'text/html;charset=utf-8')
    {
        $this->body = '';
        $this->setContentType($contentType);
    }

    /**
     * Gets the content type.
     * @return string
     */
    public function getContentType(): string
    {
        return $this->contentType;
    }

    /**
     * Sets the http content type.
     *
     * @param string $contentType
     * @return ResponseInterface Return self to be fluent.
     */
    public function setContentType(string $contentType): ResponseInterface
    {
        $this->contentType = $contentType;
        return $this;
    }

    /**
     * Gets the body.
     *
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * Add a string to the body.
     *
     * @param string $string String to add.
     * @return ResponseInterface To be fluent.
     */
    public function addToBody(string $string): ResponseInterface
    {
        $this->body .= $string;
        return $this;
    }
}