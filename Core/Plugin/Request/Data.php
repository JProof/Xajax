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
 * @property-read string                     $name
 * @property-read int                        $priority
 * @property-read string                     pluginType
 * @property-read \Xajax\Core\Plugin\Request $pluginInstance
 */
class Data extends \Xajax\Core\Plugin\Data
{
	/**
	 * Getting Access to real plugin
	 *
	 * @return \Xajax\Core\Plugin\Request
	 */
	public function getPluginInstance(): \Xajax\Core\Plugin\Request
	{
		if ($this->pluginInstance instanceof \Xajax\Core\Plugin\Request)
		{
			return $this->pluginInstance;
		}
		throw new InvalidArgumentException('Missing the Plugin-Object getPluginInstance() ');
	}

	/**
	 * @param \Xajax\Core\Plugin\Plugin $pluginInstance
	 *
	 * @return \Xajax\Core\Plugin\Request\Data
	 */
	public function setPluginInstance(?\Xajax\Core\Plugin\Plugin $pluginInstance = null): self
	{
		if ($pluginInstance instanceof \Xajax\Core\Plugin\Request)
		{
			$this->pluginInstance = $pluginInstance;

			return $this;
		}

		throw new InvalidArgumentException('Missing the Plugin-Object with correct Type setPluginInstance($pluginInstance) ');
	}
}