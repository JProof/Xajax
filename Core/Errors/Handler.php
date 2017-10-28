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
 * @since              26.10.2017
 */

declare(strict_types=1);

namespace Xajax\Core\Errors;

/**
 * Class Handler
 *
 * @package Xajax\Core\Errors
 */
class Handler
{
	/**
	 * @var array
	 */
	static protected $errors = [];

	/**
	 * @param \Exception|null $exception
	 */
	public static function addError(?\Exception $exception = null)
	{
		if ($exception instanceof \Exception)
		{
			self::$errors[] = $exception;
		}
	}

	/**
	 * @return null|string
	 */
	public static function getErrors()
	{
		$string = '';
		foreach (self::$errors as $error)
		{
			$string .= self::xajaxErrorHandler($error);
		}
		if ('' === $string)
		{
			return null;
		}

		return $string;
	}

	/**
	 * Rendering the Error-Messages
	 *
	 * @param \Exception $exception
	 *
	 * @return string|null
	 */
	protected static function xajaxErrorHandler(\Exception $exception): ?string
	{
		$errno          = $exception->getCode();
		$errorReporting = error_reporting();
		if (0 === ($errno && $errorReporting))
		{
			return null;
		}

		if (E_NOTICE === $errno)
		{
			$errTypeStr = 'NOTICE';
		}
		else if (E_WARNING === $errno)
		{
			$errTypeStr = 'WARNING';
		}
		else if (E_USER_NOTICE === $errno)
		{
			$errTypeStr = 'USER NOTICE';
		}
		else if (E_USER_WARNING === $errno)
		{
			$errTypeStr = 'USER WARNING';
		}
		else if (E_USER_ERROR === $errno)
		{
			$errTypeStr = 'USER FATAL ERROR';
		}
		elseif (E_ERROR === $errno)
		{
			$errTypeStr = 'E_ERROR';
		}
		else if (defined('E_STRICT') && E_STRICT === $errno)
		{
			return null;
		}
		else
		{
			$errTypeStr = 'UNKNOWN: ' . $errno;
		}

		$sCrLf = "\n";

		ob_start();
		echo $sCrLf;
		echo '----';
		echo $sCrLf;
		echo '[';
		echo $errTypeStr;
		echo '] ';
		echo $exception->getMessage();
		echo $sCrLf;
		echo 'Error on line ';
		echo $exception->getLine();
		echo ' of file ';
		echo $exception->getFile();
		echo $exception->getTraceAsString();

		return ob_get_clean();
	}
}