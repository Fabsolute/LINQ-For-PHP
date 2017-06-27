<?php
/**
 * Created by PhpStorm.
 * User: ahmetturk
 * Date: 25/06/2017
 * Time: 13:37
 */

namespace Fabs\LINQ\Iterator;


class ExceptIterator extends IteratorBase
{
    private $except_iterator = null;

    /**
     * @param \Traversable|array $except_iterator
     */
    public function setExceptIterator($except_iterator)
    {
        $this->except_iterator = $except_iterator;
    }

    public function getIterator()
    {
        $except_iterator = $this->except_iterator;
        if (!is_array($except_iterator)) {
            $except_iterator = iterator_to_array($except_iterator, false);
        }

        foreach ($this->before_iterator as $item) {
            if (!in_array($item, $except_iterator, true)) {
                yield $item;
            }
        }
    }
}