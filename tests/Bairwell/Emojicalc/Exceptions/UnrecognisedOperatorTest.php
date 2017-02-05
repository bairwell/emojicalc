<?php
namespace Bairwell\Emojicalc\Exceptions;

use PHPUnit\Framework\TestCase;

/**
 * Class UnrecognisedOperatorTest
 * @package Bairwell\Emojicalc\Exceptions
 * @coversDefaultClass Bairwell\Emojicalc\Exceptions\UnrecognisedOperatorTest
 */
class UnrecognisedOperatorTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testException() {
        $sut=new UnrecognisedOperator('test');
        $this->assertInstanceOf('\InvalidArgumentException',$sut);
        $this->assertSame('test',$sut->getMessage());
    }
}