<?php
/**
 * PHP version $phpversion$
 *
 * @category
 * @package            Xajax Core  Xajax\Input
 * @author             ${JProof}
 * @copyright          ${copyright}
 * @license            ${license}
 * @link
 * @see                ${docu}
 * @since              02.01.2018
 */

declare(strict_types=1);

namespace Xajax\Input;

use Xajax\Datas\Data;

/**
 * Class Parameters
 *
 * @property-read string method RequestMethod
 */
class Parameter extends Data
{
	/**
	 * @param string    $name
	 * @param null|bool $default
	 *
	 * @return bool|null
	 */
	public function getBool(string $name, ?bool $default = null): ?bool
	{
		return $this->getValue($name, 'bool', $default);
	}

	/**
	 * @param string      $name
	 * @param null|string $default
	 *
	 * @return string|null
	 */
	public function getWord(string $name, ?string $default = null): ?string
	{
		return $this->getValue($name, 'word', $default);
	}

	/**
	 * @param string      $name
	 * @param null|string $type
	 * @param null|mixed  $default
	 *
	 * @return mixed|null
	 */
	public function getValue(string $name, ?string $type = null, $default = null)
	{
		if (!$this->{$name})
		{
			return $default;
		}
		$val = $this->{$name};
		switch ($type)
		{
			case 'bool':
				return Filter::getBool($val, $default);
			case 'int':
				return Filter::getInt($val, $default);
			case  'word':
				return Filter::getWord($val, $default);
			case 'float':
				return Filter::getFloat($val, $default);
		}

		// todo log error filter method not found
		return null;
	}
}