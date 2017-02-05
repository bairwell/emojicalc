<?php


namespace Bairwell\Emojicalc\Entities\Operator;

use Bairwell\Emojicalc\Entities\Operator;
use Bairwell\Emojicalc\Entities\Symbol;
use PHPUnit\Framework\TestCase;

/**
 * Class MultiplyTest
 * @package Bairwell\Emojicalc\Entities\Operator
 * @coversDefaultClass Bairwell\Emojicalc\Entities\Operator\Multiply
 * @uses \Bairwell\Emojicalc\Entities\Operator
 */
class MultiplyTest extends TestCase
{

    /**
     * @coversNothing
     * @return Operator
     */
    private function getSut() : Operator {
        $symbol=$this->getMockBuilder(Symbol::class)->disableOriginalConstructor()->getMock();
        $symbol->expects($this->any())->method('getSymbolCode')->willReturn('firstSymbol');
        $symbol->expects($this->any())->method('getSymbolName')->willReturn('firstName');
        $sut=new Multiply($symbol);
        return $sut;
    }

    /**
     * @covers ::getSymbol
     * @covers ::__construct
     */
    public function testGetSymbol() {
        $symbol=$this->getMockBuilder(Symbol::class)->disableOriginalConstructor()->getMock();
        $symbol->expects($this->any())->method('getSymbolCode')->willReturn('firstSymbol');
        $symbol->expects($this->any())->method('getSymbolName')->willReturn('firstName');
        $sut=new Multiply($symbol);
        $this->assertSame($symbol,$sut->getSymbol());
    }

    /**
     * @covers ::getOperatorType
     */
    public function testType()
    {
        $this->assertEquals('*',$this->getSut()->getOperatorType());
    }

    /**
     * @covers ::getOperatorName
     */
    public function testName()
    {
        $this->assertEquals('multiply',$this->getSut()->getOperatorName());
    }

    /**
     * @covers ::performCalculation
     */
    public function testPerformCalculation()
    {
        $this->assertSame((float)18,$this->getSut()->performCalculation(2,9));
        $this->assertSame((float)18,$this->getSut()->performCalculation(9,2));
    }
}
