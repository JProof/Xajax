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

namespace Jybrid\Plugin {

	use InvalidArgumentException;
	use Jybrid\Interfaces\IfacePluginRequest;
	use Jybrid\Interfaces\IfacePluginRequestRequest;
	use Jybrid\Language;
	use Jybrid\Plugin\Request\Data;

	/**
	 * Class Manager
	 */
	class Manager
	{
		use \Jybrid\Errors\TraitCall;
		/**
		 * The RequestRequest-Plugins as own Objects with getters and setters
		 *
		 * @var \Jybrid\Plugin\Request\Datas
		 */
		private $requestPlugins;
		/**
		 * The RequestRequest-Plugins as own Objects with getters and setters
		 *
		 * @var \Jybrid\Plugin\Response\Datas
		 */
		private $responsePlugins;
		/*
			Function: jybridPluginManager

			Construct and initialize the one and only jybrid plugin manager.
		*/

		/**
		 * Manager constructor.
		 */
		private function __construct()
		{

			$this->requestPlugins  = new \Jybrid\Plugin\Request\Datas();
			$this->responsePlugins = new \Jybrid\Plugin\Response\Datas;
		}

		/**
		 * Function: getInstance
		 * Implementation of the singleton pattern: returns the one and only instance of the
		 * jybrid plugin manager.
		 * Returns:
		 * object : a reference to the one and only instance of the
		 * plugin manager.
		 *
		 * @return \Jybrid\Plugin\Manager
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
		 * Register RequestRequest Plugin
		 *
		 * @param \Jybrid\Interfaces\IfacePluginRequest $objPlugin
		 * @param int|null                              $nPriority
		 *
		 * @return Request\Data
		 */
		public function registerRequestPlugin( IfacePluginRequest $objPlugin, ?int $nPriority = null ): Request\Data
		{
			$plugins = $this->getRequestPlugins();

			if ( $alreadyRegisteredPlugin = $plugins->getByName( $objPlugin->getName() ) ) {
				return $alreadyRegisteredPlugin;
			}
			$pluginData = new Request\Data();
			$pluginData->setPluginInstance($objPlugin);

			$plugins->addPlugin($nPriority, $pluginData);

			return $pluginData;
		}

		/**
		 * Register RequestRequest Plugin
		 *
		 * @todo hier gehts weiter .........
		 *
		 * @param \Jybrid\Plugin\Response $objPlugin
		 * @param int|null                $nPriority
		 *
		 * @throws \InvalidArgumentException
		 */
		protected function registerResponsePlugin(Response $objPlugin, ?int $nPriority = null)
		{
			if ( ! $objPlugin instanceof IfacePluginRequestRequest )
			{
				throw new InvalidArgumentException('Request Plugin can not be registered because of missing ResponsePluginInfterface');
			}

			$plugins = $this->getRequestPlugins();

			$plugin = new \Jybrid\Plugin\Response\Data();
			$plugin->setPluginInstance($objPlugin);
		}



		/**
		 * @param \Jybrid\Plugin\Plugin $objPlugin
		 * @param int|null              $nPriority
		 *
		 * @return void|\Jybrid\Plugin\Request\Data
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
				$objLanguageManager->getText( 'JYBPM:IPLGERR:01' )
				. get_class($objPlugin)
				. $objLanguageManager->getText( 'JYBPM:IPLGERR:02' )
			    , E_USER_ERROR
			);
		}

		/**
		 * @return \Jybrid\Plugin\Request\Datas
		 */
		public function getRequestPlugins(): \Jybrid\Plugin\Request\Datas
		{
			return $this->requestPlugins;
		}

		/**
		 * @return \Jybrid\Plugin\Response\Datas
		 */
		public function getResponsePlugins(): \Jybrid\Plugin\Response\Datas
		{
			return $this->responsePlugins;
		}

		/**
		 * Calls each of the request plugins and determines if the
		 * current request can be processed by one of them.  If no processor identifies
		 * the current request, then the request must be for the initial page load.
		 *
		 * @see \Jybrid\Jybrid::canProcessRequest() for more information.
		 * @return bool
		 */
		public function canProcessRequest(): bool
		{
			$canProcessRequest = false;

			// Getting the StackObjects
			$requestPlugins = $this->getRequestPlugins();

			/** @var \Jybrid\Plugin\Request\Data $requestPlugin */
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

			/** @var \Jybrid\Plugin\Request\Data $requestPlugin */
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
		 * @return \Jybrid\Plugin\Request\Data
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
	}
}
