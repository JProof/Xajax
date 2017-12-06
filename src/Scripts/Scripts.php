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

/**
 * Class Scripts
 *
 * @package Xajax\Scripts
 */
/**
 * Class Scripts
 *
 * @package Xajax\Scripts
 */
class Scripts
{
	/**
	 * SearchDirectory for Scripts
	 *
	 * @var Queue
	 */
	protected $scriptDirs;
	/**
	 * The ScriptNames
	 *
	 * @var array
	 */
	protected $scripts;
	/**
	 * @var \Xajax\Scripts\Queue
	 */
	protected $scriptsOrdering;
	/**
	 * @var array
	 */
	protected $lockedScripts = [];
	/**
	 * Internal Configuration
	 *
	 * @var \Xajax\Configuration\Scripts
	 */
	protected $configuration;

	/**
	 * Scripts constructor.
	 */
	protected function __construct()
	{
		$this->configuration = \Xajax\Configuration\Scripts::getInstance();

		$this->scriptDirs      = new Queue();
		$this->scripts         = [];
		$this->scriptsOrdering = new Queue();

		$this->getScriptsOrdering()->insert('xajax', 50);
		$this->getScriptsOrdering()->insert('xajax.debug', 49);
		$this->getScriptsOrdering()->insert('xajax.debug.verbose', 48);
	}

	/**
	 * Get the Script-Urls
	 *
	 * @param bool|null $relative
	 *
	 * @return array
	 */
	public function getScriptUrls(?bool $relative = null): array
	{
		$scriptUrls = [];
		foreach ($this->getScriptsOrdering() as $item)
		{
			if ($this->isLockScript($item))
			{
				continue;
			}
			$tmp = $this->getScriptUrl($item, $relative);
			if ($tmp)
			{
				$scriptUrls[$item] = $tmp;
			}
		}

		return $scriptUrls;
	}

	/**
	 * @param null|string $dir
	 * @param int|null    $priority
	 */
	public function addScriptDir(string $dir = null, ?int $priority = null): void
	{
		$this->getScriptDirs()->insert($dir, (int) $priority);
	}

	/**
	 * Render the Url for an single Script
	 * todo compile filename
	 * todo compile configured minified filename
	 *
	 * @param string|null $name
	 * @param bool|null   $relative
	 *
	 * @return null|string
	 */
	public function getScriptUrl(string $name = null, ?bool $relative = null): ?string
	{
		if ($this->isLockScript($name))
		{
			return null;
		}

		if (($scriptQueue = $this->getScript($name)) instanceof Queue && 0 < $scriptQueue->count())
		{
			/** @var \Xajax\Scripts\Base $item */
			$item = $scriptQueue->top();

			if ('' !== ($dir = $item->getDir()))
			{
				return $dir . '/' . $this->getScriptFilename($item->getFileName());
			}

			$topDir = $this->getScriptDirs()->top();

			return $topDir . '/' . $this->getScriptFilename($item->getFileName());
		}

		return null;
	}

	/**
	 * @param string|null $name
	 *
	 * @return null|Queue
	 */
	public function getScript(string $name = null): ?Queue
	{
		$scripts = $this->getScripts();

		return $scripts[$name] ?? null;
	}

	/**
	 * Getting the minified or regular js-filename
	 *
	 * @param $sFilename
	 *
	 * @return string
	 */
	private function getScriptFilename(?string $sFilename = null): string
	{
		if (\is_string($sFilename) && false === self::getInstance()->getConfiguration()->isUseUncompressedScripts())
		{
			return str_replace('.js', '.min.js', $sFilename);
		}

		return $sFilename;
	}

	/**
	 * Adding an Script
	 *
	 * @example new Xajax\Scripts\Core(['scriptName' => 'xajax', 'fileName' => 'xajax_core2.js']);
	 *          replaces the script 'xajax' with the xajax_core2.js override file
	 *
	 * @param null|Iface $script   script object
	 * @param int|null   $priority Higher value will be tried to render first
	 */
	public function addScript(Iface $script = null, ?int $priority = null): void
	{
		if ($script instanceof Iface)
		{
			$scriptName = $script->getScriptName();
			$scripts    = $this->getScripts();

			if (!array_key_exists($scriptName, $scripts))
			{
				$scripts[$scriptName] = new Queue();
			}

			if (null === $priority)
			{
				$priority = $scripts[$scriptName]->count() + 1;
			}
			$scripts[$scriptName]->insert($script, $priority);
			$this->setScripts($scripts);
		}
	}

	/**
	 * Scripts and location will be as singleton
	 *
	 * @return \Xajax\Scripts\Scripts
	 */
	public static function getInstance(): Scripts
	{
		static $self;
		if (!$self)
		{
			$self = new self();
		}

		return $self;
	}

	/**
	 * @return array
	 */
	public function getScripts(): array
	{
		return $this->scripts;
	}

	/**
	 * @param array $scripts
	 */
	protected function setScripts(array $scripts): void
	{
		$this->scripts = $scripts;
	}

	/**
	 * @return Queue
	 */
	public function getScriptDirs(): Queue
	{
		return $this->scriptDirs;
	}

	/**
	 * On Large PHP/WebApps there are many ways and points where somebody adds an script which you do not want to display/use.
	 *
	 * @example 'xajax' 'xajax.debug';
	 *
	 * @param string|null $name
	 */
	public function setLockScript(string $name = null): void
	{
		if (!$this->isLockScript($name))
		{
			$this->lockedScripts[$name] = true;
		}
	}

	/**
	 * Remove the Lock of an Script
	 *
	 * @param string|null $name
	 */
	public function removeLockScript(string $name = null): void
	{
		if ($this->isLockScript($name))
		{
			unset($this->lockedScripts[$name]);
		}
	}

	/**
	 * @param string|null $name
	 *
	 * @return bool
	 */
	public function isLockScript(string $name = null): bool
	{
		return array_key_exists($name, $this->lockedScripts);
	}

	/**
	 * @return \Xajax\Scripts\Queue
	 */
	protected function getScriptsOrdering(): \Xajax\Scripts\Queue
	{
		return $this->scriptsOrdering;
	}

	/**
	 * @return \Xajax\Configuration\Scripts
	 */
	public function getConfiguration(): \Xajax\Configuration\Scripts
	{
		return $this->configuration;
	}
}