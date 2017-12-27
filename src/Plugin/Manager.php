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

namespace Xajax\Plugin {

	use InvalidArgumentException;
	use RuntimeException;
	use Xajax\Configuration\Config;
	use Xajax\Helper\Javascripts;
	use Xajax\Language;
	use Xajax\Plugin\Request\Data;
	use Xajax\Plugin\Request\RequestPluginIface;
	use Xajax\Scripts\Scripts;

	/**
	 * Class Manager
	 */
	class Manager
	{
		use Config;
		use \Xajax\Errors\Call;
		/**
		 * The Request-Plugins as own Objects with getters and setters
		 *
		 * @var \Xajax\Plugin\Request\Datas
		 */
		private $requestPlugins;
		/**
		 * The Request-Plugins as own Objects with getters and setters
		 *
		 * @var \Xajax\Plugin\Response\Datas
		 */
		private $responsePlugins;
		/*
			Array: aClientScriptGenerators
		*/
		/**
		 * @var array
		 */
		private $aClientScriptGenerators;
		/*
			Function: xajaxPluginManager

			Construct and initialize the one and only xajax plugin manager.
		*/

		/**
		 * @deprecated use the loader
		 * @var array
		 */
		public $aJsFiles = [];
		/**
		 * @var int
		 */
		private $nScriptLoadTimeout;
		/**
		 * @var
		 */
		private $nResponseQueueSize;
		/**
		 * @var
		 */
		private $sDebugOutputID;

		/**
		 * Manager constructor.
		 */
		private function __construct()
		{

			$this->requestPlugins  = new \Xajax\Plugin\Request\Datas();
			$this->responsePlugins = new \Xajax\Plugin\Response\Datas;

			$this->aClientScriptGenerators = [];

			$this->aJsFiles = [];

			$this->nScriptLoadTimeout = 2000;
		}

		/*

		*/
		/**
		 * Function: getInstance
		 * Implementation of the singleton pattern: returns the one and only instance of the
		 * xajax plugin manager.
		 * Returns:
		 * object : a reference to the one and only instance of the
		 * plugin manager.
		 *
		 * @return \Xajax\Plugin\Manager
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

		/**
		 * Register Request Plugin
		 *
		 * @todo hier gehts weiter, setting des Plugins als Data-Object
		 *
		 * @param \Xajax\Plugin\Request $objPlugin
		 * @param int|null              $nPriority
		 *
		 * @return Request\Data
		 */
		protected function registerRequestPlugin(Request $objPlugin, ?int $nPriority = null): Request\Data
		{
			if (!$objPlugin instanceof RequestPluginIface)
			{
				throw new InvalidArgumentException('Request Plugin can not be registered because of missing RequestPluginIface');
			}

			$plugins = $this->getRequestPlugins();

			$pluginData = new Request\Data();
			$pluginData->setPluginInstance($objPlugin);

			$plugins->addPlugin($nPriority, $pluginData);

			return $pluginData;
		}

		/**
		 * Register Request Plugin
		 *
		 * @todo hier gehts weiter .........
		 *
		 * @param \Xajax\Plugin\Response $objPlugin
		 * @param int|null               $nPriority
		 *
		 * @throws \InvalidArgumentException
		 */
		protected function registerResponsePlugin(Response $objPlugin, ?int $nPriority = null)
		{
			if (!$objPlugin instanceof RequestPluginIface)
			{
				throw new InvalidArgumentException('Request Plugin can not be registered because of missing ResponsePluginInfterface');
			}

			$plugins = $this->getRequestPlugins();

			$plugin = new \Xajax\Plugin\Response\Data();
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
		/**
		 * @param \Xajax\Plugin\Plugin $objPlugin
		 * @param int|null             $nPriority
		 *
		 * @return void|\Xajax\Plugin\Request\Data
		 */
		public function registerPlugin(Plugin $objPlugin, ?int $nPriority = null)
		{
			if ($objPlugin instanceof Request)
			{
				return $this->registerRequestPlugin($objPlugin, $nPriority);
			}

			if ($objPlugin instanceof Response)
			{
				return $this->registerResponsePlugin($objPlugin, $nPriority);
			}

			$objLanguageManager = Language::getInstance();
			trigger_error(
			    $objLanguageManager->getText('XJXPM:IPLGERR:01')
			    . get_class($objPlugin)
			    . $objLanguageManager->getText('XJXPM:IPLGERR:02')
			    , E_USER_ERROR
			);
		}

		/**
		 * @return \Xajax\Plugin\Request\Datas
		 */
		public function getRequestPlugins(): \Xajax\Plugin\Request\Datas
		{
			return $this->requestPlugins;
		}

		/**
		 * @return \Xajax\Plugin\Response\Datas
		 */
		public function getResponsePlugins(): \Xajax\Plugin\Response\Datas
		{
			return $this->responsePlugins;
		}

		/**
		 * Calls each of the request plugins and determines if the
		 * current request can be processed by one of them.  If no processor identifies
		 * the current request, then the request must be for the initial page load.
		 *
		 * @see \Xajax\Xajax::canProcessRequest() for more information.
		 * @return bool
		 */
		public function canProcessRequest(): bool
		{
			$canProcessRequest = false;

			// Getting the StackObjects
			$requestPlugins = $this->getRequestPlugins();

			/** @var \Xajax\Plugin\Request\Data $requestPlugin */
			foreach ($requestPlugins as $requestPlugin)
			{

				if ($requestPlugin->hasPluginMethod(__FUNCTION__) &&
				    ($canProcessRequest = $requestPlugin->getPluginInstance()->{__FUNCTION__}()))
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

			/** @var \Xajax\Plugin\Request\Data $requestPlugin */
			foreach ($requestPlugins as $requestPlugin)
			{

				// processing each plugin
				if ($requestPlugin->hasPluginMethod(__FUNCTION__) && (true === $requestPlugin->getPluginInstance()->{__FUNCTION__}()))
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
		/**
		 * @param $sName
		 * @param $mValue
		 */
		public function configure($sName, $mValue)
		{

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

		/**
		 * Public ProxyMethod to get an Plugin
		 *
		 * @todo check the method is need from the registered plugin Registry
		 *
		 * @param string $name
		 *
		 * @return \Xajax\Plugin\Request\Data
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

		/*
			Function: generateClientScript

			Call each of the request and response plugins giving them the
			opportunity to output some javascript to the page being generated.  This
			is called only when the page is being loaded initially.  This is not
			called when processing a request.
		*/
		/**
		 *
		 */
		public function generateClientScript()
		{

			$scripts       = Scripts::getInstance();
			$configScripts = $scripts->getConfiguration();

			/**
			 * already init by
			 *
			 * @see \Xajax\Scripts\Scripts::__construct()
			 */
			#	$xCoreConfig = new Core();
			#	$xCoreConfig->setScriptName('xajax')->setFileName('xajax_core.js')->setPriority(0);
			#	$scripts->addScript($xCoreConfig, 0);

			if (!$configScripts->isDebug())
			{
				$scripts->setLockScript('xajax.debug');
			}

			$scriptParts = [];

			$scriptParts[] = $initScript = $this->generateInitScript();
			$scriptParts[] = $checkScripts = $this->generateTimeOutScripts();
			if ($configScripts->isDeferScriptGeneration())
			{


				$sHash = $this->generateHash();

				$sOutFile = $sHash . '.js';
				// @todo set/get deferred folder
				$sOutPath = dirname(__DIR__) . '/xajax_js/deferred/';

				if (!is_file($sOutPath . $sOutFile))
				{
					ob_start();

					$sInPath = dirname(__DIR__) . 'Manager.php/';

					foreach ($aJsFiles as $aJsFile)
					{
						print file_get_contents($sInPath . $aJsFile[0]);
					}
					print $sCrLf;

					print $this->printPluginScripts();

					$sScriptCode = stripslashes(ob_get_clean());

					$sScriptCode = Javascripts::xajaxCompressFile($sScriptCode);

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
				echo $configScripts->isDeferScriptGeneration() ? 'defer ' : '';
				echo 'charset="UTF-8"><';
				echo '/script>';
				echo $sCrLf;
			}
			else
			{

				$scriptParts[] = $fileScripts = $this->generateFileScripts();

				$scriptParts[] = $pluginScripts = $this->generatePluginScripts();
			}

			$finalScripts = implode($scriptParts);
			return $finalScripts;
		}

		/**
		 * Rendering the src-scripts with files
		 *
		 * @return string
		 */
		protected function generateFileScripts(): string
		{
			$xScripts      = Scripts::getInstance()->getScriptUrls();
			$configScripts = Scripts::getInstance()->getConfiguration();
			$parts         = [];

			foreach ($xScripts as $xScript)
			{
				$parts[] = '<script type="text/javascript" charset="UTF-8" src="' . $xScript . '" ' . ($configScripts->isDeferScriptGeneration() ? 'defer ' : ' ') . '></script>';
			}
			return implode('', $parts);
		}

		/**
		 * Generating all Plugin Scripts
		 *
		 * @return string
		 */
		protected function generatePluginScripts(): string
		{
			// script Content
			$configScripts = Scripts::getInstance()->getConfiguration();
			$parts         = [];
			$parts[]       = '<script type="text/javascript" charset="UTF-8" ' . ($configScripts->isDeferScriptGeneration() ? 'defer' : '') . '>';
			$parts[]       = self::getCDATAOpen();

			$parts[] = $this->printPluginScripts();

			$parts[] = self::getCDATAClose();
			$parts[] = '</script>';
			return implode('', $parts);
		}

		/**
		 * @return string
		 */
		protected function generateInitScript(): string
		{
			$configScripts = Scripts::getInstance()->getConfiguration();

			$parts = [];

			$parts[] = '<script type="text/javascript" ' . ($configScripts->isDeferScriptGeneration() ? 'defer ' : '') . 'charset="UTF-8">';
			$parts[] = self::getCDATAOpen();

			$parts[] = 'try { if (undefined == typeof xajax.config) xajax.config = {};  } catch (e) { xajax = {}; xajax.config = {};  };';

			// only if configured
			if ('' !== ($requestUri = $this->getConfig()->getRequestURI()))
			{
				$parts[] = 'xajax.config.requestURI = "' . $requestUri . '";';
			}

			$parts[] = 'xajax.config.waitCursor = ' . ($configScripts->isWaitCursor() ? 'true' : 'false') . ';';
			$parts[] = 'xajax.config.version = "' . $this->getConfig()->getVersion() . '";';
			$parts[] = 'xajax.config.defaultMode = "' . $configScripts->getDefaultMode() . '";';
			$parts[] = 'xajax.config.defaultMethod = "' . $configScripts->getDefaultMethod() . '";';
			$parts[] = 'xajax.config.responseType = "' . $this->getConfig()->getResponseType() . '";';

			if (false === (null === $this->nResponseQueueSize))
			{
				$parts[] = 'xajax.config.responseQueueSize = ' . $this->nResponseQueueSize . ';';
			}

			if (true === $configScripts->isDebug())
			{
				if (false === (null === $this->sDebugOutputID))
				{

					$parts[] = 'xajax.debug = {};';
					$parts[] = 'xajax.debug.outputID = "' . $this->sDebugOutputID . '";';
				}
			}

			$parts[] = self::getCDATAClose();
			$parts[] = '</script>';

			return implode('', $parts);
		}

		/**
		 * Time-Out Scripts
		 * maybe only on development
		 *
		 * @return string
		 */
		protected function generateTimeOutScripts()
		{
			$parts    = [];
			$xScripts = Scripts::getInstance()->getScriptUrls();
			if (0 < $this->nScriptLoadTimeout)
			{
				foreach ($xScripts as $name => $xScript)
				{
					// only Xajax scripts can timeOuted
					if (false === strpos($name, 'xajax'))
					{
						continue;
					}

					$parts  [] = 'window.setTimeout( function() {  var scriptExists = false;  try { if (' . $name . '.isLoaded) scriptExists = true; }catch (e) {};if (!scriptExists) {
					alert("Error: the Javascript component could not be included. Perhaps the URL is incorrect?\nURL:' . $xScript . '");} },' . $this->nScriptLoadTimeout . ');';
				}
			}
			return implode('\n', $parts);
		}

		/**
		 * @return string
		 */
		protected static function getCDATAOpen(): string
		{
			return '/*<![CDATA[*/';
		}

		/**
		 * @return string
		 */
		protected static function getCDATAClose(): string
		{
			return '/*]]>*/';
		}

		/**
		 * @return string
		 */
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

		/**
		 * @return string
		 */
		private function printPluginScripts(): string
		{
			$scripts = [];
			$method  = 'generateClientScript';
			$plugins = $this->getRequestPlugins();
			/** @var Data $plugin */
			foreach ($plugins as $plugin)
			{
				if ($plugin->hasPluginMethod($method))
				{
					$string = $plugin->getPluginInstance()->{$method}();
					if ('' !== $string)
					{
						$scripts[] = $string;
					}
				}
			}

			return implode("\n", $scripts);
		}




		// deprecated -----------

		/**
		 * @deprecated hook with an other Plugin mechanism
		 * @todo       use spl priority queue
		 *
		 * @param $aFolders
		 */
		public function loadPlugins(?array $aFolders = null)
		{
			if (is_array($aFolders) && 0 < count($aFolders))
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
	}
}