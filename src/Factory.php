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
	use \Xajax\Errors\TraitCall;
	/**
	 * Xajax Instances
	 *
	 * @var array
	 */
	private static $instances = [];
	/**
	 * Is an Request against Xajax
	 *
	 * @var bool
	 * @since 0.7.3
	 */
	private static $isXajaxRequest;
	/**
	 * @since 0.7.3 Detect the Request is not an old xajax type
	 * @var bool
	 */
	private static $cmsRequest;
	/**
	 * @var
	 */
	private static $input;

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
	 * Xajax Request Parameters Class to handle Post Server Get Request Vars cleanly
	 *
	 * @param null|string $method
	 *
	 * @return \Xajax\Input\Parameter
	 */
	public static function getInput(?string $method = null): Input\Parameter
	{
		$input = self::$input ?? self::$input = new Input\Input();
		return $input->getInput($method);
	}

	/**
	 * Simple detection request was send against Xajax
	 *
	 * @return bool
	 */
	public static function isXajaxRequest(): bool
	{
		// todo case: you have an file with responses and an request was send by browser against this file, maybe the request should not to be handle
		return self::$isXajaxRequest ?? self::getInput()->getBool('xjxreq', false);
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
	 * Can be set if the Request came not from Xajax but you will use the response-processor from xajax to give back the response
	 *
	 * @param bool|null $is
	 *
	 * @return bool
	 */
	public static function setXajaxRequest(?bool $is = null): bool
	{
		return self::$isXajaxRequest = (bool) $is;
	}

	/**
	 * @param bool|null $is
	 *
	 * @return bool|null
	 */
	public static function setCmsRequest(?bool $is = null): ?bool
	{
		$is = (bool) $is;
		if ($is)
		{
			self::setXajaxRequest($is);
			self::$cmsRequest = $is;
		}
		else
		{
			self::$cmsRequest = $is;
		}
		return $is;
	}

	/**
	 * @since 0.7.3
	 * @todo  check more parameters or insert methods for check cms Request
	 * @return bool
	 */
	private static function detectIsCmsRequest(): bool
	{
		if (self::isXajaxRequest() && 'cms' === self::getInput()->getWord('xjxreq'))
		{
			// todo check security to auto-allow register plugin
			// automatically enable the cms Plugin which is handling found objResponses
			self::getInstance()->getRequestPlugin('cms');

			return true;
		}

		return false;
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