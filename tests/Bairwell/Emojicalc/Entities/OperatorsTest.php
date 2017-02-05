<?php
namespace Bairwell\Emojicalc\Entities;

use Bairwell\Emojicalc\Entities\Symbol;

use Bairwell\Emojicalc\Exceptions\UnrecognisedOperator;
use PHPUnit\Framework\TestCase;

/**
 * Class OperatorsTest
 * @package Bairwell\Emojicalc\Entities
 * @coversDefaultClass Bairwell\Emojicalc\Entities\Operators
 * @uses \Bairwell\Emojicalc\Entities\Operator
 * @uses \Bairwell\Emojicalc\Entities\Operators
 */
class OperatorsTest extends TestCase
{
    /**
     * @coversNothing
     */
    protected function getFirstOperator() : Operator {
        $firstSymbol=$this->getMockBuilder(Symbol::class)->disableOriginalConstructor()->getMock();
        $firstSymbol->expects($this->any())->method('getSymbolCode')->willReturn('firstSymbol');
        $firstSymbol->expects($this->any())->method('getSymbolName')->willReturn('firstName');

        $first=$this->getMockBuilder(Operator::class)->setConstructorArgs([$firstSymbol])->getMock();
        $first->expects($this->any())->method('getOperatorType')->willReturn('plus');
        $first->expects($this->any())->method('getOperatorName')->willReturn('addition');
        $first->expects($this->any())->method('performCalculation')->will($this->returnCallback(function ($a,$b) { return $a+$b; }));
        return $first;
    }
    /**
     * @coversNothing
     */
    protected function getSecondOperator() : Operator {
        $secondSymbol=$this->getMockBuilder(Symbol::class)->disableOriginalConstructor()->getMock();
        $secondSymbol->expects($this->any())->method('getSymbolCode')->willReturn('secondSymbol');
        $secondSymbol->expects($this->any())->method('getSymbolName')->willReturn('secondName');

        $second=$this->getMockBuilder(Operator::class)->setConstructorArgs([$secondSymbol])->getMock();
        $second->expects($this->any())->method('getOperatorType')->willReturn('minus');
        $second->expects($this->any())->method('getOperatorName')->willReturn('subtraction');
        $second->expects($this->any())->method('performCalculation')->will($this->returnCallback(function ($a,$b) { return $a-$b; }));
        return $second;
    }
    /**
     * @covers ::__construct
     * @covers ::addOperator
     */
    public function testAddOperator() {
        $sut=new Operators();
        $first=$this->getFirstOperator();
        $second=$this->getSecondOperator();
        $this->assertSame($sut,$sut->addOperator($first));
        $this->assertSame($sut,$sut->addOperator($second));
        try {
            $this->assertSame($sut,$sut->addOperator($first));
            $this->fail('Did not throw duplicated operator error');
        } catch (\InvalidArgumentException $e) {
            $this->assertSame('Duplicated operator',$e->getMessage());
        }
    }
    /**
     * @covers ::__construct
     * @covers ::current
     * @covers ::next
     * @covers ::key
     * @covers ::valid
     * @covers ::rewind
     */
    public function testEmptyIteration() {
        $sut=new Operators();
        $this->assertInstanceOf('\Iterator',$sut);
        $this->assertSame(0,$sut->key());
        $this->assertSame(false,$sut->valid());
        $sut->next();
        $this->assertSame(1,$sut->key());
        $this->assertSame(false,$sut->valid());
        $sut->next();
        $sut->next();
        $this->assertSame(3,$sut->key());
        $this->assertSame(false,$sut->valid());
        $sut->rewind();
        $this->assertSame(0,$sut->key());
        $this->assertSame(false,$sut->valid());
    }
    /**
     * @covers ::__construct
     * @covers ::addOperator
     * @covers ::current
     * @covers ::next
     * @covers ::key
     * @covers ::valid
     * @covers ::rewind
     */
    public function testIteration()
    {
        $sut = new Operators();
        $this->assertInstanceOf('\Iterator', $sut);
        // check empty by default
        $this->assertSame(0, $sut->key());
        $this->assertSame(false, $sut->valid());
        // add
        $first = $this->getFirstOperator();
        $sut->addOperator($first);
        // check
        $this->assertSame(0, $sut->key());
        $this->assertSame(true, $sut->valid());
        $this->assertSame($first, $sut->current());
        // check next is empty
        $sut->next();
        $this->assertSame(1, $sut->key());
        $this->assertSame(false, $sut->valid());
        // check rewind worked
        $sut->rewind();
        $this->assertSame(0, $sut->key());
        $this->assertSame(true, $sut->valid());
        $this->assertSame($first, $sut->current());
        // add next
        $second = $this->getSecondOperator();
        $sut->addOperator($second);
        $sut->rewind();
        $this->assertSame(0, $sut->key());
        $this->assertSame(true, $sut->valid());
        $this->assertSame($first, $sut->current());
        $sut->next();
        $this->assertSame(1, $sut->key());
        $this->assertSame(true, $sut->valid());
        $this->assertSame($second, $sut->current());
        // rewind and check again
        $sut->rewind();
        $this->assertSame(0, $sut->key());
        $this->assertSame(true, $sut->valid());
        $this->assertSame($first, $sut->current());
        $sut->next();
        $this->assertSame(1, $sut->key());
        $this->assertSame(true, $sut->valid());
        $this->assertSame($second, $sut->current());
    }

    /**
     * @covers ::findOperatorByType
     * @use UnrecognisedOperator
     * @expectedException \Bairwell\Emojicalc\Exceptions\UnrecognisedOperator
     * @expectedExceptionMessage dummy
     */
    public function testFindOperatorByType() {
        $sut=new Operators();
        $first=$this->getFirstOperator();
        $second=$this->getSecondOperator();
        $sut->addOperator($first)->addOperator($second);
        $this->assertSame($first,$sut->findOperatorByType($first->getOperatorType()));
        $this->assertSame($second,$sut->findOperatorByType($second->getOperatorType()));
        $this->assertSame($first,$sut->findOperatorByType($first->getOperatorType()));
        $sut->findOperatorByType('dummy');
    }
    /**
     * @covers ::findOperatorBySymbol
     * @use UnrecognisedOperator
     * @expectedException \Bairwell\Emojicalc\Exceptions\UnrecognisedOperator
     * @expectedExceptionMessage dummy
     */
    public function testFindOperatorBySymbol() {
        $sut=new Operators();
        $first=$this->getFirstOperator();
        $second=$this->getSecondOperator();
        $sut->addOperator($first)->addOperator($second);
        $this->assertSame($first,$sut->findOperatorBySymbol($first->getSymbol()->getSymbolCode()));
        $this->assertSame($second,$sut->findOperatorBySymbol($second->getSymbol()->getSymbolCode()));
        $this->assertSame($first,$sut->findOperatorBySymbol($first->getSymbol()->getSymbolCode()));
        $sut->findOperatorBySymbol('dummy');
    }
}
