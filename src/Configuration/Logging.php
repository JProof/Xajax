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
 * @since              29.10.2017
 */

declare(strict_types=1);

namespace Xajax\Configuration;

/**
 * Trait Logging
 *
 * @package Xajax\Configuration
 * @property-read bool toHtml
 */
trait Logging
{
	/**
	 * @return bool
	 */
	public function isToHtml(): bool
	{
		return $this->toHtml;
	}

	/**
	 * @param bool $toHtml
	 */
	public function setToHtml(?bool $toHtml = null)
	{
		$this->toHtml = (bool) $toHtml;

		return $this;
	}
}