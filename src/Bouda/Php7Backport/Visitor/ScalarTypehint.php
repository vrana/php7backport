<?php

namespace Bouda\Php7Backport\Visitor;

use Bouda\Php7Backport;
use Bouda\Php7Backport\ChangedNode;

use PhpParser\Node;
use PhpParser\Node\Param;


/**
 * Remove scalar typehint from function or method parameter.
 *
 * Example: 
 * function foo(string $x, SomeClass $y) {...
 * becomes
 * function foo($x, SomeClass $y) {...
 */
class ScalarTypehint extends Php7Backport\Visitor
{
    public function leaveNode(Node $node)
    {
        if ($node instanceof Param
            && isset($node->type->parts[0]) 
            && in_array($node->type->parts[0], ['int', 'float', 'string', 'bool']))
        {
            $changedNode = $this->transform($node);

            $this->changedNodes->addNode($changedNode);
        }
    }


    private function transform(Param $node)
    {
        $node->type = null;
        $node->setAttribute('changed', true);

        return new ChangedNode($node);
    }
}
