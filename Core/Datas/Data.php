<?php
/**
 * PHP version php7
 *
 * @category
 * @package            xajax-php-7
 * @author             ${JProof}
 * @copyright          ${copyright}
 * @license            ${license}
 * @link
 * @see                ${docu}
 * @since              15.10.2017
 */

declare(strict_types=1);

namespace Xajax\Core\Datas;

use ArrayIterator;
use IteratorAggregate;
use stdClass;

/**
 * Class Data
 *
 * @package Xajax\Core\Datas
 */
class Data implements IteratorAggregate, \Countable
{
	/**
	 * @var array
	 */
	private $properties = [];

	/**
	 * @param $name
	 * @param $value
	 *
	 * @return mixed
	 */
	public function __set($name, $value)
	{
		return $this->setProperty($name, $value);
	}

	/**
	 * @param $name
	 *
	 * @return bool
	 */
	public function __isset($name)
	{
		return isset($this->properties[$name]);
	}

	/**
	 * @param $name
	 *
	 * @return mixed|null
	 */
	public function __get($name)
	{
		return $this->getProperty($name);
	}

	/**
	 * @param $name
	 *
	 * @return mixed|null
	 */
	protected function getProperty($name)
	{
		return $this->properties[$name] ?? null;
	}

	/**
	 * @param $name
	 * @param $value
	 *
	 * @return mixed
	 */
	protected function setProperty($name, $value)
	{
		/*
		 * Check if the property starts with a null byte. If so, discard it because a later attempt to try to access it
		 * can cause a fatal error. See http://us3.php.net/manual/en/language.types.array.php#language.types.array.casting
		 */
		if (0 === strpos($name, "\0"))
		{
			return false;
		}

		// Set the value.
		$this->properties[$name] = $value;

		return $value;
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
		return count($this->properties);
	}

	/**
	 * @return \stdClass
	 */
	public function dump(): \stdClass
	{

		$propertiesObject = new stdClass;

		foreach (array_keys($this->properties) as $property)
		{
			// Get the property.
			$propertiesObject->$property = $property;
		}

		return $propertiesObject;
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