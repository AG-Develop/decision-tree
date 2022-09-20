<?php

namespace Coff\DecisionTree\Test;

use Coff\DecisionTree\DecisionNode;
use PHPUnit\Framework\TestCase;

class DecisionNodeTest extends TestCase
{
    public function testAssertThrowsException(): void
    {
        $node = new DecisionNode(fn (object $obj) => new \Exception('Message'));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Message');

        $node->assert(new class() {});
    }

    public function testAssertReturnsValue(): void
    {
        $node = new DecisionNode(fn (object $obj) => 'someValue');

        $result = $node->assert(new class() {});

        $this->assertEquals('someValue', $result);
    }

    public function testAssertExecutesCallback(): void
    {
        $node = new DecisionNode(fn (object $obj) => fn () => 'called');

        $result = $node->assert(new class() {});

        $this->assertEquals('called', $result);
    }

    public function testAssertReadsProperty(): void
    {
        $node = new DecisionNode(fn (object $obj) => $obj->a);

        $result = $node->assert(new class() {
            public int $a = 5;
        });

        $this->assertEquals(5, $result);
    }

    public function testAssertChainsAssertions(): void
    {
        $node1 = new DecisionNode(fn (object $obj) => true);
        $node2 = new DecisionNode(fn (object $obj) => $node1);
        $node3 = new DecisionNode(fn (object $obj) => $node2);

        $result = $node3->assert(new class() {});

        $this->assertEquals(true, $result);
    }

    public function testAssertDivergesBetweenBranches(): void
    {
        $node1a = new DecisionNode(fn (object $obj) => true);
        $node1b = new DecisionNode(fn (object $obj) => false);
        $node2 = new DecisionNode(fn (object $obj) => 'yes' === $obj->a ? $node1a : $node1b);
        $node3 = new DecisionNode(fn (object $obj) => $node2);

        $result = $node3->assert(new class() {
            public $a = 'yes';
        });

        $this->assertEquals(true, $result);

        $result = $node3->assert(new class() {
            public $a = 'no';
        });

        $this->assertEquals(false, $result);
    }
}
