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
 * Trait Deprecated
 *
 * @package Xajax\Config
 */
trait Deprecated
{
	/**
	 * @deprecated jproof/xajax 0.7.2 no usage found
	 * @var bool
	 */
	protected $allowBlankResponse = false;
	/**
	 * @deprecated jproof/xajax 0.7.2 no usage found
	 * @var bool
	 */
	protected $allowAllResponseTypes = false;
	/**
	 * @deprecated jproof/xajax 0.7.2 no usage found
	 * @var bool
	 */
	protected $generateStubs = true;
	/**
	 * @deprecated jproof/xajax 0.7.2  use an special plugin configuration
	 * @var int
	 */
	protected $timeout = 6000;

	/**
	 * @return bool
	 */
	public function isAllowBlankResponse(): bool
	{
		return $this->allowBlankResponse;
	}

	/**
	 * @param bool $allowBlankResponse
	 */
	public function setAllowBlankResponse(bool $allowBlankResponse = true)
	{
		$this->allowBlankResponse = $allowBlankResponse;
	}

	/**
	 * @return bool
	 */
	public function isAllowAllResponseTypes(): bool
	{
		return $this->allowAllResponseTypes;
	}

	/**
	 * @param bool $allowAllResponseTypes
	 */
	public function setAllowAllResponseTypes(bool $allowAllResponseTypes = true)
	{
		$this->allowAllResponseTypes = $allowAllResponseTypes;
	}

	/**
	 * @return bool
	 */
	public function isGenerateStubs(): bool
	{
		return $this->generateStubs;
	}

	/**
	 * @param bool $generateStubs
	 */
	public function setGenerateStubs(bool $generateStubs = true)
	{
		$this->generateStubs = $generateStubs;
	}

	/**
	 * @return int
	 */
	public function getTimeout(): int
	{
		return $this->timeout;
	}

	/**
	 * @param int $timeout
	 */
	public function setTimeout(int $timeout)
	{
		$this->timeout = $timeout;
	}
}