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

namespace Xajax\Core\Plugin;

/**
 * Class Plugins
 *
 * @package Xajax\Core\Plugin\Request
 */
class Datas extends \Xajax\Core\Datas\Datas
{
	/**
	 * @param      $nPriority
	 * @param null $datas
	 *
	 * @todo implement fully
	 */
	public function addPlugin(?int $nPriority = null, $datas = null)
	{

	}

	/**
	 * Try to find an Plugin by Name
	 *
	 * @param null|string $name the plugin-name
	 *
	 * @return bool|\Xajax\Core\Plugin\Data
	 */
	public function getByName(?string $name = null)
	{
		if (null === $name)
		{
			throw new \InvalidArgumentException(__METHOD__ . ' Name can not be NULL!');
		}

		/** @var \Xajax\Core\Plugin\Data $plugin */
		foreach ($this as $plugin)
		{
			if ($plugin->getName() === $name)
			{
				return $plugin;
			}
		}

		return false;
	}
}