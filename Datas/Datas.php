<?php

declare(strict_types=1);

namespace Xajax\Datas;

use ArrayAccess;
use InvalidArgumentException;

/**
 * Class Datas
 *
 * @package JProof\RedmineApi\Data
 */
class Datas implements ArrayAccess, \Iterator
{
	/**
	 * @var array
	 */
	private $container = [];
	/**
	 * @var bool
	 */
	private $current = false;

	/**
	 * Whether a offset exists
	 *
	 * @link  http://php.net/manual/en/arrayaccess.offsetexists.php
	 *
	 * @param mixed $offset <p>
	 *                      An offset to check for.
	 *                      </p>
	 *
	 * @return boolean true on success or false on failure.
	 * </p>
	 * <p>
	 * The return value will be casted to boolean if non-boolean was returned.
	 * @since 5.0.0
	 */
	public function offsetExists($offset): bool
	{
		return array_key_exists($offset, $this->container);
	}

	/**
	 * Offset to retrieve
	 *
	 * @link  http://php.net/manual/en/arrayaccess.offsetget.php
	 *
	 * @param mixed $offset <p>
	 *                      The offset to retrieve.
	 *                      </p>
	 *
	 * @return Data Can return all value types.
	 * @throws InvalidArgumentException
	 * @since 5.0.0
	 */
	public function offsetGet($offset): Data
	{
		if ($this->offsetExists($offset))
		{
			return $this->container[$offset];
		}
		throw new InvalidArgumentException('offsetGet() The offset does not exists! To prevent Errors call offsetExists($offset) Method before');
	}

	/**
	 * Offset to set
	 *
	 * @link  http://php.net/manual/en/arrayaccess.offsetset.php
	 *
	 * @param int  $offset                              <p>
	 *                                                  The offset to assign the value to.
	 *                                                  </p>
	 * @param Data $value                               <p>
	 *                                                  The value to set.
	 *                                                  </p>
	 *
	 * @return void
	 * @since 5.0.0
	 * @throws \InvalidArgumentException
	 */
	public function offsetSet($offset = null, $value = null): void
	{
		if (!$value instanceof Data)
		{
			throw new InvalidArgumentException('offsetSet needs as value an valid Object');
		}

		if (null === $offset)
		{
			$this->container[] = $value;
		}
		else
		{
			$this->container[$offset] = $value;
		}
	}

	/**
	 * Offset to unset
	 *
	 * @link  http://php.net/manual/en/arrayaccess.offsetunset.php
	 *
	 * @param mixed $offset <p>
	 *                      The offset to unset.
	 *                      </p>
	 *
	 * @return void
	 * @since 5.0.0
	 */
	public function offsetUnset($offset = null): void
	{
		if ($this->offsetExists($offset))
		{
			unset($this->container[$offset]);
		}
	}

	/**
	 * @return array
	 */
	public function getContainer(): array
	{
		return $this->container;
	}

	/**
	 * Return the current element
	 *
	 * @link  http://php.net/manual/en/iterator.current.php
	 * @return mixed Can return any type.
	 * @since 5.0.0
	 */
	public function current()
	{
		return is_scalar($this->current) ? $this->container[$this->current] : false;
	}

	/**
	 * Move forward to next element
	 *
	 * @link  http://php.net/manual/en/iterator.next.php
	 * @return void Any returned value is ignored.
	 * @since 5.0.0
	 */
	public function next(): void
	{
		// Get the object offsets.
		$keys = $this->keys();

		if (false === $this->current && isset($keys[0]))
		{

			$this->current = $keys[0];

			return;
		}

		$idxPos = array_search($this->current, $keys, true);

		if (false !== $idxPos && isset($keys[$idxPos + 1]))
		{

			$this->current = $keys[$idxPos + 1];

			return;
		}

		$this->current = null;
	}

	/**
	 * Return the key of the current element
	 *
	 * @link  http://php.net/manual/en/iterator.key.php
	 * @return mixed scalar on success, or null on failure.
	 * @since 5.0.0
	 */
	public function key()
	{
		return $this->current;
	}

	/**
	 * @return array
	 */
	public function keys(): array
	{
		return array_keys($this->container);
	}

	/**
	 * Checks if current position is valid
	 *
	 * @link  http://php.net/manual/en/iterator.valid.php
	 * @return boolean The return value will be casted to boolean and then evaluated.
	 * Returns true on success or false on failure.
	 * @since 5.0.0
	 */
	public function valid(): bool
	{
		return is_scalar($this->current) && isset($this->container[$this->current]);
	}

	/**
	 * Rewind the Iterator to the first element
	 *
	 * @link  http://php.net/manual/en/iterator.rewind.php
	 * @return void Any returned value is ignored.
	 * @since 5.0.0
	 */
	public function rewind(): void
	{
		// Set the current position to the first object.
		if (empty($this->container))
		{
			$this->current = false;

			return;
		}

		$keys          = $this->keys();
		$this->current = array_shift($keys);
	}
}