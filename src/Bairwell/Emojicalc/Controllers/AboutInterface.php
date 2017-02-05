<?php
/**
 * Created by PhpStorm.
 * User: Richard Bairwell
 * Date: 05/02/2017
 * Time: 18:09
 */

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
interface AboutInterface extends ControllerInterface
{
    /**
     * Constructor.
     * @param RenderViewInterface $renderView The view rendering system.
     */
    public function __construct(RenderViewInterface $renderView);

    /**
     * Author
     *
     * @param RequestInterface $request The request object (unused here).
     * @param ResponseInterface $response The response object for populating.
     * @throws \Exception If the views do not exist.
     * @return ResponseInterface For chaining.
     */
    public function authorAction(RequestInterface $request, ResponseInterface $response): ResponseInterface;

    /**
     * Specification
     *
     * @param RequestInterface $request The request object (unused here).
     * @param ResponseInterface $response The response object for populating.
     * @throws \Exception If the views do not exist.
     * @return ResponseInterface For chaining.
     */
    public function specificationAction(RequestInterface $request, ResponseInterface $response): ResponseInterface;

    /**
     * Licence
     *
     * @param RequestInterface $request The request object (unused here).
     * @param ResponseInterface $response The response object for populating.
     * @throws \Exception If the views do not exist.
     * @return ResponseInterface For chaining.
     */
    public function licenceAction(RequestInterface $request, ResponseInterface $response): ResponseInterface;
}