<?php

namespace Bairwell\Emojicalc;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

function realpath($path) {
    if ('vfs://exampleDir/exampleFile.html'===$path) {
        return $path;
    } elseif ('vfs://exampleDir/test.html'===$path) {
        return false;
    } else {
        return \realpath($path);
    }
}

/**
 * Class RenderViewTest
 * @package Bairwell\Emojicalc
 * @coversDefaultClass Bairwell\Emojicalc\RenderView
 */
class RenderViewTest extends TestCase
{

    /**
     * @covers ::__construct
     * @covers ::renderView
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage File vfs://exampleDir/test.html does not exist
     */
    public function testRenderViewNoFile() {
        vfsStream::setup('exampleDir');
        $view=new RenderView(vfsStream::url('exampleDir').'/');
        $view->renderView('test',[]);
    }

    /**
     * @covers ::__construct
     * @covers ::renderView
     */
    public function testRenderView() {
        vfsStream::setup('exampleDir');
        $contents='This is %VALUE% of %THING%';
        $fileName=vfsStream::url('exampleDir/exampleFile.html');
        file_put_contents($fileName,$contents);
        $view=new RenderView(vfsStream::url('exampleDir').DIRECTORY_SEPARATOR);
        $out=$view->renderView('exampleFile',['%VALUE%'=>'an example','%THING%'=>'a rendered view']);
        $this->assertSame('This is an example of a rendered view',$out);
        // ensure it is cached
        $contents='Invalid';
        file_put_contents($fileName,$contents);
        $out=$view->renderView('exampleFile',['%VALUE%'=>'ssss','%THING%'=>'xxx']);
        $this->assertSame('This is ssss of xxx',$out);
    }

}