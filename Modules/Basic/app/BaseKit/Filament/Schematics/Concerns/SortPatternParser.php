<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          8/5/25, 2:59 AM
 */

namespace Modules\Basic\BaseKit\Filament\Schematics\Concerns;


use Dom\Attr;
use Exception;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Section;
use Illuminate\Support\Arr;

trait SortPatternParser
{
    private bool $disable_validate_column_count = false;

    protected function map(string $pattern): array
    {

        foreach ($this->attributes as $attribute => $component) {
            if (isset($this->visible_attributes[$attribute])) {
                $this->visible_attributes[$attribute]=$component;
            }
        }

        $index = 0; // To track consumed attributes

        $tokens = $this->tokenize($pattern);
        $ast = $this->parseExpression($tokens);

        return $this->evaluate($ast, $this->visible_attributes, $index);
    }

    public function disableValidateColumnCount($condition = true): self
    {
        $this->disable_validate_column_count = $condition;
        return $this;
    }

    protected function tokenize(string $pattern): array
    {
        $pattern = preg_replace('/\s+/', '', $pattern);

        $tokens = [];
        $length = strlen($pattern);
        $i = 0;

        while ($i < $length) {
            $char = $pattern[$i];

            if (in_array($char, ['(', ')', '+', ','])) {
                $tokens[] = $char;
                $i++;
                continue;
            }

            if (preg_match('/[a-z]/', $char)) {
                // Read a full component token which may include '.', '>', '-', ':' and digits
                $token = $char;
                $i++;

                while ($i < $length) {
                    $next = $pattern[$i];
                    if (in_array($next, ['(', ')', '+', ','])) {
                        break;
                    }

                    if (preg_match('/[a-z0-9>\.:\-]/', $next)) {
                        $token .= $next;
                        $i++;
                        continue;
                    }

                    throw new Exception("Invalid character '$next' in pattern.");
                }

                // Basic sanity check for tokens like "g." with no digits after the dot
                if (preg_match('/^[a-z]\.$/', $token)) {
                    throw new Exception(strtoupper($token[0]) . ' parameter has no column count specified');
                }

                $tokens[] = $token;
                continue;
            }

            throw new Exception("Invalid character '$char' in pattern.");
        }

        return $tokens;
    }

    protected function parseExpression(array &$tokens): array
    {
        $nodes = [];
        while (!empty($tokens)) {
            $token = array_shift($tokens);

            if ($token === ')') {
                break;
            }

            if ($token === '(') {
                $nodes[] = $this->parseExpression($tokens);
            } elseif (in_array($token, ['+', ','])) {
                $nodes[] = $token;
            } else {
                $nodes[] = $token;
            }
        }

        return $this->groupComponents($nodes);
    }

    protected function groupComponents(array $nodes): array
    {
        $group = [];
        $current = [];

        foreach ($nodes as $node) {
            if ($node === '+') {
                continue; // '+' is implicit nesting
            } elseif ($node === ',') {
                $group[] = $current;
                $current = [];
            } else {
                $current[] = $node;
            }
        }

        if (!empty($current)) {
            $group[] = $current;
        }

        return count($group) === 1 ? $group[0] : $group;
    }

    protected function evaluate($node, array &$attributes, int &$index)
    {
        if (is_string($node)) {
            return [$this->buildComponent($node, $attributes, $index)];
        }

        if (is_array($node) && $this->isFlat($node)) {
            // Nesting like [c, g.2, g.3]
            $components = [];
            foreach ($node as $child) {
                $components[] = $this->evaluate($child, $attributes, $index)[0];
            }

            // Wrap in outer container if first is c or s with children
            $first = $node[0];

//            if ($node=='f'){
//                return  $this->attributes;
//            }
            if (is_string($first) && (str_starts_with($first, 'c') || str_starts_with($first, 's'))) {
                return [$this->wrapContainer($first, $components)];
            }

            return $components;
        }

        if (is_array($node)) {
            $results = [];
            foreach ($node as $group) {
                $results[] = $this->evaluate($group, $attributes, $index)[0];
            }
            return $results;
        }

        throw new Exception('Unknown node type.');
    }

    protected function isFlat(array $arr): bool
    {
        return count(array_filter($arr, 'is_array')) === 0;
    }

    protected function buildComponent(string $token, array &$attributes, int &$index)
    {
//        if ($token=='f'){
//            return  $this->attributes;
//        }
        // New pattern: g.[count]-[columns]:[span1].[span2]...[spanN]
        if (preg_match('/^g\.(\d+)-(\d+):((?:\d+)(?:\.\d+)*)$/', $token, $m)) {
            $itemCount = (int)$m[1];
            $columns = (int)$m[2];
            $spans = array_map('intval', explode('.', $m[3]));

            if (count($spans) !== $itemCount) {
                throw new Exception(
                    "Grid pattern '$token' has " . count($spans) . " spans but item count is $itemCount."
                );
            }

            $sum = array_sum($spans);
            if ($sum !== $columns && !$this->disable_validate_column_count) {
                throw new Exception("Grid pattern '$token' span sum ($sum) must equal declared columns ($columns).");
            }

            $attrs = $this->consumeAttributes($attributes, $index, $itemCount);
            $mapped = $this->applyColumnSpans($attrs, $spans);

            return Grid::make($columns)->schema($mapped);
        }

        // Existing pattern: g.[count]
        if (preg_match('/^g\.(\d+)$/', $token, $m)) {
            $count = (int)$m[1];
            $attrs = $this->consumeAttributes($attributes, $index, $count);
            return Grid::make($count)->schema($attrs);
        }

        if (preg_match('/^(c|s)>(\d+)$/', $token, $m)) {
            [$type, $count] = [$m[1], (int)$m[2]];
            $attrs = $this->consumeAttributes($attributes, $index, $count);
            return $this->makeContainer($type, $attrs);
        }

        if (in_array($token, ['c', 's'])) {
            $attrs = array_slice($attributes, $index);
            $index = count($attributes);
            return $this->makeContainer($token, $attrs);
        }


        throw new Exception("Unknown component token: $token");
    }

    protected function wrapContainer(string $type, array $children)
    {
        if (str_starts_with($type, 'c')) {
            return Card::make($children);
        }

        if (str_starts_with($type, 's')) {
            return Section::make($children);
        }

        throw new Exception("Cannot wrap unknown container: $type");
    }

    protected function makeContainer(string $type, array $children)
    {
        return match ($type) {
            'c' => Card::make($children),
            's' => Section::make($children),
            default => throw new Exception("Unknown container type: $type"),
        };
    }

    protected function consumeAttributes(array $attributes, int &$index, int $count): array
    {
        if ($index + $count > count($attributes)) {
            throw new Exception('Not enough attributes available for component.');
        }

        $slice = array_slice($attributes, $index, $count);
        $index += $count;
        return $slice;
    }

    protected function applyColumnSpans(array $attributes, array $spans): array
    {
        $result = [];
        $index = 0;
        foreach ($attributes as $i => $component) {
            $span = $spans[$index] ?? null;
            $index = $index + 1;
            if ($span === null) {
                $result[] = $component;
                continue;
            }

            if (method_exists($component, 'columnSpan')) {
                $result[] = $component->columnSpan($span);
                continue;
            }

            throw new Exception('Provided component does not support columnSpan method.');
        }

        return $result;
    }
}
