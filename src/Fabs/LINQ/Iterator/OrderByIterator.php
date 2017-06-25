<?php
/**
 * Created by PhpStorm.
 * User: ahmetturk
 * Date: 25/06/2017
 * Time: 12:19
 */

namespace Fabs\LINQ\Iterator;


class OrderByIterator extends IteratorBase
{
    private $direction = SORT_ASC;

    /**
     * @param int $direction
     */
    public function setDirection($direction)
    {
        $this->direction = $direction;
    }

    public function getIterator()
    {
        $ordered_list = [];
        foreach ($this->before_iterator as $key => $value) {
            $response = $value;
            if ($this->callable != null) {
                $response = call_user_func($this->callable, $value);
            }

            $ordered_list[$key] = $response;
        }

        switch ($this->direction) {
            case SORT_ASC:
                asort($ordered_list);
                break;
            case SORT_DESC:
                arsort($ordered_list);
                break;
        }

        foreach ($ordered_list as $key => $value) {
            yield $this->before_iterator[$key];
        }
    }
}