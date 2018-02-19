<?php
/**
 * PHP version $phpversion$
 *
 * @category
 * @package            Xajax Core  Xajax\Configuration
 * @author             ${JProof}
 * @copyright          ${copyright}
 * @license            ${license}
 * @link
 * @see                ${docu}
 * @since              02.01.2018
 */

declare(strict_types=1);

namespace Xajax\Configuration;

/**
 * Trait Security
 *
 * @package Xajax\Configuration
 */
trait Security
{
	/**
	 * Autoload Request Plugin on Xajax Request
	 * If Xajax Request against an other file, where the requested plugin-type was not registered before
	 *
	 * @var bool
	 */
	protected $requestPluginAutoload;

	/**
	 * @return null|bool
	 */
	public function isRequestPluginAutoload(): ?bool
	{
		return $this->requestPluginAutoload;
	}

	/**
	 * @param bool $requestPluginAutoload
	 *
	 * @return self
	 */
	public function setRequestPluginAutoload(?bool $requestPluginAutoload = null): self
	{
		$this->requestPluginAutoload = $requestPluginAutoload ?? false;
		return $this;
	}
}