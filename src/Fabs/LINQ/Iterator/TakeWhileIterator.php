<?php
/**
 * Created by PhpStorm.
 * User: ahmetturk
 * Date: 25/06/2017
 * Time: 16:45
 */

namespace Fabs\LINQ\Iterator;


class TakeWhileIterator extends IteratorBase
{
    public function getIterator()
    {
        foreach ($this->before_iterator as $item) {
            $yield = call_user_func($this->callable, $item);
            if ($yield) {
                yield $item;
            } else {
                break;
            }
        }
    }
}