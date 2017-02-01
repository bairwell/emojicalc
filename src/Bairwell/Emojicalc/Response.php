<?php
declare (strict_types = 1);
namespace Bairwell\Emojicalc;
/**
 * Class Response.
 *
 * Basic "inspired by" PSR 7 style response object, but massively massively simplified.
 */
class Response {
    /**
     * Body text.
     *
     * @var string
     */
    private $body;

    /**
     * Content type.
     *
     * @var string
     */
    public $contentType;

    public function __construct(string $contentType='text/html;charset=utf-8') {
        $this->body='';
        $this->setContentType($contentType);
    }

    /**
     * Sets the http content type.
     *
     * @param string $contentType
     * @return Response Return self to be fluent.
     */
    public function setContentType(string $contentType) : self {
        $this->contentType=$contentType;
        return $this;
    }

    /**
     * Gets the content type.
     * @return string
     */
    public function getContentType() : string {
        return $this->contentType;
    }

    /**
     * Gets the body.
     *
     * @return string
     */
    public function getBody() : string {
        return $this->body;
    }

    /**
     * Add a string to the body.
     *
     * @param string $string String to add.
     * @return self To be fluent.
     */
    public function addToBody(string $string) : self
    {
        $this->body .= $string;
        return $this;
    }
}