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

namespace Xajax\Plugins\Callableobject;

use InvalidArgumentException;
use Xajax\Argument;
use Xajax\Plugin\Request;
use Xajax\Plugin\Request\RequestPluginIface;
use Xajax\RequestIface;

/**
 * Class Plugin
 * Hold all Userfunction Plugin Instances
 *
 * @package Xajax\Plugins\Userfunction
 */
class Plugin extends Request implements RequestPluginIface
{
	/*
		Array: aCallableObjects
	*/
	private $aCallableObjects;
	/*
		String: sXajaxPrefix
	*/
	private $sXajaxPrefix;
	/*
		String: sDefer
	*/
	private $sDefer;
	private $bDeferScriptGeneration;
	/*
		String: sRequestedClass
	*/
	private $sRequestedClass;
	/*
		String: sRequestedMethod
	*/
	private $sRequestedMethod;

	/*
		Function: xajaxCallableObjectPlugin
	*/
	public function __construct()
	{
		$this->aCallableObjects = [];

		$this->sXajaxPrefix           = 'xajax_';
		$this->sDefer                 = '';
		$this->bDeferScriptGeneration = false;

		$this->sRequestedClass  = null;
		$this->sRequestedMethod = null;

		if (!empty($_GET['xjxcls']))
		{
			$this->sRequestedClass = $_GET['xjxcls'];
		}
		if (!empty($_GET['xjxmthd']))
		{
			$this->sRequestedMethod = $_GET['xjxmthd'];
		}
		if (!empty($_POST['xjxcls']))
		{
			$this->sRequestedClass = $_POST['xjxcls'];
		}
		if (!empty($_POST['xjxmthd']))
		{
			$this->sRequestedMethod = $_POST['xjxmthd'];
		}
	}

	/*
		Function: configure
	*/
	public function configure($sName, $mValue)
	{
		if ('wrapperPrefix' == $sName)
		{
			$this->sXajaxPrefix = $mValue;
		}
		else if ('scriptDefferal' == $sName)
		{
			if (true === $mValue)
			{
				$this->sDefer = 'defer ';
			}
			else
			{
				$this->sDefer = '';
			}
		}
		else if ('deferScriptGeneration' == $sName)
		{
			if (true === $mValue || false === $mValue)
			{
				$this->bDeferScriptGeneration = $mValue;
			}
			else if ('deferred' === $mValue)
			{
				$this->bDeferScriptGeneration = $mValue;
			}
		}
	}

	/*
		Function: register
	*/
	/**
	 * @param $aArgs
	 *
	 * @return array|bool
	 * @deprecated use registerRequest
	 */
	public function register($aArgs)
	{
		if (1 < count($aArgs))
		{
			$sType = $aArgs[0];

			if (XAJAX_CALLABLE_OBJECT === $sType)
			{
				$xco = $aArgs[1];

//SkipDebug
				if (false === is_object($xco))
				{
					trigger_error("To register a callable object, please provide an instance of the desired class.", E_USER_WARNING);

					return false;
				}
//EndSkipDebug

				if (false === ($xco instanceof Handler))
				{
					$xco = new Handler($xco);
				}

				if (2 < count($aArgs))
				{
					if (is_array($aArgs[2]))
					{
						foreach ($aArgs[2] as $sKey => $aValue)
						{
							foreach ($aValue as $sName => $sValue)
							{
								$xco->configure($sKey, $sName, $sValue);
							}
						}
					}
				}

				$this->aCallableObjects[] = $xco;

				return $xco->generateRequests($this->sXajaxPrefix);
			}
		}

		return false;
	}

	public function generateHash()
	{
		$sHash = '';
		foreach (array_keys($this->aCallableObjects) as $sKey)
		{
			$sHash .= $this->aCallableObjects[$sKey]->getName();
		}

		foreach (array_keys($this->aCallableObjects) as $sKey)
		{
			$sHash .= implode('|', $this->aCallableObjects[$sKey]->getMethods());
		}

		return md5($sHash);
	}

	/*
		Function: generateClientScript
	*/
	public function generateClientScript(): string
	{
		$string = '';
		if (0 < count($this->aCallableObjects))
		{
			foreach (array_keys($this->aCallableObjects) as $sKey)
			{
				$string .= $this->aCallableObjects[$sKey]->generateClientScript($this->sXajaxPrefix);
			}
		}

		return $string;
	}

	/*
		Function: canProcessRequest
	*/
	public function canProcessRequest(): bool
	{
		return !(null === $this->sRequestedClass && null === $this->sRequestedMethod);
	}

	/*
		Function: processRequest
	*/
	public function processRequest()
	{
		if (null == $this->sRequestedClass)
		{
			return false;
		}
		if (null == $this->sRequestedMethod)
		{
			return false;
		}

		$objArgumentManager = Argument::getInstance();
		$aArgs              = $objArgumentManager->process();

		foreach (array_keys($this->aCallableObjects) as $sKey)
		{
			$xco = $this->aCallableObjects[$sKey];

			if ($xco->isClass($this->sRequestedClass))
			{
				if ($xco->hasMethod($this->sRequestedMethod))
				{
					$xco->call($this->sRequestedMethod, $aArgs);

					return true;
				}
			}
		}

		return 'Invalid request for a callable object.';
	}

	/**
	 * Own Plugin Name
	 *
	 * @return string
	 * @since 7.0
	 */
	public function getName(): string
	{
		return 'callableobject';
	}

	/**
	 * Registers an Single Request
	 *
	 * @since 7.0
	 *
	 * @param array $aArgs
	 *
	 * @return RequestIface
	 * @throws \InvalidArgumentException
	 */
	public function registerRequest(array $aArgs = []): RequestIface
	{
		if (0 < count($aArgs))
		{

			$xco = $aArgs[0];

//SkipDebug
			if (false === is_object($xco))
			{
				trigger_error('To register a callable object, please provide an instance of the desired class.', E_USER_WARNING);

				throw new InvalidArgumentException('To register a callable object, please provide an instance of the desired class.');
			}
//EndSkipDebug

			if (false === ($xco instanceof Handler))
			{
				$xco = new Handler($xco);
			}

			if (2 < count($aArgs))
			{
				if (is_array($aArgs[1]))
				{
					foreach ($aArgs[1] as $sKey => $aValue)
					{
						foreach ($v = (array) $aValue as $sName => $sValue)
						{
							$xco->configure($sKey, $sName, $sValue);
						}
					}
				}
			}

			$this->aCallableObjects[] = $xco;

// @todo check that is possible to get only on Object Back
			return $xco->generateRequests($this->sXajaxPrefix);
		}

		throw new InvalidArgumentException('Wrong ParameterCount to register an xajaxCallableObjectPlugin');
	}
}