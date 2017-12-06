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
 * @since              06.12.2017
 */

declare(strict_types=1);

namespace Xajax\Helper;

/**
 * Class Directories
 *
 * @package Helper
 */
/**
 * Class Directories
 *
 * @package Xajax\Helper
 */
class Directories
{
	/**
	 * @var
	 */
	static protected $webDirectory;

	/**
	 * Visible for Browsing
	 *
	 * @return string
	 * @throws \UnexpectedValueException
	 */
	public static function getWebDirectory(): string
	{
		if (null === self::$webDirectory)
		{
			self::$webDirectory = self::setWebDirectory(self::detectWebdirectory());
		}
		return self::$webDirectory;
	}

	/**
	 * Visible for Browsing
	 *
	 * @param null|string $dir
	 *
	 * @return null|string
	 * @throws \UnexpectedValueException
	 */
	public static function setWebDirectory(?string $dir = null): ?string
	{
		if ($nDir = self::cleanDir($dir))
		{
			return self::$webDirectory = $nDir;
		}
		throw new \UnexpectedValueException('Directory can not be found on System');
	}

	/**
	 * Try to get the AbsPath of an setting directory
	 * Method to cleanup an directory name an check the directory exists in system
	 *
	 * @param null|string $dir
	 *
	 * @return null|string
	 * @throws \UnexpectedValueException
	 */
	public static function cleanDir(?string $dir = null): ?string
	{
		if (null === $dir)
		{
			return null;
		}
		/**
		 * clean first to prevent from
		 * /tests/unit/datas/directories/jsTwoDirectory/../../
		 * to
		 * /tests/unit/datas/
		 * */
		$dir = self::cleanPath($dir);

		// try relative directory
		if (false !== ($np = realpath($dir)))
		{
			// directory exists as absolute
			return $dir;
		}

		$absPath = self::concatPaths(self::getWebDirectory(), $dir);
		// next Try with full directory
		if (false !== ($np = realpath($absPath)))
		{
			// directory exists
			return $np;
		}

		return null;
	}

	/**
	 * Try to make an relative dir from Root-Directory stripped
	 *
	 * @param null|string $absDir
	 *
	 * @return bool|null|string
	 */
	public static function getRelative(?string $absDir = null)
	{
		if (null !== $absDir && null !== ($absDir = self::cleanDir($absDir)))
		{

			$webDirL = \strlen(self::getWebDirectory());
			$absDirL = \strlen($absDir);
			return substr($absDir, ($absDirL - $webDirL) - 1, $webDirL);
		}
		return null;
	}

	/**
	 * @param string $path1
	 * @param string $path2
	 *
	 * @return mixed
	 */
	public static function concatPaths(string $path1, string $path2)
	{
		return self::cleanPath($path1 . '/' . $path2);
	}

	/**
	 * CleanMethod
	 *
	 * @todo adding Tests
	 *
	 * @param null|string $path
	 *
	 * @return string
	 */
	public static function cleanPath(?string $path = null): string
	{
		$path    = (string) $path;
		$search  = ['\\', '../', './', '//'];
		$replace = ['/', '/', '/', '/', '/'];

		$path = str_replace($search, $replace, $path);

		// last chance to cleanup
		return preg_replace(['#\//#'], ['/'], $path);
	}

	/**
	 * Autodection of visible DocumentRoot
	 *
	 * @return null|string
	 */
	protected static function detectWebDirectory(): ? string
	{
		return $_SERVER['DOCUMENT_ROOT'] ?? null;
	}
}