<?php
declare (strict_types = 1);
namespace Bairwell\Emojicalc;

/**
 * Renders a view file.
 *
 * It's set a trait as I want to be able to reuse it to render parts of the controller.
 *
 * @package Bairwell\Emojicalc
 */
trait RenderViewTrait {

    private $cachedTemplates;

    /**
     * Renders a view file.
     *
     * @param string $fileName   File name to render.
     * @param array  $parameters Template parameters to substitute in.
     *
     * @return string
     *
     * @throws \Exception If file does not exist.
     */
    protected function renderView(string $fileName, array $parameters = []) : string
    {
        $fileName=__DIR__.DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.$fileName.'.html';
        $real=realpath($fileName);
        if (false===$real) {
            throw new \Exception('File '.$fileName.' does not exist');
        }
        if (false===isset($this->cachedTemplates[$real])) {
            $this->cachedTemplates[$real]=file_get_contents($real);
        }
        $page = strtr($this->cachedTemplates[$real], $parameters);

        return $page;
    }//end renderView()
}