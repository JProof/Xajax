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
 * @since              15.10.2017
 */

declare(strict_types=1);

namespace Jybrid\Scripting;

use Jybrid\Errors\TraitCall;

/**
 * Class Request
 * refactor old jybridRequest.inc.php
 *
 * @package Jybrid
 */
abstract class Button extends Base
{
	use TraitCall;
	/*
		String: sName

		The name of the function.
	*/
	private $sName;
	/*
		String: sQuoteCharacter

		A string containing either a single or a double quote character
		that will be used during the generation of the javascript for
		this function.  This can be set prior to calling <jybridRequest->printScript>
	*/
	private $sQuoteCharacter;
	/*
		Array: aParameters

		An array of parameters that will be used to populate the argument list
		for this function when the javascript is output in <jybridRequest->printScript>
	*/
	private $aParameters;

	/*
		Function: jybridRequest

		Construct and initialize this request.

		sName - (string):  The name of this request.
	*/
	public function __construct(string $sName, ?iterable $configurationIface = null, ?string $qt = null)
	{
		$this->aParameters     = [];
		$this->sQuoteCharacter = self::SQ;
		$this->sName           = $sName;
	}

	/**
	 * Function: useSingleQuote
	 * Call this to instruct the request to use single quotes when generating
	 * the javascript.
	 */
	public function useSingleQuote()
	{
		$this->sQuoteCharacter = self::SQ;

		return $this;
	}

	/**
	 * Function: useSingleQuote
	 * Call this to instruct the request to use single quotes when generating
	 * the javascript.
	 */
	public function useSingleQuoteEscape()
	{
		$this->sQuoteCharacter = self::SQE;

		return $this;
	}

	/**
	 * Function: useDoubleQuote
	 * Call this to instruct the request to use double quotes while generating
	 * the javascript.
	 */
	public function useDoubleQuote()
	{
		$this->sQuoteCharacter = self::DQ;

		return $this;
	}

	/**
	 * Function: useDoubleQuote
	 * Call this to instruct the request to use double quotes while generating
	 * the javascript.
	 */
	public function useDoubleQuoteEscape()
	{
		$this->sQuoteCharacter = self::DQE;

		return $this;
	}

	/*
		Function: clearParameters

		Clears the parameter list associated with this request.
	*/
	public function clearParameters()
	{
		$this->aParameters = [];

		return $this;
	}

	/**
	 * Adding or overwriting an Key ValuePair
	 *
	 * @param string                $key
	 * @param int|string|float|bool $value
	 * @param null|string           $sQuote
	 *
	 * @return \Jybrid\Scripting\Button
	 * @todo testing
	 */
	public function addParameter( string $key, $value = null, ?string $sQuote = null ): self {
		$this->addParameterArray( [ $key => $value ], $key, $sQuote );

		return $this;
	}

	/**
	 * Adding an Array Parameter
	 *
	 * @example ['my'=>'1','your'=>'3']; will be to js {my:'1',your:'3'}
	 *
	 * @param null|iterable $object
	 * @param null|string   $key
	 * @param null|string   $sQuote
	 *
	 * @return $this
	 * @todo    nested iteratable
	 * @todo    unittest
	 */
	public function addParameterArray(?iterable $object = null, ?string $key = null, ?string $sQuote = null): self
	{
		$string = $this->iterateKeyValuePairs($object, $sQuote);
		if ($string)
		{
			if ($key)
			{
				$this->aParameters[$key] = $string;
			} else {
				$this->aParameters[] = $string;
			}
		}

		return $this;
	}

	/**
	 * KeyValuePairIterator to get an valid js String
	 *
	 * @todo unitTest
	 *
	 * @param iterable|null $object
	 * @param null|string   $sQuote
	 * @param int|null      $depth // depth is not used currently
	 *
	 * @return null|string
	 */
	protected function iterateKeyValuePairs(?iterable $object = null, ?string $sQuote = null, ?int $depth = null): ?string
	{
		if (is_iterable($object) && 0 < \count($object))
		{
			$parts = [];

			$depth = $depth ?? 1;
			/** @var iterable $object */
			foreach ($object as $k => $v)
			{
				if (is_iterable($v) && ($s = $this->iterateKeyValuePairs($v, $sQuote, $depth)))
				{
					$parts[] = $k . ':' . $s;
				} elseif ( is_scalar( $v ) ) {
					if ( \is_string( $v ) ) {
						$parts[] = $k . ':' . $this->getQuotedString( $v, $sQuote );
					} elseif ( \is_bool( $v ) ) {
						$parts[] = $k . ':' . ( $v ? 'true' : 'false' );
					} elseif ( null === $v ) {
						$parts[] = $k . ':' . 'null';
					} else {
						// must bee float or int
						$parts[] = $k . ':' . $v;
					}
				}
				// no type found error
			}

			return '{' . implode(',', $parts) . '}';
		}

		return null;
	}

	/**
	 * Adding the jybridFormValues('formId') method to the  click-button-script
	 *
	 * @param string      $elementId
	 * @param null|string $key optional Key
	 * @param null|string $qt
	 *
	 * @return $this
	 */
	public function setGetFormValues(string $elementId, ?string $key = null, ?string $qt = null): self
	{
		$str = 'jybrid.getFormValues(' . $this->getQuotedString( $elementId, $qt ) . ')';
		if ($key)
		{
			$this->aParameters[$key] = $str;
		} else {
			$this->aParameters[] = $str;
		}

		return $this;
	}

	/**
	 * Simply get "value" of an html field
	 *
	 * @param string      $elementId
	 * @param null|string $key
	 * @param null|string $qt
	 *
	 * @return $this
	 */
	public function setGetValue(string $elementId, ?string $key = null, ?string $qt = null): self
	{
		$str = 'jybrid.getValue(' . $this->getQuotedString( $elementId, $qt ) . ')';
		if ($key)
		{
			$this->aParameters[$key] = $str;
		} else {
			$this->aParameters[] = $str;
		}

		return $this;
	}

	/**
	 * @param string      $elementId
	 * @param null|string $key
	 * @param null|string $qt
	 *
	 * @return $this
	 */
	public function getInnerHtml(string $elementId, ?string $key = null, ?string $qt = null): self
	{
		$str = 'jybrid.$(' . $this->getQuotedString( $elementId, $qt ) . ').innerHTML';
		$key ? $this->aParameters[$key] = $str : $this->aParameters[] = $str;

		return $this;
	}

	/**
	 * Internal Helper to wrap Quoutes around an js expression
	 *
	 * @param string      $str
	 * @param null|string $qt
	 *
	 * @return string
	 */
	protected function getQuotedString(string $str, ?string $qt = null): string
	{
		$qt = $this->getQuote($qt);

		// escaping Slashes or Backslashes in Commands ..observe this behavour case should never happend
		$str = ( in_array( $qt, [ self::SQ, self::SQE ] ) ) ?
			str_replace( self::SQ, self::SQE, $str ) :
			str_replace( self::DQ, self::DQE, $str );

		return $qt . $str . $qt;
	}

	/**
	 * Make sure, quotes are valid
	 *
	 * @param null|string $qt
	 *
	 * @return string
	 */
	protected function getQuote(?string $qt = null): string
	{
		return $qt && \in_array($qt, self::$allowedQuotes, true) ? $qt : $this->sQuoteCharacter;
	}

	/**
	 * Simply create json Object
	 *
	 * @param string      $key
	 * @param             $value
	 * @param null|string $qt
	 *
	 * @return string
	 */
	protected function objectivateKeyValue(string $key, $value, ?string $qt = null): string
	{
		$str = '';
		$str .= '{' . $key . ':' . $value . '}';

		return $str;
	}

	/**
	 * Get the Method-Script
	 * Returns a string representation of the script output (javascript) from this request object.
	 *
	 * @example
	 * Config:
	 * $anJybridUserFunction->useSingleQuote()->setParameter('anName', 'anAutoQuotedValue');
	 * Base-Mechanism as example
	 * echo $anJybridUserFunction->getScript(); Echos :  jybrid_linkButton('anAutoQuotedValue');
	 * PHP/HTML Rendering:
	 * <a onclick="<?php echo $anJybridUserFunction->printScript() ?>">anButton</a>
	 * Parsed in Browser to:
	 * <a onclick="jybrid_listDirectory('anAutoQuotedValue')">anButton</a>
	 * @see        \Jybrid\Request::printScript()
	 * @return string
	 * @deprecated use magic method __toString()
	 */
	public function getScript(): string
	{
		return (string) $this;
	}

	// build processor
	public function __toString(): string {
		$lines   = [];
		$lines[] = 'jybrid.Exe(\'' . $this->sName . '\'';

		$sSeparator = null;
		$params     = $this->aParameters;
		if (0 < \count($params))
		{
			// starting parameters
			$lines[] = ',' . implode(',', $params);
		}

		$lines[] = ');';

		return implode($lines);
	}
}