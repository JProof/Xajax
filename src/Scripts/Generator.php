<?php
/**
 * PHP version $phpversion$
 *
 * @category
 * @package            Xajax Core  Xajax\Scripts
 * @author             ${JProof}
 * @copyright          ${copyright}
 * @license            ${license}
 * @link
 * @see                ${docu}
 * @since              27.12.2017
 */

declare(strict_types=1);

namespace Xajax\Scripts;

use Xajax\Plugin\Manager;
use Xajax\Plugin\Request\Data;

/**
 * Javascript OutputParser
 * Class Generator
 */
class Generator
{
	/**
	 * Complex script generator which replaces the old "Response/Manager" Class
	 *
	 * @var bool
	 */
	static private $hasProcessed = false;

	/**
	 * Prepare the Script generation to handle cache/defer scripts
	 *
	 * @return bool
	 */
	protected static function processScripts(): bool
	{
		if (self::isHasProcessed())
		{
			return false;
		}
		self::setHasProcessed(true);

		return true;
	}

	/**
	 * Generate all relevant Scripts they was set by Scripts and set by Plugins()
	 */
	public static function generateClientScript()
	{
		$scripts       = Scripts::getInstance();
		$configScripts = $scripts->getConfiguration();

		if (!$configScripts->isDebug())
		{
			$scripts->setLockScript('xajax.debug');
		}

		$scriptParts = [];
		if ($configScripts->isDeferScriptGeneration())
		{

			/*$sHash = $this->generateHash();

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
			echo $sCrLf;*/
		}
		else
		{

			// full files First
			$scriptParts[] = implode(self::generateFileScripts());

			// diverse init Scripts
			$snippets      = [];
			$snippets[]    = self::generateInitScript();
			$snippets[]    = self::generateTimeoutScript();
			$snippets[]    = self::generatePluginScripts();
			$scriptParts[] = self::wrapScriptData(implode('', $snippets));
		}

		return implode($scriptParts);
	}

	/**
	 * Files in <script Src-Tags
	 *
	 * @return array
	 */
	protected static function generateFileScripts(): array
	{
		$xScripts      = Scripts::getInstance()->getScriptUrls();
		$configScripts = Scripts::getInstance()->getConfiguration();
		$parts         = [];

		foreach ($xScripts as $xScript)
		{
			$parts[] = '<script type="text/javascript" charset="UTF-8" src="' . $xScript . '" ' . ($configScripts->isDeferScriptGeneration() ? 'defer ' : ' ') . '></script>';
		}
		return $parts;
	}

	/**
	 * All Scripts from Plugins they must be rendered to Browser
	 *
	 * @return string
	 */
	public static function generatePluginScripts(): string
	{
		$scripts = [];
		$method  = 'generateClientScript';
		$plugins = self::getPluginManager()->getRequestPlugins();
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

	/**
	 * Init-JSScript which is constructing the "mainFeatures" in browser
	 *
	 * @return string
	 */
	public static function generateInitScript(): string
	{

		$xajaxConfig   = \Xajax\Configuration::getInstance();
		$configScripts = Scripts::getInstance()->getConfiguration();

		$parts = [];

		$parts[] = 'try { if (undefined == typeof xajax.config) xajax.config = {};  } catch (e) { xajax = {}; xajax.config = {};  };';

		// only if configured
		if ('' !== ($requestUri = $xajaxConfig->getRequestURI()))
		{
			$parts[] = 'xajax.config.requestURI = "' . $requestUri . '";';
		}

		$parts[] = 'xajax.config.waitCursor = ' . ($configScripts->isWaitCursor() ? 'true' : 'false') . ';';
		$parts[] = 'xajax.config.version = "' . $xajaxConfig->getVersion() . '";';
		$parts[] = 'xajax.config.defaultMode = "' . $configScripts->getDefaultMode() . '";';
		$parts[] = 'xajax.config.defaultMethod = "' . $configScripts->getDefaultMethod() . '";';
		//$parts[] = 'xajax.config.responseType = "' . $this->getConfig()->getResponseType() . '";';

		if (null !== $xajaxConfig->getResponseQueueSize())
		{
			$parts[] = 'xajax.config.responseQueueSize = ' . $xajaxConfig->getResponseQueueSize() . ';';
		}

		if (true === $configScripts->isDebug())
		{
			if (null !== $xajaxConfig->getDebugOutputID())
			{

				$parts[] = 'xajax.debug = {};';
				$parts[] = 'xajax.debug.outputID = "' . $xajaxConfig->getDebugOutputID() . '";';
			}
		}

		return implode('', $parts);
	}

	/**
	 * Load Check-Scripts if set
	 *
	 * @return string
	 */
	public static function generateTimeoutScript(): string
	{
		$parts       = [];
		$xajaxConfig = \Xajax\Configuration::getInstance();
		$xScripts    = Scripts::getInstance()->getScriptUrls();
		if (0 < ($sto = $xajaxConfig->getScriptLoadTimeout()))
		{
			foreach ($xScripts as $name => $xScript)
			{
				// only Xajax scripts can timeOuted
				if (false === strpos($name, 'xajax'))
				{
					continue;
				}

				$parts  [] = 'window.setTimeout( function() {  var scriptExists = false;  try { if (' . $name . '.isLoaded) scriptExists = true; }catch (e) {};if (!scriptExists) {
					alert("Error: the Javascript component could not be included. Perhaps the URL is incorrect?\nURL:' . $xScript . '");} },' . $sto . ');';
			}
		}

		return implode('\n', $parts);
	}

	/**
	 * @return bool
	 */
	public static function isHasProcessed(): ?bool
	{
		return self::$hasProcessed;
	}

	/**
	 * @param bool $hasProcessed
	 *
	 * @return bool
	 */
	private static function setHasProcessed(?bool $hasProcessed = null): bool
	{
		return self::$hasProcessed = $hasProcessed ?? false;
	}

	/**
	 * @param string $str
	 *
	 * @return string
	 */
	protected static function wrapScriptData(string $str): string
	{
		return self::wrapScriptTag(self::wrapCDATA($str));
	}

	/**
	 * @param string $str
	 *
	 * @return string
	 */
	protected static function wrapScriptTag(string $str): string
	{
		return self::getOpenScript() . $str . self::getCloseScript();
	}

	/**
	 * @param string $str
	 *
	 * @return string
	 */
	protected static function wrapCDATA(string $str): string
	{
		return self::getCDATAOpen() . $str . self::getCDATAClose();
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
	protected static function getOpenScript(): string
	{
		return '<script type="text/javascript" charset="UTF-8" ' . (Scripts::getInstance()
		                                                                   ->getConfiguration() ? 'defer ' : '') . '>';
	}

	/**
	 * @return string
	 */
	protected static function getCloseScript()
	{
		return '</script>';
	}

	/**
	 * Because of old bindings
	 *
	 * @return \Xajax\Plugin\Manager
	 */
	protected static function getPluginManager(): Manager
	{
		return Manager::getInstance();
	}
}