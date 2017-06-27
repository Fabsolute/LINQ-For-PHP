<?php
/**
 * Created by PhpStorm.
 * User: ahmetturk
 * Date: 25/06/2017
 * Time: 12:08
 */

namespace Fabs\LINQ\Iterator;

abstract class IteratorBase implements \IteratorAggregate
{
    protected $before_iterator = null;
    protected $callable = null;

    /**
     * WhereIterator constructor.
     * @param \Traversable $before_iterator
     * @param callable $callable
     */
    public function __construct($before_iterator, $callable)
    {
        $this->before_iterator = $before_iterator;
        $this->callable = $callable;

    }

    public abstract function getIterator();
}