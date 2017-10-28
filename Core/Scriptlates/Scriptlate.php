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
 * @since              27.10.2017
 */

declare(strict_types=1);

namespace Xajax\Core\Scriptlates;

class Scriptlate
{
	/**
	 * @var \SplPriorityQueue
	 */
	protected static $dirs;

	/**
	 * @param null|string $name "core.xajax"
	 * @param array|null  $data array with datas will be send to the script
	 * @param null|string $dir  override directory. priority-queue will not used for the file
	 */
	public static function renderTemplate(?string $name = null, ?array $data = null, ?string $dir = null)
	{
		if ('' !== ($name = (string) $name))
		{
			throw new \InvalidArgumentException('');
		}
	}

	protected static function getFileName(?string $name = null)
	{

	}

	protected static function getPath()
	{
	}

	public static function addDir(?string $dir = null, ?int $priority = null)
	{
		self::getDirs()->insert((string) $dir, (int) $priority);
	}

	/**
	 * @return \SplPriorityQueue
	 */
	public static function getDirs(): \SplPriorityQueue
	{
		if (self::$dirs instanceof \SplPriorityQueue)
		{
			return self::$dirs;
		}

		return self::$dirs = new \SplPriorityQueue();
	}
}