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

use Xajax\Language;

/**
 * Class Response
 *
 * @package Xajax\Plugin
 */
abstract class Response extends Plugin
{
	use \Xajax\Errors\Call;

	/*
		Object: objResponse

		A reference to the current <xajaxResponse> object that is being used
		to build the response that will be sent to the client browser.
	*/
	/**
	 * @var
	 */
	var $objResponse;
	/*
		Function: setResponse

		Called by the <xajaxResponse> object that is currently being used
		to build the response that will be sent to the client browser.

		Parameters:

		objResponse - (object):  A reference to the <xajaxResponse> object
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
		will call <xajaxResponse->addPluginCommand> using the reference provided
		in <xajaxResponsePlugin->setResponse>.
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

		Called by the <xajaxPluginManager> when the user script requests a plugin.
		This name must match the plugin name requested in the called to
		<xajaxResponse->plugin>.
	*/
	/**
	 *
	 */
	function getName()
	{
//SkipDebug
		$objLanguageManager = Language::getInstance();
		trigger_error(
		    $objLanguageManager->getText('XJXPLG:GNERR:01')
		    , E_USER_ERROR
		);
//EndSkipDebug
	}

	/*
		Function: process

		Called by <xajaxResponse> when a user script requests the service of a
		response plugin.  The parameters provided by the user will be used to
		determine which response command and parameters will be sent to the
		client upon completion of the xajax request process.
	*/
	/**
	 *
	 */
	function process()
	{
//SkipDebug
		$objLanguageManager = Language::getInstance();
		trigger_error(
		    $objLanguageManager->getText('XJXPLG:PERR:01')
		    , E_USER_ERROR
		);
//EndSkipDebug
	}
}