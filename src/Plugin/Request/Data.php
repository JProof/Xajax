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

namespace Jybrid\Plugin\Request;

use InvalidArgumentException;

/**
 * Class Plugin
 * PluginObject
 *
 * @package Jybrid\Plugin\RequestRequest
 * @property-read string                 $name
 * @property-read int                    $priority
 * @property-read string                 pluginType
 * @property-read \Jybrid\Plugin\Request $pluginInstance
 */
class Data extends \Jybrid\Plugin\Data
{
	/**
	 * Getting Access to real plugin
	 *
	 * @return \Jybrid\Plugin\Request
	 */
	public function getPluginInstance(): \Jybrid\Plugin\Request {
		if ( $this->pluginInstance instanceof \Jybrid\Plugin\Request )
		{
			return $this->pluginInstance;
		}
		throw new InvalidArgumentException('Missing the Plugin-Object getPluginInstance() ');
	}

	/**
	 * @param \Jybrid\Plugin\Plugin $pluginInstance
	 *
	 * @return \Jybrid\Plugin\Request\Data
	 */
	public function setPluginInstance( ?\Jybrid\Plugin\Plugin $pluginInstance = null ): self {
		if ( $pluginInstance instanceof \Jybrid\Plugin\Request )
		{
			$this->pluginInstance = $pluginInstance;

			return $this;
		}

		throw new InvalidArgumentException('Missing the Plugin-Object with correct Type setPluginInstance($pluginInstance) ');
	}
}