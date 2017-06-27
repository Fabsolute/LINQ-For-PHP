<?php
/**
 * Created by PhpStorm.
 * User: ahmetturk
 * Date: 25/06/2017
 * Time: 13:37
 */

namespace Fabs\LINQ\Iterator;


class IntersectIterator extends IteratorBase
{
    private $intersect_iterator = null;

    /**
     * @param \Traversable|array $intersect_iterator
     */
    public function setIntersectIterator($intersect_iterator)
    {
        $this->intersect_iterator = $intersect_iterator;
    }

    public function getIterator()
    {
        $except_iterator = $this->intersect_iterator;
        if (!is_array($except_iterator)) {
            $except_iterator = iterator_to_array($except_iterator, false);
        }

        foreach ($this->before_iterator as $item) {
            if (in_array($item, $except_iterator, true)) {
                yield $item;
            }
        }
    }
}