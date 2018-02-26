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

namespace Xajax\Plugin;

/**
 * Class Plugin
 * Basic Plugin Class
 *
 * @package Xajax\Plugin
 */

/**
 * Class Plugin
 *
 * @package Xajax\Plugin
 */
abstract class Plugin
{
	use \Xajax\Errors\TraitCall;

	/**
	 * Request-Plugin-Type
	 */
	public const TYPE_REQUEST = 'request';

	/**
	 * Response-Plugin-Type
	 */
	public const TYPE_RESPONSE = 'response';

	/**
	 * The child-Plugin i.e. Userfunction has an Request-Type (self::REQUEST or self::RESPONSE)
	 *
	 * @var string
	 */
	private $pluginType;

	/**
	 * Plugin constructor.
	 *
	 * @param string $pluginType
	 */
	protected function __construct(?string $pluginType = null)
	{
		$this->setPluginType($pluginType);
	}

	/**
	 * @return string
	 */
	public function getPluginType(): string
	{
		if (null === $this->pluginType)
		{
			throw new \InvalidArgumentException('Plugin-Type was not set');
		}

		return $this->pluginType;
	}

	/**
	 * @param string $pluginType
	 *
	 * @return string
	 */
	public function setPluginType(?string $pluginType = null): string
	{
		if ($this->isAllowedType($pluginType))
		{
			return $this->pluginType = $pluginType;
		}
		throw new \InvalidArgumentException('The setPluginType($type) is not valid');
	}

	/**
	 * @param string $type
	 *
	 * @return bool
	 */
	public static function isRequestType(?string $type = null): bool
	{
		return null !== $type && self::getRequestType() === $type;
	}

	/**
	 * @param string $type
	 *
	 * @return bool
	 */
	public static function isResponseType(?string $type = null): bool
	{
		return null !== $type && self::getResponseType() === $type;
	}

	/**
	 * @return string
	 */
	public static function getRequestType(): string
	{
		return self::TYPE_REQUEST;
	}

	/**
	 * @return string
	 */
	public static function getResponseType(): string
	{
		return self::TYPE_RESPONSE;
	}

	/**
	 * Check the Typ is listed in Plugins
	 *
	 * @param null|string $pluginType
	 *
	 * @return bool
	 */
	protected function isAllowedType(?string $pluginType = null): bool
	{
		return ($pluginType === self::getRequestType() || $pluginType === self::getResponseType());
	}
}