<?php


namespace Bairwell\Emojicalc\Entities\Operator;

use Bairwell\Emojicalc\Entities\Operator;
use Bairwell\Emojicalc\Entities\Symbol;
use PHPUnit\Framework\TestCase;

/**
 * Class AdditionTest
 * @package Bairwell\Emojicalc\Entities\Operator
 * @coversDefaultClass Bairwell\Emojicalc\Entities\Operator\Addition
 * @uses \Bairwell\Emojicalc\Entities\Operator
 */
class AdditionTest extends TestCase
{

    /**
     * @coversNothing
     * @return Operator
     */
    private function getSut() : Operator {
        $symbol=$this->getMockBuilder(Symbol::class)->disableOriginalConstructor()->getMock();
        $symbol->expects($this->any())->method('getSymbolCode')->willReturn('firstSymbol');
        $symbol->expects($this->any())->method('getSymbolName')->willReturn('firstName');
        $sut=new Addition($symbol);
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
        $sut=new Addition($symbol);
        $this->assertSame($symbol,$sut->getSymbol());
    }

    /**
     * @covers ::getOperatorType
     */
    public function testType()
    {
        $this->assertEquals('+',$this->getSut()->getOperatorType());
    }

    /**
     * @covers ::getOperatorName
     */
    public function testName()
    {
        $this->assertEquals('addition',$this->getSut()->getOperatorName());
    }

    /**
     * @covers ::performCalculation
     */
    public function testPerformCalculation()
    {
        $this->assertSame((float)26,$this->getSut()->performCalculation(11,15));
        $this->assertSame((float)26,$this->getSut()->performCalculation(15,11));
    }
}
