<?php

declare(strict_types=1);

namespace Worksome\Graphlint\Inspections;

use GraphQL\Language\AST\Node;
use GraphQL\Language\AST\NodeList;
use Worksome\Graphlint\Contracts\SuppressorInspection;
use Worksome\Graphlint\Utils\NodeNameResolver;

class IgnoreByNameSuppressorInspection implements SuppressorInspection
{
    private array $names = [];

    public function __construct(
        private readonly NodeNameResolver $nameResolver,
    ) {
    }

    public function shouldSuppress(Node $node, array $parents, Inspection $inspection): bool
    {
        $name = $this->nameResolver->getName($node);
        $parentName = $this->nameResolver->getName(last($parents));

        if ($name === null) {
            return false;
        }

        // Check if name in names
        if (in_array($name, $this->names)) {
            return true;
        }

        // Check if name dotted with parents in names
        if (in_array("$parentName.$name", $this->names)) {
            return true;
        }

        return false;
    }

    public function configure(string... $names): void
    {
        $this->names = $names;
    }
}