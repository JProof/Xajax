<?php
/**
 * PHP version $phpversion$
 *
 * @category
 * @package            Jybrid Core  Jybrid\Input
 * @author             ${JProof}
 * @copyright          ${copyright}
 * @license            ${license}
 * @link
 * @see                ${docu}
 * @since              02.01.2018
 */

declare(strict_types=1);

namespace Jybrid\Input;

/**
 * Class Filter
 */
class Filter
{
	/**
	 * @param null        $val
	 * @param null|string $default
	 *
	 * @return string
	 */
	public static function getString( $val = null, ?string $default = null )
	{
		// todo regex;
		return (string) $val;
	}

	/**
	 * @param null     $val
	 * @param int|null $default
	 *
	 * @return int|null
	 */
	public static function getInt($val = null, ?int $default = null): ?int
	{
		if (null === $val)
		{
			return $default;
		}
		if (\is_string($val) || \is_int($val) || \is_float($val))
		{

			return (int) $val;
		}
		return $default;
	}

	/**
	 * @param null      $val
	 * @param bool|null $default
	 *
	 * @return bool|null
	 */
	public static function getBool($val = null, ?bool $default = null): ?bool
	{
		if (null === $val)
		{
			return $default;
		}
		return (bool) $val;
	}

	/**
	 * @param null       $val
	 * @param float|null $default
	 *
	 * @return float|null
	 */
	public static function getFloat($val = null, ?float $default = null): ?float
	{
		if (null === $val)
		{
			return $default;
		}
		return (float) $val;
	}
}