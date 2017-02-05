<?php


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
     * @param array $environment Allow override $_SERVER settings for testing.
     * @param RenderViewInterface $renderView The view rendering system.
     */
    public function __construct(array $environment = [], RenderViewInterface $renderView);

    /**
     * Register a route.
     *
     * @param string $method The method we will be matching against.
     * @param string $route The URL we we be matching against.
     * @param callable $callable The callable route.
     */
    public function registerRoute(string $method, string $route, callable $callable);

    /**
     * Run the routes.
     */
    public function run();

    /**
     * Checks and returns true if any routes are defined.
     * @return bool
     */
    public function areRoutesDefined(): bool;
}