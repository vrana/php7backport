<?php

namespace Bouda\Php7Backport\Transformation;

use PhpParser\Node\Stmt\ClassMethod;
use Bouda\Php7Backport\ChangedNode;


class Constructor
{
    /**
     * Rename PHP4-style constructor to __construct.
     *
     * Example: 
     * class foo() { function Foo() {} }
     * becomes
     * class foo() { function __construct() {} }
     *
     * @param PhpParser\Node\Stmt\ClassMethod $node (ClassMethod)
     * @return Bouda\Php7Backport\ChangedNode
     */
    public static function transform(ClassMethod $node)
    {
        $node->name = '__construct';
        $node->setAttribute('changed', true);

        return new ChangedNode($node);
    }


    /**
     * Find end position of function header declaration in original code 
     * and set to node attribute.
     */
    public static function setOriginalEndOfHeaderPosition(ClassMethod $node, array $tokens)
    {
        $currentTokenPosition = $node->getAttribute('startTokenPos');

        $offset = 0;


        // find first occurence of ")" (end of header declaration)
        do
        {
            $currentToken = $tokens[$currentTokenPosition];

            $offset += is_array($currentToken) ? strlen($currentToken[1]) : strlen($currentToken);
            
            $currentTokenPosition++;
        }
        while ($currentToken != ")");


        $endFilePos =  $node->getAttribute('startFilePos') + $offset;

        // lower by 1 to stay consistent with original (wrong) values by parser
        $endFilePos -= 1;

        $node->setAttribute('endFilePos', $endFilePos);
    }
}
