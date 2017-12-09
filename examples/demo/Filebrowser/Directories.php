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
 * @since              27.09.2017
 */

declare(strict_types=1);

namespace Xajax\Examples\Demo\Filebrowser {

	use RecursiveDirectoryIterator;
	use RuntimeException;

	/**
	 * Class Directories
	 *
	 * @package Xajax\Examples\Demo\Scriptdirectories
	 */
	class Directories
	{
		/**
		 * public_html Directory
		 *
		 * @var string
		 */
		protected $docRoot;
		/**
		 * Where the current called php-script is located
		 *
		 * @var string
		 */
		protected $scriptRoot;
		/**
		 * Getting directories from RootDir to this dir
		 *
		 * @var string
		 */
		protected $docFromDir;
		/**
		 * @var string
		 */
		protected static $parentNodePrefix = 'parent_';
		/**
		 * @var string
		 */
		protected static $childNodePrefix = 'child_';

		/**
		 * @return \RecursiveDirectoryIterator
		 * @throws \RuntimeException
		 */
		public function getDirectoriesFromDir(): \RecursiveDirectoryIterator
		{

			if ('' === $fdDir = $this->getDocFromDir())
			{
				if ('' === ($fdDirRoot = $this->getDocRoot()))
				{
					throw new RuntimeException('Missing of setting the from Dir');
				}
				$fdDir = $fdDirRoot;
				$this->setDocFromDir($fdDir);
			}

			return new RecursiveDirectoryIterator($fdDir);
		}

		/**
		 * Has Found or not
		 *
		 * @param string $nodeHash
		 * @param bool   $start
		 *
		 * @return bool
		 */
		public function getRecursiveDir($nodeHash = '', $start = false): bool
		{
			// dummy call
			$this->getDirectoriesFromDir();
			$changed = false;
			$nodes   = self::splitPathway($nodeHash);

			foreach ($nodes as $cntr => $node)
			{

				if ($start)
				{
					if ($nodes[0] === self::getHash($this->getDocFromDir()))
					{
						$start   = false;
						$changed = true;
						continue;
					}
				}

				if ($node === $nodeHash)
				{
					return true;
				}

				$dirs = $this->getDirectoriesFromDir();
				foreach ($dirs as $dir)
				{
					$pName = $dir->getPathname();
					$hash  = self::getHash($pName);
					if ($hash !== $node)
					{
						continue;
					}
					if ($changed)
					{
						unset($nodes[$cntr - 1]);
					}
					else
					{
						unset($nodes[$cntr]);
					}
					if (0 < count($nodes))
					{
						$this->setDocFromDir($dir->getPathname());
						$this->getRecursiveDir(self::stringifyPathway($nodes));

						return true;
					}
					else
					{
						return true;
					}
				}
			}

			return false;
		}

		/**
		 * @return string
		 */
		public function getDocRoot(): string
		{
			return (string) $this->docRoot;
		}

		/**
		 * @param string $docRoot
		 *
		 * @return Directories
		 */
		public function setDocRoot(string $docRoot): Directories
		{
			$this->docRoot = $docRoot;

			return $this;
		}

		/**
		 * @return string
		 */
		public function getScriptRoot(): string
		{
			return (string) $this->scriptRoot;
		}

		/**
		 * @param string $scriptRoot
		 *
		 * @return Directories
		 */
		public function setScriptRoot(string $scriptRoot): Directories
		{
			$this->scriptRoot = $scriptRoot;

			return $this;
		}

		/**
		 * @return string
		 */
		public function getDocFromDir(): string
		{
			return (string) $this->docFromDir;
		}

		/**
		 * @param string $docFromDir
		 *
		 * @return Directories
		 */
		public function setDocFromDir(string $docFromDir): Directories
		{
			$this->docFromDir = $docFromDir;

			return $this;
		}

		public static function getParentNodeForNewChild($parent = ''): string
		{
			return self::prefixLastPart($parent, self::getChildNodePrefix());
		}

		/**
		 * Simple Helper
		 *
		 * @param string $parent
		 * @param string $prefix
		 *
		 * @return string
		 */
		public static function prefixLastPart($parent = '', $prefix = '_'): string
		{
			$splits = self::splitPathway($parent);

			return $prefix . (string) end($splits);
		}

		/**
		 * Simple helper to get NodeName of the <ul> container
		 *
		 * @param string $dir
		 *
		 * @return string
		 */
		public static function getParentNodeHashId($dir = ''): string
		{
			return self::getParentNodePrefix() . self::getHash($dir);
		}

		/**
		 * Simple helper to get NodeName of the <ul> container
		 *
		 * @param string $dir
		 *
		 * @return string
		 */
		public static function getChildNodeHashId($dir = ''): string
		{
			return self::getChildNodePrefix() . self::getHash($dir);
		}

		/**
		 * Getting the wayBack
		 *
		 * @example  "md5(rootDir)"."md5(subDir)"."md5(subSubDir)"
		 * @example  9950eb156b8279af6a81c0ed3fc45989.6e657a8541dcdfb47f4c81ce6db2486d
		 *
		 * @param string $child
		 * @param string $parent
		 *
		 * @return string
		 */
		public static function compilePathway(string $child = null, ?string $parent = null): string
		{
			$parent = trim((string) $parent);
			if ('' === $parent)
			{
				return $child;
			}
			$parentParts   = self::splitPathway($parent);
			$parentParts[] = $child;

			return implode('#', $parentParts);
		}

		/**
		 * @param string $nodes
		 *
		 * @return array
		 */
		public static function splitPathway(?string $nodes = null): array
		{
			return explode('#', (string) $nodes);
		}

		/**
		 * @param array $parts
		 *
		 * @return string
		 */
		public static function stringifyPathway(?array $parts = null): string
		{
			return implode('#', (array) $parts);
		}

		/**
		 * Navigate through the nodes should be only do with an hash
		 *
		 * @param string $dir
		 *
		 * @return string
		 */
		public static function getHash(?string $dir = null): string
		{
			return basename((string) $dir);
		}

		/**
		 * @return string
		 */
		public static function getParentNodePrefix(): string
		{
			return self::$parentNodePrefix;
		}

		/**
		 * @return string
		 */
		public static function getChildNodePrefix(): string
		{
			return self::$childNodePrefix;
		}
	}
}