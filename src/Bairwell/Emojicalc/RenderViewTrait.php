<?php
declare (strict_types=1);

namespace Bairwell\Emojicalc;

/**
 * Renders a view file.
 *
 * It's set a trait as I want to be able to reuse it to render parts of the controller.
 *
 * @package Bairwell\Emojicalc
 */
trait RenderViewTrait
{

    /**
     * Holds the cached templates.
     * @var array
     */
    private $cachedTemplates;

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
    protected function renderView(string $fileName, array $parameters = []): string
    {
        $fileName = __DIR__ . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR . $fileName . '.html';
        $real = realpath($fileName);
        if (false === $real) {
            throw new \InvalidArgumentException('File ' . $fileName . ' does not exist');
        }
        if (false === isset($this->cachedTemplates[$real])) {
            $this->cachedTemplates[$real] = file_get_contents($real);
        }
        return strtr($this->cachedTemplates[$real], $parameters);

    }
}