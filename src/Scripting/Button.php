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
 * @since              15.10.2017
 */

declare(strict_types=1);

namespace Xajax\Scripting;

/**ability to configure each request particular with his own config*/

if (!\defined('XAJAX_FORM_VALUES'))
{
	/**@deprecated XAJAX_FORM_VALUES */
	\define('XAJAX_FORM_VALUES', 'get form values');
}
/*
	Constant: XAJAX_INPUT_VALUE
		Specifies that the parameter will contain the value of an input control.
*/
if (!\defined('XAJAX_INPUT_VALUE'))
{
	/**@deprecated XAJAX_INPUT_VALUE */
	\define('XAJAX_INPUT_VALUE', 'get input value');
}
/*
	Constant: XAJAX_CHECKED_VALUE
		Specifies that the parameter will consist of a boolean value of a checkbox.
*/
if (!\defined('XAJAX_CHECKED_VALUE'))
{
	/**@deprecated XAJAX_CHECKED_VALUE */
	\define('XAJAX_CHECKED_VALUE', 'get checked value');
}
/*
	Constant: XAJAX_ELEMENT_INNERHTML
		Specifies that the parameter value will be the innerHTML value of the element.
*/
if (!\defined('XAJAX_ELEMENT_INNERHTML'))
{
	/**@deprecated XAJAX_ELEMENT_INNERHTML */
	\define('XAJAX_ELEMENT_INNERHTML', 'get element innerHTML');
}
/*
	Constant: XAJAX_QUOTED_VALUE
		Specifies that the parameter will be a quoted value (string).
*/
if (!\defined('XAJAX_QUOTED_VALUE'))
{
	/**@deprecated XAJAX_QUOTED_VALUE */
	\define('XAJAX_QUOTED_VALUE', 'quoted value');
}
/*
	Constant: XAJAX_JS_VALUE
		Specifies that the parameter will be a non-quoted value (evaluated by the
		browsers javascript engine at run time.
*/
if (!\defined('XAJAX_JS_VALUE'))
{
	/**@deprecated XAJAX_JS_VALUE */
	\define('XAJAX_JS_VALUE', 'unquoted value');
}

/**
 * Class Request
 * refactor old xajaxRequest.inc.php
 *
 * @package Xajax
 */
abstract class Button
{
	use \Xajax\Errors\Call;
	static protected $allowedQuotes = ["'", '"'];
	/*
		String: sName

		The name of the function.
	*/
	private $sName;
	/*
		String: sQuoteCharacter

		A string containing either a single or a double quote character
		that will be used during the generation of the javascript for
		this function.  This can be set prior to calling <xajaxRequest->printScript>
	*/
	private $sQuoteCharacter;
	/*
		Array: aParameters

		An array of parameters that will be used to populate the argument list
		for this function when the javascript is output in <xajaxRequest->printScript>
	*/
	private $aParameters;

	/*
		Function: xajaxRequest

		Construct and initialize this request.

		sName - (string):  The name of this request.
	*/
	public function __construct(string $sName, ?iterable $configurationIface = null, ?string $qt = null)
	{
		$this->aParameters     = [];
		$this->sQuoteCharacter = '"';
		$this->sName           = $sName;
	}

	/*
		Function: useSingleQuote

		Call this to instruct the request to use single quotes when generating
		the javascript.
	*/
	public function useSingleQuote()
	{
		$this->sQuoteCharacter = "'";

		return $this;
	}

	/*
		Function: useDoubleQuote

		Call this to instruct the request to use double quotes while generating
		the javascript.
	*/
	public function useDoubleQuote()
	{
		$this->sQuoteCharacter = '"';

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
	 * Adding an Array Parameter
	 *
	 * @example ['my'=>'1','your'=>'3']; will be to js {my:'1',your:'3'}
	 *
	 * @param null|iterable $object
	 * @param null|string   $sQuote
	 *
	 * @return $this
	 * @todo    nested iteratable
	 * @todo    unittest
	 */
	public function addParameterArray(?iterable $object = null, ?string $sQuote = null): self
	{
		$string = $this->iterateKeyValuePairs($object);
		if ($string)
		{
			$this->aParameters[] = $string;
		}

		return $this;
	}

	/**
	 * KeyValuePairIterator to get an valid js String
	 *
	 * @todo unittest
	 *
	 * @param iterable|null $object
	 * @param null|string   $sQuote
	 * @param int|null      $depth
	 *
	 * @return null|string
	 */
	protected function iterateKeyValuePairs(?iterable $object = null, ?string $sQuote = null, ?int $depth = null): ?string
	{
		if (is_iterable($object) && 0 < \count($object))
		{
			$parts  = [];
			$sQuote = $sQuote ?: $this->sQuoteCharacter;
			/** @var iterable $object */
			foreach ($object as $k => $v)
			{
				if (is_iterable($v) && ($s = $this->iterateKeyValuePairs($v, $sQuote, 1)))
				{
					$parts[] = $k . ':' . $s;
				}
				else
				{
					$parts[] = $k . ':' . $sQuote . $v . $sQuote;
				}
			}

			return '{' . implode(',', $parts) . '}';
		}

		return null;
	}

	/**
	 * Adding an Key-Value-Pair
	 * Function: addParameter
	 * Adds a parameter value to the parameter list for this request.
	 * sType - (string): The type of the value to be used.
	 * sValue - (string: The value to be used.
	 * See Also:
	 * See <xajaxRequest->setParameter> for details.
	 *
	 * @param  $key
	 * @param  $value
	 *
	 * @return $this
	 */
	public function addParameter($key = null, $value = null): self
	{
		if ((\is_string($key) || \is_int($key)) && (null === $value || is_scalar($value)))
		{
			$this->setParameter(
			    \count($this->aParameters),
			    $key,
			    $value);
		}

		return $this;
	}

	public function setGetFormValues(string $formId, ?string $qt = null)
	{
		$this->aParameters[] = 'xajax.getFormValues(' . $this->getQuotedString($formId, $qt) . ')';
		return $this;
	}

	public function setGetInputValue(string $elementId, ?string $qt = null)
	{
		$this->aParameters[] = 'xajax.$(' . $this->getQuotedString($elementId, $qt) . ').value';
		return $this;
	}

	public function setGetCheckedValue(string $elementId, ?string $qt = null)
	{
		$this->aParameters[] = 'xajax.$(' . $this->getQuotedString($elementId, $qt) . ').checked';
		return $this;
	}

	public function getInnerHtml(string $elementId, ?string $qt = null)
	{
		$this->aParameters[] = 'xajax.$(' . $this->getQuotedString($elementId, $qt) . ').innerHTML';
		return $this;
	}

	protected function getQuotedString(string $str, ?string $qt = null): string
	{
		$qt = $qt && \in_array($qt, self::$allowedQuotes, true) ? $qt : $this->sQuoteCharacter;
		return $qt . trim($str) . $qt;
	}

	/**
	 * Function: setParameter
	 * Sets a specific parameter value.
	 * Parameters:
	 * nParameter - (number): The index of the parameter to set
	 * sType - (string): The type of value
	 * sValue - (string): The value as it relates to the specified type
	 * Note:
	 * Types should be one of the following
	 * <XAJAX_FORM_VALUES>,
	 * <XAJAX_QUOTED_VALUE>,
	 * <XAJAX_JS_VALUE>,
	 * <XAJAX_INPUT_VALUE>,
	 * <XAJAX_CHECKED_VALUE>.
	 * The value should be as follows:
	 * <XAJAX_FORM_VALUES> - Use the ID of the form you want to process.
	 * <XAJAX_QUOTED_VALUE> - The string data to be passed.
	 * <XAJAX_JS_VALUE> - A string containing valid javascript (either a javascript
	 * variable name that will be in scope at the time of the call or a
	 * javascript function call whose return value will become the parameter.
	 **/
	/**
	 * @return $this|\Xajax\Scripting\Button
	 * @deprecated use the particular methods
	 */
	public function setParameter()
	{
		$aArgs   = func_get_args();
		$cntArgs = \count($aArgs);
		if (1 < $cntArgs)
		{
			[$nParameter, $sType] = $aArgs;
			if (2 === $cntArgs)
			{
				$this->aParameters[$nParameter] = $this->sQuoteCharacter . $sType . $this->sQuoteCharacter;
			}
			else if (2 < $cntArgs)
			{
				if (XAJAX_FORM_VALUES === $sType)
				{
					return $this->setGetFormValues((string) $aArgs[2]);
				}
				if (XAJAX_INPUT_VALUE === $sType)
				{
					return $this->setGetInputValue((string) $aArgs[2]);
				}
				if (XAJAX_CHECKED_VALUE === $sType)
				{
					return $this->setGetCheckedValue((string) $aArgs[2]);
				}
				if (XAJAX_ELEMENT_INNERHTML === $sType)
				{
					return $this->getInnerHtml((string) $aArgs[2]);
				}
				if (XAJAX_QUOTED_VALUE === $sType)
				{
					$sValue                         = $aArgs[2];
					$this->aParameters[$nParameter] =
					    $this->sQuoteCharacter
					    . $sValue
					    . $this->sQuoteCharacter;
				}
				else if (XAJAX_JS_VALUE === $sType)
				{
					$sValue                         = $aArgs[2];
					$this->aParameters[$nParameter] = $sValue;
				}
				else
				{
					$sValue                         = $aArgs[2];
					$this->aParameters[$nParameter] = $sValue;
				}
			}
		}

		return $this;
	}

	/**
	 * Get the Method-Script
	 * Returns a string representation of the script output (javascript) from this request object.
	 *
	 * @example
	 * Config:
	 * $anXajaxUserFunction->useSingleQuote()->setParameter('anName', 'anAutoQuotedValue');
	 * Base-Mechanism as example
	 * echo $anXajaxUserFunction->getScript(); Echos :  xajax_linkButton('anAutoQuotedValue');
	 * PHP/HTML Rendering:
	 * <a onclick="<?php echo $anXajaxUserFunction->printScript() ?>">anButton</a>
	 * Parsed in Browser to:
	 * <a onclick="xajax_listDirectory('anAutoQuotedValue')">anButton</a>
	 * @see        \Xajax\Scripting\Request::printScript()
	 * @return string
	 * @deprecated use magic method __toString()
	 */
	public function getScript(): string
	{
		return (string) $this;
	}

	// build processor
	public function __toString()
	{
		$lines   = [];
		$lines[] = 'xajax.Exe(\'' . $this->sName . '\'';

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