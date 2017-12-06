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
 * Trait Language
 *
 * @package Xajax\Config
 * @todo    check language folder
 * @todo    refacture languageManager or make an Vote
 */
trait Language
{
	/**
	 * @var bool
	 */
	protected $useDebugLanguage = false;
	/**
	 * @see \Language
	 * @var string
	 */
	protected $language;

	/**
	 * @return string
	 */
	public function getLanguage(): string
	{
		return (string) $this->language;
	}

	/**
	 * @param string $language
	 *
	 * @return self
	 */
	public function setLanguage(?string $language = null)
	{
		$this->language = (string) $language;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function isUseDebugLanguage(): bool
	{
		return (bool) $this->useDebugLanguage;
	}

	/**
	 * @param bool $useDebugLanguage
	 *
	 * @return self
	 */
	public function setUseDebugLanguage(?bool $useDebugLanguage = null)
	{
		$this->useDebugLanguage = (bool) $useDebugLanguage;

		return $this;
	}
}