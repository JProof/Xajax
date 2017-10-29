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
 * @since              26.10.2017
 */

declare(strict_types=1);

namespace Xajax\Errors;

use BadMethodCallException;

/**
 * Trait Call
 *
 * @package Xajax\Errors
 */
trait Call
{
	/**
	 * @param $name
	 * @param $arguments
	 *
	 * @throws BadMethodCallException
	 */
	public function __call($name, $arguments)
	{
		if (!method_exists($this, $name))
		{
			throw new BadMethodCallException('The method: "' . $name . '" was not found', E_ERROR);
		}
	}
}