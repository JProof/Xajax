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
 * @since              24.09.2017
 */

declare(strict_types=1);

namespace Jybrid\Configuration;

/**
 * Trait Scripts
 *
 * @package Jybrid\Config
 */
class Scripts extends Base
{
	/**
	 * @var array
	 */
	protected static $modes = [ 'asynchronous', 'synchronous', ];
	/**
	 * @var self
	 */
	private static $instance;
	/**
	 * Uncompressed Javascript if exists
	 *
	 * @var bool
	 */
	protected $useUncompressedScripts;
	/**
	 * JS
	 * true - jybrid should update the status bar during a request
	 * false - jybrid should not display the status of the request
	 *
	 * @var bool
	 */
	protected $statusMessages;
	/**
	 * true - jybrid should display a wait cursor when making a request
	 * false - jybrid should not show a wait cursor during a request
	 *
	 * @var bool
	 */
	protected $waitCursor;
	/**
	 * A flag that indicates whether
	 * script deferral is in effect or not
	 *
	 * @var bool
	 */
	protected $deferScriptGeneration;
	/**
	 * Debug Flag for Jybrid. Set to true only during development.
	 *
	 * @var bool
	 */
	protected $debug;
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
	 * @return \Jybrid\Configuration\Scripts
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
		return $this->useUncompressedScripts ?? false;
	}

	/**
	 * @param bool $useUncompressedScripts
	 *
	 * @return \Jybrid\Configuration\Scripts
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
		return $this->statusMessages ?? false;
	}

	/**
	 * @param bool $statusMessages
	 *
	 * @return \Jybrid\Configuration\Scripts
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
		return $this->waitCursor ?? false;
	}

	/**
	 * @param bool $waitCursor
	 *
	 * @return \Jybrid\Configuration\Scripts
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
		return $this->deferScriptGeneration ?? false;
	}

	/**
	 * @param bool $deferScriptGeneration
	 *
	 * @return \Jybrid\Configuration\Scripts
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
		return $this->defaultMode ?? $this->setDefaultMode( self::getJSDefaultMode() );
	}

	/**
	 * @param string $defaultMode
	 *
	 * @return string the set'd default mode
	 */
	public function setDefaultMode(string $defaultMode): string
	{
		$defaultMode = strtolower($defaultMode);
		if (\in_array($defaultMode, self::getModes(), true))
		{
			$this->defaultMode = $defaultMode;
		} else
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
		return $this->defaultMethod ?? $this->setDefaultMethod();
	}

	/**
	 * @param string $defaultMethod
	 *
	 * @return string
	 */
	public function setDefaultMethod(?string $defaultMethod = null): string
	{
		$defaultMethod = strtoupper((string) $defaultMethod);

		return $this->defaultMethod = ('GET' === $defaultMethod) ? 'GET' : 'POST';
	}

	/**
	 * @todo explain
	 * @return bool
	 */
	public function isDebug(): bool
	{
		return $this->debug ?? false;
	}

	/**
	 * @param bool $debug
	 *
	 * @return \Jybrid\Configuration\Scripts
	 */
	public function setDebug( ?bool $debug = null ): Scripts {
		$this->debug = (bool) $debug;

		return $this;
	}

	/**
	 * enable debug
	 *
	 * @return \Jybrid\Configuration\Scripts
	 */
	public function enableDebug(): Scripts
	{
		$this->setDebug(true);

		return $this;
	}

	/**
	 * disable debug
	 *
	 * @return \Jybrid\Configuration\Scripts
	 */
	public function disableDebug(): Scripts
	{
		$this->setDebug(false);

		return $this;
	}
}