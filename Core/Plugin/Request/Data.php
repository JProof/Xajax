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

namespace Xajax\Core\Plugin\Request;

use InvalidArgumentException;

/**
 * Class Plugin
 * PluginObject
 *
 * @package Xajax\Core\Plugin\Request
 * @property-read string                $name
 * @property-read int                   $priority
 * @property-read string                pluginType
 * @property-read \Xajax\Plugin\Request $pluginInstance
 */
class Data extends \Xajax\Core\Datas\Data
{
	/**
	 * Plugin constructor.
	 */
	public function __construct()
	{
		$this->setPluginType(\Xajax\Plugin\Plugin::getRequestType());
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return (string) $this->name;
	}

	/**
	 * @param string $name
	 *
	 * @return \Xajax\Core\Plugin\Request\Data
	 */
	public function setName(?string $name = null)
	{
		$this->name = (string) $name;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getPriority(): int
	{
		return (int) $this->priority;
	}

	/**
	 * @param int $priority
	 *
	 * @return \Xajax\Core\Plugin\Request\Data
	 */
	public function setPriority(?int $priority = null)
	{
		$this->priority = (int) $priority;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getPluginType(): string
	{
		return (string) $this->pluginType;
	}

	/**
	 * @param string $pluginType
	 *
	 * @return \Xajax\Core\Plugin\Request\Data
	 */
	public function setPluginType(?string $pluginType = null)
	{
		$this->pluginType = (string) $pluginType;

		return $this;
	}

	/**
	 * Getting Access to real plugin
	 *
	 * @return \Xajax\Plugin\Request
	 */
	public function getPluginInstance(): \Xajax\Plugin\Request
	{
		if ($this->pluginInstance instanceof \Xajax\Plugin\Request)
		{
			return $this->pluginInstance;
		}
		throw new InvalidArgumentException('Missing the Plugin-Object getPluginInstance() ');
	}

	/**
	 * @param \Xajax\Plugin\Request $pluginInstance
	 *
	 * @return \Xajax\Core\Plugin\Request\Data
	 */
	public function setPluginInstance(\Xajax\Plugin\Request $pluginInstance)
	{
		$this->pluginInstance = $pluginInstance;

		return $this;
	}

	/**
	 * Check the Plugin has an Method
	 *
	 * @param string $method
	 *
	 * @return bool
	 */
	public function hasPluginMethod(?string $method = null): bool
	{
		return method_exists($this->getPluginInstance(), (string) $method);
	}
}