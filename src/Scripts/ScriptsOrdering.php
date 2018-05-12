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
 * @since              08.12.2017
 */

declare(strict_types=1);

namespace Jybrid\Scripts;

/**
 * Class ScriptsOrdering
 *
 * @package Jybrid\Scripts
 */
class ScriptsOrdering extends Queue
{
	/**
	 * scriptNames jybrid and jybrid.debug are already registered. If you want to use Jybrid for outputting other scrips like jQuery they must be
	 * inserted before
	 *
	 * @see \Jybrid\Scripts\Scripts::getScriptUrls()
	 *
	 * @param string $scriptName
	 *
	 * @return bool
	 */
	public function scriptExists(string $scriptName): bool
	{
		foreach ($this as $sName)
		{
			if ($sName === $scriptName)
			{
				return true;
			}
		}
		return false;
	}

	/**
	 * New Scripts (scriptNames) can only be append at this Time
	 *
	 * @param string $scriptName
	 */
	public function appendScript(string $scriptName)
	{
		$this->insert($scriptName, $this->getLowestPriority() - 1);
	}
}