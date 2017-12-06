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

use Xajax\Factory;
use Xajax\RequestIface;

/**
 * Class UserFunction
 *
 * @package Xajax\Plugin\Request
 */
class Request extends \Xajax\Request\Request implements RequestIface
{
	/**
	 * Automatic Register-Plugin-Method on demand
	 *
	 * @param string $fName
	 * @param null   $localConfig
	 *
	 * @return \Xajax\Plugins\Userfunction\Request
	 * @throws \RuntimeException
	 */
	public static function autoRegister(?string $fName = null, $localConfig = null): Request
	{
		/** @var Plugin $request */
		$requestPlugin = Factory::getInstance()->getRequestPlugin('userfunction');
		$handler       = null;
		if ($requestPlugin instanceof Plugin)
		{
			try
			{
				$handler = $requestPlugin->registerRequest((array) $fName);
			}
			catch (\RuntimeException $exception)
			{
				var_dump($exception);
				die;
			}
		}
		else
		{
			throw new \RuntimeException(__CLASS__ . '::' . __METHOD__ . ' The Plugin was not autoregistered');
		}

		return $handler;
	}
}