<?php
/**
 * PHP version php7
 *
 * @category
 * @package            jybrid-php-7
 * @author             ${JProof}
 * @copyright          ${copyright}
 * @license            ${license}
 * @link
 * @see                ${docu}
 * @since              24.09.2017
 */

declare(strict_types=1);

namespace Jybrid\Helper {

	/**
	 * Class Extensions
	 *
	 * @package Jybrid\Helper
	 */
	class Extensions
	{
		/**
		 * @return array
		 */
		public static function getExtensions(): array
		{
			return get_loaded_extensions();
		}

		/**
		 * Checks, PhpExtension is installed
		 *
		 * @param string $extension
		 *
		 * @return bool
		 */
		public static function isExtension(?string $extension = null): bool
		{
			return in_array((string) $extension, self::getExtensions(), true);
		}

		/**
		 * @see http://php.net/manual/de/book.mbstring.php
		 * @see https://stackoverflow.com/questions/8233517/what-is-the-difference-between-iconv-and-mb-convert-encoding-in-php
		 * @return bool
		 */
		public static function isMultibyteString(): bool
		{
			return self::isExtension('mbstring');
		}

		/**
		 * @see http://php.net/manual/de/book.iconv.php
		 * @return bool
		 */
		public static function isIconv(): bool
		{
			return self::isExtension('iconv');
		}
	}
}