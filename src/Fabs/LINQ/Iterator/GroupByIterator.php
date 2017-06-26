<?php
/**
 * Created by PhpStorm.
 * User: ahmetturk
 * Date: 25/06/2017
 * Time: 12:23
 */

namespace Fabs\LINQ\Iterator;


class GroupByIterator extends IteratorBase
{
    public function getIterator()
    {
        $group_list = [];

        foreach ($this->before_iterator as $key => $value) {
            $new_key = call_user_func($this->callable, $value);
            if (!array_key_exists($new_key, $group_list)) {
                $group_list[$new_key] = [];
            }
            $group_list[$new_key][] = $value;
        }

        foreach ($group_list as $key => $value) {
            yield $key => $value;
        }
    }
}