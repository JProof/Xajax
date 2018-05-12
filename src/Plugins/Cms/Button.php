<?php
/**
 * PHP version $phpversion$
 *
 * @category
 * @package            Jybrid Core  Jybrid\Plugins\Cms
 * @author             ${JProof}
 * @copyright          ${copyright}
 * @license            ${license}
 * @link
 * @see                ${docu}
 * @since              31.12.2017
 */

declare(strict_types=1);

namespace Jybrid\Plugins\Cms;

use Jybrid\Interfaces\IfaceButton;

class Button extends \Jybrid\Scripting\Button implements IfaceButton
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