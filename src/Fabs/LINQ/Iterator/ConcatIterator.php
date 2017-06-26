<?php
/**
 * Created by PhpStorm.
 * User: ahmetturk
 * Date: 25/06/2017
 * Time: 12:40
 */

namespace Fabs\LINQ\Iterator;


class ConcatIterator extends IteratorBase
{
    private $others = [];

    /**
     * @param array|\Traversable $iterator
     */
    public function addIterator($iterator)
    {
        $this->others[] = $iterator;
    }

    public function getIterator()
    {
        foreach ($this->before_iterator as $item) {
            yield $item;
        }

        foreach ($this->others as $other_iterator) {
            foreach ($other_iterator as $item) {
                yield $item;
            }
        }
    }
}