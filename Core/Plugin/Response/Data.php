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

namespace Xajax\Core\Plugin\Response;

use InvalidArgumentException;

/**
 * Class Plugin
 * PluginObject
 *
 * @package Xajax\Core\Plugin\Request
 * @property-read string                      $name
 * @property-read int                         $priority
 * @property-read string                      pluginType
 * @property-read \Xajax\Core\Plugin\Response $pluginInstance
 */
class Data extends \Xajax\Core\Plugin\Data
{
	/**
	 * Getting Access to real plugin
	 *
	 * @return \Xajax\Core\Plugin\Response
	 * @throws \InvalidArgumentException
	 */
	public function getPluginInstance(): \Xajax\Core\Plugin\Response
	{
		if ($this->pluginInstance instanceof \Xajax\Core\Plugin\Response)
		{
			return $this->pluginInstance;
		}
		throw new InvalidArgumentException('Missing the Plugin-Object getPluginInstance() ');
	}

	/**
	 * @param \Xajax\Core\Plugin\Response $pluginInstance
	 *
	 * @return \Xajax\Core\Plugin\Response\Data
	 */
	public function setPluginInstance(? $pluginInstance = null): self
	{
		if ($pluginInstance instanceof \Xajax\Core\Plugin\Response)
		{
			$this->pluginInstance = $pluginInstance;

			return $this;
		}

		throw new InvalidArgumentException('Missing the Plugin-Object with correct Type setPluginInstance($pluginInstance) ');
	}
}