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

namespace Jybrid\Plugin;

use Jybrid\Errors\TraitCall;
use Jybrid\Factory;
use Jybrid\Interfaces\IfacePluginRequestRequest;

/**
 * Class RequestRequest
 *
 * @package Jybrid\Plugin
 */
abstract class Request extends Plugin
{
	use TraitCall;
	/**
	 * @var
	 */
	static protected $instance;

	/**
	 * RequestRequest constructor.
	 *
	 * @param string $pluginType
	 */
	protected function __construct($pluginType = self::TYPE_REQUEST)
	{
		parent::__construct($pluginType);
	}

	/**
	 * RequestRequest Plugin can check the request was against it
	 *
	 * @return bool
	 */
	public function isRequestAgainstPlugin(): bool
	{
		return Factory::getInput()->getString( 'jybreq', '' ) === $this->getName();
	}


	/*
		Function: register

		Called by the <jybridPluginManager> when a user script when a function, event
		or callable object is to be registered.  Additional plugins may support other
		registration types.
	*/
	/**
	 * @param $aArgs
	 *
	 * @return bool
	 */
	function register($aArgs)
	{
		return false;
	}

	/**
	 *
	 */
	function generateHash()
	{
	}

	/*
		Function: generateClientScript

		Called by <jybridPluginManager> when the page's HTML is being sent to the browser.
		This allows each plugin to inject some script / style or other appropriate tags
		into the HEAD of the document.  Each block must be appropriately enclosed, meaning
		javascript code must be enclosed in SCRIPT and /SCRIPT tags.
	*/
	/**
	 * @return string
	 */
	abstract public function generateClientScript(): string;

	/*
		Function: canProcessRequest

		Called by the <jybridPluginManager> when a request has been received to determine
		if the request is for a jybrid enabled function or for the initial page load.
	*/
	/**
	 * @return bool
	 */
	abstract public function canProcessRequest(): bool;

	/*
		Function: processRequest

		Called by the <jybridPluginManager> when a request is being processed.  This
		will only occur when <jybrid> has determined that the current request is a valid
		(registered) jybrid enabled function via <jybrid->canProcessRequest>.

		Returns:
			false
	*/
	/**
	 * @return mixed
	 */
	abstract public function processRequest();

	/**
	 * Getting the Internal/External Name of the Plugin
	 * which is need for detecting Requests against an Plugin
	 *
	 * @return string
	 */
	abstract public function getName(): string;

	/**
	 * Getting Access to an RequestRequest-Object.
	 * It auto-registers the plugin if it was not load before
	 *
	 * @param string                                                     $jsName
	 * @param iterable|\Jybrid\Interfaces\IfacePluginRequestRequest|null $configure
	 *
	 * @return IfacePluginRequestRequest|null
	 */
	abstract public static function registerRequest( string $jsName, $configure = null ): ?IfacePluginRequestRequest;

	/**
	 * Try to get an already registered Request Object
	 *
	 * @param string $jsName
	 *
	 * @return \Jybrid\Interfaces\IfacePluginRequestRequest|null
	 * @since 0.7.8 more convenient Button-Handling
	 */
	abstract public static function getRequestObject( string $jsName ): ?IfacePluginRequestRequest;
}