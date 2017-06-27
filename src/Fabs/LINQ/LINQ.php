<?php

namespace Fabs\LINQ;

use Fabs\LINQ\Exception\ArgumentOutOfRangeException;
use Fabs\LINQ\Exception\InvalidArgumentException;
use Fabs\LINQ\Exception\InvalidOperationException;
use Fabs\LINQ\Iterator\ConcatIterator;
use Fabs\LINQ\Iterator\DistinctIterator;
use Fabs\LINQ\Iterator\ExceptIterator;
use Fabs\LINQ\Iterator\GroupByIterator;
use Fabs\LINQ\Iterator\IntersectIterator;
use Fabs\LINQ\Iterator\OrderByIterator;
use Fabs\LINQ\Iterator\SelectIterator;
use Fabs\LINQ\Iterator\SelectManyIterator;
use Fabs\LINQ\Iterator\SkipWhileIterator;
use Fabs\LINQ\Iterator\TakeWhileIterator;
use Fabs\LINQ\Iterator\WhereIterator;
use Fabs\LINQ\Iterator\ZipIterator;

class LINQ implements \IteratorAggregate, \Countable
{
    /**
     * @var \Traversable
     */
    private $iterator = null;

    private function __construct($source)
    {
        if (is_array($source)) {
            $this->iterator = new \ArrayIterator($source);
        } else if ($source instanceof \IteratorAggregate || $source instanceof \Iterator) {
            $this->iterator = $source;
        } else {
            throw new InvalidArgumentException('source must be an array, iterator or iteratorAggregate');
        }
    }

    /**
     * @param array $data
     * @return LINQ
     */
    public static function from($data)
    {
        return new LINQ($data);
    }

    #region Queries
    /**
     * @param callable $callable
     * @return LINQ
     */
    public function where($callable)
    {
        return new LINQ(new WhereIterator($this->iterator, $callable));
    }

    /**
     * @param callable $callable
     * @return LINQ
     */
    public function select($callable)
    {
        return new LINQ(new SelectIterator($this->iterator, $callable));
    }

    /**
     * @param callable $callable
     * @return LINQ
     */
    public function selectMany($callable)
    {
        return new LINQ(new SelectManyIterator($this->iterator, $callable));
    }

    /**
     * @param array|\Traversable $source
     * @return LINQ
     */
    public function except($source)
    {
        $except_iterator = new ExceptIterator($this->iterator, null);
        $except_iterator->setExceptIterator($source);
        return new LINQ($except_iterator);
    }

    /**
     * @param array|\Traversable $source
     * @return LINQ
     */
    public function intersect($source)
    {
        $intersect_iterator = new IntersectIterator($this->iterator, null);
        $intersect_iterator->setIntersectIterator($source);
        return new LINQ($intersect_iterator);
    }

    /**
     * @return LINQ
     */
    public function distinct()
    {
        return new LINQ(new DistinctIterator($this->iterator, null));
    }

    /**
     * @param callable $callable
     * @return LINQ
     */
    public function orderBy($callable = null)
    {
        return new LINQ(new OrderByIterator($this->iterator, $callable));
    }

    /**
     * @param callable $callable
     * @return LINQ
     */
    public function orderByDescending($callable = null)
    {
        $order_by_iterator = new OrderByIterator($this->iterator, $callable);
        $order_by_iterator->setDirection(SORT_DESC);
        return new LINQ($order_by_iterator);
    }

    /**
     * @param callable $callable
     * @return LINQ
     */
    public function groupBy($callable)
    {
        return new LINQ(new GroupByIterator($this->iterator, $callable));
    }

    /**
     * @return LINQ
     */
    public function reverse()
    {
        $array = array_reverse(iterator_to_array($this->iterator, false));
        return new LINQ(new \ArrayIterator($array));
    }

    /**
     * @param int $count
     * @return LINQ
     */
    public function skip($count)
    {
        return new LINQ(new \LimitIterator($this->iterator, $count, -1));
    }

    /**
     * @param callable $callable
     * @return LINQ
     */
    public function skipWhile($callable)
    {
        return new LINQ(new SkipWhileIterator($this->iterator, $callable));
    }

    /**
     * @param int $count
     * @return LINQ
     */
    public function take($count)
    {
        return new LINQ(new \LimitIterator($this->iterator, 0, $count));
    }

    /**
     * @param callable $callable
     * @return LINQ
     */
    public function takeWhile($callable)
    {
        return new LINQ(new TakeWhileIterator($this->iterator, $callable));
    }

    /**
     * @param array|\Traversable $new_source
     * @return LINQ
     */
    public function concat($new_source)
    {
        $concat_iterator = new ConcatIterator($this->iterator, null);
        $concat_iterator->addIterator($new_source);
        return new LINQ($concat_iterator);
    }

    /**
     * @param callable $callable
     * @param array|\Traversable $new_source
     * @return LINQ
     */
    public function zip($callable, $new_source)
    {
        $zip_iterator = new ZipIterator($this->iterator, $callable);
        $zip_iterator->setZipIterator($new_source);
        return new LINQ($zip_iterator);
    }

    /**
     * @param array|\Traversable $new_source
     * @return LINQ
     */
    public function union($new_source)
    {
        return $this->concat($new_source)->distinct();
    }

    #region Finishers
    /**
     * @param callable $callable
     * @return mixed
     * @throws InvalidOperationException
     */
    public function aggregate($callable)
    {
        $folded = null;
        $first = true;

        foreach ($this as $item) {
            if ($first) {
                $folded = $item;
                $first = false;
            } else {
                $folded = call_user_func_array($callable, [$folded, $item]);
            }
        }

        if ($first) {
            throw new InvalidOperationException ("No elements in source list");
        }

        return $folded;
    }

    /**
     * @param callable $callable
     */
    public function each($callable)
    {
        foreach ($this as $key => $value) {
            $response = call_user_func_array($callable, [$key, $value]);
            if ($response === false) {
                break;
            }
        }
    }

    /**
     * @param callable $callable
     * @param bool $throw_if_not_found
     * @param mixed $default
     * @return mixed
     * @throws InvalidOperationException
     */
    public function single($callable = null, $throw_if_not_found = true, $default = null)
    {
        $linq = $this->where($callable);
        if ($linq->count() > 1) {
            throw  new InvalidOperationException();
        } else if ($linq->count() === 1) {
            return $linq->first();
        }

        if ($throw_if_not_found) {
            throw new InvalidOperationException();
        } else {
            return $default;
        }
    }

    /**
     * @param callable $callable
     * @param mixed $default
     * @return mixed
     */
    public function singleOrDefault($callable = null, $default = null)
    {
        return $this->single($callable, false, $default);
    }

    /**
     * @param int $index
     * @param bool $throw_if_not_found
     * @param mixed $default
     * @return mixed
     * @throws ArgumentOutOfRangeException
     */
    public function elementAt($index, $throw_if_not_found = true, $default = null)
    {
        $counter = 0;
        foreach ($this->iterator as $key => $value) {
            if ($index === $counter) {
                return $value;
            }
            $counter++;
        }

        if ($throw_if_not_found) {
            throw new ArgumentOutOfRangeException();
        } else {
            return $default;
        }
    }

    /**
     * @param int $index
     * @param mixed $default
     * @return mixed
     */
    public function elementAtOrDefault($index, $default = null)
    {
        return $this->elementAt($index, false, $default);
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function contains($value)
    {
        return $this->any(function ($item) use ($value) {
            return $value === $item;
        });
    }

    /**
     * @param callable $callable
     * @return int
     */
    public function count($callable = null)
    {
        if ($callable != null) {
            return $this->where($callable)->count();
        }
        return iterator_count($this->iterator);
    }

    /**
     * @param callable $callable
     * @return float|int
     */
    public function sum($callable = null)
    {
        $sum = 0;
        foreach ($this->iterator as $value) {
            if ($callable != null) {
                $response = call_user_func($callable, $value);
            } else {
                $response = $value;
            }

            $sum += $response;
        }
        return $sum;
    }

    /**
     * @param callable $callable
     * @return float|int
     * @throws InvalidOperationException
     */
    public function average($callable = null)
    {
        $count = $this->count();
        if ($count === 0) {
            throw new InvalidOperationException ();
        }

        $sum = $this->sum($callable);
        return $sum / $count;
    }

    /**
     * @param callable $callable
     * @return float|int
     * @throws InvalidOperationException
     */
    public function max($callable = null)
    {
        $selected = $this;
        if ($callable != null) {
            $selected = $selected->select($callable);
        }

        return $selected->orderByDescending()->first();
    }


    /**
     * @param callable $callable
     * @return float|int
     * @throws InvalidOperationException
     */
    public function min($callable = null)
    {
        $selected = $this;
        if ($callable != null) {
            $selected = $selected->select($callable);
        }

        return $selected->orderBy()->first();
    }

    /**
     * @param callable $callable
     * @return bool
     */
    public function any($callable = null)
    {
        foreach ($this->iterator as $item) {
            $response = call_user_func($callable, $item);
            if ($response === true) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param callable $callable
     * @return bool
     */
    public function all($callable)
    {
        foreach ($this->iterator as $item) {
            $response = call_user_func($callable, $item);
            if ($response === false) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param callable $callable
     * @param bool $throw_if_not_found
     * @param mixed $default
     * @return mixed
     * @throws InvalidOperationException
     */
    public function first($callable = null, $throw_if_not_found = true, $default = null)
    {
        $linq = $this;
        if ($callable != null) {
            $linq = $this->where($callable);
        }

        if ($linq->count() > 0) {
            foreach ($linq as $item) {
                return $item;
            }
        }

        if ($throw_if_not_found) {
            throw  new InvalidOperationException();
        } else {
            return $default;
        }
    }

    /**
     * @param callable $callable
     * @param mixed $default
     * @return mixed
     */
    public function firstOrDefault($callable = null, $default = null)
    {
        return $this->first($callable, false, $default);
    }


    /**
     * @param callable $callable
     * @param bool $throw_if_not_found
     * @param mixed $default
     * @return mixed
     * @throws InvalidOperationException
     */
    public function last($callable = null, $throw_if_not_found = true, $default = null)
    {
        $linq = $this->where($callable);

        if ($linq->count() > 0) {

            $last = null;
            foreach ($this as $item) {
                $last = $item;
            }
            return $last;

        }

        if ($throw_if_not_found) {
            throw  new InvalidOperationException();
        } else {
            return $default;
        }
    }

    /**
     * @param callable $callable
     * @param mixed $default
     * @return mixed
     */
    public function lastOrDefault($callable = null, $default = null)
    {
        return $this->last($callable, false, $default);
    }

    /**
     * @param callable $key_selector
     * @param callable $value_selector
     * @return array
     */
    public function toArray($key_selector = null, $value_selector = null)
    {
        if ($key_selector === null && $value_selector === null) {
            $response = [];
            foreach ($this as $key => $value) {
                $response[$key] = $value;
            }
            return $response;
        }

        if ($key_selector === null) {
            return iterator_to_array(new SelectIterator($this->iterator, $value_selector), false);
        }

        $response = [];
        foreach ($this as $item) {
            $key = call_user_func($key_selector, $item);
            $value = call_user_func($value_selector, $item);
            $response[$key] = $value;
        }

        return $response;
    }

    public function getIterator()
    {
        return $this->iterator;
    }

#endregion

#endregion

}