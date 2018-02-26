<?php
/**
 * PHP version $phpversion$
 *
 * @category
 * @package            Xajax Core  Xajax\Scripting
 * @author             ${JProof}
 * @copyright          ${copyright}
 * @license            ${license}
 * @link
 * @see                ${docu}
 * @since              26.02.2018
 */

declare(strict_types=1);

namespace Xajax\Scripting;

/**
 * Class Base
 * Scripting Helper/BaseClass
 */
class Base
{
	/**
	 * SingleQuotes
	 */
	public const SQ = "'";

	/**
	 * SingleQuotesEscape
	 */
	public const SQE = "\'";

	/**
	 * DoubleQuotes
	 */
	public const DQ = '"';

	/**
	 * DoubleQuotesEscape
	 */
	public const DQE = '\"';

	static protected $allowedQuotes = [self::SQ, self::SQE, self::DQ, self::DQE];
}