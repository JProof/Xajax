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

namespace Xajax\Plugin\Request;

use InvalidArgumentException;

/**
 * Class Plugin
 * PluginObject
 *
 * @package Xajax\Plugin\Request
 * @property-read string                $name
 * @property-read int                   $priority
 * @property-read string                pluginType
 * @property-read \Xajax\Plugin\Request $pluginInstance
 */
class Data extends \Xajax\Plugin\Data
{
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
	 * @param \Xajax\Plugin\Plugin $pluginInstance
	 *
	 * @return \Xajax\Plugin\Request\Data
	 */
	public function setPluginInstance(?\Xajax\Plugin\Plugin $pluginInstance = null): self
	{
		if ($pluginInstance instanceof \Xajax\Plugin\Request)
		{
			$this->pluginInstance = $pluginInstance;

			return $this;
		}

		throw new InvalidArgumentException('Missing the Plugin-Object with correct Type setPluginInstance($pluginInstance) ');
	}
}