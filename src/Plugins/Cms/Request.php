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
	/**
	 * @var string
	 */
	private $sAlias;
	/*
		Object: uf

		A string or array which defines the function to be registered.
	*/
	/**
	 * @var array
	 */
	private $uf;
	/*
		Array: aConfiguration

		An associative array containing call options that will be sent to the
		browser curing client script generation.
	*/
	/**
	 * @var iterable|null
	 */
	private $aConfiguration;
	/**
	 * Holds the Command Button
	 *
	 * @var \Xajax\Plugins\Cms\Button
	 */
	protected $buttonObject;

	public function __construct(string $uf, ?iterable $clientscriptConfig = null)
	{

		$this->sAlias = $uf;

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
	/**
	 * @return string
	 */
	public function getName(): string
	{
		// Do not use sAlias here!
		return $this->sAlias;
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

	/**
	 * @param iterable|null $configure
	 *
	 * @return \Xajax\Plugins\Cms\Button
	 */
	public function getButtonScript(?iterable $configure = null): Button
	{
		return $this->buttonObject ?? $this->buttonObject = new Button($this->getName(), $configure);
	}

	/**
	 * @return array
	 */
	protected function getRequestConfiguration(): array
	{
		return (array) $this->aConfiguration;
	}

	/*
		Function: generateRequest

		Constructs and returns a <xajaxRequest> object which is capable
		of generating the javascript call to invoke this xajax enabled
		function.
	*/

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
	 * @return string
	 */
	public function generateClientScript(): string
	{
		$string = '';

		$sFunction = $this->getName();
		$sAlias    = $sFunction;
		if (0 < \strlen($this->sAlias))
		{
			$sAlias = $this->sAlias;
		}

		$sSeparator = ', ';

		$string .= "xajax.Reg('{$sAlias}', function() {";
		$string .= 'return xajax.request( ';
		$string .= '{ xjxreq: \'cms\' }, ';
		$string .= '{ parameters:arguments';

		$stringParts = [];
		$configs     = $this->getRequestConfiguration();

		// todo handle XajaxRequest Values and handle additional Post/Get Parameters

		foreach ($configs as $sKey => $sValue)
		{
			$stringParts[] = "{$sKey}: '{$sValue}'";
		}

		if (0 < \count($stringParts))
		{
			$string .= $sSeparator . '' . implode($sSeparator, $stringParts) . '';
		}
		$string .= ' }); ';
		$string .= '});';

		return $string;
	}

	/*
		Function: call

		Called by the <xajaxPlugin> that references this function during the
		request processing phase.  This function will call the specified
		function, including an external file if needed and passing along
		the specified arguments.
	*/
	/**
	 * Generic "execution" Handler
	 *
	 * @return bool
	 */
	public function call(): bool
	{


		$objResponseManager = Manager::getInstance();

		$objResponseManager->append(Factory::getResponseInstance());

		return true;
	}
}