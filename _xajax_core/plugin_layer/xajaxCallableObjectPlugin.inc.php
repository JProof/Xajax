<?php
/*
	File: xajaxCallableObjectPlugin.inc.php

	Contains the xajaxCallableObjectPlugin class

	Title: xajaxCallableObjectPlugin class

	Please see <copyright.inc.php> for a detailed description, copyright
	and license information.
*/

/*
	@package xajax
	@version $Id: xajaxCallableObjectPlugin.inc.php 362 2007-05-29 15:32:24Z calltoconstruct $
	@copyright Copyright (c) 2005-2007 by Jared White & J. Max Wilson
	@copyright Copyright (c) 2008-2010 by Joseph Woolley, Steffen Konerow, Jared White  & J. Max Wilson
	@license http://www.xajaxproject.org/bsd_license.txt BSD License
*/

/*
	Constant: XAJAX_CALLABLE_OBJECT
		Specifies that the item being registered via the <xajax->register> function is a
		object who's methods will be callable from the browser.
*/

use Xajax\plugin_layer\RequestIface;

if (!defined('XAJAX_CALLABLE_OBJECT'))
{
	define('XAJAX_CALLABLE_OBJECT', 'callable object');
}

//SkipAIO
require __DIR__ . '/support/xajaxCallableObject.inc.php';
//EndSkipAIO

/*
	Class: xajaxCallableObjectPlugin
*/

final class xajaxCallableObjectPlugin extends xajaxRequestPlugin implements RequestIface
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

			if (XAJAX_CALLABLE_OBJECT == $sType)
			{
				$xco = $aArgs[1];

//SkipDebug
				if (false === is_object($xco))
				{
					trigger_error("To register a callable object, please provide an instance of the desired class.", E_USER_WARNING);

					return false;
				}
//EndSkipDebug

				if (false === ($xco instanceof xajaxCallableObject))
				{
					$xco = new xajaxCallableObject($xco);
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
	public function generateClientScript()
	{

		if (0 < count($this->aCallableObjects))
		{
			foreach (array_keys($this->aCallableObjects) as $sKey)
			{
				$this->aCallableObjects[$sKey]->generateClientScript($this->sXajaxPrefix);
			}
		}
	}

	/*
		Function: canProcessRequest
	*/
	public function canProcessRequest()
	{
		if (null == $this->sRequestedClass)
		{
			return false;
		}
		if (null == $this->sRequestedMethod)
		{
			return false;
		}

		return true;
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

		$objArgumentManager = xajaxArgumentManager::getInstance();
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
		return 'callableObject';
	}

	/**
	 * Registers an Single Request
	 *
	 * @since 7.0
	 *
	 * @param array $aArgs
	 *
	 * @return \xajaxRequest
	 * @throws \InvalidArgumentException
	 */
	public function registerRequest(array $aArgs = []): \xajaxRequest
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

			if (false === ($xco instanceof xajaxCallableObject))
			{
				$xco = new xajaxCallableObject($xco);
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
			return $xco;// $xco->generateRequests($this->sXajaxPrefix);
		}

		throw new InvalidArgumentException('Wrong ParameterCount to register an xajaxCallableObjectPlugin');
	}
}

$objPluginManager = xajaxPluginManager::getInstance();
$objPluginManager->registerPlugin(new xajaxCallableObjectPlugin(), 102);
