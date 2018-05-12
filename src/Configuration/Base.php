<?php
/**
 * PHP version php7
 *
 * @category
 * @package            jybrid-php-7
 * @author             ${JProof}
 * @copyright          ${copyright}
 * @license            ${license}
 * @link
 * @see                ${docu}
 * @since              28.10.2017
 */

declare(strict_types=1);

namespace Jybrid\Configuration;

use BadMethodCallException;
use Jybrid\Datas\Data;
use Jybrid\Errors\Handler;

/**
 * Class Base
 *
 * @package Jybrid\Configuration
 */
abstract class Base extends Data
{
	/**
	 * @return self
	 */
	abstract public static function getInstance();

	/**
	 * Legacy-Mode can be used to refacture jybrid 6 versions. The Legacy-Flag allows to get and set vars without type checking
	 *
	 * @deprecated jproof/jybrid 0.7.2 Legacy Mode will be removed
	 * @var bool
	 */
	static protected $legacy = false;
	/**
	 * @var string
	 */
	protected $version = 'jproof/jybrid 0.7.8';

	/**
	 *  Getter
	 *
	 * @param $name
	 *
	 * @return mixed
	 * @throws BadMethodCallException
	 * @throws BadMethodCallException
	 */
	public function __get($name)
	{
		$return = null;
		$method = self::getMethodName('get', $name);
		if (method_exists($this, $method))
		{
			$return = $this->{$method()};
		}

		// never giveback an parameter without getter
		Handler::addError( new BadMethodCallException( __CLASS__ . '::' . __METHOD__ . ' Method ' . $method . ' for variable ' . $name . ' does not exists' ) );

		return $return;
	}

	/**
	 * Setter
	 *
	 * @param $name
	 * @param $value
	 *
	 * @return mixed
	 */
	public function __set($name, $value)
	{
		$_value = null;
		$method = self::getMethodName('set', $name);
		if (method_exists($this, $method))
		{
			$_value = $this->$method( $value );
		} else {
			// never overload the setter! Make sure you have an
			Handler::addError( new BadMethodCallException( __CLASS__ . '::' . __METHOD__ . ' Method ' . $method . ' for variable ' . $name . ' does not exists', E_ERROR ) );
		}

		return $_value;
	}

	/**
	 * @param $name
	 *
	 * @example create an method isTestVar() then  isset(Config::getInstance()->testVar) will be checked by the method
	 * @return bool|mixed
	 */
	public function __isset($name)
	{
		$method = self::getMethodName('is', $name);

		return method_exists( $this, $method ) ? $this->{$method()} : isset( $this->{$name} );
	}

	/**
	 * Internal MethodName Compiler
	 *
	 * @param string $type get|set|is
	 * @param string $name variable-name which should interact with the method
	 *
	 * @return string
	 */
	private static function getMethodName(?string $type = null, ?string $name = null): string
	{
		return (string) $type . ucfirst((string) $name);
	}

	/**
	 * Current Version
	 *
	 * @return string
	 */
	public function getVersion(): string
	{
		return $this->version;
	}
}