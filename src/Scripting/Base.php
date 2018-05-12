<?php
/**
 * PHP version $phpversion$
 *
 * @category
 * @package            Jybrid Core  Jybrid\Scripting
 * @author             ${JProof}
 * @copyright          ${copyright}
 * @license            ${license}
 * @link
 * @see                ${docu}
 * @since              26.02.2018
 */

declare(strict_types=1);

namespace Jybrid\Scripting;

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

	/**
	 * javascript window
	 */
	public const WIN = 'window';

	/**
	 * javascript document
	 */
	public const DOC = 'document';

	static protected $allowedQuotes = [self::SQ, self::SQE, self::DQ, self::DQE];

	/**
	 * Method to check the perhapsString can be made to an string
	 * Null can be stringify'd as empty string
	 *
	 * @param $perhapsString
	 *
	 * @return string
	 */
	public static function canStringify( $perhapsString ) {
		if ( null === $perhapsString ) {
			return '';
		}
		try {
			return (string) $perhapsString;
		}
		catch ( \InvalidArgumentException $exception ) {
			throw $exception;
		}
	}
}