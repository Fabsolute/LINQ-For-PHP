<?php
/**
 * Created by PhpStorm.
 * User: ahmetturk
 * Date: 25/06/2017
 * Time: 13:57
 */

namespace Fabs\LINQ\Iterator;


class SelectManyIterator extends IteratorBase
{
    public function getIterator()
    {
        foreach ($this->before_iterator as $second_iterator) {
            foreach ($second_iterator as $item) {
                yield $item;
            }
        }
    }
}