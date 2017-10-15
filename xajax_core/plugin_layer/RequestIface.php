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
 * @since              21.09.2017
 */

declare(strict_types=1);

namespace Xajax\plugin_layer;

use xajaxRequest;

/**
 * Interface Iface
 * @deprecated in old Plugin
 * @package Xajax\plugin_layer
 */
interface RequestIface
{
	/**
	 * @return string
	 */
	public function getName(): string;

	/**
	 * Registers an Single Request
	 *
	 * @since 7.0
	 *
	 * @param array $aArgs
	 *
	 * @return xajaxRequest
	 */
	public function registerRequest(array $aArgs = []): xajaxRequest;
}