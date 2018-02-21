<?php
/**
 * PHP version $phpversion$
 *
 * @category
 * @package            Xajax Core  Xajax\Plugins\Cms
 * @author             ${JProof}
 * @copyright          ${copyright}
 * @license            ${license}
 * @link
 * @see                ${docu}
 * @since              31.12.2017
 */

declare(strict_types=1);

namespace Xajax\Plugins\Cms;

class Button extends \Xajax\Scripting\Button
{
	/**
	 * Button constructor.
	 *
	 * @param string        $sName
	 * @param iterable|null $configurationIface
	 */
	public function __construct(string $sName, ?iterable $configurationIface = null)
	{
		parent::__construct($sName, $configurationIface);

		$this->useSingleQuote();
	}
}