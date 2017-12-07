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

use Xajax\Helper\Directories;

/**
 * Class DirectoriesTest
 */
class DirectoriesTest extends \Codeception\Test\Unit
{
	/**
	 * simple Helper
	 *
	 * @return string
	 */
	protected static function getRootDir()
	{
		return dirname(__DIR__, 3);
	}

	/**
	 * Method to cleanup an directory name an check the directory exists in system()
	 *
	 * @param $dir
	 * @param $expected
	 * @param $message
	 *
	 * @dataProvider providerDirectories
	 */
	public function testCleanDir($dir, $expected, $message)
	{
		$cleaned = Directories::cleanPath($dir);
		$this->assertEquals($expected, $cleaned, $message);
	}

	/**
	 * Different types of File-Issues
	 *
	 * @return array
	 */
	public function providerDirectories(): array
	{
		return [
		    ['/\\/\\/tests/unit/datas/directories/jsoneDirectory',
		     '/tests/unit/datas/directories/jsoneDirectory',
		     'Directory Exists',
		    ],
		    ['\\/tests/unit/datas/.\\/./../directories/jsTwoDirectory/../..\.\/\\',
		     '/tests/unit/datas/directories/jsTwoDirectory/',
		     'Directory must be cleaned up between',
		    ],
		    ['\\C:\Windows/directory\Testvar/../..\.\/\\',
		     '/C:/Windows/directory/Testvar/',
		     'Directory must be cleaned up between',
		    ],
		];
	}

	/**
	 * Testing get Relative dir from Absolute Dir
	 *
	 * @param $absDir
	 * @param $expected
	 * @param $message
	 *
	 * @dataProvider providerGetRelative
	 */
	public function testGetValidRelativeDirectory($absDir, $expected, $message)
	{
		Directories::setWebDirectory(self::getRootDir());
		$actual = Directories::getValidRelativeDirectory($absDir);
		$this->assertEquals($expected, $actual, $message);
	}

	/**
	 * @return array
	 */
	public function providerGetRelative(): array
	{
		return [
		    [self::getRootDir()
		     . '/tests/unit/datas\\//directories/jsTwoDirectory',
		     '/tests/unit/datas/directories/jsTwoDirectory',
		     'Ugly Input dir is cleaned an valid relative Dir',
		    ],
		    [self::getRootDir()
		     ,
		     '/',
		     'Root is Root',
		    ],
		    [
			self::getRootDir() . '/tests/unit/datas\\//',
			'/tests/unit/datas/',
			'Ugly Input dir is cleaned an valid relative Dir',
		    ],
		    [
			'//unit/datas\\//',
			null,
			'Non Recoverable Directory ',
		    ],
		    [self::getRootDir() . '/tests/unit/datas/directories/jsoneDirectory',
		     '/tests/unit/datas/directories/jsoneDirectory',
		     'input dir is an absolute dir',
		    ],
		    ['/tests/unit/datas/directories/jsTwoDirectory',
		     '/tests/unit/datas/directories/jsTwoDirectory',
		     'input dir is already an relative dir',
		    ],
		    [
			'/tests/unit/datas/directories/jsTwoDirectory/../../',
			'/tests/unit/datas/directories/jsTwoDirectory/',
			'relative directory is evil but will be cleaned true',
		    ],
		];
	}

	/**
	 * Testing get Relative dir from Absolute Dir
	 *
	 * @param $absDir
	 * @param $expected
	 * @param $message
	 *
	 * @dataProvider providerGetAbsolute
	 */
	public function testGetValidAbsoluteDirectory($absDir, $expected, $message)
	{
		Directories::setWebDirectory(self::getRootDir());
		$actual = Directories::getValidAbsoluteDirectory($absDir);
		$this->assertEquals(Directories::cleanPath($expected), $actual, $message);
	}

	/**
	 * @return array
	 */
	public function providerGetAbsolute(): array
	{
		return [
		    ['/tests/unit/datas\\//directories/jsTwoDirectory',
		     self::getRootDir() . '/tests/unit/datas/directories/jsTwoDirectory',
		     'Ugly Input dir is cleaned an valid Absolute Dir',
		    ],
		    [
			self::getRootDir(),
			self::getRootDir(),
			'Root is Root',
		    ],
		    [
			'/tests/unit/datas\\//',
			self::getRootDir() . '/tests/unit/datas/',
			'Ugly Input dir is cleaned an valid relative Dir',
		    ],
		    [
			'//unit/datas\\//',
			null,
			'Non Recoverable Directory ',
		    ],
		    [self::getRootDir() . '/tests/unit/datas/directories/jsoneDirectory',
		     self::getRootDir() . '/tests/unit/datas/directories/jsoneDirectory',
		     'input dir is an absolute dir',
		    ],
		    ['/tests/unit/datas/directories/jsTwoDirectory',
		     self::getRootDir() . '/tests/unit/datas/directories/jsTwoDirectory',
		     'input dir is already an relative dir',
		    ],
		    [
			'/tests/unit/datas/directories/jsTwoDirectory/../../',
			self::getRootDir() . '/tests/unit/datas/directories/jsTwoDirectory/',
			'relative directory is evil but will be cleaned true',
		    ],
		    [
			'/unit/datas/directories/jsTwoDirectory/../../',
			null,
			'is not an relative dir',
		    ],
		];
	}
}
