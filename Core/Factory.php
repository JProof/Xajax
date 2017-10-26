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
 * @since              21.09.2017
 */

declare(strict_types=1);

namespace Xajax\Core;

use Xajax\Core\Response\Response;

/**
 * Class Factory
 *
 * @package Xajax
 */
class Factory
{
	use \Xajax\Core\Errors\Call;
	/**
	 * Xajax Instances
	 *
	 * @var array
	 */
	private static $instances = [];

	/**
	 * Factory constructor.
	 */
	protected function __construct()
	{
	}

	/**
	 * Getting Access to Xajax
	 *
	 * @param string $instance      instanceName is necessary
	 * @param array  $configuration configuration to Xajax
	 *
	 * @return \Xajax\Core\Xajax
	 */
	public static function getInstance(string $instance = null, array $configuration = null): \Xajax\Core\Xajax
	{
		// todo errors and logger on less instanceName
		// todo clearing evil name stuff if need
		$instances = self::getInstances();
		if (array_key_exists($instance, $instances) && ($foundInstance = $instances[$instance]) instanceof \Xajax\Core\Xajax)
		{
			return $foundInstance;
		}
		$instances[$instance] = self::createXajax($configuration);
		self::setInstances($instances);

		return $instances[$instance];
	}

	/**
	 * @param array $configuration
	 *
	 * @return \Xajax\Core\Xajax
	 */
	private static function createXajax(array $configuration = null): \Xajax\Core\Xajax
	{
		return new \Xajax\Core\Xajax($configuration);
	}

	/**
	 * @return array
	 */
	private static function getInstances(): array
	{
		return self::$instances;
	}

	/**
	 * @param array $instances
	 */
	private static function setInstances(array $instances)
	{
		self::$instances = $instances;
	}

	/**
	 * Getting the Response instances
	 *
	 * @param int|null $instanceNr let null to get default instance
	 *
	 * @return Response
	 */
	public static function getResponseInstance(?int $instanceNr = null): Response
	{
		return Response::getInstance($instanceNr);
	}
}