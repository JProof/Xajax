<?php
declare(strict_types=1);

namespace Xajax;

//error_reporting(E_STRICT | E_ERROR | E_WARNING | E_PARSE);
/*
	File: xajax.inc.php

	Main xajax class and setup file.

	Title: xajax class

	Please see <copyright.inc.php> for a detailed description, copyright
	and license information.
*/

/*
	@package xajax
	@version $Id: xajax.inc.php 362 2007-05-29 15:32:24Z calltoconstruct $
	@copyright Copyright (c) 2005-2007 by Jared White & J. Max Wilson
	@copyright Copyright (c) 2008-2010 by Joseph Woolley, Steffen Konerow, Jared White  & J. Max Wilson
	@license http://www.xajaxproject.org/bsd_license.txt BSD License
*/

/*
	Section: Standard Definitions
*/

/*
	String: XAJAX_DEFAULT_CHAR_ENCODING

	Default character encoding used by both the <xajax> and
	<xajaxResponse> classes.
*/

use RuntimeException;
use Xajax\Configuration\Config;
use Xajax\Errors\Handler;
use Xajax\Plugin\Manager;
use Xajax\Plugin\Plugin;
use Xajax\Response\Response;
use Xajax\Scripts\Generator;

if (!defined('XAJAX_DEFAULT_CHAR_ENCODING'))
{
	/**@deprecated use the Xajax\Config::getDefaultCharacterEncoding* */
	define('XAJAX_DEFAULT_CHAR_ENCODING', 'utf-8');
}

/*
	String: XAJAX_PROCESSING_EVENT
	String: XAJAX_PROCESSING_EVENT_BEFORE
	String: XAJAX_PROCESSING_EVENT_AFTER
	String: XAJAX_PROCESSING_EVENT_INVALID

	Identifiers used to register processing events.  Processing events are essentially
	hooks into the xajax Core that can be used to add functionality into the request
	processing sequence.
*/
if (!defined('XAJAX_PROCESSING_EVENT'))
{
	/**
	 *
	 */
	define('XAJAX_PROCESSING_EVENT', 'xajax processing event');
}
if (!defined('XAJAX_PROCESSING_EVENT_BEFORE'))
{
	/**
	 *
	 */
	define('XAJAX_PROCESSING_EVENT_BEFORE', 'beforeProcessing');
}
if (!defined('XAJAX_PROCESSING_EVENT_AFTER'))
{
	/**
	 *
	 */
	define('XAJAX_PROCESSING_EVENT_AFTER', 'afterProcessing');
}
if (!defined('XAJAX_PROCESSING_EVENT_INVALID'))
{
	/**
	 *
	 */
	define('XAJAX_PROCESSING_EVENT_INVALID', 'invalidRequest');
}

/*
	Class: xajax

	The xajax class uses a modular plug-in system to facilitate the processing
	of special Ajax requests made by a PHP page.  It generates Javascript that
	the page must include in order to make requests.  It handles the output
	of response commands (see <xajaxResponse>).  Many flags and settings can be
	adjusted to effect the behavior of the xajax class as well as the client-side
	javascript.
*/

/**
 * Class Xajax
 * Refactured Xajax Main Class
 *
 * @package Xajax
 */
class Xajax
{
	// temporarily Helper Method
	use Config;
	use \Xajax\Errors\Call;

	/*
		Array: aSettings
		
		This array is used to store all the configuration settings that are set during
		the run of the script.  This provides a single data store for the settings
		in case we need to return the value of a configuration option for some reason.
		
		It is advised that individual plugins store a local copy of the settings they
		wish to track, however, settings are available via a reference to the <xajax> 
		object using <xajax->getConfiguration>.

	*/
	/**
	 * @deprecated jproof/xajax 0.7.2 use the Config Class
	 * @see        Config
	 **/
	private $aSettings = [];
	/*
		Boolean: bErrorHandler
		
		This is a configuration setting that the main xajax object tracks.  It is used
		to enable an error handler function which will trap php errors and return them
		to the client as part of the response.  The client can then display the errors
		to the user if so desired.
	*/
	/**
	 * @var bool
	 * @deprecated jproof/xajax 0.7.2 use the Config Class
	 */
	private $bErrorHandler;
	/*
		Array: aProcessingEvents
		
		Stores the processing event handlers that have been assigned during this run
		of the script.
	*/
	/**
	 * @var array
	 */
	private $aProcessingEvents;
	/*
		Boolean: bExitAllowed
		
		A configuration option that is tracked by the main <xajax>object.  Setting this
		to true allows <xajax> to exit immediatly after processing a xajax request.  If
		this is set to false, xajax will allow the remaining code and HTML to be sent
		as part of the response.  Typically this would result in an error, however, 
		a response processor on the client side could be designed to handle this condition.
	*/
	/**
	 * @var bool
	 * @deprecated jproof/xajax 0.7.2 use the Config Class
	 */
	private $bExitAllowed;
	/*
		Boolean: bCleanBuffer
		
		A configuration option that is tracked by the main <xajax> object.  Setting this
		to true allows <xajax> to clear out any pending output buffers so that the 
		<xajaxResponse> is (virtually) the only output when handling a request.
	*/
	/**
	 * @var bool
	 * @deprecated jproof/xajax 0.7.2 use the Config Class
	 */
	private $bCleanBuffer;
	/*
		String: sLogFile
	
		A configuration setting tracked by the main <xajax> object.  Set the name of the
		file on the server that you wish to have php error messages written to during
		the processing of <xajax> requests.	
	*/
	/**
	 * @var string
	 * @deprecated jproof/xajax 0.7.2 use the Config Class
	 */
	private $sLogFile;
	/*
		String: sCoreIncludeOutput
		
		This is populated with any errors or warnings produced while including the xajax
		Core components.  This is useful for debugging Core updates.
	*/
	/**
	 * @deprecated never used
	 **/
	private $sCoreIncludeOutput;
	/*
		Object: objPluginManager
		
		This stores a reference to the global <xajaxPluginManager>
	*/
	/**
	 * @var \xajaxPluginManager
	 */
	private $objPluginManager;
	/*
		Object: objArgumentManager
		
		Stores a reference to the global <xajaxArgumentManager>
	*/
	/**
	 * @var \xajaxArgumentManager
	 */
	private $objArgumentManager;
	/*
		Object: objResponseManager
		
		Stores a reference to the global <xajaxResponseManager>
	*/
	/**
	 * @var \Xajax\Response\Manager
	 */
	private $objResponseManager;
	/*
		Object: objLanguageManager
		
		Stores a reference to the global <Language>
	*/
	/**
	 * @var \Xajax\Language
	 **/
	private $objLanguageManager;
	/**
	 * @var
	 */
	private $challengeResponse;
	/*
		Constructor: xajax

		Constructs a xajax instance and initializes the plugin system.
		
		Parameters:

		sRequestURI - (optional):  The <xajax->sRequestURI> to be used
			for calls back to the server.  If empty, xajax fills in the current
			URI that initiated this request.
	*/
	/**
	 * xajax constructor.
	 *
	 * @param array $configuration
	 */
	public function __construct(array $configuration = null)
	{
		$this->bErrorHandler     = false;
		$this->aProcessingEvents = [];
		$this->bExitAllowed      = true;
		$this->bCleanBuffer      = true;
		$this->sLogFile          = '';

		$this->__wakeup();

		// The default configuration settings.
		$this->configureMany(
		    [
		        'characterEncoding'     => XAJAX_DEFAULT_CHAR_ENCODING,
		        'decodeUTF8Input'       => false,
		        'outputEntities'        => false,
		        'responseType'          => 'JSON',
		        'wrapperPrefix'         => 'xajax_',
		        'exitAllowed'           => true,
		        'errorHandler'          => null,
		        'cleanBuffer'           => false,
		        'allowBlankResponse'    => false,
		        'allowAllResponseTypes' => false,
		        'generateStubs'         => true,
		        'logFile'               => '',
		        'timeout'               => 6000,

		    ]
		);

		if (\is_array($configuration))
		{

			if (array_key_exists('sRequestURI', $configuration) && null !== ($sRequestURI = $configuration['sRequestURI']))
			{
				$this->configure('requestURI', $sRequestURI);
			}
			else
			{
				$this->configure('requestURI', $this->_detectURI());
			}
			if (array_key_exists('language', $configuration) && null !== ($sLanguage = $configuration['language']))
			{
				$this->configure('language', $sLanguage);
			}

			if ('utf-8' !== XAJAX_DEFAULT_CHAR_ENCODING)
			{
				$this->configure('decodeUTF8Input', true);
			}
		}
	}

	/*
		Function: __sleep
	*/
	/**
	 * @return array
	 */
	public function __sleep()
	{
		$aMembers = get_class_vars(get_class($this));

		if (isset($aMembers['objLanguageManager']))
		{
			unset($aMembers['objLanguageManager']);
		}

		if (isset($aMembers['objPluginManager']))
		{
			unset($aMembers['objPluginManager']);
		}

		if (isset($aMembers['objArgumentManager']))
		{
			unset($aMembers['objArgumentManager']);
		}

		if (isset($aMembers['objResponseManager']))
		{
			unset($aMembers['objResponseManager']);
		}

		if (isset($aMembers['sCoreIncludeOutput']))
		{
			unset($aMembers['sCoreIncludeOutput']);
		}

		return array_keys($aMembers);
	}

	/*
		Function: __wakeup
	*/
	/**
	 *
	 */
	public function __wakeup()
	{


		$sLocalFolder = __DIR__;

//SkipAIO
		/**@deprecated  load via composer
		 * require_once $sLocalFolder . '/xajaxPluginManager.inc.php';
		 * require_once $sLocalFolder . '/Language.inc.php';
		 * require_once $sLocalFolder . '/xajaxArgumentManager.inc.php';
		 * require_once $sLocalFolder . '/xajaxResponseManager.inc.php';
		 * require_once $sLocalFolder . '/xajaxRequest.inc.php';
		 * require_once $sLocalFolder . '/xajaxResponse.inc.php';* */
//EndSkipAIO

		// this is the list of folders where xajax will look for plugins
		// that will be automatically included at startup.
		$aPluginFolders   = [];
		$aPluginFolders[] = dirname($sLocalFolder) . '/xajax_plugins';

//SkipAIO
		$aPluginFolders[] = $sLocalFolder . '/plugin_layer';
//EndSkipAIO

		// Setup plugin manager
		//$this->objPluginManager = xajaxPluginManager::getInstance();
		$this->getObjPluginManager()->loadPlugins($aPluginFolders);

		$this->objLanguageManager = Language::getInstance();
		$this->objArgumentManager = Argument::getInstance();
		$this->objResponseManager = \Xajax\Response\Manager::getInstance();

		$this->configureMany($this->aSettings);
	}

	/*
		Function: getGlobalResponse

		Returns the <xajaxResponse> object preconfigured with the encoding
		and entity settings from this instance of <xajax>.  This is used
		for singleton-pattern response development.

		Returns:

		<xajaxResponse> : A <xajaxResponse> object which can be used to return
			response commands.  See also the <xajaxResponseManager> class.
	*/
	/**
	 * @return \Xajax\Response\Response
	 */
	public static function getGlobalResponse(): Response
	{
		return Response::getInstance();
	}

	/*
		Function: getVersion

		Returns:

		string : The current xajax version.
	*/
	/**
	 * @return string
	 */
	public static function getVersion(): string
	{
		return 'xajax 0.7.2';
	}


	/*
		Function: register

		Call this function to register request handlers, including functions,
		callable objects and events.  New plugins can be added that support
		additional registration methods and request processors.


		Parameters:

		$sType - (string): Type of request handler being registered; standard
			options include:
				XAJAX_FUNCTION: a function declared at global scope.
				XAJAX_CALLABLE_OBJECT: an object who's methods are to be registered.
				XAJAX_EVENT: an event which will cause zero or more event handlers
					to be called.
				XAJAX_EVENT_HANDLER: register an event handler function.

		$sFunction || $objObject || $sEvent - (mixed):
			when registering a function, this is the name of the function
			when registering a callable object, this is the object being registered
			when registering an event or event handler, this is the name of the event

		$sIncludeFile || $aCallOptions || $sEventHandler
			when registering a function, this is the (optional) include file.
			when registering a callable object, this is an (optional) array
				of call options for the functions being registered.
			when registering an event handler, this is the name of the function.
	*/
	/**
	 * @param $sType
	 * @param $mArg
	 *
	 * @return bool|\xajaxRequest
	 * @deprecated use registerRequest or direct Registering in
	 */
	public function register($sType, $mArg)
	{
		$aArgs = func_get_args();
		$nArgs = func_num_args();

		if (2 < $nArgs)
		{
			if (XAJAX_PROCESSING_EVENT == $aArgs[0])
			{
				$sEvent = $aArgs[1];
				$xuf    = $aArgs[2];

				if (false == is_a($xuf, 'xajaxUserFunction'))
				{
					$xuf = new xajaxUserFunction($xuf);
				}

				$this->aProcessingEvents[$sEvent] = $xuf;

				return true;
			}
		}

		return $this->objPluginManager->register($aArgs);
	}

	/*
		Function: configure
		
		Call this function to set options that will effect the processing of 
		xajax requests.  Config settings can be specific to the xajax
		Core, request processor plugins and response plugins.


		Parameters:
		
		Options include:
			javascript URI - (string): The path to the folder that contains the 
				xajax javascript files.
			errorHandler - (boolean): true to enable the xajax error handler, see
				<xajax->bErrorHandler>
			exitAllowed - (boolean): true to allow xajax to exit after processing
				a request.  See <xajax->bExitAllowed> for more information.
	*/
	/**
	 * @param $sName
	 * @param $mValue
	 *
	 * @deprecated old Config use @see
	 */
	public function configure($sName, $mValue)
	{

		$isBoolValue = is_bool($mValue);
		if ('exitAllowed' === $sName && $isBoolValue)
		{

			$this->bExitAllowed = $mValue;
		}
		else if ('cleanBuffer' === $sName && $isBoolValue)
		{
			$this->bCleanBuffer = $mValue;
		}
		else if ('logFile' === $sName)
		{
			$this->sLogFile = $mValue;
		}

		$this->objLanguageManager->configure($sName, $mValue);
		$this->objArgumentManager->configure($sName, $mValue);
		$this->getObjPluginManager()->configure($sName, $mValue);
		$this->getObjResponseManager()->configure($sName, $mValue);

		$this->getConfig()->{$sName} = $mValue;

		$this->aSettings[$sName] = $mValue;
	}

	/*
		Function: configureMany
		
		Set an array of configuration options.

		Parameters:
		
		$aOptions - (array): Associative array of configuration settings
	*/
	/**
	 * @param $aOptions
	 */
	public function configureMany($aOptions)
	{
		foreach ($aOptions as $sName => $mValue)
		{
			$this->configure($sName, $mValue);
		}
	}

	/*
		Function: getConfiguration
		
		Get the current value of a configuration setting that was previously set
		via <xajax->configure> or <xajax->configureMany>

		Parameters:
		
		$sName - (string): The name of the configuration setting
				
		Returns:
		
		$mValue : (mixed):  The value of the setting if set, null otherwise.
	*/
	/**
	 * @param $sName
	 *
	 * @return mixed|null
	 */
	public function getConfiguration($sName)
	{
		if (isset($this->aSettings[$sName]))
		{
			return $this->aSettings[$sName];
		}

		return null;
	}

	/**
	 * xajaxPluginManager getter
	 *
	 * @return Manager
	 * @since 7.0
	 */
	public function getObjPluginManager(): Manager
	{
		return $this->objPluginManager instanceof Manager ? $this->objPluginManager : $this->setObjPluginManager(Manager::getInstance());
	}

	/**
	 * xajaxPluginManager Setter
	 *
	 * @since 7.0
	 *
	 * @param Manager $objPluginManager
	 *
	 * @return Manager
	 */
	private function setObjPluginManager(Manager $objPluginManager): Manager
	{
		return $this->objPluginManager = $objPluginManager;
	}

	/**
	 * Public Getter of an Plugin
	 *
	 * @param null|string $plgName Userfunction or Custom RequestPlugin
	 *
	 * @return  \Xajax\Plugin\Request
	 */
	public function getRequestPlugin(?string $plgName = null): ?\Xajax\Plugin\Request
	{
		$instance   = null;
		$reqPlugins = $this->getObjPluginManager()->getRequestPlugins();
		if ($reqPlugins->offsetExists($plgName))
		{

			try
			{
				$instance = $reqPlugins->offsetGet($plgName)->getPluginInstance();
			}
			catch (RuntimeException$exception)
			{
				var_dump($exception);
			}
		}
		else
		{
			$instance = $this->autoregisterRequestPlugin($plgName);
		}

		return $instance;
	}

	/**
	 * @param null|string $plgName
	 *
	 * @return null|\Xajax\Plugin\Request
	 */
	protected function autoregisterRequestPlugin(?string $plgName = null): ?\Xajax\Plugin\Request
	{
		$plgObject      = $this->loadPlugin($plgName, Plugin::getRequestType());
		$plugin         = $this->getObjPluginManager()->registerPlugin($plgObject);
		$pluginInstance = null;
		try
		{

			if ($plugin instanceof \Xajax\Plugin\Request\Data)
			{
				$pluginInstance = $plugin->getPluginInstance();
			}
		}
		catch (RuntimeException $exception)
		{
			throw $exception;
		}

		return $pluginInstance;
	}

	/**
	 * @param null|string $plgName
	 */
	protected function autoregisterResponsePlugin(?string $plgName = null)
	{
		$plgObject = $this->loadPlugin($plgName, Plugin::getResponseType());
	}

	/**
	 * @param null|string $plgName
	 * @param string      $type
	 *
	 * @return mixed
	 */
	private function loadPlugin(?string $plgName = null, string $type)
	{
		$ns = 'Xajax\\Plugins\\';
		try
		{

			$class = $ns . ucfirst($plgName) . '\\Plugin';

			$pluginInstance = new $class;
		}
		catch (RuntimeException $exception)
		{
			var_dump($exception);
			die;
		}

		return $pluginInstance;
	}

	/*
		Function: canProcessRequest

		Determines if a call is a xajax request or a page load request.

		Return:

		boolean - True if this is a xajax request, false otherwise.
	*/
	/**
	 * @return bool
	 */
	public function canProcessRequest(): bool
	{
		return $this->getObjPluginManager()->canProcessRequest();
	}

	/*
		Function: VerifySession

		Ensure that an active session is available (primarily used
		for storing challenge / response codes).
	*/
	/**
	 * @return bool
	 */
	private function verifySession(): bool
	{
		$sessionID = session_id();
		if ($sessionID === '')
		{
			$this->getObjResponseManager()->debug(
			    'Must enable sessions to use challenge/response.'
			);

			return false;
		}

		return true;
	}

	/**
	 * @param $sessionKey
	 *
	 * @return array
	 */
	private function loadChallenges($sessionKey): array
	{
		$challenges = [];

		if (isset($_SESSION[$sessionKey]))
		{
			$challenges = $_SESSION[$sessionKey];
		}

		return $challenges;
	}

	/**
	 * @param $sessionKey
	 * @param $challenges
	 */
	private function saveChallenges($sessionKey, $challenges)
	{
		if (count($challenges) > 10)
		{
			array_shift($challenges);
		}

		$_SESSION[$sessionKey] = $challenges;
	}

	/**
	 * @param $algo
	 * @param $value
	 *
	 * @return string
	 * @throws \Exception
	 */
	private function makeChallenge($algo, $value): string
	{
		// TODO: Move to configuration option
		if (null === $algo)
		{
			$algo = 'md5';
		}

		// TODO: Move to configuration option
		if (null === $value)
		{
			$value = random_int(100000, 999999);
		}

		return hash($algo, $value);
	}

	/*
		Function: challenge

		Call this from the top of a xajax enabled request handler
		to introduce a challenge and response cycle into the request
		response process.

		NOTE:  Sessions must be enabled to use this feature.
	*/
	/**
	 * @param null $algo
	 * @param null $value
	 *
	 * @return bool
	 * @throws \Exception
	 */
	public function challenge($algo = null, $value = null): bool
	{
		if (false === $this->verifySession())
		{
			return false;
		}

		// TODO: Move to configuration option
		$sessionKey = 'xajax_challenges';

		$challenges = $this->loadChallenges($sessionKey);

		if (isset($this->challengeResponse))
		{
			$key = array_search($this->challengeResponse, $challenges);

			if ($key !== false)
			{
				unset($challenges[$key]);
				$this->saveChallenges($sessionKey, $challenges);

				return true;
			}
		}

		$challenge = $this->makeChallenge($algo, $value);

		$challenges[] = $challenge;

		$this->saveChallenges($sessionKey, $challenges);

		header("challenge: {$challenge}");

		return false;
	}

	/*
		Function: processRequest

		If this is a xajax request (see <xajax->canProcessRequest>), call the
		requested PHP function, build the response and send it back to the
		browser.

		This is the main server side engine for xajax.  It handles all the
		incoming requests, including the firing of events and handling of the
		response.  If your RequestURI is the same as your web page, then this
		function should be called before ANY headers or HTML is output from
		your script.

		This function may exit, if a request is processed.  See <xajax->bAllowExit>
	*/
	/**
	 *
	 */
	public function processRequest()
	{
		if (isset($_SERVER['HTTP_CHALLENGE_RESPONSE']))
		{
			$this->challengeResponse = $_SERVER['HTTP_CHALLENGE_RESPONSE'];
		}

//SkipDebug
		// @todo check the error Response
		// Check to see if headers have already been sent out, in which case we can't do our job
		if (headers_sent($filename, $linenumber))
		{

			echo "Output has already been sent to the browser at {$filename}:{$linenumber}.\n";
			echo 'Please make sure the command $xajax->processRequest() is placed before this.';
			exit();
		}
//EndSkipDebug

		if ($this->canProcessRequest())
		{
			// Use custom error handler if necessary
			if ($this->getConfig()->isErrorHandler())
			{
				set_error_handler($this->getConfig()->getErrorHandler());
			}

			$mResult = true;

			// handle beforeProcessing event
			if (isset($this->aProcessingEvents[XAJAX_PROCESSING_EVENT_BEFORE]))
			{
				$bEndRequest = false;

				$this->aProcessingEvents[XAJAX_PROCESSING_EVENT_BEFORE]->call(
				    [&$bEndRequest]
				);

				$mResult = (false === $bEndRequest);
			}

			if (true === $mResult)
			{
				$mResult = $this->getObjPluginManager()->processRequest();
			}

			if (true === $mResult)
			{
				if ($this->bCleanBuffer)
				{
					$er = error_reporting(0);
					while (ob_get_level() > 0) ob_end_clean();
					error_reporting($er);
				}

				// handle afterProcessing event
				if (isset($this->aProcessingEvents[XAJAX_PROCESSING_EVENT_AFTER]))
				{
					$bEndRequest = false;

					$this->aProcessingEvents[XAJAX_PROCESSING_EVENT_AFTER]->call(
					    [$bEndRequest]
					);

					if (true === $bEndRequest)
					{
						$this->getObjResponseManager()->clear();
						$this->getObjResponseManager()->append($aResult[1]);
					}
				}
			}
			else if (is_string($mResult))
			{
				if ($this->bCleanBuffer)
				{
					$er = error_reporting(0);
					while (ob_get_level() > 0) ob_end_clean();
					error_reporting($er);
				}

				// $mResult contains an error message
				// the request was missing the cooresponding handler function
				// or an error occurred while attempting to execute the
				// handler.  replace the response, if one has been started
				// and send a debug message.

				$this->getObjResponseManager()->clear();
				$this->getObjResponseManager()->append(Response::getInstance());

				// handle invalidRequest event
				if (isset($this->aProcessingEvents[XAJAX_PROCESSING_EVENT_INVALID]))
				{
					$this->aProcessingEvents[XAJAX_PROCESSING_EVENT_INVALID]->call();
				}
				else
				{
					$this->getObjResponseManager()->debug($mResult);
				}
			}

			if ($this->getConfig()->isErrorHandler())
			{
				$sErrorMessage = Handler::getErrors();
				if (is_string($sErrorMessage))
				{

					if ($this->getConfig()->isToHtml())
					{

					}

					if (0 < strlen($this->sLogFile))
					{
						$fH = @fopen($this->sLogFile, 'ab');
						if (null !== $fH)
						{
							fwrite(
							    $fH,
							    $this->objLanguageManager->getText('LOGHDR:01')
							    . strftime("%b %e %Y %I:%M:%S %p")
							    . $this->objLanguageManager->getText('LOGHDR:02')
							    . $sErrorMessage
							    . $this->objLanguageManager->getText('LOGHDR:03')
							);
							fclose($fH);
						}
						else
						{
							$this->getObjResponseManager()->debug(
							    $this->objLanguageManager->getText('LOGERR:01')
							    . $this->sLogFile
							);
						}
					}
					$this->getObjResponseManager()->debug(
					    $this->objLanguageManager->getText('LOGMSG:01')
					    . $sErrorMessage
					);
				}
			}

			$this->getObjResponseManager()->send();

			if ($this->getConfig()->isErrorHandler())
			{
				restore_error_handler();
			}

			if ($this->getConfig()->isExitAllowed())
			{
				exit();
			}
		}
	}

	/*
		Function: printJavascript

		Prints the xajax Javascript header and wrapper code into your page.
		This should be used to print the javascript code between the HEAD
		and /HEAD tags at the top of the page.

		The javascript code output by this function is dependent on the plugins
		that are included and the functions that are registered.

	*/
	/**
	 * @deprecated use directly the Generator or the Factory
	 */
	public function printJavascript()
	{
		echo Generator::generateClientScript();
	}

	/**
	 * Function: getJavascript
	 *
	 * @return string
	 */
	public function getJavascript()
	{
		return Generator::generateClientScript();
	}

	/*
		Function: autoCompressJavascript

		Creates a new xajax_core, xajax_debug, etc... file out of the
		_uncompressed file with a similar name.  This strips out the
		comments and extraneous whitespace so the file is as small as
		possible without modifying the function of the code.

		Parameters:

		sJsFullFilename - (string):  The relative path and name of the file
			to be compressed.
		bAlways - (boolean):  Compress the file, even if it already exists.
	*/

	/*
		Function: _detectURI

		Returns the current requests URL based upon the SERVER vars.

		Returns:

		string : The URL of the current request.
	*/
	/**
	 * @return string
	 * @deprecated use an other place
	 */
	private function _detectURI(): string
	{
		$aURL = [];

		// Try to get the request URL
		if (!empty($_SERVER['REQUEST_URI']))
		{

			$_SERVER['REQUEST_URI'] = str_replace(
			    ['"', "'", '<', '>'],
			    ['%22', '%27', '%3C', '%3E'],
			    $_SERVER['REQUEST_URI']
			);

			$aURL = parse_url($_SERVER['REQUEST_URI']);
		}

		// Fill in the empty values
		if (empty($aURL['scheme']))
		{
			if (!empty($_SERVER['HTTP_SCHEME']))
			{
				$aURL['scheme'] = $_SERVER['HTTP_SCHEME'];
			}
			else
			{
				$aURL['scheme'] =
				    (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off')
					? 'https'
					: 'http';
			}
		}

		if (empty($aURL['host']))
		{
			if (!empty($_SERVER['HTTP_X_FORWARDED_HOST']))
			{
				if (strpos($_SERVER['HTTP_X_FORWARDED_HOST'], ':') > 0)
				{
					list($aURL['host'], $aURL['port']) = explode(':', $_SERVER['HTTP_X_FORWARDED_HOST']);
				}
				else
				{
					$aURL['host'] = $_SERVER['HTTP_X_FORWARDED_HOST'];
				}
			}
			else if (!empty($_SERVER['HTTP_HOST']))
			{
				if (strpos($_SERVER['HTTP_HOST'], ':') > 0)
				{
					list($aURL['host'], $aURL['port']) = explode(':', $_SERVER['HTTP_HOST']);
				}
				else
				{
					$aURL['host'] = $_SERVER['HTTP_HOST'];
				}
			}
			else if (!empty($_SERVER['SERVER_NAME']))
			{
				$aURL['host'] = $_SERVER['SERVER_NAME'];
			}
			else
			{
				echo $this->objLanguageManager->getText('DTCTURI:01');
				echo $this->objLanguageManager->getText('DTCTURI:02');
				exit();
			}
		}

		if (empty($aURL['port']) && !empty($_SERVER['SERVER_PORT']))
		{
			$aURL['port'] = $_SERVER['SERVER_PORT'];
		}

		if (!empty($aURL['path']))
		{
			if ('' === basename($aURL['path']))
			{
				unset($aURL['path']);
			}
		}

		if (empty($aURL['path']))
		{
			$sPath = [];
			if (!empty($_SERVER['PATH_INFO']))
			{
				$sPath = parse_url($_SERVER['PATH_INFO']);
			}
			else
			{
				$sPath = parse_url($_SERVER['PHP_SELF']);
			}
			if (isset($sPath['path']))
			{
				$aURL['path'] = str_replace(['"', "'", '<', '>'], ['%22', '%27', '%3C', '%3E'], $sPath['path']);
			}
			unset($sPath);
		}

		if (empty($aURL['query']) && !empty($_SERVER['QUERY_STRING']))
		{
			$aURL['query'] = $_SERVER['QUERY_STRING'];
		}

		if (!empty($aURL['query']))
		{
			$aURL['query'] = '?' . $aURL['query'];
		}

		// Build the URL: Start with scheme, user and pass
		$sURL = $aURL['scheme'] . '://';
		if (!empty($aURL['user']))
		{
			$sURL .= $aURL['user'];
			if (!empty($aURL['pass']))
			{
				$sURL .= ':' . $aURL['pass'];
			}
			$sURL .= '@';
		}

		// Add the host
		$sURL .= $aURL['host'];

		// Add the port if needed
		if (!empty($aURL['port'])
		    && (($aURL['scheme'] == 'http' && $aURL['port'] != 80)
			|| ($aURL['scheme'] == 'https' && $aURL['port'] != 443)))
		{
			$sURL .= ':' . $aURL['port'];
		}

		// Add the path and the query string
		$sURL .= $aURL['path'] . @$aURL['query'];

		// Clean up
		unset($aURL);

		$aURL = explode('?', $sURL);

		if (1 < count($aURL))
		{
			$aQueries = explode('&', $aURL[1]);

			foreach ($aQueries as $sKey => $sQuery)
			{
				if (0 === strpos($sQuery, 'xjxGenerate'))
				{
					unset($aQueries[$sKey]);
				}
			}

			$sQueries = implode('&', $aQueries);

			$aURL[1] = $sQueries;

			$sURL = implode('?', $aURL);
		}

		return $sURL;
	}

	/**
	 * @return \Xajax\Response\Manager
	 */
	public function getObjResponseManager(): \Xajax\Response\Manager
	{
		return $this->objResponseManager;
	}
}

/*
	Section: Global functions
*/

/*
	Function xajaxErrorHandler

	This function is registered with PHP's set_error_handler if the xajax
	error handling system is enabled.

	See <xajax->bUserErrorHandler>
*/
