<?php


namespace EzSystems\Behat\Test\MinkElementDecorator;

use Countable;
use Iterator;

class NodeElementCollection implements Iterator, Countable
{
    /** @var NodeElement[] */
    private $elements;

    private $index;

    public function __construct($elements)
    {
        $this->elements = $elements;
        $this->index = 0;
    }

    public function current()
    {
        return $this->elements[$this->index];
    }

    public function next()
    {
        $this->index++;
    }


    public function key()
    {
        return $this->index;
    }

    public function valid()
    {
        return isset($this->elements[$this->key()]);
    }

    /**
     * Rewind the Iterator to the first element
     * @link https://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind()
    {
        $this->index = 0;
    }

    public function getVisibleElements(): NodeElementCollection
    {
        $visibleElements =  array_filter($this->elements, function (NodeElement $element) { return $element->isVisible(); });

        return new NodeElementCollection($visibleElements);
    }

    public function getIndexForElementWithText(string $text): int
    {
        $counter = 0;
        foreach ($this->elements as $element) {
            if ($element->getText() === $text) {
                return $counter;
            }
            $counter++;
        }

        return 0;
    }

    public function count(): int
    {
        return count($this->elements);
    }
}