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
 * @since              08.12.2017
 */

declare(strict_types=1);

namespace Xajax\Scripts;

/**
 * Class ScriptsOrdering
 *
 * @package Xajax\Scripts
 */
class ScriptsOrdering extends Queue
{
	/**
	 * scriptNames xajax and xajax.debug are already registered. If you want to use Xajax for outputting other scrips like jQuery they must be
	 * inserted before
	 *
	 * @see \Xajax\Scripts\Scripts::getScriptUrls()
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