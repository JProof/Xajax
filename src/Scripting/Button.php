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
	public function addParameterArray(?iterable $object = null, ?string $key = null, ?string $sQuote = null): self
	{
		$string = $this->iterateKeyValuePairs($object);
		if ($string)
		{
			if ($key)
			{
				$this->aParameters[$key] = $string;
			}
			else
			{
				$this->aParameters[] = $string;
			}
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
	 * Adding the xajaxFormValues('formId') method to the  click-button-script
	 *
	 * @param string      $elementId
	 * @param null|string $key optional Key
	 * @param null|string $qt
	 *
	 * @return $this
	 */
	public function setGetFormValues(string $elementId, ?string $key = null, ?string $qt = null): self
	{
		$str = 'xajax.getFormValues(' . $this->getQuotedString($elementId, $qt) . ')';
		if ($key)
		{
			$this->aParameters[$key] = $str;
		}
		else
		{
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
		$str = 'xajax.getValue(' . $this->getQuotedString($elementId, $qt) . ')';
		if ($key)
		{
			$this->aParameters[$key] = $str;
		}
		else
		{
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
	public function getInnerHtml(string $elementId, ?string $key = null, ?string $qt = null)
	{
		$str = 'xajax.$(' . $this->getQuotedString($elementId, $qt) . ').innerHTML';
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
		$qt = $qt && \in_array($qt, self::$allowedQuotes, true) ? $qt : $this->sQuoteCharacter;
		return $qt . trim($str) . $qt;
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