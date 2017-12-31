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

namespace Xajax;

use Xajax\Response\Response;
use Xajax\Scripts\Generator;
use Xajax\Scripts\Scripts;

/**
 * Class Factory
 *
 * @package Xajax
 */
class Factory
{
	use \Xajax\Errors\Call;
	/**
	 * Xajax Instances
	 *
	 * @var array
	 */
	private static $instances = [];
	/**
	 * @since 0.7.3 Detect the Request is not an old xajax type
	 * @var bool
	 */
	private static $cmsRequest;

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
	 * @return \Xajax\Xajax
	 */
	public static function getInstance(string $instance = null, array $configuration = null): \Xajax\Xajax
	{
		// todo errors and logger on less instanceName
		// todo clearing evil name stuff if need
		$instances = self::getInstances();
		if (array_key_exists($instance, $instances) && ($foundInstance = $instances[$instance]) instanceof \Xajax\Xajax)
		{
			return $foundInstance;
		}
		$instances[$instance] = self::createXajax($configuration);
		self::setInstances($instances);

		return $instances[$instance];
	}

	/**
	 * Getting the Script-Handler
	 *
	 * @return \Xajax\Scripts\Scripts
	 */
	public static function getScripts(): \Xajax\Scripts\Scripts
	{
		return Scripts::getInstance();
	}

	/**
	 * @param array $configuration
	 *
	 * @return \Xajax\Xajax
	 */
	private static function createXajax(array $configuration = null): \Xajax\Xajax
	{
		return new \Xajax\Xajax($configuration);
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
	private static function setInstances(array $instances): void
	{
		self::$instances = $instances;
	}

	/**
	 * Short hand for closing the Response
	 *
	 * @param bool|null $exit
	 */
	public static function processRequest(?bool $exit = null): void
	{
		null !== $exit ? self::getInstance()->getConfig()->setExitAllowed($exit) : null;

		self::getInstance()->processRequest();
	}

	/**
	 * Method to check the calls was an new Cms Xajax Call
	 *
	 * @since 0.7.3
	 * @return bool
	 */
	public static function isCmsRequest(): bool
	{
		return self::$cmsRequest ?? self::$cmsRequest = self::detectIsCmsRequest();
	}

	/**
	 * @since 0.7.3
	 * @todo  check more parameters or insert methods for check cms Request
	 * @return bool
	 */
	private static function detectIsCmsRequest(): bool
	{
		if ($isDetected = (bool) ($_GET['xjxcms'] ?? $_POST['xjxcms'] ?? false))
		{
			// automatically enable the cms Plugin which is handling found objResponses
			self::getInstance()->getRequestPlugin('cms');
		}
		return $isDetected;
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

	/**
	 * @param bool|null $forceNew
	 *
	 * @return string
	 */
	public static function getClientScript(?bool $forceNew = null): string
	{
		return Generator::generateClientScript($forceNew);
	}


}