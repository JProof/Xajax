<?php
/**
 * PHP version php7
 *
 * @category
 * @package            jybrid-php-7
 * @author             ${JProof}
 * @copyright          ${copyright}
 * @license            ${license}
 * @link
 * @see                ${docu}
 * @since              15.10.2017
 */

declare(strict_types=1);

namespace Jybrid\Plugin\Request;

/**
 * Class Plugins
 *
 * @package Jybrid\Plugin\RequestRequest
 * @method Data offsetGet($offset)
 */
class Datas extends \Jybrid\Datas\Datas
{
	/**
	 * @param      $nPriority
	 * @param null $datas
	 */
	public function addPlugin(?int $nPriority = null, $datas = null)
	{
		if (null === $nPriority)
		{
			$nPriority = \count( $this->getContainer() );
		}
		$this->offsetSet($nPriority, $datas);
	}

	/**
	 * Try to get an registered request plugin by his name
	 *
	 * @param string $name
	 *
	 * @return \Jybrid\Plugin\Request\Data|null
	 */
	public function getByName( string $name ): ?Data {

		/** @var \Jybrid\Plugin\Request\Data $item */
		foreach ( $this->getContainer() as $item ) {
			if ( $item->getPluginInstance()->getName() === $name ) {
				return $item;
			}
		}

		return null;
	}
}