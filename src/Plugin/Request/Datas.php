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
 * @since              15.10.2017
 */

declare(strict_types=1);

namespace Xajax\Plugin\Request;

/**
 * Class Plugins
 *
 * @package Xajax\Plugin\Request
 * @method Data offsetGet($offset)
 */
class Datas extends \Xajax\Datas\Datas
{
	/**
	 * @param      $nPriority
	 * @param null $datas
	 */
	public function addPlugin(?int $nPriority = null, $datas = null)
	{
		if (null === $nPriority)
		{
			$nPriority = count($this->getContainer());
		}
		$this->offsetSet($nPriority, $datas);
	}
}