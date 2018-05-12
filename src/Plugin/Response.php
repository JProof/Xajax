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

use Jybrid\Language;

/**
 * Class Response
 *
 * @package Jybrid\Plugin
 */
abstract class Response extends Plugin
{
	use \Jybrid\Errors\TraitCall;

	/*
		Object: objResponse

		A reference to the current <jybridResponse> object that is being used
		to build the response that will be sent to the client browser.
	*/
	/**
	 * @var
	 */
	var $objResponse;
	/*
		Function: setResponse

		Called by the <jybridResponse> object that is currently being used
		to build the response that will be sent to the client browser.

		Parameters:

		objResponse - (object):  A reference to the <jybridResponse> object
	*/
	/**
	 * @param $objResponse
	 */
	function setResponse($objResponse)
	{
		$this->objResponse = $objResponse;
	}

	/*
		Function: addCommand

		Used internally to add a command to the response command list.  This
		will call <jybridResponse->addPluginCommand> using the reference provided
		in <jybridResponsePlugin->setResponse>.
	*/
	/**
	 * @param $aAttributes
	 * @param $sData
	 */
	function addCommand($aAttributes, $sData)
	{
		$this->objResponse->addPluginCommand($this, $aAttributes, $sData);
	}

	/*
		Function: getName

		Called by the <jybridPluginManager> when the user script requests a plugin.
		This name must match the plugin name requested in the called to
		<jybridResponse->plugin>.
	*/
	/**
	 *
	 */
	function getName()
	{
//SkipDebug
		$objLanguageManager = Language::getInstance();
		trigger_error(
			$objLanguageManager->getText( 'JYBPLG:GNERR:01' )
		    , E_USER_ERROR
		);
//EndSkipDebug
	}

	/*
		Function: process

		Called by <jybridResponse> when a user script requests the service of a
		response plugin.  The parameters provided by the user will be used to
		determine which response command and parameters will be sent to the
		client upon completion of the jybrid request process.
	*/
	/**
	 *
	 */
	function process()
	{
//SkipDebug
		$objLanguageManager = Language::getInstance();
		trigger_error(
			$objLanguageManager->getText( 'JYBPLG:PERR:01' )
		    , E_USER_ERROR
		);
//EndSkipDebug
	}
}