<?php
/**
 * Created by PhpStorm.
 * User: ahmetturk
 * Date: 25/06/2017
 * Time: 12:12
 */

namespace Fabs\LINQ\Iterator;

class DistinctIterator extends IteratorBase
{
    public function getIterator()
    {
        $temp = [];
        foreach ($this->before_iterator as $item) {
            if (!in_array($item, $temp, true)) {
                $temp[] = $item;
                yield $item;
            }
        }
    }
}