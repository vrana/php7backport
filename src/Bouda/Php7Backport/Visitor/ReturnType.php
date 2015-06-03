<?php

namespace Bouda\Php7Backport\Visitor;

use Bouda\Php7Backport;
use Bouda\Php7Backport\ChangedNode;

use PhpParser\Node;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\ClassMethod;


class ReturnType extends Php7Backport\Visitor
{
    public function leaveNode(Node $node)
    {
        if (($node instanceof Function_ || $node instanceof ClassMethod)
            && isset($node->returnType))
        {
            $changedNode = $this->transform($node);
            $this->setOriginalEndOfFunctionHeaderPosition($node);

            $this->changedNodes->addNode($changedNode);
        }
    }

  
    /**
     * Remove return types from function or method.
     *
     * Example: 
     * function foo() : string {...
     * becomes
     * function foo() {...
     *
     * @param PhpParser\Node\Stmt $node (Function_ or ClassMethod)
     * @return Bouda\Php7Backport\ChangedNode
     */
    private function transform(Stmt $node)
    {
        $node->returnType = null;
        $node->setAttribute('changed', true);

        return new ChangedNode($node);
    }
}