<?php

namespace Bairwell\Emojicalc\Controllers;

use Bairwell\Emojicalc\RenderViewInterface;
use Bairwell\Emojicalc\RequestInterface;
use Bairwell\Emojicalc\ResponseInterface;

/**
 * Class About Controller.
 *
 * Handles the about pages
 *
 * @package Bairwell\Emojicalc\Controllers
 */
class About implements AboutInterface
{
    /**
     * Render view
     * @var RenderViewInterface
     */
    protected $renderView;
    /**
     * Constructor.
     * @param RenderViewInterface $renderView The view rendering system.
     */
    public function __construct(RenderViewInterface $renderView)
    {
        $this->renderView = $renderView;
    }

    /**
     * Author
     *
     * @param RequestInterface $request The request object (unused here).
     * @param ResponseInterface $response The response object for populating.
     * @throws \Exception If the views do not exist.
     * @return ResponseInterface For chaining.
     */
    public function authorAction(RequestInterface $request, ResponseInterface $response): ResponseInterface {
        $response->addToBody($this->renderView->renderView('author'));
        return $response;
    }

    /**
     * Specification
     *
     * @param RequestInterface $request The request object (unused here).
     * @param ResponseInterface $response The response object for populating.
     * @throws \Exception If the views do not exist.
     * @return ResponseInterface For chaining.
     */
    public function specificationAction(RequestInterface $request, ResponseInterface $response): ResponseInterface {
        $response->addToBody($this->renderView->renderView('specification'));
        return $response;
    }

    /**
     * Licence
     *
     * @param RequestInterface $request The request object (unused here).
     * @param ResponseInterface $response The response object for populating.
     * @throws \Exception If the views do not exist.
     * @return ResponseInterface For chaining.
     */
    public function licenceAction(RequestInterface $request, ResponseInterface $response): ResponseInterface {
        $response->addToBody($this->renderView->renderView('licence'));
        return $response;
    }
}
