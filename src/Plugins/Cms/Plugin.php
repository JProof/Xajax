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
use Xajax\Plugin\Request\RequestPluginIface;
use Xajax\Response\Manager;

/**
 * Class Plugin
 * Hold all Cms Plugin Instances
 *
 * @package Xajax\Plugins\Cms
 */
class Plugin extends \Xajax\Plugin\Request implements RequestPluginIface
{
	/*
		Array: aFunctions

		An array of <xajaxCms> object that are registered and
		available via a <xajax.request> call.
	*/
	/**
	 * @var array
	 */
	protected $aFunctions;
	/*
		String: sXajaxPrefix

		A configuration setting that is stored locally and used during
		the client script generation phase.
	*/

	/*
		String: sDefer

		Configuration option that can be used to request that the
		javascript file is loaded after the page has been fully loaded.
	*/
	/**
	 * @var
	 */
	protected $sDefer;
	/**
	 * @var
	 */
	protected $bDeferScriptGeneration;
	/*
		String: sRequestedFunction

		This string is used to temporarily hold the name of the function
		that is being requested (during the request processing phase).

		Since canProcessRequest loads this value from the get or post
		data, it is unnecessary to load it again.
	*/
	/**
	 * @var
	 */
	protected $sRequestedFunction;
	/**
	 * @var bool
	 */
	protected $isXajaxRequest = false;
	/*
		Function: xajaxFunctionPlugin

		Constructs and initializes the <xajaxFunctionPlugin>.  The GET and POST
		data is searched for xajax function call parameters.  This will later
		be used to determine if the request is for a registered function in
		<xajaxFunctionPlugin->canProcessRequest>
	*/
	/**
	 * Plugin constructor.
	 */
	protected function __construct()
	{
		// populate which type the current plugin is
		parent::__construct(self::getRequestType());
		$this->aFunctions = [];

		// Autoregister this Plugin on construction
		\Xajax\Plugin\Manager::getInstance()->registerPlugin($this);
	}

	/**
	 * @return \Xajax\Plugins\Cms\Plugin
	 */
	public static function getInstance(): Plugin
	{
		return self::$instance ?? self::$instance = new self();
	}
	/*
		Function: configure

		Sets/stores configuration options used by this plugin.
	*/
	/**
	 * @param $sName
	 * @param $mValue
	 *
	 * @deprecated
	 */
	public function configure($sName, $mValue)
	{

	}

	/**
	 * @return string
	 */
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

	/**
	 * Function: generateClientScript
	 * Called by the <xajaxPluginManager> during the client script generation
	 * phase.  This is used to generate a block of javascript code that will
	 * contain function declarations that can be used on the browser through
	 * javascript to initiate xajax requests.
	 *
	 * @return string
	 */
	public function generateClientScript(): string
	{
		$script = '';
		if (0 < \count($this->aFunctions))
		{
			foreach (array_keys($this->aFunctions) as $sKey)
			{
				$script .= $this->getMethodByIndex($sKey)->generateClientScript();
			}
		}

		return $script;
	}

	/**
	 * Getting an registered Request Object or create it if not exists
	 *
	 * @param string $jsName
	 * @param        $configure
	 *
	 * @return \Xajax\Plugins\Cms\Request
	 * @since 0.7.3
	 */
	public static function getRequest(string $jsName, ?iterable $configure = null): Request
	{

		$instance = self::getInstance();
		// todo check modify $jsName
		return $instance->aFunctions[$jsName] ?? $instance->createRequest($jsName, $configure);
	}

	/**
	 * @param string $jsName
	 * @param        $configure
	 *
	 * @return \Xajax\Plugins\Cms\Request
	 */
	protected function createRequest(string $jsName, $configure = null): Request
	{
		return $this->aFunctions[$jsName] = new Request($jsName, $configure);
	}

	/**
	 * Function: canProcessRequest
	 * Determines whether or not the current request can be processed
	 * by this plugin.
	 * Returns:
	 * boolean - True if the current request can be handled by this plugin;
	 * false otherwise.
	 *
	 * @return bool
	 */
	public function canProcessRequest(): bool
	{
		return Factory::isCmsRequest();
	}

	/**
	 * Function: processRequest
	 * Called by the <xajaxPluginManager> when a request needs to be
	 * processed.
	 * Returns:
	 * mixed - True when the request has been processed successfully.
	 * An error message when an error has occurred.
	 *
	 * @return bool|mixed
	 */
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

	/**
	 * @param null|string $idx
	 *
	 * @todo check if possible in parent class
	 * @return \Xajax\Plugins\Cms\Request
	 */
	protected function getMethodByIndex(?string $idx = null): Request
	{
		if (\is_string($idx) && array_key_exists($idx, $this->aFunctions) && $this->aFunctions[$idx] instanceof Request)
		{
			return $this->aFunctions[$idx];
		}
		throw new InvalidArgumentException(self::class . '::getFunctionByIndex(?string $idx = null) The function was not registered or is invalid');
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