<?php
/**
 * PHP version $phpversion$
 *
 * @category
 * @package            Jybrid Core  Jybrid\Scripts
 * @author             ${JProof}
 * @copyright          ${copyright}
 * @license            ${license}
 * @link
 * @see                ${docu}
 * @since              27.12.2017
 */

declare(strict_types=1);

namespace Jybrid\Scripts;

use Jybrid\Factory;
use Jybrid\Plugin\Manager;
use Jybrid\Plugin\Request\Data;
use Jybrid\Scripts\Generate\Init;
use Jybrid\Scripts\Generate\Timeout;
use Jybrid\Snippets\Snippets;

/**
 * Javascript OutputParser
 * Class Generator
 */
abstract class Generator {
	/**
	 * @return iterable
	 */
	abstract public static function generate(): iterable;

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
			$scripts->setLockScript( 'jybrid.debug' );
		}
		/* @since 0.7.5 Javascript-Snippet-Update beforeScriptUrls */
		$processorArray = [
			[ 'generateSnippetPosition' => Snippets::beforeScriptUrls ],
			[ 'generateScriptUrls' => '' ],
			[ 'generateSnippetPosition' => Snippets::beforeInitScript ],
			[ 'generateInitScript' => '' ],
			[ 'generateSnippetPosition' => Snippets::beforeTimeoutScript ],
			[ 'generateTimeoutScript' => '' ],
			[ 'generateSnippetPosition' => Snippets::beforePluginScripts ],
			[ 'generatePluginScripts' => '' ],
			[ 'generateSnippetPosition' => Snippets::afterPluginScripts ],
			[ 'generateFileScripts' => '' ],
		];

		self::compileProcessScripts( $processorArray );

		self::setHasProcessed(true);

		return true;
	}

	/**
	 * Internal processing of the script generation
	 *
	 * @param array $processorArray
	 *
	 * @return bool
	 * @since 0.7.6.1
	 */
	protected static function compileProcessScripts( array $processorArray ): bool {
		foreach ( $processorArray as $command ) {
			if ( \is_array( $command ) ) {
				foreach ( $command as $method => $parameters ) {
					if ( method_exists( __CLASS__, $method ) ) {
						if ( '' !== $parameters ) {
							self::$method( $parameters );
						} else {
							self::$method();
						}
					}
				}
			}
		}

		return true;
	}

	/**
	 * Getting all found script-urls back as array (used in cms situations)
	 *
	 * @return array
	 */
	public static function getClientScriptUrls(): array {
		self::processScripts();
		if ( ! $scriptUrls = self::getGeneratedPart( 'scripts' ) ) {
			self::generateScriptUrls();
		}
		if ( ! $scriptUrls = self::getGeneratedPart( 'scripts' ) ) {
			return [];
		}

		return $scriptUrls;
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
	 * Getting all Snippets (jybrid init-script,timeout-script if exists, plugin-scripts)
	 *
	 * @param bool|null $wrapCDATA     Wrap the whole <script>-->/*<![CDATA[*\/ <--</script> in an CDATA-Block
	 * @param bool|null $wrapScriptTag Wrap the <script></script> tag around the string
	 *
	 * @return string
	 */
	public static function getClientSnippets(?bool $wrapCDATA = null, ?bool $wrapScriptTag = null): string
	{
		self::processScripts();

		$positionsOrder = [
			Snippets::beforeInitScript,
			'init',
			Snippets::beforeTimeoutScript,
			'timeout',
			Snippets::beforePluginScripts,
			'plugins',
			Snippets::afterPluginScripts,
		];

		$snippets = self::compileSnippets( $positionsOrder );

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
	 * Automatic internal getter of available snippets parts
	 *
	 * @param array $positions
	 *
	 * @return array
	 * @since 0.7.6.1
	 */
	protected static function compileSnippets( array $positions ): array {
		$snippets = [];
		foreach ( $positions as $positionName ) {
			if ( $generatedScriptArray = self::getGeneratedPart( $positionName ) ) {
				$snippets[ $positionName ] = implode( $generatedScriptArray );
			}
		}

		return $snippets;
	}

	/**
	 * Generate all relevant Scripts they was set by Scripts and set by Jybrid-Plugins and give it back as String to "echo" it in <head></head>
	 * tag This Method is used for "simple" own applications
	 *
	 * @example
	 * <script src="/jybrid.min.js"></script>
	 * <script type="text/javascript" charset="UTF-8" defer="">
	 *      try { if (undefined == typeof jybrid.config) jybrid.config = {};  } catch (e) { jybrid = {}; jybrid.config = {};};
	 * </script>
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

		self::processScripts();
		$scriptParts = [];

		// @since 0.7.5 Javascript-Snippet-Update
		if ( $on_Part = self::getGeneratedPart( Snippets::beforeScriptUrls ) ) {
			$scriptParts[] = self::wrapScriptTag( self::wrapCDATA( implode( $on_Part ) ) );
		}

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
		return Init::generate();
	}

	/**
	 * Load Check-Scripts if set
	 *
	 * @return array
	 */
	protected static function generateTimeoutScript(): array
	{
		return Timeout::generate();
	}

	/**
	 * Stringify the "Event-Position" that this snippetPosition is an compact script-area
	 *
	 * @since 0.7.5 Javascript-Snippet-Update
	 *
	 * @param string $snippetPosition
	 */
	protected static function generateSnippetPosition( string $snippetPosition ) {
		if ( ( $pos = Factory::getSnippets()->getPosition( $snippetPosition ) )
		     && $pos->hasPositionSnippets()
		     && '' !== ( $string = $pos->__toString() ) ) {

			self::setGeneratedPart( $snippetPosition, [ $string ] );
		}
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
	 * @todo Perhaps an Override defer or other attributes Helper
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
	 * @return \Jybrid\Plugin\Manager
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
	protected static function getGeneratedPart( string $name ): ?array
	{
		return self::getGeneratedParts()[$name] ?? null;
	}

	/**
	 * Stack
	 *
	 * @param string     $name
	 * @param null|array $piece return the set array
	 *
	 * @return array
	 */
	protected static function setGeneratedPart( string $name, ?array $piece = null ): ?array
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