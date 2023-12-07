<?php
/**
 * @package     PIM
 *
 * @author      Pieter-Jan de Vries/Obix webtechniek <pieter@obix.nl>
 * @copyright   Copyright (C) 2023+ Obix webtechniek. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://www.obix.nl
 */

class TodoItem
{
    public readonly string $title;

    public function __construct(string $title)
    {
        $this->title = $title;
    }
}

class TodoList implements \Iterator
{
    private array $items = [];

    private int $position = 0;

    public function __construct(TodoItem ...$items)
    {
        $this->items = [...$items];
    }

    public function add(TodoItem ...$items): static
    {
        $this->items = [...$this->items, ...$items];

        return $this;
    }

    public function current(): mixed
    {
        return $this->items[$this->position];
    }

    public function next(): void
    {
        ++$this->position;
    }

    public function key(): int
    {
        return $this->position;
    }

    public function valid(): bool
    {
        return isset($this->items[$this->position]);
    }

    public function rewind(): void
    {
        $this->position = 0;
    }
}

$good = [
    new \TodoItem('Foo'),
    new \TodoItem('Bar'),
    new \TodoItem('Baz'),
];

$bad = [
    new \TodoItem('FooBar'),
    new \stdClass(),
];

$list = new \TodoList();

$list->add(...$good);
foreach ($list as $item) {
    printf("%s\n", $item->title);
}
$list->add(...$bad);
