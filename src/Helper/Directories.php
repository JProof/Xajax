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
 * @since              06.12.2017
 */

declare(strict_types=1);

namespace Jybrid\Helper;

/**
 * Class Directories
 *
 * @package Jybrid\Helper
 */
class Directories
{
	/**
	 * @var
	 */
	static protected $webDirectory;

	/**
	 * Try to make an relative dir from Root-Directory stripped
	 *
	 * @param null|string $absDir
	 *
	 * @return null|string null Directory does not exists |String with leading Slash
	 */
	public static function getValidRelativeDirectory(?string $absDir = null): ?string
	{
		if (null !== $absDir)
		{
			$absDir = self::cleanPath($absDir);
			// strips the absolute dir from the first of the relative Dir
			if ($checkedAbsDir = self::isValidAbsoluteDir($absDir))
			{
				$rootParts = substr_count(self::getWebDirectory(), '/') + 1;

				$absParts = explode('/', $absDir);
				$sliced   = \array_slice($absParts, $rootParts);
				return '/' . implode('/', $sliced);
			}
			// is already an relative dir
			if ($relDir = self::isValidRelativeDir($absDir))
			{
				return $relDir;
			}
		}
		return null;
	}

	/**
	 * Try to make an relative dir from Root-Directory stripped
	 *
	 * @param null|string $relDir
	 *
	 * @return null|string
	 */
	public static function getValidAbsoluteDirectory(?string $relDir = null): ?string
	{
		if (null !== $relDir)
		{
			// perhaps is already valid
			if ($checkedAbsDir = self::isValidAbsoluteDir($relDir))
			{

				return $checkedAbsDir;
			}
			// is an Relative dir, add the WebDirectory
			if ($relDir = self::isValidRelativeDir($relDir))
			{
				return self::concatPaths(self::getWebDirectory(), $relDir);
			}
		}
		return null;
	}

	/**
	 * Method to check the directory is an existing absolute directory
	 * The string-result can be Safely used as directory
	 *
	 * @param null|string $directory
	 *
	 * @return null| string
	 */
	public static function isValidAbsoluteDir(?string $directory = null): ?string
	{
		$directory = $directory ?? '/';

		$cp = self::cleanPath($directory);
		return false !== realpath($cp) ? $cp : null;
	}

	/**
	 * Method to check the directory is an existing relative directory
	 *
	 * @param null|string $directory
	 *
	 * @return null|string
	 */
	public static function isValidRelativeDir(?string $directory = null): ?string
	{
		$directory = $directory ?? '/';

		$cp     = self::cleanPath($directory);
		$absDir = self::concatPaths(self::getWebDirectory(), $cp);

		return false !== realpath($absDir) ? $cp : null;
	}

	/**
	 * Merge to Paths
	 *
	 * @param string $path1
	 * @param string $path2
	 *
	 * @return string
	 */
	public static function concatPaths(string $path1, string $path2): string
	{
		return self::cleanPath($path1 . '/' . $path2);
	}

	/**
	 * Cleaning path from wrong dots, traversals
	 *
	 * @param null|string $path
	 *
	 * @return string
	 */
	public static function cleanPath(?string $path = null): string
	{
		$ds      = '/';
		$path    = (string) $path;
		$search  = ['\\', '../', './', '.\\', '..\\', '//'];
		$replace = $ds;

		$path = str_replace($search, $replace, $path);

		// last chance to cleanup
		return preg_replace(['#\//#', '#[/\\\\]+#', '#[/\\\\]+#'], [$ds, $ds, $ds], $path);
	}

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
			self::$webDirectory = self::setWebDirectory(self::detectWebDirectory());
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
		if ($nDir = self::cleanPath($dir))
		{
			return self::$webDirectory = $nDir;
		}
		throw new \UnexpectedValueException( 'WebDirectory can not be found on System. Please the  Jybrid\Helper\Directories::setWebDirectory($_SERVER[\'DOCUMENT_ROOT\']) or similar.' );
	}

	/**
	 * AutoDetection of visible DocumentRoot
	 *
	 * @return null|string
	 */
	protected static function detectWebDirectory(): ? string
	{
		if (null !== $_SERVER && \is_array($_SERVER) && array_key_exists('DOCUMENT_ROOT', $_SERVER))
		{
			return $_SERVER['DOCUMENT_ROOT'];
		}
		return null;
	}
}