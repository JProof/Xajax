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

namespace Xajax\Plugins\Cms;

use Xajax\Factory;
use Xajax\RequestIface;
use Xajax\Response\Manager;

/**
 * Class Handler
 *
 * @package Xajax\Plugins\Cms
 */
class Request
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
		Array: aConfiguration

		An associative array containing call options that will be sent to the
		browser curing client script generation.
	*/
	private $aConfiguration;

	public function __construct(string $uf, ?iterable $clientscriptConfig = null)
	{
		$this->sXajaxPrefix   = 'xajax_';
		$this->sAlias         = '';
		$this->uf             = $uf;
		$this->aConfiguration = $clientscriptConfig;

		if (\is_array($this->uf) && 2 < \count($this->uf))
		{
			$this->sAlias = $this->uf[0];
			$this->uf     = \array_slice($this->uf, 1);
		}

		if (\is_array($this->uf) && 2 !== \count($this->uf))
		{
			trigger_error(
			    'Invalid function declaration for xajaxCms.',
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

	}

	public function getClientScript(): string
	{

		$string = '';

		$sFunction = $this->getName();
		$sAlias    = $sFunction;
		if (0 < \strlen($this->sAlias))
		{
			$sAlias = $this->sAlias;
		}

		$sSeparator = ', ';

		$string .= "{$this->sXajaxPrefix}{$sAlias} = function() { ";
		$string .= 'return xajax.request( ';
		$string .= '{ xjxcms: 1 }, ';
		$string .= '{ parameters: arguments';

		$stringParts = [];
		foreach ($this->getRequestConfiguration() as $sKey => $sValue)
		{
			$stringParts[] = "{$sKey}: {$sValue}";
		}
		if (0 < \count($stringParts))
		{
			$string .= $sSeparator . implode($sSeparator, $stringParts);
		}
		$string .= ' } ); ';
		$string .= "};\n";

		return $string;
	}

	public function getButtonScript()
	{
	}

	protected function getRequestConfiguration()
	{
		return (array) $this->aConfiguration;
	}

	/*
		Function: generateRequest

		Constructs and returns a <xajaxRequest> object which is capable
		of generating the javascript call to invoke this xajax enabled
		function.
	*/
	/**
	 * @param string       $sXajaxPrefix
	 * @param RequestIface $requestConfig
	 *
	 * @return RequestIface
	 * @todo set possible configuration to the single request
	 */
	public function generateRequest(?string $sXajaxPrefix = null): RequestIface
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
		return new \Xajax\Plugins\Cms\Request("{$sXajaxPrefix}{$sAlias}");
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
	 * @deprecated  use getClientScript
	 */
	public function generateClientScript(?string $sXajaxPrefix = null): string
	{
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


		$objResponseManager = Manager::getInstance();

		$objResponseManager->append(Factory::getResponseInstance());

		return true;
	}
}