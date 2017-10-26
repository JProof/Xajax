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
 * @since              23.09.2017
 */

declare(strict_types=1);

namespace Xajax\Configuration;

use Xajax\Core\Configuration;

/**
 * Trait Config
 *
 * @package Xajax\Config
 */
trait Config
{
	/**
	 * Simple Helper to get the Xajax Config with the old way in xajax Core  and plugins
	 *
	 * @notice this is an tmp helper method
	 * @return \Xajax\Core\Configuration
	 */
	public function getConfig(): Configuration
	{
		return \Xajax\Core\Configuration::getInstance();
	}
}