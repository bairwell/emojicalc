<?php


namespace Bairwell\Emojicalc\Entities\Operator;

use Bairwell\Emojicalc\Entities\Operator;
use Bairwell\Emojicalc\Entities\Symbol;
use PHPUnit\Framework\TestCase;

/**
 * Class DivisionTest
 * @package Bairwell\Emojicalc\Entities\Operator
 * @coversDefaultClass Bairwell\Emojicalc\Entities\Operator\Division
 * @uses \Bairwell\Emojicalc\Entities\Operator
 */
class DivisionTest extends TestCase
{

    /**
     * @coversNothing
     * @return Operator
     */
    private function getSut() : Operator {
        $symbol=$this->getMockBuilder(Symbol::class)->disableOriginalConstructor()->getMock();
        $symbol->expects($this->any())->method('getSymbolCode')->willReturn('firstSymbol');
        $symbol->expects($this->any())->method('getSymbolName')->willReturn('firstName');
        $sut=new Division($symbol);
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
        $sut=new Division($symbol);
        $this->assertSame($symbol,$sut->getSymbol());
    }

    /**
     * @covers ::getOperatorType
     */
    public function testType()
    {
        $this->assertEquals('/',$this->getSut()->getOperatorType());
    }

    /**
     * @covers ::getOperatorName
     */
    public function testName()
    {
        $this->assertEquals('division',$this->getSut()->getOperatorName());
    }

    /**
     * @covers ::performCalculation
     */
    public function testPerformCalculation()
    {
        $this->assertSame((float)10,$this->getSut()->performCalculation(100,10));
        $this->assertSame((float)0.1,$this->getSut()->performCalculation(10,100));
    }
    /**
     * @covers ::performCalculation
     * @expectedException \DivisionByZeroError
     * @expectedExceptionMessage Cannot divide by zero
     */
    public function testDivisionByZero() {
        $sut=$this->getSut();
        $sut->performCalculation(100,0);
    }
}
