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
 * @since              15.10.2017
 */

declare(strict_types=1);

namespace Jybrid\Plugin\Response;

use InvalidArgumentException;

/**
 * Class Plugin
 * PluginObject
 *
 * @package Jybrid\Plugin\RequestRequest
 * @property-read string                  $name
 * @property-read int                     $priority
 * @property-read string                  pluginType
 * @property-read \Jybrid\Plugin\Response $pluginInstance
 */
class Data extends \Jybrid\Plugin\Data
{
	/**
	 * Getting Access to real plugin
	 *
	 * @return \Jybrid\Plugin\Response
	 * @throws \InvalidArgumentException
	 */
	public function getPluginInstance(): \Jybrid\Plugin\Response {
		if ( $this->pluginInstance instanceof \Jybrid\Plugin\Response )
		{
			return $this->pluginInstance;
		}
		throw new InvalidArgumentException('Missing the Plugin-Object getPluginInstance() ');
	}

	/**
	 * @param \Jybrid\Plugin\Response $pluginInstance
	 *
	 * @return \Jybrid\Plugin\Response\Data
	 */
	public function setPluginInstance(? $pluginInstance = null): self
	{
		if ( $pluginInstance instanceof \Jybrid\Plugin\Response )
		{
			$this->pluginInstance = $pluginInstance;

			return $this;
		}

		throw new InvalidArgumentException('Missing the Plugin-Object with correct Type setPluginInstance($pluginInstance) ');
	}
}