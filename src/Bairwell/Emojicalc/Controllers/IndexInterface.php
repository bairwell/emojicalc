<?php

namespace Bairwell\Emojicalc\Controllers;

use Bairwell\Emojicalc\RequestInterface;
use Bairwell\Emojicalc\ResponseInterface;


/**
 * Class Index Controller.
 *
 * Main index system.
 *
 * @package Bairwell\Emojicalc\Controllers
 */
interface IndexInterface extends ControllerInterface
{
    /**
     * Start/homepage action.
     *
     * Just a public "proxy" for the renderStartPage function.
     *
     * @param RequestInterface $request The request object (unused here).
     * @param ResponseInterface $response The response object for populating.
     * @throws \Exception If the views do not exist.
     * @return ResponseInterface For chaining.
     */
    public function startAction(RequestInterface $request, ResponseInterface $response): ResponseInterface;

    /**
     * Do the calculations.
     *
     * @param RequestInterface $request The inbound request object.
     * @param ResponseInterface $response The response object.
     * @return ResponseInterface Populated response object.
     */
    public function calculateAction(RequestInterface $request, ResponseInterface $response): ResponseInterface;
}