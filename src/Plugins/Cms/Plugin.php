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

namespace Xajax\Plugins\Cms;

use InvalidArgumentException;
use Xajax\Factory;
use Xajax\Plugin\Request;
use Xajax\Plugin\Request\RequestPluginIface;
use Xajax\RequestIface;
use Xajax\Response\Manager;

/**
 * Class Plugin
 * Hold all Cms Plugin Instances
 *
 * @package Xajax\Plugins\Cms
 */
class Plugin extends Request implements RequestPluginIface
{
	/*
		Array: aFunctions

		An array of <xajaxCms> object that are registered and
		available via a <xajax.request> call.
	*/
	protected $aFunctions;
	/*
		String: sXajaxPrefix

		A configuration setting that is stored locally and used during
		the client script generation phase.
	*/
	protected $sXajaxPrefix;
	/*
		String: sDefer

		Configuration option that can be used to request that the
		javascript file is loaded after the page has been fully loaded.
	*/
	protected $sDefer;
	protected $bDeferScriptGeneration;
	/*
		String: sRequestedFunction

		This string is used to temporarily hold the name of the function
		that is being requested (during the request processing phase).

		Since canProcessRequest loads this value from the get or post
		data, it is unnecessary to load it again.
	*/
	protected $sRequestedFunction;
	protected $isXajaxRequest = false;

	/*
		Function: xajaxFunctionPlugin

		Constructs and initializes the <xajaxFunctionPlugin>.  The GET and POST
		data is searched for xajax function call parameters.  This will later
		be used to determine if the request is for a registered function in
		<xajaxFunctionPlugin->canProcessRequest>
	*/
	public function __construct()
	{


		// populate which type the current plugin is
		parent::__construct(Plugin::getRequestType());
		$this->aFunctions = [];

		$this->isXajaxRequest = (bool) ($_GET['xjxcms'] ?? $_POST['xjxcms'] ?? false);
	}

	/*
		Function: configure

		Sets/stores configuration options used by this plugin.
	*/
	public function configure($sName, $mValue)
	{
		if ('wrapperPrefix' === $sName)
		{
			$this->sXajaxPrefix = $mValue;
		}
		else if ('scriptDefferal' === $sName)
		{
			if (true === $mValue)
			{
				$this->sDefer = 'defer ';
			}
			else
			{
				$this->sDefer = '';
			}
		}
		else if ('deferScriptGeneration' === $sName)
		{
			if (true === $mValue || false === $mValue)
			{
				$this->bDeferScriptGeneration = $mValue;
			}
			else if ('deferred' === $mValue)
			{
				$this->bDeferScriptGeneration = $mValue;
			}
		}
	}

	public function generateHash()
	{
		$sHash = '';
		foreach (array_keys($this->aFunctions) as $sKey)
		{
			$sHash .= $this->aFunctions[$sKey]->getName();
		}

		return md5($sHash);
	}

	/*
		Function: register

		Provides a mechanism for functions to be registered and made available to
		the page via the javascript <xajax.request> call.
	*/

	public function generateClientScript(): string
	{
		$script = '';
		if (0 < \count($this->aFunctions))
		{
			foreach (array_keys($this->aFunctions) as $sKey)
			{
				$script .= $this->getMethodByIndex($sKey)->generateClientScript($this->sXajaxPrefix);
			}
		}

		return $script;
	}

	/*
		Function: generateClientScript

		Called by the <xajaxPluginManager> during the client script generation
		phase.  This is used to generate a block of javascript code that will
		contain function declarations that can be used on the browser through
		javascript to initiate xajax requests.
	*/

	public function canProcessRequest(): bool
	{
		return Factory::isCmsRequest();
	}

	public function processRequest()
	{
		if (false === $this->canProcessRequest())
		{
			return false;
		}

		$objResponseManager = Manager::getInstance();

		$objResponseManager->append(Factory::getResponseInstance());

		return true;
	}

	/*
		Function: canProcessRequest

		Determines whether or not the current request can be processed
		by this plugin.

		Returns:

		boolean - True if the current request can be handled by this plugin;
			false otherwise.
	*/

	/**
	 * @param array $aArgs
	 *
	 * @return RequestIface
	 */
	public function registerRequest(array $aArgs = null): RequestIface
	{
		$cntArgs = \count($aArgs);
		if (0 < $cntArgs)
		{

			$xuf = $aArgs[0];

			if (false === ($xuf instanceof Handler))
			{
				$xuf = new Handler($xuf);
			}

			if (2 < $cntArgs)
			{
				if (\is_array($aArgs[2]))
				{
					foreach ($aArgs[2] as $sName => $sValue)
					{
						$xuf->configure($sName, $sValue);
					}
				}
				else
				{
					$xuf->configure('include', $aArgs[2]);
				}
			}
			$this->aFunctions[] = $xuf;

			return $xuf->generateRequest($this->sXajaxPrefix);
		}

		throw new InvalidArgumentException('Wrong ParameterCount to register an Cms');
	}

	/*
		Function: processRequest

		Called by the <xajaxPluginManager> when a request needs to be
		processed.

		Returns:

		mixed - True when the request has been processed successfully.
			An error message when an error has occurred.
	*/

	protected function getMethodByIndex(?int $idx = null): Handler
	{
		if (\is_int($idx) && array_key_exists($idx, $this->aFunctions) && $this->aFunctions[$idx] instanceof Handler)
		{
			return $this->aFunctions[$idx];
		}
		throw new InvalidArgumentException(self::class . '::getFunctionByIndex(?int $idx = null) The function was not registered or is invalid');
	}

	/**
	 * Own Plugin Name
	 *
	 * @return string
	 * @since 7.0
	 */
	public function getName(): string
	{
		return 'cms';
	}
}