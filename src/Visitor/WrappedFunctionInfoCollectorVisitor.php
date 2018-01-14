<?php
/**
 * Copyright © 2017-2018 Maks Rafalko
 *
 * License: https://opensource.org/licenses/BSD-3-Clause New BSD License
 */

declare(strict_types=1);

namespace Infection\Visitor;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class WrappedFunctionInfoCollectorVisitor extends NodeVisitorAbstract
{
    const IS_INSIDE_FUNCTION_KEY = 'isInsideFunction';
    const FUNCTION_SCOPE_KEY = 'functionScope';

    private $scopeStack = [];

    public function beforeTraverse(array $nodes)
    {
        $this->scopeStack = [];
    }

    public function enterNode(Node $node)
    {
        $isInsideFunction = $this->isInsideFunction($node);

        if ($isInsideFunction) {
            $node->setAttribute(self::IS_INSIDE_FUNCTION_KEY, true);
        }

        if ($this->isFunctionLikeNode($node)) {
            $this->scopeStack[] = $node;
        } elseif ($isInsideFunction) {
            $node->setAttribute(self::FUNCTION_SCOPE_KEY, $this->scopeStack[count($this->scopeStack) - 1]);
        }
    }

    public function leaveNode(Node $node)
    {
        if ($this->isFunctionLikeNode($node)) {
            array_pop($this->scopeStack);
        }
    }

    /**
     * Recursively determine whether the node is inside the function
     *
     * @param Node $node
     *
     * @return bool
     */
    private function isInsideFunction(Node $node): bool
    {
        if (!$node->hasAttribute(ParentConnectorVisitor::PARENT_KEY)) {
            return false;
        }

        $parent = $node->getAttribute(ParentConnectorVisitor::PARENT_KEY);

        if ($parent->getAttribute(self::IS_INSIDE_FUNCTION_KEY)) {
            return true;
        }

        if ($this->isFunctionLikeNode($parent)) {
            return true;
        }

        return $this->isInsideFunction($parent);
    }

    private function isFunctionLikeNode(Node $node): bool
    {
        $isFunction = $node instanceof Node\Stmt\Function_;
        $isClassMethod = $node instanceof Node\Stmt\ClassMethod;
        $isClosure = $node instanceof Node\Expr\Closure;

        return $isFunction || $isClassMethod || $isClosure;
    }
}
