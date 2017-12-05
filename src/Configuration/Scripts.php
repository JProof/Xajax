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
 * @since              24.09.2017
 */

declare(strict_types=1);

namespace Xajax\Configuration;

/**
 * Trait Scripts
 *
 * @package Xajax\Config
 */
class Scripts extends Base
{
	/**
	 * Uncompressed Javascript if exists
	 *
	 * @var bool
	 */
	protected $useUncompressedScripts = false;
	/**
	 * JS
	 * true - xajax should update the status bar during a request
	 * false - xajax should not display the status of the request
	 *
	 * @var bool
	 */
	protected $statusMessages = false;
	/**
	 * true - xajax should display a wait cursor when making a request
	 * false - xajax should not show a wait cursor during a request
	 *
	 * @var bool
	 */
	protected $waitCursor = true;
	/**
	 * A flag that indicates whether
	 * script deferral is in effect or not
	 *
	 * @var bool
	 */
	protected $deferScriptGeneration = true;
	/**
	 * Debug Flag for Xajax. Set to true only during development.
	 *
	 * @var bool
	 */
	protected $debug = false;
	/**
	 * If debug is true xajax will explain more debug-messages
	 *
	 * @var bool
	 */
	protected $verbose = false;
	/**
	 * @var array
	 */
	protected static $modes = ['asynchronous', 'synchronous',];
	/**
	 * @var self
	 */
	private static $instance;
	/**
	 * The request mode.
	 * 'asynchronous' - The request will immediately return, the
	 * response will be processed when (and if) it is received.
	 * 'synchronous' - The request will block, waiting for the
	 * response.  This option allows the server to return
	 * a value directly to the caller.
	 *
	 * @var string
	 */
	protected $defaultMode;
	/**
	 * POST or GET case-insensitive automatic default is post
	 *
	 * @var string
	 */
	protected $defaultMethod;

	/**
	 * @return \Xajax\Configuration\Scripts
	 */
	public static function getInstance(): self
	{
		if (!self::$instance instanceof self)
		{
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * @return bool
	 */
	public function isUseUncompressedScripts(): bool
	{
		return (bool) $this->useUncompressedScripts;
	}

	/**
	 * @param bool $useUncompressedScripts
	 *
	 * @return \Xajax\Configuration\Scripts
	 */
	public function setUseUncompressedScripts(?bool $useUncompressedScripts = null): Scripts
	{
		$this->useUncompressedScripts = (bool) $useUncompressedScripts;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function isStatusMessages(): bool
	{
		return (bool) $this->statusMessages;
	}

	/**
	 * @param bool $statusMessages
	 *
	 * @return \Xajax\Configuration\Scripts
	 */
	public function setStatusMessages(?bool $statusMessages = null): Scripts
	{
		$this->statusMessages = (bool) $statusMessages;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function isWaitCursor(): bool
	{
		return (bool) $this->waitCursor;
	}

	/**
	 * @param bool $waitCursor
	 *
	 * @return \Xajax\Configuration\Scripts
	 */
	public function setWaitCursor(?bool $waitCursor = null): Scripts
	{
		$this->waitCursor = (bool) $waitCursor;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function isDeferScriptGeneration(): bool
	{
		return (bool) $this->deferScriptGeneration;
	}

	/**
	 * @param bool $deferScriptGeneration
	 *
	 * @return \Xajax\Configuration\Scripts
	 */
	public function setDeferScriptGeneration(?bool $deferScriptGeneration = null): Scripts
	{
		$this->deferScriptGeneration = (bool) $deferScriptGeneration;

		return $this;
	}

	/**
	 * JS Mode POST or GET
	 *
	 * @return string
	 */
	public function getDefaultMode(): string
	{
		// Automatic setup
		if (null === $this->defaultMode)
		{
			return $this->setDefaultMode(self::getJSDefaultMode());
		}

		return $this->defaultMode;
	}

	/**
	 * @param string $defaultMode
	 *
	 * @return string the set'd default mode
	 */
	public function setDefaultMode(string $defaultMode): string
	{
		$defaultMode = strtolower($defaultMode);
		if (in_array($defaultMode, self::getModes(), true))
		{
			$this->defaultMode = $defaultMode;
		}
		else
		{
			$this->defaultMode = self::getJSDefaultMode();
		}

		return $this->defaultMode;
	}

	/**
	 * internal modes
	 *
	 * @see $defaultMode
	 * @return array
	 */
	public static function getModes(): array
	{
		return self::$modes;
	}

	/**
	 * @return string
	 */
	public static function getJSDefaultMode(): string
	{
		return self::getModes()[0];
	}

	/**
	 * @todo test
	 * @return string
	 */
	public function getDefaultMethod(): string
	{
		return $this->defaultMethod ?? $this->setDefaultMethod('');
	}

	/**
	 * @param string $defaultMethod
	 *
	 * @return string
	 */
	public function setDefaultMethod(?string $defaultMethod = null): string
	{
		$defaultMethod = strtoupper((string) $defaultMethod);

		return $this->defaultMethod = 'GET' === $defaultMethod ? 'GET' : 'POST';
	}

	/**
	 * @todo explain
	 * @return bool
	 */
	public function isDebug(): bool
	{
		return (bool) $this->debug;
	}

	/**
	 * enable debug
	 *
	 * @return \Xajax\Configuration\Scripts
	 */
	public function enableDebug(): Scripts
	{
		$this->setDebug(true);

		return $this;
	}

	/**
	 * disable debug
	 *
	 * @return \Xajax\Configuration\Scripts
	 */
	public function disableDebug(): Scripts
	{
		$this->setDebug(false);

		return $this;
	}

	/**
	 * @param bool $debug
	 *
	 * @return \Xajax\Configuration\Scripts
	 */
	public function setDebug(?bool $debug = null): Scripts
	{
		$this->debug = (bool) $debug;

		return $this;
	}

	/**
	 * @todo explain
	 * @return bool
	 */
	public function isVerbose(): bool
	{
		return $this->verbose;
	}

	/**
	 * @param bool $verbose
	 *
	 * @return \Xajax\Configuration\Scripts
	 */
	public function setVerbose(?bool $verbose = null): Scripts
	{
		$this->verbose = (bool) $verbose;

		return $this;
	}
}