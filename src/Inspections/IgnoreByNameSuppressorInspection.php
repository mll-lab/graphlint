<?php

declare(strict_types=1);

namespace Worksome\Graphlint\Inspections;

use GraphQL\Language\AST\Node;
use Worksome\Graphlint\Contracts\SuppressorInspection;
use Worksome\Graphlint\Utils\NodeNameResolver;

class IgnoreByNameSuppressorInspection implements SuppressorInspection
{
    /**
     * @var string[]
     */
    private array $names = [];

    public function __construct(
        private readonly NodeNameResolver $nameResolver,
    ) {
    }

    public function shouldSuppress(Node $node, array $parents, Inspection $inspection): bool
    {
        $path = [];
        foreach ([...$parents, $node] as $ancestor) {
            $name = $this->nameResolver->getName($ancestor);
            if ($name === null) {
                continue;
            }

            $path[] = $name;
            $fullName = implode('.', $path);
            if (in_array($fullName, $this->names)) {
                return true;
            }
        }

        return false;
    }

    public function configure(string ...$names): void
    {
        $this->names = $names;
    }
}
