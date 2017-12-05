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
 * @since              27.10.2017
 */

declare(strict_types=1);

namespace Xajax\Scripts;

/**
 * Trait Base
 *
 * @package Xajax\Scripts
 * @property-read string $scriptName internal Name of an script -> unique identifier
 * @property-read string $fileName   the real fullname of the file
 * @property-read string $dir        override dir for an single script
 * @property-read int    $priority   Priority
 * @property-read bool   useUncompressedScript regular or min.js
 */
trait Base
{
	/**
	 * @param string $scriptName
	 *
	 * @todo test against empty
	 * @return Base
	 */
	public function setScriptName(?string $scriptName = null)
	{
		$this->scriptName = (string) $scriptName;

		return $this;
	}

	/**
	 * @param string $fileName
	 *
	 * @todo test against empty
	 * @return Base
	 */
	public function setFileName(?string $fileName = null)
	{
		$this->fileName = (string) $fileName;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getScriptName(): string
	{
		return (string) $this->scriptName;
	}

	/**
	 * @return string
	 */
	public function getDir(): string
	{
		return (string) $this->dir;
	}

	/**
	 * @param string $dir
	 *
	 * @return Base
	 */
	public function setDir(string $dir = null)
	{
		$this->dir = $dir;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getFileName(): string
	{
		return (string) $this->fileName;
	}

	/**
	 * @return int
	 */
	public function getPriority(): int
	{
		return (int) $this->priority;
	}

	/**
	 * @param int $priority
	 */
	public function setPriority(int $priority = null)
	{
		$this->priority = $priority;
	}
}