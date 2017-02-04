<?php
declare (strict_types=1);

namespace Bairwell\Emojicalc;

/**
 * Renders a view file.
 *
 * @package Bairwell\Emojicalc
 */
class RenderView implements RenderViewInterface
{

    /**
     * Holds the cached templates.
     * @var array
     */
    private $cachedTemplates;

    /**
     * Location of the views files.
     * @var string
     */
    private $viewsLocation;

    /**
     * RenderView constructor.
     * @param string $viewsLocation
     */
    public function __construct(string $viewsLocation)
    {
        $this->viewsLocation = $viewsLocation;
    }

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
    public function renderView(string $fileName, array $parameters = []): string
    {
        $fileName = $this->viewsLocation . $fileName . '.html';
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