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
 * @since              27.10.2017
 */

declare(strict_types=1);

namespace Jybrid\Scripts;

interface Iface
{
	/**
	 * Getting the Script name
	 *
	 * @return null|string null = file was locket | string = the relative url of the js file
	 * @throws \UnexpectedValueException // script was not set or not found in directories
	 */
	public function getScriptName(): ? string;

	/**
	 * Each script has his own priority saved in the Object
	 *
	 * @return int|null null was never set
	 */
	public function getPriority(): ?int;
}