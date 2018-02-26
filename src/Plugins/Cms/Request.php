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
use Xajax\Scripting\Base;

/**
 * Class Handler
 *
 * @package Xajax\Plugins\Cms
 */
class Request
{
	/**
	 * Need to set the ExecutionName
	 * xajax.Exe('{$this->name}')
	 *
	 * @var string
	 */
	private $name;
	/**
	 * An associative array containing call options that will be sent to the
	 * browser curing client script generation.
	 *
	 * @var iterable|null
	 */
	private $aConfiguration;
	/**
	 * Holds the Command Button
	 *
	 * @var \Xajax\Plugins\Cms\Button
	 */
	protected $buttonObject;

	/**
	 * Request constructor.
	 *
	 * @param string        $name
	 * @param iterable|null $clientscriptConfig
	 */
	public function __construct(string $name, ?iterable $clientscriptConfig = null)
	{

		// todo check name against valid method name
		$this->name = $name;

		// todo modify config params such as "uri" as uppercase
		$this->aConfiguration = $clientscriptConfig;
	}

	/**
	 * Function: getName
	 * Get the name of the function being referenced.
	 *
	 * @return string the name of the function
	 */
	public function getName(): string
	{
		// Do not use sAlias here!
		return $this->name;
	}

	/**
	 * Call this to set call options for this instance.
	 *
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

	/**
	 * Refactured Generation
	 * Called by the <xajaxPlugin> that is referencing this function
	 * reference during the client script generation phase.  This function
	 * will generate the javascript function stub that is sent to the
	 * browser on initial page load.
	 *
	 * @return string
	 */
	public function generateClientScript(): string
	{
		$string = '';

		$name = $this->getName();

		$sSeparator = ', ';

		$string .= "xajax.Reg('{$name}', function() {";
		$string .= 'return xajax.request( ';
		$string .= '{ xjxreq: \'cms\' }, ';
		$string .= '{ parameters:arguments';

		$stringParts = [];
		$configs     = $this->getRequestConfiguration();

		// todo handle XajaxRequest Values and handle additional Post/Get Parameters

		foreach ($configs as $sKey => $sValue)
		{
			if (\is_int($sValue) || \is_float($sValue) || \is_bool($sValue))
			{

			}
			elseif (\is_string($sValue))
			{
				$sValue = Base::SQ . $sValue . Base::SQ;
			}
			else
			{
				continue;
			}

			$stringParts[] = "{$sKey}:" . $sValue;
		}

		if (0 < \count($stringParts))
		{
			$string .= $sSeparator . '' . implode($sSeparator, $stringParts) . '';
		}
		$string .= ' }); ';
		$string .= '});';

		return $string;
	}

	/**
	 * Generic "execution" Handler
	 * Function: call
	 * Called by the <xajaxPlugin> that references this function during the
	 * request processing phase.  This function will call the specified
	 * function, including an external file if needed and passing along
	 * the specified arguments.
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