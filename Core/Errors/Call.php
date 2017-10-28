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

namespace Xajax\Core\Errors;

trait Call
{
	public function __call($name, $arguments)
	{
		if (!method_exists($this, $name))
		{
			throw new \BadMethodCallException($name . ' BadMethodCall', E_ERROR);
		}
	}
}