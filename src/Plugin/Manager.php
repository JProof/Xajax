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
	use Xajax\Language;
	use Xajax\Plugin\Request\Data;
	use Xajax\Plugin\Request\RequestPluginIface;

	/**
	 * Class Manager
	 */
	class Manager
	{
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
		 * Manager constructor.
		 */
		private function __construct()
		{

			$this->requestPlugins  = new \Xajax\Plugin\Request\Datas();
			$this->responsePlugins = new \Xajax\Plugin\Response\Datas;

			$this->aClientScriptGenerators = [];
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

		public function configure()
		{
			$args = func_get_args();
		}

		/**
		 * @todo move to the Generator
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
