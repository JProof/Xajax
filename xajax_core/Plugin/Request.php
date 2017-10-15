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

namespace Xajax\Plugin;

/**
 * Class Request
 *
 * @package Xajax\Plugin
 */
abstract class Request extends Plugin
{
	/**
	 * Request constructor.
	 *
	 * @param string $pluginType
	 */
	public function __construct($pluginType = self::TYPE_REQUEST)
	{
		parent::__construct($pluginType);
	}

	/*
		Function: configure

		Called by the <xajaxPluginManager> when a configuration setting is changing.
		Plugins should store a local copy of the settings they wish to use during
		registration, client script generation or request processing.
	*/
	function configure($sName, $mValue)
	{
	}

	/*
		Function: register

		Called by the <xajaxPluginManager> when a user script when a function, event
		or callable object is to be registered.  Additional plugins may support other
		registration types.
	*/
	function register($aArgs)
	{
		return false;
	}

	function generateHash()
	{
	}

	/*
		Function: generateClientScript

		Called by <xajaxPluginManager> when the page's HTML is being sent to the browser.
		This allows each plugin to inject some script / style or other appropriate tags
		into the HEAD of the document.  Each block must be appropriately enclosed, meaning
		javascript code must be enclosed in SCRIPT and /SCRIPT tags.
	*/
	abstract public function generateClientScript(): string;

	/*
		Function: canProcessRequest

		Called by the <xajaxPluginManager> when a request has been received to determine
		if the request is for a xajax enabled function or for the initial page load.
	*/
	/**
	 * @return bool
	 */
	abstract public function canProcessRequest(): bool;

	/*
		Function: processRequest

		Called by the <xajaxPluginManager> when a request is being processed.  This
		will only occur when <xajax> has determined that the current request is a valid
		(registered) xajax enabled function via <xajax->canProcessRequest>.

		Returns:
			false
	*/
	abstract public function processRequest();
}