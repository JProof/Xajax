<?php
/**
 * PHP version $phpversion$
 *
 * @category
 * @package            Jybrid Core  Jybrid\Input
 * @author             ${JProof}
 * @copyright          ${copyright}
 * @license            ${license}
 * @link
 * @see                ${docu}
 * @since              02.01.2018
 */

declare(strict_types=1);

namespace Jybrid\Input;

use Jybrid\Datas\Data;

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
	 * Save get int
	 *
	 * @param string   $name
	 * @param int|null $default
	 *
	 * @return int|null
	 */
	public function getInt( string $name, ?int $default = null ): ?int {
		return $this->getValue( $name, 'int', $default );
	}

	/**
	 * @param string      $name
	 * @param null|string $default
	 *
	 * @return string|null
	 */
	public function getString( string $name, ?string $default = null ): ?string
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
		if ( ! isset( $this->{$name} ) )
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
				return Filter::getString( $val, $default );
			case 'float':
				return Filter::getFloat($val, $default);
		}

		// todo log error filter method not found
		return null;
	}

	/**
	 * Getting the Jybrid called RequestName from Javascript as "task" Parameter
	 * Jybrid\Plugins\Cms\Plugin::registerRequest('myMethodName');
	 * jybrid.Exe('myMethodName');
	 *
	 * @see https://github.com/JProof/jybrid-examples#sc
	 * @return null|string
	 */
	public function getRequestName(): ?string {
		return $this->getString( 'jybridRequestName' );
	}
}