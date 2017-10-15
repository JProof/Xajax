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

namespace Xajax\Core\Plugin;

use InvalidArgumentException;
use Xajax\Configuration\Config;
use Xajax\Core\Plugin\Request\Data;
use Xajax\Core\Plugin\Request\Datas;
use Xajax\Core\Plugin\Request\RequestPluginIface;
use Xajax\Plugin\Plugin;
use Xajax\Plugin\Request;

class Manager
{
	use Config;

	/*
		Array: aRequestPlugins
	*/
	/**
	 * @deprecated
	 * @var array
	 */
	private $aRequestPlugins;
	/*
		Array: aResponsePlugins
	*/

	/**
	 * The Request-Plugins as own Objects with getters and setters
	 *
	 * @var \Xajax\Core\Plugin\Request\Datas
	 */
	private $requestPlugins;
	/**
	 * @deprecated
	 * @var array
	 */
	private $aResponsePlugins;
	/*
		Array: aConfigurable
	*/
	/**
	 * @deprecated
	 * @var array
	 */
	private $aConfigurable;
	/*
		Array: aRegistrars
	*/
	/**
	 * @var array
	 */
	private $aRegistrars = [];
	/*
		Array: aProcessors
	*/
	/**
	 * @deprecated
	 * @var array
	 */
	private $aProcessors;
	/*
		Array: aClientScriptGenerators
	*/
	private $aClientScriptGenerators;
	/*
		Function: xajaxPluginManager

		Construct and initialize the one and only xajax plugin manager.
	*/

	private $sJsURI;
	/**
	 * @var array
	 */
	public  $aJsFiles = [];
	private $nScriptLoadTimeout;
	private $sLanguage;
	private $nResponseQueueSize;
	private $sDebugOutputID;

	private function __construct()
	{

		$this->requestPlugins = new Datas();

		$this->aRequestPlugins  = [];
		$this->aResponsePlugins = [];

		$this->aConfigurable = [];

		$this->aProcessors             = [];
		$this->aClientScriptGenerators = [];

		$this->aJsFiles = [];

		$this->nScriptLoadTimeout = 2000;
	}

	/*
		Function: getInstance

		Implementation of the singleton pattern: returns the one and only instance of the
		xajax plugin manager.

		Returns:

		object : a reference to the one and only instance of the
			plugin manager.
	*/
	public static function &getInstance(): Manager
	{
		static $obj;
		if (!$obj)
		{
			$obj = new self;
		}

		return $obj;
	}

	/*
		Function: loadPlugins

		Loads plugins from the folders specified.

		Parameters:
			$aFolders - (array): Array of folders to check for plugins
	*/
	/**
	 * @deprecated hook with an other Plugin mechanism
	 * @todo       use spl priority queue
	 *
	 * @param $aFolders
	 */
	public function loadPlugins(array $aFolders = [])
	{
		if (0 < count($aFolders))
		{
			foreach ($aFolders as $sFolder)
			{
				if (is_dir($sFolder) && $handle = opendir($sFolder))
				{
					while (!(false === ($sName = readdir($handle))))
					{
						$nLength = strlen($sName);
						if (8 < $nLength)
						{
							$sFileName  = substr($sName, 0, $nLength - 8);
							$sExtension = substr($sName, $nLength - 8, 8);
							if ('.inc.php' === $sExtension)
							{
								require $sFolder . '/' . $sFileName . $sExtension;
							}
						}
					}

					closedir($handle);
				}
			}
		}
	}

	/*
		Function: _insertIntoArray

		Inserts an entry into an array given the specified priority number.
		If a plugin already exists with the given priority, the priority is
		automatically incremented until a free spot is found.  The plugin
		is then inserted into the empty spot in the array.

		Parameters:

		$aPlugins - (array): Plugins array
		$objPlugin - (object): A reference to an instance of a plugin.
		$nPriority - (number): The desired priority, used to order
			the plugins.

	*/
	/**
	 * @param $aPlugins
	 * @param $objPlugin
	 * @param $nPriority
	 *
	 * @deprecated never never use
	 */
	private function _insertIntoArray(&$aPlugins, $objPlugin, $nPriority)
	{
		while (isset($aPlugins[$nPriority]))
			$nPriority ++;

		$aPlugins[$nPriority] = $objPlugin;
	}

	/**
	 * Register Request Plugin
	 *
	 * @param \Xajax\Plugin\Request $objPlugin
	 * @param int|null              $nPriority
	 */
	protected function registerRequestPlugin(Request $objPlugin, ?int $nPriority = null)
	{
		if (!$objPlugin instanceof RequestPluginIface)
		{
			throw new InvalidArgumentException('Request Plugin can not be registered because of missing RequestPluginIface');
		}

		$plugins = $this->getRequestPlugins();

		$plugin = new \Xajax\Core\Plugin\Request\Data();
		$plugin->setPluginInstance($objPlugin);
	}

	/*
		Function: registerPlugin

		Registers a plugin.

		Parameters:

		objPlugin - (object):  A reference to an instance of a plugin.

		Note:
		Below is a table for priorities and their description:
		0 thru 999: Plugins that are part of or extensions to the xajax core
		1000 thru 8999: User created plugins, typically, these plugins don't care about order
		9000 thru 9999: Plugins that generally need to be last or near the end of the plugin list
	*/
	public function registerPlugin(Plugin $objPlugin, ?int $nPriority = null)
	{
		if ($objPlugin instanceof Request)
		{
			$this->registerRequestPlugin($objPlugin, $nPriority);
		}
		else if ($objPlugin instanceof xajaxResponsePlugin)
		{
			$this->aResponsePlugins[] = $objPlugin;
		}
		else
		{
//SkipDebug
			$objLanguageManager = xajaxLanguageManager::getInstance();
			trigger_error(
			    $objLanguageManager->getText('XJXPM:IPLGERR:01')
			    . get_class($objPlugin)
			    . $objLanguageManager->getText('XJXPM:IPLGERR:02')
			    , E_USER_ERROR
			);
//EndSkipDebug
		}

		if (method_exists($objPlugin, 'configure'))
		{
			$this->_insertIntoArray($this->aConfigurable, $objPlugin, $nPriority);
		}

		if (method_exists($objPlugin, 'generateClientScript'))
		{
			$this->_insertIntoArray($this->aClientScriptGenerators, $objPlugin, $nPriority);
		}
	}

	/**
	 * Check an Request Plugin has an specific Method
	 *
	 * @param string $methodName
	 */
	protected function hasRequestPluginMethod($methodName = '')
	{
		return $this->searchPluginMethod($methodName, Plugin::getRequestType());
	}

	/**
	 * Check an Response Plugin has an specific Method
	 *
	 * @param string $methodName
	 */
	protected function hasResponsePluginMethod($methodName = '')
	{
		return $this->searchPluginMethod($methodName, Plugin::getResponseType());
	}

	protected function getRequestPluginsWithMethod(string $methodName = ''): array
	{

	}

	/**
	 * @return \Xajax\Core\Plugin\Request\Datas
	 */
	public function getRequestPlugins(): Datas
	{
		return $this->requestPlugins;
	}

	protected function searchPluginMethod($methodName = '', $type = '')
	{

		Plugin::getResponseType();
	}

	/*
		Function: canProcessRequest



	*/
	/**
	 * Calls each of the request plugins and determines if the
	 * current request can be processed by one of them.  If no processor identifies
	 * the current request, then the request must be for the initial page load.
	 *
	 * @see \Xajax\Core\Xajax::canProcessRequest() for more information.
	 * @return bool
	 */
	public function canProcessRequest(): bool
	{
		$canProcessRequest = false;

		// Getting the StackObjects
		$requestPlugins = $this->getRequestPlugins();

		/** @var \Xajax\Core\Plugin\Request\Data $requestPlugin */
		foreach ($requestPlugins as $requestPlugin)
		{

			if ($requestPlugin->hasPluginMethod(__METHOD__) && ($canProcessRequest = $requestPlugin->getPluginInstance()->{__METHOD__}()))
			{
				return $canProcessRequest;
			}
		}

		return $canProcessRequest;
	}

	/**
	 * Calls each of the request plugins to request that they process the
	 * current request.  If the plugin processes the request, it will
	 *
	 * @return bool
	 */
	public function processRequest(): bool
	{
		$hasProcessRequested = false;

		// Getting the StackObjects
		$requestPlugins = $this->getRequestPlugins();

		/** @var \Xajax\Core\Plugin\Request\Data $requestPlugin */
		foreach ($requestPlugins as $requestPlugin)
		{

			if ($requestPlugin->hasPluginMethod(__METHOD__) && (true === $requestPlugin->getPluginInstance()->{__METHOD__}()))
			{
				$hasProcessRequested = true;
			}
		}

		return $hasProcessRequested;
	}

	/*
		Function: configure

		Call each of the request plugins passing along the configuration
		setting specified.

		Parameters:

		sName - (string):  The name of the configuration setting to set.
		mValue - (mixed):  The value to be set.
	*/
	public function configure($sName, $mValue)
	{


		$aKeys = array_keys($this->aConfigurable);
		sort($aKeys);
		foreach ($aKeys as $sKey)
		{
			$this->aConfigurable[$sKey]->configure($sName, $mValue);
		}

		if ('javascript files' === $sName)
		{
			$this->aJsFiles = array_merge($this->aJsFiles, $mValue);
		}

		else if ('scriptLoadTimeout' === $sName)
		{
			$this->nScriptLoadTimeout = $mValue;
		}

		else if ('responseQueueSize' === $sName)
		{
			$this->nResponseQueueSize = $mValue;
		}
		else if ('debugOutputID' === $sName)
		{
			$this->sDebugOutputID = $mValue;
		}
	}

	/*
		Function: register

		Call each of the request plugins and give them the opportunity to
		handle the registration of the specified function, event or callable object.

		Parameters:
		 $aArgs - (array) :
	*/

	/**
	 * @param array $aArgs
	 *
	 * @todo       check return type
	 * @deprecated not used anymore
	 * @return bool
	 */
	public function registerRequest(array $aArgs = []): bool
	{
		$aKeys = array_keys($this->getRegistrars());
		sort($aKeys);
		foreach ($aKeys as $sKey)
		{
			$objPlugin = $this->getRegistrar($sKey);
			$mResult   = $objPlugin->registerRequest($aArgs);
			if ($mResult instanceof xajaxRequest)
			{
				return $mResult;
			}

			else
			{
				throw new RuntimeException(__FILE__ . ' ' . __LINE__ . 'Result is not an Xajax Request instance');
			}

			if (is_array($mResult))
			{
				return $mResult;
			}
			if (is_bool($mResult))
			{
				if (true === $mResult)
				{
					return true;
				}
			}
		}
//SkipDebug
		$objLanguageManager = xajaxLanguageManager::getInstance();
		trigger_error(
		    $objLanguageManager->getText('XJXPM:MRMERR:01')
		    . print_r($aArgs, true)
		    , E_USER_ERROR
		);

		return false;
//EndSkipDebug
	}

	/*
		Function: register

		Call each of the request plugins and give them the opportunity to
		handle the registration of the specified function, event or callable object.

		Parameters:
		 $aArgs - (array) :
	*/
	/**
	 * @param $aArgs
	 *
	 * @return bool
	 * @deprecated  use registerRequest
	 */
	public function register($aArgs)
	{
		$aKeys = array_keys($this->aRegistrars);
		sort($aKeys);
		foreach ($aKeys as $sKey)
		{
			$objPlugin = $this->aRegistrars[$sKey];
			$mResult   = $objPlugin->register($aArgs);
			if ($mResult instanceof xajaxRequest)
			{
				return $mResult;
			}
			if (is_array($mResult))
			{
				return $mResult;
			}
			if (is_bool($mResult))
			{
				if (true === $mResult)
				{
					return true;
				}
			}
		}
//SkipDebug
		$objLanguageManager = xajaxLanguageManager::getInstance();
		trigger_error(
		    $objLanguageManager->getText('XJXPM:MRMERR:01')
		    . print_r($aArgs, true)
		    , E_USER_ERROR
		);
//EndSkipDebug
	}

	/**
	 * Public ProxyMethod to get an Plugin
	 *
	 * @todo check the method is need from the registered plugin Registry
	 *
	 * @param string $name
	 *
	 * @return \Xajax\Core\Plugin\Request\Data
	 */
	public function getRequestPlugin(?string $name = null): Data
	{
		$pluginData = $this->getRequestPlugins()->getByName($name);
		if ($pluginData instanceof Data)
		{
			return $pluginData;
		}
		throw new InvalidArgumentException('RequestPlugin not registered: ' . (string) $name);
	}

	/*
		Function: _getScriptFilename

		Returns the name of the script file, based on the current settings.

		sFilename - (string):  The base filename.

		Returns:

		string - The filename as it should be specified in the script tags
		on the browser.
	*/
	/**
	 * Getting the minified or regular js-filename
	 *
	 * @param $sFilename
	 *
	 * @return string
	 */
	private function _getScriptFilename(string $sFilename = ''): string
	{
		if (false === $this->getConfig()->isUseUncompressedScripts())
		{
			return str_replace('.js', '.min.js', $sFilename);
		}

		return $sFilename;
	}

	/*
		Function: generateClientScript

		Call each of the request and response plugins giving them the
		opportunity to output some javascript to the page being generated.  This
		is called only when the page is being loaded initially.  This is not
		called when processing a request.
	*/
	public function generateClientScript()
	{

		$sJsURI = $this->getConfig()->getJavascriptURI();

		$aJsFiles = $this->aJsFiles;

		if ($sJsURI !== '' && substr($sJsURI, - 1) !== '/')
		{
			$sJsURI .= '/';
		}

		// @todo check useless
		if ($this->getConfig()->isDeferScriptGeneration())
		{
			$sJsURI .= 'xajax_js/';
		}

		$aJsFiles[] = [$this->_getScriptFilename('xajax_js/xajax_core.js'), 'xajax'];

		if ($this->getConfig()->isDebug())
		{
			$aJsFiles[] = [$this->_getScriptFilename('xajax_js/xajax_debug.js'), 'xajax.debug'];
			if ($this->getConfig()->isVerbose())
			{
				$aJsFiles[] = [$this->_getScriptFilename('xajax_js/xajax_verbose.js'), 'xajax.debug.verbose'];
			}
			if ($this->getConfig()->isUseDebugLanguage())
			{
				$aJsFiles[] = [$this->_getScriptFilename('xajax_js/xajax_lang_' . $this->getConfig()->getLanguage() . '.js'),
				               'xajax'];
			}
		}

		$sCrLf = "\n";
		ob_start();

		echo $sCrLf;
		echo '<';
		echo 'script type="text/javascript" ';
		echo $this->getConfig()->isDeferScriptGeneration() ? 'defer ' : '';
		echo 'charset="UTF-8">';
		echo $sCrLf;
		echo '/* <';
		echo '![CDATA[ */';
		echo $sCrLf;
		echo 'try { if (undefined == typeof xajax.config) xajax.config = {};  } catch (e) { xajax = {}; xajax.config = {};  };';
		echo $sCrLf;
		echo 'xajax.config.requestURI = "';
		echo $this->getConfig()->getRequestURI();
		echo '";';
		echo $sCrLf;
		echo 'xajax.config.statusMessages = ';
		echo $this->getConfig()->isStatusMessages() ? 'true' : 'false';
		echo ';';
		echo $sCrLf;
		echo 'xajax.config.waitCursor = ';
		echo $this->getConfig()->isWaitCursor() ? 'true' : 'false';
		echo ';';
		echo $sCrLf;
		echo 'xajax.config.version = "';
		echo $this->getConfig()->getVersion();
		echo '";';
		echo $sCrLf;
		echo 'xajax.config.defaultMode = "';
		echo $this->getConfig()->getDefaultMode();
		echo '";';
		echo $sCrLf;
		echo 'xajax.config.defaultMethod = "';
		echo $this->getConfig()->getDefaultMethod();
		echo '";';
		echo $sCrLf;
		echo 'xajax.config.JavaScriptURI = "';
		echo $this->getConfig()->getJavascriptURI();
		echo '";';
		echo $sCrLf;
		echo 'xajax.config.responseType = "';
		echo $this->getConfig()->getResponseType();
		echo '";';

		$jsContent = ob_get_contents();

		ob_end_clean();

		echo $jsContent;

		if (false === (null === $this->nResponseQueueSize))
		{
			echo $sCrLf;
			echo 'xajax.config.responseQueueSize = ';
			echo $this->nResponseQueueSize;
			echo ';';
		}

		if (true === $this->getConfig()->isDebug())
		{
			if (false === (null === $this->sDebugOutputID))
			{
				echo $sCrLf;
				echo 'xajax.debug = {};';
				echo $sCrLf;
				echo 'xajax.debug.outputID = "';
				echo $this->sDebugOutputID;
				echo '";';
			}
		}
		if (0 < $this->nScriptLoadTimeout)
		{
			foreach ($aJsFiles as $aJsFile)
			{
				//				echo '<';
				//				echo 'script type="text/javascript" ';
				//				echo $this->sDefer;
				//				echo 'charset="UTF-8">';
				echo $sCrLf;
				echo '/* <';
				echo '![CDATA[ */';
				echo $sCrLf;
				echo 'window.setTimeout(';
				echo $sCrLf;
				echo ' function() {';
				echo $sCrLf;
				echo '  var scriptExists = false;';
				echo $sCrLf;
				echo '  try { if (';
				echo $aJsFile[1];
				echo '.isLoaded) scriptExists = true; }';
				echo $sCrLf;
				echo '  catch (e) {}';
				echo $sCrLf;
				echo '  if (!scriptExists) {';
				echo $sCrLf;
				echo '   alert("Error: the ';
				echo $aJsFile[1];
				echo ' Javascript component could not be included. Perhaps the URL is incorrect?\nURL: ';
				echo $sJsURI;
				echo $aJsFile[0];
				echo '");';
				echo $sCrLf;
				echo '  }';
				echo $sCrLf;
				echo ' }, ';
				echo $this->nScriptLoadTimeout;
				echo ');';
				echo $sCrLf;
				//				echo '/* ]]> */';
				//				echo $sCrLf;
				//				echo '<';
				//				echo '/script>';
				//				echo $sCrLf;
			}
		}

		echo $sCrLf;
		echo '/* ]]> */';
		echo $sCrLf;
		echo '<';
		echo '/script>';
		echo $sCrLf;

		if ($this->getConfig()->isDeferScriptGeneration())
		{


			$sHash = $this->generateHash();

			$sOutFile = $sHash . '.js';
			// @todo set/get deferred folder
			$sOutPath = dirname(__DIR__) . '/xajax_js/deferred/';

			if (!is_file($sOutPath . $sOutFile))
			{
				ob_start();

				$sInPath = dirname(__DIR__) . '/';

				foreach ($aJsFiles as $aJsFile)
				{
					print file_get_contents($sInPath . $aJsFile[0]);
				}
				print $sCrLf;

				print $this->printPluginScripts();

				$sScriptCode = stripslashes(ob_get_clean());

				require_once __DIR__ . '/xajaxCompress.inc.php';
				$sScriptCode = xajaxCompressFile($sScriptCode);

				if (!is_dir($sOutPath))
				{
					if (!mkdir($sOutPath) && !is_dir($sOutPath))
					{
						throw new RuntimeException('Can not create deferred out dir: ' . $sOutPath);
					}
				}

				file_put_contents($sOutPath . $sOutFile, $sScriptCode);
			}

			echo '<';
			echo 'script type="text/javascript" src="';
			echo $sJsURI;
			// @todo set/get deferred folder
			echo 'deferred/';
			echo $sOutFile;
			echo '" ';
			echo $this->getConfig()->isDeferScriptGeneration() ? 'defer ' : '';
			echo 'charset="UTF-8"><';
			echo '/script>';
			echo $sCrLf;
		}
		else
		{


			echo $sCrLf;
			echo '<';
			echo 'script type="text/javascript" ';
			echo $this->getConfig()->isDeferScriptGeneration() ? 'defer ' : '';
			echo 'charset="UTF-8">';
			echo $sCrLf;
			echo '/* <';
			echo '![CDATA[ */';
			echo $sCrLf;

			$this->printPluginScripts();

			echo $sCrLf;
			echo '/* ]]> */';
			echo $sCrLf;
			echo '<';
			echo '/script>';
			echo $sCrLf;

			foreach ($aJsFiles as $aJsFile)
			{
				echo '<';
				echo 'script type="text/javascript" src="';
				echo $sJsURI;
				echo $aJsFile[0];
				echo '" ';
				echo $this->getConfig()->isDeferScriptGeneration() ? 'defer ' : '';
				echo 'charset="UTF-8"><';
				echo '/script>';
				echo $sCrLf;
			}
		}
	}

	private function generateHash(): string
	{
		$aKeys = array_keys($this->aClientScriptGenerators);
		sort($aKeys);
		$sHash = '';
		foreach ($aKeys as $sKey)
		{
			$sHash .= $this->aClientScriptGenerators[$sKey]->generateHash();
		}

		return md5($sHash);
	}

	private function printPluginScripts()
	{
		$aKeys = array_keys($this->aClientScriptGenerators);
		sort($aKeys);
		foreach ($aKeys as $sKey)
		{
			$this->aClientScriptGenerators[$sKey]->generateClientScript();
		}
	}

	/*
		Function: getResponsePlugin

		Locate the specified response plugin by name and return
		a reference to it if one exists.

		Parameters:
			$sName - (string): Name of the plugin.

		Returns:
			mixed : Returns plugin or false if not found.
	*/
	public function getResponsePlugin($sName)
	{
		$aKeys = array_keys($this->aResponsePlugins);
		sort($aKeys);
		foreach ($aKeys as $sKey)
		{
			if ($this->aResponsePlugins[$sKey] instanceof $sName)
			{
				return $this->aResponsePlugins[$sKey];
			}
		}

		return false;
	}

	/*
		Function: getRequestPlugin

		Locate the specified response plugin by name and return
		a reference to it if one exists.

		Parameters:
			$sName - (string): Name of the plugin.

		Returns:
			mixed : Returns plugin or false if not found.
	*/

	/**
	 * Internal Adding an Plugin to Registrars
	 *
	 * @param \Xajax\plugin_layer\RequestIface $plugin
	 * @param int                              $nPriority
	 */
	private function addRegistrar(RequestIface $plugin, int $nPriority = 0)
	{
		// @todo check if need this ns priority counter

		$registrars = $this->getRegistrars();
		while (isset($registrars[$nPriority]))
			$nPriority ++;

		$registrars[$nPriority] = $plugin;
		$this->setRegistrars($registrars);
	}

	/**
	 * Access to registered Stack of Plugins
	 *
	 * @param string $name
	 *
	 * @return \Xajax\plugin_layer\RequestIface
	 * @throws InvalidArgumentException
	 */
	private function getRegistrar(string $name = ''): \Xajax\plugin_layer\RequestIface
	{
		$registrars = $this->getRegistrars();
		/** @var \Xajax\plugin_layer\RequestIface $plugin */
		foreach ($registrars as $nPriority => $plugin)
		{
			if ($plugin->getName() === $name)
			{
				return $plugin;
			}
		}

		throw new InvalidArgumentException('Registrar Plugin is not registered ' . $name);
	}

	/**
	 * @return array
	 */
	public function getRegistrars(): array
	{
		return $this->aRegistrars;
	}

	/**
	 * @param array $aRegistrars
	 */
	private function setRegistrars(array $aRegistrars = [])
	{
		$this->aRegistrars = $aRegistrars;
	}
}