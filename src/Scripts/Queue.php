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

namespace Xajax\Scripts;

use Countable;
use IteratorAggregate;
use SplPriorityQueue;

/**
 * Class Queue
 *
 * @package Xajax\Scripts
 * @see     https://mwop.net/blog/253-Taming-SplPriorityQueue.html
 */
class Queue implements Countable, IteratorAggregate
{
	/**
	 * @var \SplPriorityQueue
	 */
	protected $innerQueue;

	/**
	 * Queue constructor.
	 */
	public function __construct()
	{
		// I'll explain the lack of global namespacing later...
		$this->innerQueue = new SplPriorityQueue;
	}

	/**
	 * @return int
	 */
	public function count(): int
	{
		return \count($this->innerQueue);
	}

	/**
	 * @param $key
	 * @param $priority
	 */
	public function insert($key, $priority)
	{
		$this->innerQueue->insert($key, $priority);
	}

	/**
	 * @return \SplPriorityQueue|\Traversable
	 */
	public function getIterator()
	{
		return clone $this->innerQueue;
	}
}