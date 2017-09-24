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
 * @since              23.09.2017
 */

declare(strict_types=1);

namespace Xajax\Helper;

/**
 * Class Encoding
 * Helper to detect and get valid Encoding Names
 *
 * @package Xajax\Helpers
 */
class Encoding
{
	/**
	 * Available Php-Encodings in this System
	 *
	 * @var array
	 */
	static protected $encodings = [];
	/**
	 * Which Encoding Library is preferred
	 *
	 * @var array
	 */
	static protected $orderEncodingLib = ['mb', 'iconv'];
	/**
	 * Flag, that we have try'd to load Encodings with multibyte extension
	 *
	 * @var bool
	 */
	static private $loadSystemEncodings = false;

	/**
	 * @param string $name
	 * @param bool   $strict
	 *
	 * @return bool
	 */
	public static function getEncoding($name = '', $strict = true): bool
	{
		$encodings = self::getPhpEncodings();

		if ($strict && 0 < count($encodings))
		{
			return array_key_exists($name, $encodings);
		}

		return false;
	}

	/**
	 * @return array
	 */
	public static function getPhpEncodings(): array
	{
		if (!self::$loadSystemEncodings)
		{
			self::loadSystemEncodings();

			$encodings = self::getEncodings();

			if (0 === count($encodings))
			{
				//todo can be removed or set as notice
				throw new \RuntimeException('missing MB extension');
			}
		}

		return self::getEncodings();
	}

	/**
	 * @return array
	 */
	protected static function getEncodings(): array
	{
		return self::$encodings;
	}

	/**
	 * @param array $encodings
	 *
	 * @return array
	 */
	protected static function setEncodings(array $encodings): array
	{
		return self::$encodings = $encodings;
	}

	/**
	 * @return array
	 */
	protected static function loadSystemEncodings(): array
	{
		// already loadFlag
		self::$loadSystemEncodings = true;

		return self::setEncodings(Extensions::isMultibyteString() ? mb_list_encodings() : []);
	}
}