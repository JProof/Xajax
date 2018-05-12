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
 * @since              21.09.2017
 */

declare(strict_types=1);

namespace Jybrid;

use Jybrid\Emitter\Core;
use Jybrid\Header\PhpRequest;
use Jybrid\Interfaces\IfacePluginRequestRequest;
use Jybrid\Plugin\Request;
use Jybrid\Response\Response;
use Jybrid\Scripts\Generator;
use Jybrid\Scripts\Scripts;
use Jybrid\Snippets\Snippets;

/**
 * Class Factory
 *
 * @package Jybrid
 */
class Factory
{
	use \Jybrid\Errors\TraitCall;
	/**
	 * Jybrid Instances
	 *
	 * @var array
	 */
	private static $instances = [];
	/**
	 * Is an RequestRequest against Jybrid
	 *
	 * @var bool
	 * @since 0.7.3
	 */
	private static $isJybridRequest;
	/**
	 * @since 0.7.3 During the detect jybrid request method the target-jybrid-plugin will be automatically load(currently only cms)
	 * @var bool
	 */
	private static $requestAgainstPlugin;
	/**
	 * @var
	 */
	private static $input;
	/**
	 * @var Core
	 */
	private static $emitters;
	/**
	 * @var Snippets
	 */
	private static $snippets;
	/**
	 * Headers sent during an Ajax request
	 *
	 * @var \Jybrid\Header\PhpRequest
	 * @since 0.7.8 Global Headers for all jybrid requests can be set
	 */
	private static $headers;

	/**
	 * Factory constructor.
	 */
	protected function __construct()
	{
	}

	/**
	 * Getting Access to Jybrid
	 *
	 * @param string $instance instanceName is necessary
	 *
	 * @return \Jybrid\Jybrid
	 */
	public static function getInstance( string $instance = null ): \Jybrid\Jybrid
	{
		// todo errors and logger on less instanceName
		// todo clearing evil name stuff if need
		$instances = self::getInstances();
		if ( array_key_exists( $instance, $instances ) && ( $foundInstance = $instances[ $instance ] ) instanceof \Jybrid\Jybrid )
		{
			return $foundInstance;
		}
		$instances[ $instance ] = self::createJybrid();
		self::setInstances($instances);

		return $instances[$instance];
	}

	/**
	 * Getting the Script-Handler
	 *
	 * @return \Jybrid\Scripts\Scripts
	 */
	public static function getScripts(): \Jybrid\Scripts\Scripts
	{
		return Scripts::getInstance();
	}

	/**
	 * @return \Jybrid\Jybrid
	 */
	private static function createJybrid(): \Jybrid\Jybrid {
		return new \Jybrid\Jybrid();
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
	 * Jybrid RequestRequest Parameters Class to handle Post Server Get RequestRequest Vars cleanly
	 *
	 * @param null|string $method
	 *
	 * @return \Jybrid\Input\Parameter
	 */
	public static function getInput(?string $method = null): Input\Parameter
	{
		$input = self::$input ?? self::$input = new Input\Input();

		return $input->getInput($method);
	}

	/**
	 * Simple detection request was send against Jybrid
	 *
	 * @return bool
	 */
	public static function isJybridRequest(): bool
	{
		// todo case: you have an file with responses and an request was send by browser against this file, maybe the request should not to be handle
		if ( \is_bool( self::$isJybridRequest ) ) {
			return self::$isJybridRequest;
		}
		if ( false !== ( self::$isJybridRequest = self::getInput( 'request' )->getBool( 'jybreq', false ) ) ) {
			self::detectJybridRequestPlugin( self::getInput( 'request' )->getString( 'jybreq', '' ) );
		}

		return self::$isJybridRequest;
	}

	/**
	 * Method to check the calls was an new Cms Jybrid Call
	 *
	 * @since 0.7.3
	 * @return bool
	 */
	public static function isCmsRequest(): bool
	{
		return self::isJybridRequest() && self::$requestAgainstPlugin === 'cms';
	}

	/**
	 * Can be set if the RequestRequest came not from Jybrid but you will use the response-processor from jybrid to give back the response
	 *
	 * @param bool|null $is
	 *
	 * @return bool
	 */
	public static function setJybridRequest( ?bool $is = null ): bool {
		return self::$isJybridRequest = (bool) $is;
	}

	/**
	 * @since 0.7.3
	 * @todo  check more parameters or insert methods for check cms RequestRequest
	 *
	 * @param string $pluginName
	 *
	 * @return null|string
	 */
	private static function detectJybridRequestPlugin( string $pluginName ): ?string {
		// todo check security to auto-allow register plugin

		return ( self::getInstance()->getRequestPlugin( $pluginName ) instanceof Request ) ? self::$requestAgainstPlugin = $pluginName : null;
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

	/**
	 * Replacement of the old $xajax->register Method.
	 *
	 * @param string                                                  $pluginName         Name of the Plugin 'cms' or 'userfunction' or your own
	 *                                                                                    plugin
	 * @param string                                                  $jsMethodName       Js-MethodName to call via jybrid.Exe('js-MethodName')
	 *                                                                                    in Browser-Script to execute an RequestRequest
	 * @param iterable|\Jybrid\Interfaces\IfaceRequestParameters|null $configure          Configuring the RequestRequest with parameters if need
	 *
	 * @return IfacePluginRequestRequest
	 */
	public static function registerRequest( string $pluginName, string $jsMethodName, $configure = null ): ?IfacePluginRequestRequest {
		try {
			$plugin = self::getInstance()->getRequestPlugin( $pluginName );
		}
		catch ( \RuntimeException $exception ) {
			// todo log
		}
		if ( $plugin ) {
			return $plugin->registerRequest( $jsMethodName, $configure );
		}

		// todo error handling
		return null;
	}

	/**
	 * Try to get an already registered Request object
	 *
	 * @param string $pluginName
	 * @param string $jsMethodName
	 *
	 * @since 0.7.8 more convenient Button-Handling
	 * @return \Jybrid\Interfaces\IfacePluginRequestRequest|null
	 */
	public static function getRequestObject( string $pluginName, string $jsMethodName ): ?IfacePluginRequestRequest {
		try {
			/** @var \Jybrid\Plugin\Request $plugin */
			$plugin = self::getInstance()->getRequestPlugin( $pluginName );
		}
		catch ( \RuntimeException $exception ) {
			// todo log
		}

		return ( $plugin instanceof \Jybrid\Plugin\Request && ( $registeredRequestObject = $plugin->getRequestObject( $jsMethodName ) ) ) ? $registeredRequestObject : null;
	}

	/**
	 * Getting the Core Emitter for initial PageLoad
	 *
	 * @since 0.7.4 Emitters-Update
	 * @return Core
	 */
	public static function getEmitters(): Core {
		return self::$emitters ?? self::$emitters = new Core();
	}

	/**
	 * Snippets are small pieces of Javascript they will be rendered on initial page-load into the script tag
	 *
	 * @since 0.7.5 Javascript-Snippet-Update
	 * @return Snippets
	 */
	public static function getSnippets(): Snippets {
		return self::$snippets ?? self::$snippets = new Snippets();
	}

	/**
	 * Headers sent during an Ajax request
	 *
	 * @since 0.7.8 Global Headers for all jybrid requests can be set
	 * @return \Jybrid\Header\PhpRequest
	 */
	public static function getHeaders(): PhpRequest {
		return self::$headers ?? self::$headers = new PhpRequest();
	}
}