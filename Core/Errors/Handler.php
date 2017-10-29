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

use Exception;
use Throwable;

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
	static protected $exceptions = [];
	/**
	 * @var array
	 */
	static private $userErrors = [];

	public static function getErrors()
	{
		$string = '';
		$string .= self::getExceptions();
		$string .= self::getUserErrors();
		if ('' !== $string)
		{
			return $string;
		}

		return null;
	}

	public static function addError()
	{
		$args = func_get_args();
		if ($args[0] instanceof Throwable)
		{
			self::addException($args[0]);
		}
		else
		{

			self::addUserError($args);
		}
	}

	private static function addUserError(?array $args = null)
	{
		self::$userErrors[] = (array) $args;
	}

	public static function getUserErrors()
	{
		$string = '';
		foreach (self::$userErrors as list($code, $message, $file, $line, $trace))
		{
			$string .= self::compileMessage($code, $message, $file, $line, self::errorTrace($trace));
		}
		if ('' === $string)
		{
			return null;
		}

		return $string;
	}

	protected static function errorTrace(?array $array = null)
	{
		$string = '';
		if (!is_array($array))
		{
			return $string;
		}
		$params = [];
		foreach ($array as $key => $value)
		{
			$params[] = '# ' . $key . ':' . $value;
		}

		return implode("\n", $params);
	}

	/**
	 * @param \Exception|null $exception
	 */
	public static function addException(?Exception $exception = null)
	{
		if ($exception instanceof Exception)
		{
			self::$exceptions[] = $exception;
		}
	}

	/**
	 * @return null|string
	 */
	public static function getExceptions()
	{
		$string = '';
		if (0 < count(self::$exceptions))
		{
			foreach (self::$exceptions as $error)
			{
				$string .= self::compileExceptionMessage($error);
			}
		}
		if ('' === $string)
		{
			return null;
		}

		return $string;
	}

	protected static function compileExceptionMessage(Exception $exception)
	{
		return self::compileMessage($exception->getCode(), $exception->getMessage(), $exception->getFile(), $exception->getLine(), $exception->getTraceAsString());
	}

	/**
	 * Rendering the Error-Messages
	 *
	 * @return string|null
	 */
	protected static function compileMessage(): ?string
	{
		list($code, $message, $file, $line, $trace) = func_get_args();

		$errorReporting = error_reporting();
		if (0 === ($code && $errorReporting))
		{
			return null;
		}

		$errorString = [];

		$errorString[] = '----';

		$errorString[] = '[' . self::getErrorTypeString($code) . '] ' . $message;

		$errorString[] = 'Error on line ' . $line . ' of file ' . $file;
		if ($trace)
		{
			$errorString[] = $trace;
		}

		return implode("\n", $errorString);
	}

	/**
	 * Readable Error-Type-String
	 *
	 * @param int $code
	 *
	 * @return string
	 */
	public static function getErrorTypeString(int $code): string
	{
		if (E_NOTICE === $code)
		{
			$errTypeStr = 'NOTICE';
		}
		else if (E_WARNING === $code)
		{
			$errTypeStr = 'WARNING';
		}
		else if (E_USER_NOTICE === $code)
		{
			$errTypeStr = 'USER NOTICE';
		}
		else if (E_USER_WARNING === $code)
		{
			$errTypeStr = 'USER WARNING';
		}
		else if (E_USER_ERROR === $code)
		{
			$errTypeStr = 'USER FATAL ERROR';
		}
		elseif (E_ERROR === $code)
		{
			$errTypeStr = 'E_ERROR';
		}
		else if (defined('E_STRICT') && E_STRICT === $code)
		{
			$errTypeStr = 'E_STRICT';
		}
		else
		{
			$errTypeStr = 'UNKNOWN: ' . $code;
		}

		return $errTypeStr;
	}
}