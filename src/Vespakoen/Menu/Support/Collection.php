<?php namespace Vespakoen\Menu\Support;

class Collection {

	/**
	 * Get the first child from the collection.
	 *
	 * @return mixed|null
	 */
	public function first()
	{
		return count($this->children) > 0 ? reset($this->children) : null;
	}

	/**
	* Get the last child from the collection.
	*
	* @return mixed|null
	*/
	public function last()
	{
		return count($this->children) > 0 ? end($this->children) : null;
	}

	/**
	 * Get and remove the first child from the collection.
	 *
	 * @return mixed|null
	 */
	public function shift()
	{
		return array_shift($this->children);
	}

	/**
	 * Get and remove the last child from the collection.
	 *
	 * @return mixed|null
	 */
	public function pop()
	{
		return array_pop($this->children);
	}

	/**
	 * Execute a callback over each child.
	 *
	 * @param  Closure  $callback
	 * @return \Illuminate\Support\Collection
	 */
	public function each(Closure $callback)
	{
		array_map($callback, $this->children);

		return $this;
	}

	/**
	 * Run a map over each of the children.
	 *
	 * @param  Closure  $callback
	 * @return array
	 */
	public function map(Closure $callback)
	{
		$this->children = array_map($callback, $this->children);

		return $this;
	}

	/**
	 * Run a filter over each of the children.
	 *
	 * @param  Closure  $callback
	 * @return \Illuminate\Support\Collection
	 */
	public function filter(Closure $callback)
	{
		$this->children = array_filter($this->children, $callback);

		return $this;
	}

	/**
	 * Sort through each child with a callback.
	 *
	 * @param  Closure  $callback
	 * @return \Illuminate\Support\Collection
	 */
	public function sort(Closure $callback)
	{
		uasort($this->children, $callback);

		return $this;
	}

	/**
	 * Sort the collection using the given Closure.
	 *
	 * @param  \Closure  $callback
	 * @return \Illuminate\Support\Collection
	 */
	public function sortBy(Closure $callback)
	{
		$results = array();

		// First we will loop through the children and get the comparator from a callback
		// function which we were given. Then, we will sort the returned values and
		// and grab the corresponding values for the sorted keys from this array.
		foreach ($this->children as $key => $value)
		{
			$results[$key] = $callback($value);
		}

		asort($results);

		// Once we have sorted all of the keys in the array, we will loop through them
		// and grab the corresponding model so we can set the underlying children list
		// to the sorted version. Then we'll just return the collection instance.
		foreach (array_keys($results) as $key)
		{
			$results[$key] = $this->children[$key];
		}

		$this->children = $results;

		return $this;
	}

	/**
	 * Reverse children order.
	 *
	 * @return \Illuminate\Support\Collection
	 */
	public function reverse()
	{
		$this->children = array_reverse($this->children);

		return $this;
	}

	/**
	 * Get an array with the values of a given key.
	 *
	 * @param  string  $value
	 * @param  string  $key
	 * @return array
	 */
	public function lists($value, $key = null)
	{
		$results = array();

		foreach ($this->children as $child)
		{
			$childValue = $this->getListValue($child, $value);

			// If the key is "null", we will just append the value to the array and keep
			// looping. Otherwise we will key the array using the value of the key we
			// received from the developer. Then we'll return the final array form.
			if (is_null($key))
			{
				$results[] = $childValue;
			}
			else
			{
				$childKey = $this->getListValue($child, $key);

				$results[$childKey] = $childValue;
			}
		}

		return $results;
	}

	/**
	 * Get the value of a list child object.
	 *
	 * @param  mixed  $child
	 * @param  mixed  $key
	 * @return mixed
	 */
	protected function getListValue($child, $key)
	{
		return is_object($child) ? $child->{$key} : $child[$key];
	}

	/**
	 * Determine if the collection is empty or not.
	 *
	 * @return bool
	 */
	public function isEmpty()
	{
		return empty($this->children);
	}

	/**
	 * Count the number of children in the collection.
	 *
	 * @return int
	 */
	public function count()
	{
		return count($this->children);
	}

	/**
	 * Determine if an child exists at an offset.
	 *
	 * @param  mixed  $key
	 * @return bool
	 */
	public function offsetExists($key)
	{
		return array_key_exists($key, $this->children);
	}

	/**
	 * Get an child at a given offset.
	 *
	 * @param  mixed  $key
	 * @return mixed
	 */
	public function offsetGet($key)
	{
		return $this->children[$key];
	}

	/**
	 * Set the child at a given offset.
	 *
	 * @param  mixed  $key
	 * @param  mixed  $value
	 * @return void
	 */
	public function offsetSet($key, $value)
	{
		if (is_null($key))
		{
			$this->children[] = $value;
		}
		else
		{
			$this->children[$key] = $value;
		}
	}

	/**
	 * Unset the child at a given offset.
	 *
	 * @param  string  $key
	 * @return void
	 */
	public function offsetUnset($key)
	{
		unset($this->children[$key]);
	}

}
