<?php
/**
 * Created by PhpStorm.
 * User: ahmetturk
 * Date: 25/06/2017
 * Time: 16:42
 */

namespace Fabs\LINQ\Iterator;


class SkipWhileIterator extends IteratorBase
{
    public function getIterator()
    {
        $yield = false;
        foreach ($this->before_iterator as $item) {
            if ($yield) {
                yield $item;
            } else {
                $yield = !call_user_func($this->callable, $item);
                if ($yield) {
                    yield $item;
                }
            }
        }
    }
}