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
 * @since              30.09.2017
 */

declare(strict_types=1);

namespace Xajax\Plugins\Userfunction;

use Xajax\Core\RequestIface;
use Xajax\Factory;

/**
 * Class UserFunction
 *
 * @package Xajax\Plugin\Request
 */
class Request extends \Xajax\Core\Request implements RequestIface
{
	/**
	 *
	 * @param string $fName
	 * @param null   $localConfig
	 *
	 * @return Request
	 */
	public static function autoRegister(?string $fName = null, $localConfig = null): Request
	{
		/** @var Request $request */
		$request = Factory::getInstance()->getPlugin('function')->registerRequest((array) $fName);

		return $request;
	}
}