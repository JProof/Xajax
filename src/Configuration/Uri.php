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
 * Trait Uri
 *
 * @package Xajax\Config
 */
trait Uri
{
	/**
	 * requestURI - (optional):  The <xajax->sRequestURI> to be used
	 * for calls back to the server.  If empty, xajax fills in the current
	 * URI that initiated this request.
	 *
	 * @var string
	 */
	protected $requestURI;
	/**
	 * The path to the folder that contains the xajax javascript files.
	 *
	 * @var string
	 */
	protected $javascriptURI;

	/**
	 * @todo testings
	 * @return string
	 */
	public function getRequestURI(): string
	{
		return (string) $this->requestURI;
	}

	/**
	 * @todo testings
	 *
	 * @param string $requestURI
	 */
	public function setRequestURI(?string $requestURI = null)
	{
		$this->requestURI = $requestURI;

		return $this;
	}


}