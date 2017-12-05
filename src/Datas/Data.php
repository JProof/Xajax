<?php

declare(strict_types=1);

namespace Xajax\Datas;

use ArrayIterator;
use IteratorAggregate;
use stdClass;

/**
 * Class Data
 *
 * @package JProof\RedmineApi\Data
 */
class Data implements IteratorAggregate, \Countable
{
	/**
	 * @var array
	 */
	private $datas = [];

	/**
	 * @return null|array
	 */
	public function getDatas(): ?array
	{
		return $this->datas;
	}

	/**
	 * Data constructor.
	 *
	 * @param iterable null $datas
	 */
	public function __construct(?iterable $datas = null)
	{
		if (\is_iterable($datas))
		{
			$this->bind($datas);
		}
	}

	/**
	 * @param $name
	 *
	 * @return bool
	 */
	public function __isset($name)
	{
		return isset($this->datas[$name]);
	}

	/**
	 * @param $name
	 *
	 * @return mixed|null
	 */
	public function __get($name)
	{
		return $this->get($name);
	}

	/**
	 * @param $name
	 * @param $value
	 *
	 * @return mixed
	 */
	public function __set($name, $value)
	{
		return $this->set($name, $value);
	}

	/**
	 * @param null $name
	 *
	 * @return mixed|null Null on every not successfully try
	 */
	protected function get($name = null)
	{
		if (null === $name)
		{
			return null;
		}
		return $this->datas[$name] ?? null;
	}

	/**
	 * @param $name
	 * @param $value
	 *
	 * @see http://us3.php.net/manual/en/language.types.array.php#language.types.array.casting
	 * @return mixed
	 */
	protected function set($name, $value)
	{
		if (false === strpos($name, "\0"))
		{
			$this->datas[$name] = $value;

			return $value;
		}
		return false;
	}

	/**
	 * Count elements of an object
	 *
	 * @link  http://php.net/manual/en/countable.count.php
	 * @return int The custom count as an integer.
	 * </p>
	 * <p>
	 * The return value is cast to an integer.
	 * @since 5.1.0
	 */
	public function count(): int
	{
		return \count($this->datas);
	}

	/**
	 * @return \stdClass
	 */
	public function dump(): \stdClass
	{
		$datasObject = new stdClass;

		foreach (array_keys($this->datas) as $property)
		{
			$datasObject->$property = $this->{$property};
		}

		return $datasObject;
	}

	/**
	 * @return array
	 */
	public function toArray(): array
	{
		return (array) $this->dump();
	}

	/**
	 * @param iterable|null $datas
	 */
	public function bind(?iterable $datas = null)
	{
		if (\is_iterable($datas))
		{
			foreach ($datas as $key => $value)
			{
				$this->set($key, $value);
			}
		}
	}

	/**
	 * Retrieve an external iterator
	 *
	 * @link  http://php.net/manual/en/iteratoraggregate.getiterator.php
	 * @return ArrayIterator Traversable An instance of an object implementing <b>Iterator</b> or
	 * <b>Traversable</b>
	 * @since 5.0.0
	 */
	public function getIterator(): \ArrayIterator
	{
		return new ArrayIterator($this->dump());
	}
}