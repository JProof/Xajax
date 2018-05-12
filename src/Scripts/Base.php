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
 * @since              27.10.2017
 */

declare(strict_types=1);

namespace Jybrid\Scripts;

/**
 * Trait Base
 *
 * @package Jybrid\Scripts
 * @property-read string $scriptName           internal Name of an script -> unique identifier
 * @property-read string $fileName             the real fullName of the file without "min"
 * @property-read string $dir                  concrete directory for this file
 * @property-read int    $priority             Priority
 * @property-read bool   $useScriptLoadTimeout
 * @property-read string $relativeDir          During checking which script file has to be used the relative Directory will be set
 */
trait Base
{
	/**
	 * @param string $scriptName
	 *
	 * @return self
	 */
	public function setScriptName(?string $scriptName = null): self
	{
		$this->scriptName = $scriptName;

		return $this;
	}

	/**
	 * @param string $fileName
	 *
	 * @return self
	 */
	public function setFileName(?string $fileName = null): self
	{
		$this->fileName = $fileName;

		return $this;
	}

	/**
	 * @return null|string
	 */
	public function getScriptName(): ?string
	{
		return $this->scriptName;
	}

	/**
	 * @return null|string
	 */
	public function getDir(): ?string
	{
		return $this->dir;
	}

	/**
	 * @param string $dir
	 *
	 * @return self
	 */
	public function setDir(?string $dir = null): self
	{
		$this->dir = $dir;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getFileName(): ?string
	{
		return $this->fileName;
	}

	/**
	 * @return int
	 */
	public function getPriority(): ?int
	{
		return $this->priority;
	}

	/**
	 * @param int $priority
	 */
	public function setPriority(?int $priority = null): void
	{
		$this->priority = $priority;
	}

	/**
	 * @return null|bool
	 */
	public function isUseScriptLoadTimeout(): ?bool {
		return $this->useScriptLoadTimeout;
	}

	/**
	 * @param bool $useScriptLoadTimeout
	 */
	public function setUseScriptLoadTimeout( ?bool $useScriptLoadTimeout = null ): void {
		$this->useScriptLoadTimeout = (bool) $useScriptLoadTimeout;
	}

	/**
	 * @return null|string
	 */
	public function getRelativeDir(): ?string {
		return $this->relativeDir;
	}

	/**
	 * @param null|string $relOutDir
	 */
	public function setRelativeDir( ?string $relOutDir = null ): void {
		$this->relativeDir = $relOutDir;
	}
}