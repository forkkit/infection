<?php
/**
 * Copyright © 2017-2018 Maks Rafalko
 *
 * License: https://opensource.org/licenses/BSD-3-Clause New BSD License
 */

declare(strict_types=1);

namespace Infection\Mutator\ConditionalBoundary;

use Infection\Mutator\FunctionBodyMutator;
use PhpParser\Node;

class LessThan extends FunctionBodyMutator
{
    /**
     * Replaces "<" with "<="
     *
     * @param Node $node
     *
     * @return Node\Expr\BinaryOp\SmallerOrEqual
     */
    public function mutate(Node $node)
    {
        return new Node\Expr\BinaryOp\SmallerOrEqual($node->left, $node->right, $node->getAttributes());
    }

    public function shouldMutate(Node $node): bool
    {
        return $node instanceof Node\Expr\BinaryOp\Smaller;
    }
}
