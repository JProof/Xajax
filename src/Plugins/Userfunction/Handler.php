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
 * @since              14.10.2017
 */

declare(strict_types=1);

namespace Xajax\Plugins\Userfunction;

use Xajax\Configuration\RequestConfigurationIface;
use Xajax\RequestIface;
use Xajax\Response\Manager;

/**
 * Class Handler
 *
 * @package Xajax\Plugins\Userfunction
 */
class Handler
{
	/*
		String: sAlias

		An alias to use for this function.  This is useful when you want
		to call the same xajax enabled function with a different set of
		call options from what was already registered.
	*/
	private $sAlias;
	/*
		Object: uf

		A string or array which defines the function to be registered.
	*/
	private $uf;
	/*
		String: sInclude

		The path and file name of the include file that contains the function.
	*/
	private $sInclude;
	/*
		Array: aConfiguration

		An associative array containing call options that will be sent to the
		browser curing client script generation.
	*/
	private $aConfiguration;

	/*
		Function: xajaxUserFunction

		Constructs and initializes the <xajaxUserFunction> object.

		$uf - (mixed): A function specification in one of the following formats:

			- a three element array:
				(string) Alternate function name: when a method of a class has the same
					name as another function in the system, you can provide an alias to
					help avoid collisions.
				(object or class name) Class: the name of the class or an instance of
					the object which contains the function to be called.
				(string) Method:  the name of the method that will be called.
			- a two element array:
				(object or class name) Class: the name of the class or an instance of
					the object which contains the function to be called.
				(string) Method:  the name of the method that will be called.
			- a string:
				the name of the function that is available at global scope (not in a
				class.

		$sInclude - deprecated syntax - use ->configure('include','/path/to/file'); instead
		$sInclude - (string, optional):  The path and file name of the include file
			that contains the class or function to be called.

		$aConfiguration - marked as deprecated - might become reactivated as argument #2
		$aConfiguration - (array, optional):  An associative array of call options
			that will be used when sending the request from the client.

		Examples:

			$myFunction = array('alias', 'myClass', 'myMethod');
			$myFunction = array('alias', &$myObject, 'myMethod');
			$myFunction = array('myClass', 'myMethod');
			$myFunction = array(&$myObject, 'myMethod');
			$myFunction = 'myFunction';

			$myUserFunction = new xajaxUserFunction($myFunction, 'myFile.inc.php', array(
				'method' => 'get',
				'mode' => 'synchronous'
				));

			$xajax->register(XAJAX_FUNCTION, $myUserFunction);
	*/
	public function __construct($uf) // /*deprecated parameters */ $sInclude=NULL, $aConfiguration=array())
	{
		$this->sAlias         = '';
		$this->uf             = $uf;
		$this->aConfiguration = [];

		if (\is_array($this->uf) && 2 < \count($this->uf))
		{
			$this->sAlias = $this->uf[0];
			$this->uf     = \array_slice($this->uf, 1);
		}

		if (\is_array($this->uf) && 2 !== \count($this->uf))
		{
			trigger_error(
			    'Invalid function declaration for xajaxUserFunction.',
			    E_USER_ERROR
			);
		}
	}

	/*
		Function: getName

		Get the name of the function being referenced.

		Returns:

		string - the name of the function contained within this object.
	*/
	public function getName(): string
	{
		// Do not use sAlias here!
		return \is_array($this->uf) ? (string) $this->uf[1] : (string) $this->uf;
	}

	/*
		Function: configure

		Call this to set call options for this instance.
	*/
	/**
	 * @param $sName
	 * @param $sValue
	 *
	 * @deprecated use an global or plugin or handler config
	 */
	public function configure($sName, $sValue): void
	{
		if ('alias' === $sName)
		{
			$this->sAlias = $sValue;
		}
		if ('include' === $sName)
		{
			$this->sInclude = $sValue;
		}
		else
		{
			$this->aConfiguration[$sName] = $sValue;
		}
	}

	/*
		Function: generateRequest

		Constructs and returns a <xajaxRequest> object which is capable
		of generating the javascript call to invoke this xajax enabled
		function.
	*/
	/**
	 * @param string                    $sXajaxPrefix
	 * @param RequestConfigurationIface $configuration
	 *
	 * @return RequestIface
	 * @todo set possible configuration to the single request
	 */
	public function generateRequest(?string $sXajaxPrefix = null, ? RequestConfigurationIface $configuration = null): RequestIface
	{
		$sAlias = $this->getName();
		if (0 < \strlen($this->sAlias))
		{
			$sAlias = $this->sAlias;
		}

		/**
		 * @var string $sXajaxPrefix
		 * @deprecated use the plugin or instance configuration
		 * */
		return new \Xajax\Plugins\Userfunction\Request("{$sXajaxPrefix}{$sAlias}");
	}

	/*
		Function: generateClientScript

		Called by the <xajaxPlugin> that is referencing this function
		reference during the client script generation phase.  This function
		will generate the javascript function stub that is sent to the
		browser on initial page load.
	*/
	/**
	 * Refactured Generation
	 *
	 * @param $sXajaxPrefix
	 *
	 * @return string
	 */
	public function generateClientScript($sXajaxPrefix): string
	{
		$string = '';

		$sFunction = $this->getName();
		$sAlias    = $sFunction;
		if (0 < \strlen($this->sAlias))
		{
			$sAlias = $this->sAlias;
		}
		$string .= "{$sXajaxPrefix}{$sAlias} = function() { ";
		$string .= 'return xajax.request( ';
		$string .= "{ xjxfun: '{$sFunction}' }, ";
		$string .= '{ parameters: arguments';

		$sSeparator = ', ';
		foreach ($this->aConfiguration as $sKey => $sValue)
		{
			$string .= "{$sSeparator}{$sKey}: {$sValue}";
		}

		$string .= ' } ); ';
		$string .= "};\n";

		return $string;
	}

	/*
		Function: call

		Called by the <xajaxPlugin> that references this function during the
		request processing phase.  This function will call the specified
		function, including an external file if needed and passing along
		the specified arguments.
	*/
	public function call(?array $aArgs = null): bool
	{
		if (null === $aArgs)
		{
			return false;
		}

		$objResponseManager = Manager::getInstance();

		// @todo remove it!
		if (null !== $this->sInclude)
		{
			ob_start();
			require_once $this->sInclude;
			$sOutput = ob_get_clean();

//SkipDebug
			if (0 < \strlen($sOutput))
			{
				$sOutput = 'From include file: ' . $this->sInclude . ' => ' . $sOutput;
				$objResponseManager->debug($sOutput);
			}
//EndSkipDebug
		}

		$mFunction = $this->uf;

		// regular function
		if (\is_string($mFunction))
		{
			if (\function_exists($mFunction))
			{
				try
				{
					$result = \call_user_func_array($mFunction, $aArgs);
				}
				catch (\Exception$exception)
				{
					// todo Log
					return false;
				}
				$objResponseManager->append($result);
				return true;
			}
		}        // todo Log
		return false;
	}
}