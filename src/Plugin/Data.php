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

namespace Jybrid\Plugin;

use Jybrid\Plugin\Plugin;

/**
 * Class Plugin
 * PluginObject
 *
 * @package Jybrid\Plugin\RequestRequest
 * @property-read string                $name
 * @property-read int                   $priority
 * @property-read string                pluginType
 * @property-read \Jybrid\Plugin\Plugin $pluginInstance
 */
abstract class Data extends \Jybrid\Datas\Data
{
	/**
	 * Plugin constructor.
	 *
	 * @param iterable|null $datas
	 */
	public function __construct( ?iterable $datas = null ) {
		parent::__construct( $datas );
		$this->setPluginType( Plugin::getRequestType() );
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
	 * @return \Jybrid\Plugin\Data
	 */
	public function setName( ?string $name = null ): Data
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
	 * @return \Jybrid\Plugin\Data
	 */
	public function setPriority( ?int $priority = null ): Data
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
	 * @return \Jybrid\Plugin\Data
	 */
	public function setPluginType( ?string $pluginType = null ): Data
	{
		$this->pluginType = (string) $pluginType;

		return $this;
	}

	/**
	 * Getting Access to real plugin
	 *
	 * @return \Jybrid\Plugin\Plugin
	 */
	abstract protected function getPluginInstance();

	/**
	 * @param \Jybrid\Plugin\Plugin $pluginInstance
	 *
	 * @return \Jybrid\Plugin\Data
	 */
	abstract protected function setPluginInstance( ?Plugin $pluginInstance = null );

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