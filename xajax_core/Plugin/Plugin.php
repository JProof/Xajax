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
	/**
	 * Request-Plugin-Type
	 */
	const TYPE_REQUEST = 'request';

	/**
	 * Response-Plugin-Type
	 */
	const TYPE_RESPONSE = 'response';

	/**
	 * The child-Plugin i.e. Userfunction has an Request-Type (self::REQUEST or self::RESPONSE)
	 *
	 * @var null
	 */
	private $pluginType;

	/**
	 * Plugin constructor.
	 *
	 * @param string $pluginType
	 */
	public function __construct($pluginType = self::TYPE_REQUEST)
	{
		$this->setPluginType($pluginType);
	}

	/**
	 * @return string
	 */
	public function getPluginType()
	{
		if (null === $this->pluginType)
		{
			throw new \InvalidArgumentException('Plugin-Type was not set');
		}

		return $this->pluginType;
	}

	/**
	 * @param string $pluginType
	 */
	public function setPluginType($pluginType = self::TYPE_REQUEST)
	{
		if ($this->isAllowedType($pluginType))
		{
			$this->pluginType = $pluginType;
		}
		throw new \InvalidArgumentException('The setPluginType($type) is not valid');
	}

	/**
	 * @param string $type
	 *
	 * @return bool
	 */
	static public function isRequestType($type = '')
	{
		return self::getRequestType() === (string) $type;
	}

	/**
	 * @param string $type
	 *
	 * @return bool
	 */
	static public function isResponseType($type = '')
	{
		return self::getResponseType() === (string) $type;
	}

	/**
	 * @return string
	 */
	static public function getRequestType()
	{
		return self::TYPE_REQUEST;
	}

	/**
	 * @return string
	 */
	static public function getResponseType()
	{
		return self::TYPE_RESPONSE;
	}

	/**
	 * Check the Typ is listed in Plugins
	 *
	 * @param null $type
	 *
	 * @return bool
	 */
	protected function isAllowedType($type = null)
	{
		return ($type === self::getRequestType() || $type === self::getResponseType());
	}
}