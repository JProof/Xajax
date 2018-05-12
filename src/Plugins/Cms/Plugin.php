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

namespace Jybrid\Plugins\Cms;

use InvalidArgumentException;
use Jybrid\Factory;
use Jybrid\Interfaces\IfaceButton;
use Jybrid\Interfaces\IfacePluginRequest;
use Jybrid\Interfaces\IfacePluginRequestRequest;
use Jybrid\Response\Manager;

/**
 * Class Plugin
 * The Class is the Stack for handling all Requests of type Cms
 *
 * @package Jybrid\Plugins\Cms
 */
class Plugin extends \Jybrid\Plugin\Request implements IfacePluginRequest {
	/**
	 * Own Plugin Name is important to check the Plugin was load(or not) in jybrid's Plugins stack
	 *
	 * @return string
	 * @since 7.0
	 */
	public function getName(): string {
		return 'cms';
	}
	/**
	 * Array: aFunctions
	 * An array of <jybridCms> object that are registered and
	 * available via a <jybrid.request> call.
	 */
	/**
	 * @var array
	 */
	protected $aFunctions;
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
	protected $isJybridRequest = false;
	/*
		Function: jybridFunctionPlugin

		Constructs and initializes the <jybridFunctionPlugin>.  The GET and POST
		data is searched for jybrid function call parameters.  This will later
		be used to determine if the request is for a registered function in
		<jybridFunctionPlugin->canProcessRequest>
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
		\Jybrid\Plugin\Manager::getInstance()->registerPlugin( $this );
	}

	/**
	 * @return \Jybrid\Plugins\Cms\Plugin
	 */
	public static function getInstance(): IfacePluginRequest
	{
		return self::$instance ?? self::$instance = new self();
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
		the page via the javascript <jybrid.request> call.
	*/

	/**
	 * Function: generateClientScript
	 * Called by the <jybridPluginManager> during the client script generation
	 * phase.  This is used to generate a block of javascript code that will
	 * contain function declarations that can be used on the browser through
	 * javascript to initiate jybrid requests.
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
	 * Called by the <jybridPluginManager> when a request needs to be
	 * processed.
	 * Returns:
	 * mixed - True when the request has been processed successfully.
	 * An error message when an error has occurred.
	 *
	 * @return bool
	 */
	public function processRequest(): bool {
		$return = false;
		if ( $this->canProcessRequest() ) {
			$objResponseManager = Manager::getInstance();

			$objResponseManager->append( Factory::getResponseInstance() );
			$return = true;
		}

		return $return;
	}

	/**
	 * Getting an registered RequestRequest Object or create it if not exists
	 *
	 * @param string                                                  $jsName
	 * @param iterable|\Jybrid\Interfaces\IfaceRequestParameters|null $configure
	 *
	 * @return IfacePluginRequestRequest
	 * @since 0.7.3
	 */
	public static function registerRequest( string $jsName, $configure = null ): ?IfacePluginRequestRequest {
		$instance = self::getInstance();

		// todo check modify $jsName
		return $instance->aFunctions[ $jsName ] ?? $instance->createRequest( $jsName, $configure );
	}

	/**
	 * @param string                                     $jsName
	 * @param  \Jybrid\Interfaces\IfaceRequestParameters $configure
	 *
	 * @return IfacePluginRequestRequest
	 */
	protected function createRequest( string $jsName, $configure = null ): IfacePluginRequestRequest {
		return $this->aFunctions[ $jsName ] = new RequestRequest( $jsName, $configure );
	}

	/**
	 * @param null|string $idx
	 *
	 * @todo check if possible in parent class
	 * @return \Jybrid\Plugins\Cms\RequestRequest
	 */
	protected function getMethodByIndex( ?string $idx = null ): RequestRequest {
		if ( \is_string( $idx ) && array_key_exists( $idx, $this->aFunctions ) && $this->aFunctions[ $idx ] instanceof RequestRequest )
		{
			return $this->aFunctions[$idx];
		}
		throw new InvalidArgumentException(self::class . '::getFunctionByIndex(?string $idx = null) The function was not registered or is invalid');
	}

	/**
	 * @param iterable|null $configure
	 *
	 * @return IfaceButton
	 */
	public function getButtonScript( ?iterable $configure = null ): IfaceButton {
		// TODO: Implement getButtonScript() method.
	}

	/**
	 * Try to get an already registered Request Object
	 *
	 * @param string $jsName
	 *
	 * @return \Jybrid\Interfaces\IfacePluginRequestRequest|null
	 * @since 0.7.8 more convenient Button-Handling
	 */
	public static function getRequestObject( string $jsName ): ?IfacePluginRequestRequest {
		return ( ( $registeredRequestObject = self::getInstance()->aFunctions[ $jsName ] ) instanceof RequestRequest ) ? $registeredRequestObject : null;
	}
}