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

namespace Xajax\Core\Scripts;

use Xajax\Core\Datas\Data;

/**
 * Class Plugin
 * Plugin Script Data Object
 *
 * @package Xajax\Core\Scripts
 * @property-read string $name
 */
class Plugin extends Data implements Iface
{
	use Base;
}