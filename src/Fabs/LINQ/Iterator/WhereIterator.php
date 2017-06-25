<?php

namespace Fabs\LINQ\Iterator;

class WhereIterator extends IteratorBase
{
    public function getIterator()
    {
        foreach ($this->before_iterator as $item) {

            if ($this->callable != null && is_callable($this->callable)) {
                $response = call_user_func($this->callable, $item);
            } else {
                $response = $item;
            }

            if ($response === true) {
                yield $item;
            }
        }
    }
}