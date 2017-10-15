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
		return $this->useUncompressedScripts;
	}

	/**
	 * @param bool $useUncompressedScripts
	 */
	public function setUseUncompressedScripts(bool $useUncompressedScripts = true)
	{
		$this->useUncompressedScripts = $useUncompressedScripts;
	}

	/**
	 * @return bool
	 */
	public function isStatusMessages(): bool
	{
		return $this->statusMessages;
	}

	/**
	 * @param bool $statusMessages
	 */
	public function setStatusMessages(bool $statusMessages = false)
	{
		$this->statusMessages = $statusMessages;
	}

	/**
	 * @return bool
	 */
	public function isWaitCursor(): bool
	{
		return $this->waitCursor;
	}

	/**
	 * @param bool $waitCursor
	 */
	public function setWaitCursor(bool $waitCursor = true)
	{
		$this->waitCursor = $waitCursor;
	}

	/**
	 * @return bool
	 */
	public function isDeferScriptGeneration(): bool
	{
		return $this->deferScriptGeneration;
	}

	/**
	 * @param bool $deferScriptGeneration
	 */
	public function setDeferScriptGeneration(bool $deferScriptGeneration = true)
	{
		$this->deferScriptGeneration = $deferScriptGeneration;
	}
}