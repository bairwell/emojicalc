<?php
/**
 * Created by PhpStorm.
 * User: Richard Bairwell
 * Date: 05/02/2017
 * Time: 16:52
 */

namespace Bairwell\Emojicalc;


/**
 * A very basic router.
 * @package Bairwell\Emojicalc
 */
interface RouterInterface
{
    /**
     * Router constructor.
     *
     * @param RequestInterface $request The request.
     * @param ResponseInterface $response The response.
     * @param RenderViewInterface $renderView The view rendering system.
     */
    public function __construct(
        RequestInterface $request,
        ResponseInterface $response,
        RenderViewInterface $renderView
    );

    /**
     * Get all current configured routes. Only really useful for testing.
     * @return array
     */
    public function getAllRoutes(): array;

    /**
     * Returns all routes for a method. Only really useful for testing.
     * @param string $method The route we are asking about.
     * @return array
     */
    public function getAllRoutesForMethod(string $method): array;

    /**
     * Register a route.
     *
     * @param string $method The method we will be matching against.
     * @param string $route The URL we we be matching against.
     * @param callable $callable The callable route.
     * @returns RouterInterface To be fluent
     */
    public function registerRoute(string $method, string $route, callable $callable) : RouterInterface;

    /**
     * Checks and returns true if any routes are defined.
     * @return bool
     */
    public function areRoutesDefined(): bool;

    /**
     * Run the routes.
     */
    public function run();

    /**
     * Finds a matching route.
     *
     * @param string $method HTTP Method.
     * @param string $url URL to match against.
     * @param array $matches Returns a list of matches of the path parameter.
     * @return false|callable
     */
    public function findMatchingRoute(string $method,string $url,array &$matches=[]);
}