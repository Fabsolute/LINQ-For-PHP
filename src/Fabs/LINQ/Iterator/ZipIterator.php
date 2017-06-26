<?php
/**
 * Created by PhpStorm.
 * User: ahmetturk
 * Date: 26/06/2017
 * Time: 12:44
 */

namespace Fabs\LINQ\Iterator;


class ZipIterator extends IteratorBase
{
    private $zip_iterator = null;

    /**
     * @param \Traversable|array $zip_iterator
     */
    public function setZipIterator($zip_iterator)
    {
        $this->zip_iterator = $zip_iterator;
    }

    public function getIterator()
    {
        $zip_array = iterator_to_array($this->zip_iterator, false);

        $counter = 0;
        foreach ($this->before_iterator as $item) {
            if (array_key_exists($counter, $zip_array)) {
                yield call_user_func_array($this->callable, [$item, $zip_array[$counter]]);
                $counter++;
            } else {
                break;
            }
        }
    }
}