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
 * @since              27.10.2017
 */

declare(strict_types=1);

namespace Xajax\Scripts;

interface Iface
{
	/**
	 * Getting the Script name
	 *
	 * @return string
	 */
	public function getScriptName(): string;

	/**
	 * Each script has his own priority saved in the Object
	 *
	 * @return int
	 */
	public function getPriority(): int;
}