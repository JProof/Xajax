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
 * @since              28.10.2017
 */

declare(strict_types=1);

namespace Xajax\Configuration;

use BadMethodCallException;
use function Xajax\Core\addError;
use Xajax\Core\Datas\Data;

/**
 * Class Base
 *
 * @package Xajax\Configuration
 */
abstract class Base extends Data
{
	/**
	 * @return self
	 */
	abstract public static function getInstance();

	/**
	 * Legacy-Mode can be used to refacture xajax 6 versions. The Legacy-Flag allows to get and set vars without type checking
	 *
	 * @deprecated jproof/xajax 0.7.2 Legacy Mode will be removed
	 * @var bool
	 */
	static protected $legacy = false;
	/**
	 * @var string
	 */
	protected $version = 'jproof/xajax 0.7.2';

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
		$name = self::deprecatedNameAlias($name);
		if (self::isLegacy())
		{
			return $this->{$name};
		}

		$method = self::getMethodName('get', $name);
		if (method_exists($this, $method))
		{
			return $this->{$method()};
		}

		// never giveback an parameter without getter
		addError(new BadMethodCallException(__CLASS__ . '::' . __METHOD__ . ' Method ' . $method . ' for variable ' . $name . ' does not exists'));

		return null;
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
		$name = self::deprecatedNameAlias($name);

		if (self::isLegacy())
		{
			return $this->{$name} = $value;
		}

		$method = self::getMethodName('set', $name);
		if (method_exists($this, $method))
		{
			return $this->$method($value);
		}
		// never overload the setter! Make sure you have an
		addError(new BadMethodCallException(__CLASS__ . '::' . __METHOD__ . ' Method ' . $method . ' for variable ' . $name . ' does not exists'));

		return null;
	}

	/**
	 * @param $name
	 *
	 * @example create an method isTestVar() then  isset(Config::getInstance()->testVar) will be checked by the method
	 * @return bool|mixed
	 */
	public function __isset($name)
	{
		$name = self::deprecatedNameAlias($name);
		if (self::isLegacy())
		{
			return isset($this->{$name});
		}

		$method = self::getMethodName('is', $name);
		if (method_exists($this, $method))
		{
			return $this->{$method()};
		}

		return isset($this->{$name});
	}

	/**
	 * Old Array Key names
	 *
	 * @param string $name
	 *
	 * @deprecated jproof/xajax 0.7.2
	 * @return string
	 */
	protected static function deprecatedNameAlias(?string $name = null)
	{
		switch ($name)
		{
			case 'javascript URI':
				return 'javascriptUri';
			default:
				return $name;
		}
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
	 * @return bool
	 * @deprecated jproof/xajax 0.7.2 will be removed
	 */
	public static function isLegacy(): bool
	{
		return self::$legacy;
	}

	/**
	 * @param bool $legacy
	 *
	 * @deprecated jproof/xajax 0.7.2 will be removed
	 */
	public static function setLegacy(?bool $legacy = null)
	{
		self::$legacy = (bool) $legacy;
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