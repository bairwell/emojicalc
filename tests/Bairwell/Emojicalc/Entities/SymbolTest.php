<?php
namespace Bairwell\Emojicalc\Entities;

use Bairwell\Emojicalc\Entities\Symbol;
use PHPUnit\Framework\TestCase;

/**
 * Class SymbolTest
 * @package Bairwell\Emojicalc\Entities
 * @coversDefaultClass Bairwell\Emojicalc\Entities\Symbol
 */
class SymbolTest extends TestCase
{

    /**
     * @covers ::__construct
     * @covers ::getSymbolCode
     * @covers ::getSymbolName
     */
    public function testAll() {
        $sut=new Symbol('abc','def','ghi');
        $this->assertSame('abc',$sut->getSymbolCode());
        $this->assertSame('def',$sut->getSymbolName());
    }

}
