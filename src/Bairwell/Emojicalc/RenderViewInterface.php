<?php
/**
 * Created by PhpStorm.
 * User: Richard Bairwell
 * Date: 04/02/2017
 * Time: 14:47
 */

namespace Bairwell\Emojicalc;


/**
 * Renders a view file.
 *
 * @package Bairwell\Emojicalc
 */
interface RenderViewInterface
{
    /**
     * RenderView constructor.
     * @param string $viewsLocation
     */
    public function __construct(string $viewsLocation);

    /**
     * Renders a view file.
     *
     * @param string $fileName File name to render.
     * @param array $parameters Template parameters to substitute in.
     *
     * @return string
     *
     * @throws \InvalidArgumentException If file does not exist.
     */
    public function renderView(string $fileName, array $parameters = []): string;
}