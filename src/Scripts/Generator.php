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
	 * During the Script-Generating-Process all Parts will be stacked (is need to detect cache an direct files and snippets)
	 *
	 * @var array
	 */
	static private $generatedParts = [];

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
		$scripts       = Scripts::getInstance();
		$configScripts = $scripts->getConfiguration();

		if (!$configScripts->isDebug())
		{
			$scripts->setLockScript('xajax.debug');
		}

		self::generateScriptUrls();
		self::generateInitScript();
		self::generateTimeoutScript();
		self::generatePluginScripts();

		// at least because the caching in next version will probably add extra cache Files
		self::generateFileScripts();

		self::setHasProcessed(true);

		return true;
	}

	/**
	 * Getting all Script-Files with Tag
	 *
	 * @return string
	 */
	public static function getClientScripts(): string
	{
		self::processScripts();
		return implode('', self::getGeneratedPart('scriptTags'));
	}

	/**
	 * Getting all Snippets in ScriptTag
	 *
	 * @param bool|null $wrapCDATA
	 * @param bool|null $wrapScriptTag
	 *
	 * @return string
	 */
	public static function getClientSnippets(?bool $wrapCDATA = null, ?bool $wrapScriptTag = null): string
	{
		self::processScripts();

		$snippets = [];

		if ($init = self::getGeneratedPart('init'))
		{
			$snippets[] = implode('', $init);
		}
		if ($timeout = self::getGeneratedPart('timeout'))
		{
			$snippets[] = implode('', $timeout);
		}

		if ($plugins = self::getGeneratedPart('plugins'))
		{
			$snippets[] = implode('', $plugins);
		}

		$str = implode('', $snippets);
		if ($wrapCDATA ?? false)
		{
			$str = self::wrapCDATA($str);
		}
		if ($wrapScriptTag ?? false)
		{
			$str = self::wrapScriptTag($str);
		}

		return $str;
	}

	/**
	 * Generate all relevant Scripts they was set by Scripts and set by Plugins()
	 *
	 * @param bool|null $forceNew If one of an rendering was already processed and an script or snippet was after the generation process added,
	 *                            then you can process again and re-generate all <script src=""> and <script></script>
	 *
	 * @return string complete script-src tags an script-content tags
	 */
	public static function generateClientScript(?bool $forceNew = null): string
	{
		if ((bool) $forceNew && self::isHasProcessed())
		{
			self::setHasProcessed(false);
		}

		$scriptParts = [];

		// full files First
		$scriptParts[] = self::getClientScripts();

		// diverse init Scripts
		$scriptParts[] = self::getClientSnippets(true, true);

		return implode($scriptParts);
	}

	/**
	 * Collecting all Script-Src in array
	 */
	protected static function generateScriptUrls()
	{
		$xScripts = Scripts::getInstance()->getScriptUrls();

		$parts = [];
		foreach ($xScripts as $xScript)
		{
			$parts[] = $xScript;
		}
		// todo add Cache Files also!!!!
		self::setGeneratedPart('scripts', $parts);
	}

	/**
	 * Files in <script Src-Tags
	 *
	 * @return array
	 */
	private static function generateFileScripts(): array
	{
		if (!$scriptUrls = self::getGeneratedPart('scripts'))
		{
			self::generateScriptUrls();
		}
		if (!$scriptUrls = self::getGeneratedPart('scripts'))
		{
			return [];
		}

		$configScripts = Scripts::getInstance()->getConfiguration();
		$parts         = [];

		foreach ($scriptUrls as $scriptUrl)
		{
			$parts[] = '<script type="text/javascript" charset="UTF-8" src="' . $scriptUrl . '" ' . ($configScripts->isDeferScriptGeneration() ? 'defer ' : ' ') . '></script>';
		}
		self::setGeneratedPart('scriptTags', $parts);

		return $parts;
	}

	/**
	 * All Scripts from Plugins they must be rendered to Browser
	 *
	 * @return array
	 */
	protected static function generatePluginScripts(): array
	{
		$parts   = [];
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
					$parts[] = $string;
				}
			}
		}

		self::setGeneratedPart('plugins', $parts);

		return $parts;
	}

	/**
	 * Init-JSScript which is constructing the "mainFeatures" in browser
	 *
	 * @return array
	 */
	protected static function generateInitScript(): array
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

		self::setGeneratedPart('init', $parts);
		return $parts;
	}

	/**
	 * Load Check-Scripts if set
	 *
	 * @return array
	 */
	protected static function generateTimeoutScript(): array
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

		self::setGeneratedPart('timeout', $parts);
		return $parts;
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
	protected static function getCloseScript(): string
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

	/**
	 * @param string $name
	 *
	 * @return array|null
	 */
	private static function getGeneratedPart(string $name): ?array
	{
		return self::getGeneratedParts()[$name] ?? null;
	}

	/**
	 * Stack
	 *
	 * @param string $name
	 * @param array  $piece
	 *
	 * @return array
	 */
	private static function setGeneratedPart(string $name, array $piece = null): array
	{
		$parts        = self::getGeneratedParts();
		$parts[$name] = $piece;
		self::setGeneratedParts($parts);

		return $piece;
	}

	/**
	 * @return array
	 */
	private static function getGeneratedParts(): array
	{
		return self::$generatedParts;
	}

	/**
	 * @param array $generatedParts
	 */
	private static function setGeneratedParts(array $generatedParts): void
	{
		self::$generatedParts = $generatedParts;
	}
}