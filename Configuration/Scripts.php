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
trait Scripts
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
	 * @return bool
	 */
	public function isUseUncompressedScripts(): bool
	{
		return (bool) $this->useUncompressedScripts;
	}

	/**
	 * @param bool $useUncompressedScripts
	 */
	public function setUseUncompressedScripts(?bool $useUncompressedScripts = null)
	{
		$this->useUncompressedScripts = (bool) $useUncompressedScripts;
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
	 */
	public function setStatusMessages(?bool $statusMessages = null)
	{
		$this->statusMessages = (bool) $statusMessages;
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
	 */
	public function setWaitCursor(?bool $waitCursor = null)
	{
		$this->waitCursor = (bool) $waitCursor;
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
	 */
	public function setDeferScriptGeneration(?bool $deferScriptGeneration = null)
	{
		$this->deferScriptGeneration = (bool) $deferScriptGeneration;
	}
}