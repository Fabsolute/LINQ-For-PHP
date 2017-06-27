<?php
/**
 * Created by PhpStorm.
 * User: ahmetturk
 * Date: 25/06/2017
 * Time: 12:08
 */

namespace Fabs\LINQ\Iterator;


class SelectIterator extends IteratorBase
{
    public function getIterator()
    {
        foreach ($this->before_iterator as $item) {
            $response = call_user_func($this->callable, $item);
            yield $response;
        }
    }
}